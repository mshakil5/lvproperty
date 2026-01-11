<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Tenancy;
use App\Models\Transaction;
use Carbon\Carbon;
use Mpdf\Mpdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $landlords = Landlord::where('status', 1)->get();
        
        if ($request->ajax() && $request->has('landlord_id')) {
            $properties = Property::where('landlord_id', $request->landlord_id)->get();
            return response()->json($properties);
        }

        return view('admin.invoice.index', compact('landlords'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'landlord_id' => 'required|exists:landlords,id',
            'property_id' => 'required|exists:properties,id',
            'month' => 'required|date_format:Y-m',
        ]);

        $landlord = Landlord::findOrFail($request->landlord_id);
        $property = Property::with('landlord')->findOrFail($request->property_id);

        // Parse month
        $month = Carbon::createFromFormat('Y-m', $request->month);
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        // Get current tenant for this property (latest active tenancy)
        $currentTenancy = $property->tenancies()
            ->where('status', 'active')
            ->latest()
            ->first();
        
        $currentTenant = $currentTenancy ? $currentTenancy->tenant : null;

        // Get all transactions for this property in this month
        $transactions = Transaction::where('property_id', $property->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        // Calculate summary data
        $summary = $this->calculateInvoiceSummary($transactions);

        // Get monthly rent from tenancy
        $monthlyRent = $currentTenancy ? $currentTenancy->amount : 0;

        $data = [
            'landlord' => $landlord,
            'property' => $property,
            'currentTenant' => $currentTenant,
            'currentTenancy' => $currentTenancy,
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'transactions' => $transactions,
            'summary' => $summary,
            'monthlyRent' => $monthlyRent
        ];

        return view('admin.invoice.view', $data);
    }

    private function calculateInvoiceSummary($transactions)
    {
        $summary = [
            'totalReceipts' => 0,
            'totalDeductions' => 0,
            'balance' => 0,
            'rentAmount' => 0,
            'monthlyRent' => 0
        ];

        foreach ($transactions as $transaction) {
            if ($transaction->transaction_type === 'received') {
                $summary['totalReceipts'] += $transaction->amount ?? $transaction->amount;
                
                // Check if it's rent income
                if ($transaction->income && strtolower($transaction->income->name) === 'rent') {
                    $summary['rentAmount'] += $transaction->amount ?? $transaction->amount;
                }
            } elseif ($transaction->expense_id) {
                $summary['totalDeductions'] += $transaction->amount;
            } elseif ($transaction->transaction_type === 'due') {
                // For due transactions, add to receipts side (expected rent)
                $summary['monthlyRent'] = $transaction->amount;
            }
        }

        // Calculate balance (receipts - deductions)
        $summary['balance'] = $summary['totalReceipts'] - $summary['totalDeductions'];

        return $summary;
    }

    public function generatePdf(Request $request)
    {
        $request->validate([
            'landlord_id' => 'required|exists:landlords,id',
            'property_id' => 'required|exists:properties,id',
            'month' => 'required|date_format:Y-m'
        ]);

        $landlord = Landlord::findOrFail($request->landlord_id);
        $property = Property::findOrFail($request->property_id);
        $month = Carbon::createFromFormat('Y-m', $request->month);

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        // Get current tenant
        $currentTenancy = Tenancy::where('property_id', $property->id)
            ->where('status', 'active')
            ->latest()
            ->first();
        
        $currentTenant = $currentTenancy ? $currentTenancy->tenant : null;

        // Get monthly rent
        $monthlyRent = $currentTenancy ? $currentTenancy->amount : 0;

        // Get transactions
        $transactions = Transaction::where('property_id', $property->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['income', 'expense'])
            ->orderBy('date')
            ->get();

        // Calculate summary
        $totalReceipts = $transactions->where('transaction_type', 'received')->sum('amount');
        $totalDeductions = $transactions->where('expense_id', '!=', null)->sum('amount');
        $balance = $totalReceipts - $totalDeductions;

        $summary = [
            'totalReceipts' => $totalReceipts,
            'totalDeductions' => $totalDeductions,
            'balance' => $balance
        ];

        $data = [
            'landlord' => $landlord,
            'property' => $property,
            'month' => $month,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'currentTenant' => $currentTenant,
            'monthlyRent' => $monthlyRent,
            'transactions' => $transactions,
            'summary' => $summary
        ];

        // Generate HTML from view
        $html = view('admin.invoice.pdf', $data)->render();

        // Create MPDF instance
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
            'autoScriptToLang' => true,
            'autoLangToScript' => true,
        ]);

        // Set default font
        $mpdf->SetDefaultFont('Arial');

        // Write HTML to PDF
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        // Generate filename
        $filename = $property->property_reference . '_Invoice_' . $month->format('m-Y') . '.pdf';

        // Output PDF to browser stream (display instead of download)
        return response($mpdf->Output($filename, 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}