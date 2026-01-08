<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Tenancy;
use App\Models\Transaction;
use App\Models\Tenant;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;
use Mpdf\Mpdf;

class StatementController extends Controller
{
    public function index(Request $request)
    {
        $landlords = Landlord::where('status', 1)->get();
        
        if ($request->ajax() && $request->has('landlord_id')) {
            $properties = Property::where('landlord_id', $request->landlord_id)->get();
            return response()->json($properties);
        }

        return view('admin.statement.index', compact('landlords'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'landlord_id' => 'required|exists:landlords,id',
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $landlord = Landlord::findOrFail($request->landlord_id);
        $property = Property::findOrFail($request->property_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get all tenancies for this property within the date range
        $tenancies = Tenancy::where('property_id', $property->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->with('tenant')
            ->get();

        // Get all months in range
        $months = $this->getMonthsInRange($startDate, $endDate);

        // Build statement data with categories as rows
        $statementData = $this->buildStatementData($property, $months, $startDate, $endDate);

        $data = [
            'landlord' => $landlord,
            'property' => $property,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tenancies' => $tenancies,
            'months' => $months,
            'statementData' => $statementData,
            'summary' => $this->calculateSummary($statementData, $months)
        ];

        return view('admin.statement.view', $data);
    }

    private function getMonthsInRange($startDate, $endDate)
    {
        $months = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $months[] = [
                'key' => $current->format('Y-m'),
                'display' => $current->format('M Y'),
                'start' => $current->copy()->startOfMonth(),
                'end' => $current->copy()->endOfMonth()
            ];
            $current->addMonth();
        }

        return $months;
    }

    private function buildStatementData($property, $months, $startDate, $endDate)
    {
        $data = [];

        // Add Rent Income Row
        $data['rent'] = [
            'type' => 'income',
            'category' => 'Rent Received',
            'values' => []
        ];

        // Add Other Income Categories
        $incomeCategories = Income::where('status', 1)->get();
        foreach ($incomeCategories as $income) {
            $data['income_' . $income->id] = [
                'type' => 'income',
                'category' => $income->name,
                'income_id' => $income->id,
                'values' => []
            ];
        }

        // Add Expense Categories
        $expenseCategories = Expense::where('status', 1)->get();
        foreach ($expenseCategories as $expense) {
            $data['expense_' . $expense->id] = [
                'type' => 'expense',
                'category' => $expense->name,
                'expense_id' => $expense->id,
                'values' => []
            ];
        }

        // Fill in values for each month
        foreach ($months as $month) {
            $monthKey = $month['key'];

            // Rent Income
            $rentAmount = $this->getRentForMonth($property->id, $month['start'], $month['end']);
            $data['rent']['values'][$monthKey] = $rentAmount;

            // Other Income
            foreach ($incomeCategories as $income) {
                $amount = $this->getIncomeForMonth($property->id, $income->id, $month['start'], $month['end']);
                $data['income_' . $income->id]['values'][$monthKey] = $amount;
            }

            // Expenses
            foreach ($expenseCategories as $expense) {
                $amount = $this->getExpenseForMonth($property->id, $expense->id, $month['start'], $month['end']);
                $data['expense_' . $expense->id]['values'][$monthKey] = $amount;
            }
        }

        return $data;
    }

    private function getRentForMonth($propertyId, $start, $end)
    {
        // Get rent transactions (received)
        $received = Transaction::where('property_id', $propertyId)
            ->where('transaction_type', 'received')
            ->whereHas('income', function ($query) {
                $query->whereRaw('LOWER(name) = ?', ['rent']);
            })
            ->whereBetween('date', [$start, $end])
            ->sum('received_amount');

        // If no received, check due transactions
        if ($received == 0) {
            $received = Transaction::where('property_id', $propertyId)
                ->where('transaction_type', 'due')
                ->whereBetween('date', [$start, $end])
                ->sum('amount');
        }

        return $received;
    }

    private function getIncomeForMonth($propertyId, $incomeId, $start, $end)
    {
        return Transaction::where('property_id', $propertyId)
            ->where('income_id', $incomeId)
            ->where('transaction_type', 'received')
            ->whereBetween('date', [$start, $end])
            ->sum('received_amount');
    }

    private function getExpenseForMonth($propertyId, $expenseId, $start, $end)
    {
        return Transaction::where('property_id', $propertyId)
            ->where('expense_id', $expenseId)
            ->whereBetween('date', [$start, $end])
            ->sum('amount');
    }

    private function calculateSummary($statementData, $months)
    {
        $summary = [
            'totalRent' => 0,
            'totalOtherIncome' => 0,
            'totalExpenses' => 0,
            'monthlyTotals' => []
        ];

        // Calculate totals
        foreach ($months as $month) {
            $monthKey = $month['key'];
            $monthTotal = 0;

            // Add rent
            $rent = $statementData['rent']['values'][$monthKey] ?? 0;
            $summary['totalRent'] += $rent;
            $monthTotal += $rent;

            // Add other income
            foreach ($statementData as $key => $row) {
                if ($row['type'] === 'income' && $key !== 'rent') {
                    $amount = $row['values'][$monthKey] ?? 0;
                    $summary['totalOtherIncome'] += $amount;
                    $monthTotal += $amount;
                }
            }

            // Subtract expenses
            foreach ($statementData as $key => $row) {
                if ($row['type'] === 'expense') {
                    $amount = $row['values'][$monthKey] ?? 0;
                    $summary['totalExpenses'] += $amount;
                    $monthTotal -= $amount;
                }
            }

            $summary['monthlyTotals'][$monthKey] = $monthTotal;
        }

        $summary['netIncome'] = $summary['totalRent'] + $summary['totalOtherIncome'] - $summary['totalExpenses'];

        return $summary;
    }

    public function generatePdf(Request $request)
    {
        $request->validate([
            'landlord_id' => 'required|exists:landlords,id',
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $landlord = Landlord::findOrFail($request->landlord_id);
        $property = Property::findOrFail($request->property_id);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get all tenancies for this property within the date range
        $tenancies = Tenancy::where('property_id', $property->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
            })
            ->with('tenant')
            ->get();

        // Get all months in range
        $months = $this->getMonthsInRange($startDate, $endDate);

        // Build statement data with categories as rows
        $statementData = $this->buildStatementData($property, $months, $startDate, $endDate);

        $data = [
            'landlord' => $landlord,
            'property' => $property,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'tenancies' => $tenancies,
            'months' => $months,
            'statementData' => $statementData,
            'summary' => $this->calculateSummary($statementData, $months)
        ];

        // Generate HTML from view
        $html = view('admin.statement.pdf', $data)->render();

        // Create MPDF instance
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
        ]);

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Generate filename
        $filename = $property->property_reference . '_Statement_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf';

        // Output PDF for download
        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}