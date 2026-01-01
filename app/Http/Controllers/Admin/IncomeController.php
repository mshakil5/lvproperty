<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Property;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Income;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
        $incomes = Transaction::with(['property', 'tenant', 'income'])
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('transaction_type', 'due')
                       ->where('received_amount', '>', 0);
                })
                ->orWhere(function ($q2) {
                    $q2->where('transaction_type', 'received')
                    ->whereHas('income', function ($iq) {
                        $iq->whereRaw('LOWER(name) != ?', ['rent']);
                    });
                });
                
            })
            ->orderBy('id', 'desc');

            return DataTables::of($incomes)
                ->addIndexColumn()
                ->addColumn('date', fn($row) => $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A')
                ->addColumn('amount', fn($row) => '£' . number_format($row->received_amount, 2)) // show received_amount
                ->addColumn('action', fn($row) => '
                    <button class="btn btn-soft-primary btn-sm view-income-details" data-income-id="' . $row->id . '">
                        <i class="ri-eye-fill align-middle"></i> View
                    </button>
                ')
                ->addColumn('property', fn ($row) =>
                    $row->property?->property_reference ?? 'N/A'
                )
                ->addColumn('tenant', fn ($row) =>
                    $row->tenant?->name ?? 'N/A'
                )
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.income.index');
    }

    public function create()
    {
        $properties = Property::where('status', 1)->latest()->get();
        $incomes = Income::where('status', 1)->latest()->get();
        return view('admin.income.create', compact('properties', 'incomes'));
    }

    public function getDueTransactions(Request $request)
    {
        $dues = Transaction::with(['property', 'tenant'])
            ->where('transaction_type', 'due')
            ->where('status', false)
            ->when($request->property_id, fn($q) => $q->where('property_id', $request->property_id))
            ->get()
            ->filter(fn($d) => $d->remaining_due > 0)
            ->map(fn($d) => [
                'id' => $d->id,
                'tran_id' => $d->tran_id,
                'property_reference' => $d->property?->property_reference ?? 'N/A',
                'tenant_name' => $d->tenant?->name ?? 'N/A',
                'amount' => '£' . number_format($d->remaining_due, 2),
                'raw_amount' => $d->remaining_due,
            ])
            ->values();

        return response()->json($dues);
    }

    public function store(Request $request)
    {
        $income = Income::find($request->income_id);
        $isRent = strtolower($income->name) === 'rent';

        if ($isRent) {
            $request->validate([
                'income_id' => 'required|exists:incomes,id',
                'property_id' => 'required|exists:properties,id',
                'date' => 'required|date',
                'payment_type' => 'required',
                'selected_transactions' => 'required|array|min:1',
                'total_amount' => 'required|numeric|min:0.01',
            ]);
        } else {
            $request->validate([
                'income_id' => 'required|exists:incomes,id',
                'date' => 'required|date',
                'payment_type' => 'required',
                'total_amount' => 'required|numeric|min:0.01',
            ]);
        }

        DB::beginTransaction();

        try {
            if (!$isRent) {
                $receivedData = [
                    'tran_id' => 'INC-' . time() . '-' . rand(1000, 9999),
                    'date' => $request->date,
                    'amount' => $request->total_amount,
                    'received_amount' => $request->total_amount,
                    'payment_type' => $request->payment_type,
                    'transaction_type' => 'received',
                    'status' => true,
                    'income_id' => $request->income_id,
                    'description' => $request->description ?? 'Payment received'
                ];

                // If property is selected, get tenant/landlord from last transaction of that property
                if ($request->property_id) {
                    $lastTransaction = Transaction::where('property_id', $request->property_id)
                        ->latest()
                        ->first();

                    $receivedData['property_id'] = $request->property_id;
                    $receivedData['tenant_id'] = $lastTransaction?->tenant_id;
                    $receivedData['landlord_id'] = $lastTransaction?->landlord_id;
                    $receivedData['tenancy_id'] = $lastTransaction?->tenancy_id;
                }

                $received = Transaction::create($receivedData);

                DB::commit();
                return response()->json(['message' => 'Payment received successfully']);
            }

            // If RENT - Process with dues
            $dues = Transaction::whereIn('id', $request->selected_transactions)
                ->where('transaction_type', 'due')
                ->where('status', false)
                ->orderBy('id')
                ->get();

            if ($dues->isEmpty()) {
                return response()->json(['message' => 'No valid dues selected'], 422);
            }

            // Get tenant_id, landlord_id, property_id, tenancy_id from first due
            $firstDue = $dues->first();

            $received = Transaction::create([
                'tran_id' => 'INC-' . time() . '-' . rand(1000, 9999),
                'date' => $request->date,
                'amount' => $request->total_amount,
                'payment_type' => $request->payment_type,
                'transaction_type' => 'received',
                'status' => true,
                'income_id' => $request->income_id,
                'property_id' => $request->property_id,
                'tenant_id' => $firstDue->tenant_id,
                'landlord_id' => $firstDue->landlord_id,
                'tenancy_id' => $firstDue->tenancy_id,
                'description' => $request->description ?? 'Payment received'
            ]);

            $remaining = $request->total_amount;

            foreach ($dues as $due) {
                if ($remaining <= 0) break;

                $pay = min($remaining, $due->remaining_due);

                $received_ids = $due->received_ids ?? [];
                $received_ids[] = ['id' => $received->id, 'amount' => $pay];

                $due->update([
                    'received_amount' => $due->received_amount + $pay,
                    'received_ids' => $received_ids,
                    'status' => ($due->received_amount + $pay) >= $due->amount,
                ]);

                $remaining -= $pay;
            }

            DB::commit();
            return response()->json(['message' => 'Payment received successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getIncomeDetails($id)
    {
        $trx = Transaction::with(['property', 'tenant', 'income'])->findOrFail($id);

        // Build received transactions list
        $receivedList = [];

        if (!empty($trx->received_ids)) {
            // If received_ids exist, map them
            $receivedList = collect($trx->received_ids)->map(function($r) use ($trx) {
                $rTransaction = Transaction::find($r['id']);
                if (!$rTransaction) return null;

                $amount = array_reduce($trx->received_ids ?? [], function($carry, $ri) use ($rTransaction) {
                    return $ri['id'] == $rTransaction->id ? $ri['amount'] : $carry;
                }, 0);

                return [
                    'tran_id' => $rTransaction->tran_id,
                    'date' => date('d M, Y', strtotime($rTransaction->date)),
                    'amount' => '£' . number_format($amount, 2),
                    'payment_type' => ucfirst($rTransaction->payment_type),
                ];
            })->filter()->values();
        } else {
            // If no received_ids (like non-rent single payment), just return this transaction
            $receivedList[] = [
                'tran_id' => $trx->tran_id,
                'date' => date('d M, Y', strtotime($trx->date)),
                'amount' => '£' . number_format($trx->received_amount, 2),
                'payment_type' => ucfirst($trx->payment_type),
            ];
        }

        return response()->json([
            'income' => [
                'tran_id' => $trx->tran_id,
                'date' => date('d M, Y', strtotime($trx->date)),
                'property_reference' => $trx->property?->property_reference ?? 'N/A',
                'tenant_name' => $trx->tenant?->name ?? 'N/A',
                'total_amount' => '£' . number_format($trx->amount, 2),
                'received_amount' => '£' . number_format($trx->received_amount, 2),
                'remaining_due' => '£' . number_format($trx->remaining_due, 2),
                'status' => $trx->status ? 'Paid' : 'Due',
            ],
            'received_transactions' => $receivedList
        ]);
    }
}