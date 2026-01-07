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
            $startDate = $request->get('start_date') ?: now()->startOfMonth()->format('Y-m-d');
            $endDate = $request->get('end_date') ?: now()->format('Y-m-d');

            $expenses = Transaction::with(['expense', 'property'])
                ->whereNotNull('expense_id')
                ->where('transaction_type', 'payable')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $balance = 0;

            $data = $expenses->map(function($row) use (&$balance) {
                $amount = $row->amount ?? 0;

                if ($amount > 0) {
                    $balance -= $amount;

                    return [
                        'date' => $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A',
                        'tran_id' => $row->tran_id,
                        'description' => $row->description ?? 'Expense',
                        'debit' => '£' . number_format($amount, 2),
                        'credit' => '-',
                        'balance' => '£' . number_format($balance, 2),
                        'property' => $row->property?->property_reference ?? 'N/A',
                        'expense' => $row->expense?->name ?? 'N/A',
                    ];
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
        return view('admin.report.expense', compact('startDate', 'endDate'));
    }

    public function profitLossReport(Request $request)
    {
        $startDate = $request->get('start_date') ?: now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->get('end_date') ?: now()->format('Y-m-d');

        // Get all income transactions
        $incomeTransactions = Transaction::with(['property', 'tenant', 'income'])
            ->where(function ($q) {
                $q->where('transaction_type', 'due')
                ->orWhere('transaction_type', 'received');
            })
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Get all expense transactions
        $expenseTransactions = Transaction::with(['expense', 'property'])
            ->whereNotNull('expense_id')
            ->where('transaction_type', 'payable')
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Build income breakdown with debit/credit/balance
        $incomeBalance = 0;
        $incomeDetails = [];
        
        foreach ($incomeTransactions as $transaction) {
            $amount = $transaction->amount ?? 0;
            
            if ($amount > 0) {
                if ($transaction->transaction_type === 'due') {
                    $incomeBalance += $amount;
                    $incomeDetails[] = [
                        'date' => $transaction->date ? date('d M, Y', strtotime($transaction->date)) : 'N/A',
                        'tran_id' => $transaction->tran_id,
                        'description' => $transaction->description ?? 'Payment due',
                        'debit' => '£' . number_format($amount, 2),
                        'credit' => '-',
                        'balance' => '£' . number_format($incomeBalance, 2),
                        'property' => $transaction->property?->property_reference ?? 'N/A',
                        'tenant' => $transaction->tenant?->name ?? 'N/A',
                    ];
                } else {
                    $incomeBalance -= $amount;
                    $incomeDetails[] = [
                        'date' => $transaction->date ? date('d M, Y', strtotime($transaction->date)) : 'N/A',
                        'tran_id' => $transaction->tran_id,
                        'description' => $transaction->description ?? 'Payment received',
                        'debit' => '-',
                        'credit' => '£' . number_format($amount, 2),
                        'balance' => '£' . number_format($incomeBalance, 2),
                        'property' => $transaction->property?->property_reference ?? 'N/A',
                        'tenant' => $transaction->tenant?->name ?? 'N/A',
                    ];
                }
            }
        }

        // Build expense breakdown with debit/credit/balance
        $expenseBalance = 0;
        $expenseDetails = [];
        
        foreach ($expenseTransactions as $transaction) {
            $amount = $transaction->amount ?? 0;
            
            if ($amount > 0) {
                $expenseBalance -= $amount;
                $expenseDetails[] = [
                    'date' => $transaction->date ? date('d M, Y', strtotime($transaction->date)) : 'N/A',
                    'tran_id' => $transaction->tran_id,
                    'description' => $transaction->description ?? 'Expense',
                    'debit' => '£' . number_format($amount, 2),
                    'credit' => '-',
                    'balance' => '£' . number_format($expenseBalance, 2),
                    'property' => $transaction->property?->property_reference ?? 'N/A',
                    'expense' => $transaction->expense?->name ?? 'N/A',
                ];
            }
        }

        // Calculate totals
        $totalIncome = $incomeBalance;
        $totalExpense = abs($expenseBalance);
        $profitLoss = $totalIncome - $totalExpense;
        $isProfit = $profitLoss >= 0;

        $reportData = [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'profit_loss' => $profitLoss,
            'is_profit' => $isProfit,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'income_details' => $incomeDetails,
            'expense_details' => $expenseDetails
        ];

        if ($request->ajax()) {
            return response()->json($reportData);
        }

        return view('admin.report.profitloss', compact('reportData', 'startDate', 'endDate'));
    }
}