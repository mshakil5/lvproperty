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
            $incomes = Transaction::with(['property', 'tenant'])
                ->where('transaction_type', 'due')
                ->where('received_amount', '>', 0)
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
        $properties = Property::where('status', 1)->get();
        return view('admin.income.create', compact('properties'));
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
        $request->validate([
            'date' => 'required|date',
            'payment_type' => 'required',
            'selected_transactions' => 'required|array|min:1',
            'total_amount' => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $dues = Transaction::whereIn('id', $request->selected_transactions)
                ->where('transaction_type', 'due')
                ->where('status', false)
                ->orderBy('id')
                ->get();

            $received = Transaction::create([
                'tran_id' => 'INC-' . time(),
                'date' => $request->date,
                'amount' => $request->total_amount,
                'payment_type' => $request->payment_type,
                'transaction_type' => 'received',
                'status' => true,
                'description' => 'Payment received'
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
        $due = Transaction::where('id', $id)->firstOrFail();

        // Show all received transactions linked to this due
        $receivedList = collect($due->received_ids ?? [])->map(fn($r) => Transaction::find($r['id']))
            ->filter()
            ->map(fn($r) => [
                'tran_id' => $r->tran_id,
                'date' => date('d M, Y', strtotime($r->date)),
                'amount' => '£' . number_format(array_reduce($due->received_ids ?? [], fn($carry, $ri) => $ri['id'] == $r->id ? $ri['amount'] : $carry, 0), 2),
                'payment_type' => ucfirst($r->payment_type),
            ]);

        return response()->json([
            'income' => [
                'tran_id' => $due->tran_id,
                'date' => date('d M, Y', strtotime($due->date)),
                'property_reference' => $due->property?->property_reference ?? 'N/A',
                'tenant_name' => $due->tenant?->name ?? 'N/A',
                'total_amount' => '£' . number_format($due->amount, 2),
                'received_amount' => '£' . number_format($due->received_amount, 2),
                'remaining_due' => '£' . number_format($due->remaining_due, 2),
                'status' => $due->status ? 'Paid' : 'Due',
            ],
            'received_transactions' => $receivedList
        ]);
    }
}