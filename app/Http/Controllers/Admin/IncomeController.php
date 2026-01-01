<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Property;
use DataTables;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $incomes = Transaction::with(['property', 'tenant', 'landlord'])
                ->where('transaction_type', 'received')
                ->select(['id', 'tran_id', 'date', 'property_id', 'tenant_id', 'landlord_id', 'amount', 'payment_type', 'description', 'status', 'created_at'])
                ->orderBy('id', 'desc');

            return DataTables::of($incomes)
                ->addIndexColumn()
                ->addColumn('property_reference', function ($row) {
                    return $row->property ? $row->property->property_reference : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('tenant_name', function ($row) {
                    return $row->tenant ? $row->tenant->name : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('date', function ($row) {
                    return $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A';
                })
                ->addColumn('amount', function ($row) {
                    return '£' . number_format($row->amount, 2);
                })
                ->addColumn('payment_type', function ($row) {
                    $badge_class = [
                        'cash' => 'bg-success',
                        'bank' => 'bg-primary', 
                        'card' => 'bg-info',
                        'online' => 'bg-warning'
                    ][$row->payment_type] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badge_class.'">'.ucfirst($row->payment_type).'</span>';
                })
                ->addColumn('status', function ($row) {
                    $badge_class = $row->status ? 'bg-success' : 'bg-danger';
                    $status_text = $row->status ? 'Completed' : 'Pending';
                    return '<span class="badge '.$badge_class.'">'.$status_text.'</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-soft-primary btn-sm view-income-details" 
                                data-income-id="'.$row->id.'">
                            <i class="ri-eye-fill align-middle"></i> View
                        </button>
                    ';
                })
                ->rawColumns(['property_reference', 'tenant_name', 'amount', 'payment_type', 'status', 'action'])
                ->make(true);
        }

        return view('admin.income.index');
    }

    public function create()
    {
        $properties = Property::where('status', 1)->get();
        return view('admin.income.create', compact('properties'));
    }

    public function getDueTransactions(Request $request)
    {
        $dueTransactions = Transaction::with(['property', 'tenant'])
            ->where('transaction_type', 'due')
            ->where('status', false)
            ->when($request->property_id, function($query) use ($request) {
                return $query->where('property_id', $request->property_id);
            })
            ->select(['id', 'tran_id', 'date', 'property_id', 'tenant_id', 'amount', 'description'])
            ->get()
            ->map(function($transaction) {
                return [
                    'id' => $transaction->id,
                    'tran_id' => $transaction->tran_id,
                    'date' => date('d M, Y', strtotime($transaction->date)),
                    'property_reference' => $transaction->property ? $transaction->property->property_reference : 'N/A',
                    'tenant_name' => $transaction->tenant ? $transaction->tenant->name : 'N/A',
                    'amount' => '£' . number_format($transaction->amount, 2),
                    'raw_amount' => $transaction->amount,
                    'description' => $transaction->description
                ];
            });

        return response()->json($dueTransactions);
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'date' => 'required|date',
            'payment_type' => 'required|in:cash,bank,card,online',
            'selected_transactions' => 'required|array|min:1',
            'selected_transactions.*' => 'exists:transactions,id',
            'total_amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $dueTransactions = Transaction::whereIn('id', $request->selected_transactions)
                ->where('transaction_type', 'due')
                ->where('status', false)
                ->get();

            if ($dueTransactions->count() !== count($request->selected_transactions)) {
                return response()->json([
                    'message' => 'Some selected transactions are invalid or already paid.'
                ], 422);
            }

            $calculatedTotal = $dueTransactions->sum('amount');
            if ($calculatedTotal != $request->total_amount) {
                return response()->json([
                    'message' => 'Total amount mismatch. Please refresh and try again.'
                ], 422);
            }

            $receivedTransaction = new Transaction();
            $receivedTransaction->tran_id = 'INC-' . time() . '-' . rand(1000, 9999);
            $receivedTransaction->date = $request->date;
            $receivedTransaction->property_id = $request->property_id;
            $receivedTransaction->amount = $request->total_amount;
            $receivedTransaction->payment_type = $request->payment_type;
            $receivedTransaction->transaction_type = 'received';
            $receivedTransaction->status = true;
            $receivedTransaction->description = $request->description ?: 'Payment received for selected dues';
            $receivedTransaction->save();

            foreach ($dueTransactions as $dueTransaction) {
                $dueTransaction->status = true;
                $dueTransaction->received_id = $receivedTransaction->id;
                $dueTransaction->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Payment received successfully!',
                'received_transaction' => $receivedTransaction,
                'paid_transactions_count' => $dueTransactions->count()
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getIncomeDetails($id)
    {
        $income = Transaction::with(['property', 'tenant', 'landlord'])
            ->where('id', $id)
            ->where('transaction_type', 'received')
            ->firstOrFail();

        $paidTransactions = Transaction::with(['property', 'tenant'])
            ->where('received_id', $id)
            ->where('transaction_type', 'due')
            ->get()
            ->map(function($transaction) {
                return [
                    'tran_id' => $transaction->tran_id,
                    'date' => date('d M, Y', strtotime($transaction->date)),
                    'property_reference' => $transaction->property ? $transaction->property->property_reference : 'N/A',
                    'tenant_name' => $transaction->tenant ? $transaction->tenant->name : 'N/A',
                    'amount' => '£' . number_format($transaction->amount, 2),
                    'description' => $transaction->description
                ];
            });

        return response()->json([
            'income' => [
                'tran_id' => $income->tran_id,
                'date' => date('d M, Y', strtotime($income->date)),
                'property_reference' => $income->property ? $income->property->property_reference : 'N/A',
                'amount' => '£' . number_format($income->amount, 2),
                'payment_type' => ucfirst($income->payment_type),
                'description' => $income->description,
                'created_at' => $income->created_at->format('d M, Y h:i A')
            ],
            'paid_transactions' => $paidTransactions
        ]);
    }
}