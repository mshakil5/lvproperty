<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Income;
use App\Models\Expense;
use DataTables;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function incomeReport(Request $request)
    {
        if ($request->ajax()) {
            $startDate = $request->get('start_date') ?: now()->startOfMonth()->format('Y-m-d');
            $endDate = $request->get('end_date') ?: now()->format('Y-m-d');

            $incomes = Transaction::with(['property', 'tenant', 'income'])
                ->where(function ($q) {
                    $q->where('transaction_type', 'due')
                    ->orWhere('transaction_type', 'received');
                })
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $balance = 0;

            $data = $incomes->map(function($row) use (&$balance) {
                $amount = $row->amount ?? 0;

                if ($amount > 0) {
                    if ($row->transaction_type === 'due') {
                        $balance += $amount;
                        return [
                            'date' => $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A',
                            'tran_id' => $row->tran_id,
                            'description' => $row->description ?? 'Payment due',
                            'debit' => '£' . number_format($amount, 2),
                            'credit' => '-',
                            'balance' => '£' . number_format($balance, 2),
                            'property' => $row->property?->property_reference ?? 'N/A',
                            'tenant' => $row->tenant?->name ?? 'N/A',
                        ];
                    } else {
                        $balance -= $amount;
                        return [
                            'date' => $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A',
                            'tran_id' => $row->tran_id,
                            'description' => $row->description ?? 'Payment received',
                            'debit' => '-',
                            'credit' => '£' . number_format($amount, 2),
                            'balance' => '£' . number_format($balance, 2),
                            'property' => $row->property?->property_reference ?? 'N/A',
                            'tenant' => $row->tenant?->name ?? 'N/A',
                        ];
                    }
                }
                
                return null;
            })->filter()->values();

            return DataTables::of($data)
                ->addIndexColumn()
                ->rawColumns(['debit', 'credit', 'balance'])
                ->make(true);
        }

        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        return view('admin.report.income', compact('startDate', 'endDate'));
    }

    public function expenseReport(Request $request)
    {
        if ($request->ajax()) {
            $expenses = Expense::orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $balance = 0;

            $data = $expenses->map(function($row) use (&$balance) {
                $balance -= $row->amount;

                return [
                    'date' => $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A',
                    'exp_id' => $row->id,
                    'description' => $row->description ?? 'Expense',
                    'category' => $row->category ?? 'N/A',
                    'debit' => '£' . number_format($row->amount, 2),
                    'credit' => '-',
                    'balance' => '£' . number_format($balance, 2),
                ];
            })->values();

            return DataTables::of($data)
                ->addIndexColumn()
                ->rawColumns(['debit', 'credit', 'balance'])
                ->make(true);
        }

        return view('admin.report.expense');
    }

    public function profitLossReport(Request $request)
    {
        $totalIncome = Transaction::where(function($q) {
            $q->where('transaction_type', 'due')
              ->orWhere('transaction_type', 'received');
        })
        ->sum('received_amount');

        $totalExpense = Expense::sum('amount');
        $profitLoss = $totalIncome - $totalExpense;

        $reportData = [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'profit_loss' => $profitLoss,
            'is_profit' => $profitLoss >= 0
        ];

        if ($request->ajax()) {
            return response()->json($reportData);
        }

        return view('admin.report.profitloss', compact('reportData'));
    }

    public function getReportDetails(Request $request)
    {
        $type = $request->get('type');

        if ($type === 'income') {
            $transactions = Transaction::with(['property', 'tenant', 'income'])
                ->where('transaction_type', 'due')
                ->orWhere('transaction_type', 'received')
                ->orderBy('date', 'asc')
                ->get();

            $balance = 0;
            $details = $transactions->map(function($row) use (&$balance) {
                $amount = $row->received_amount ?? 0;

                if ($amount > 0) {
                    $balance += $amount;
                    return [
                        'date' => $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A',
                        'tran_id' => $row->tran_id,
                        'description' => $row->description ?? 'Payment received',
                        'amount' => $amount,
                        'balance' => $balance,
                        'property' => $row->property?->property_reference ?? 'N/A',
                        'tenant' => $row->tenant?->name ?? 'N/A',
                    ];
                }
                return null;
            })->filter()->values()->toArray();
        } else {
            $expenses = Expense::orderBy('date', 'asc')->get();

            $balance = 0;
            $details = $expenses->map(function($row) use (&$balance) {
                $balance -= $row->amount;

                return [
                    'date' => $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A',
                    'exp_id' => $row->id,
                    'description' => $row->description ?? 'Expense',
                    'category' => $row->category ?? 'N/A',
                    'amount' => $row->amount,
                    'balance' => $balance,
                ];
            })->toArray();
        }

        return response()->json(['details' => $details]);
    }
}