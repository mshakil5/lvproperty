<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Property;
use DataTables;
use Illuminate\Support\Facades\DB;

class ExpenseTransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $expenses = Transaction::with(['expense', 'property'])
                ->whereNotNull('expense_id')
                ->select(['id', 'tran_id', 'date', 'expense_id', 'property_id', 'amount', 'description', 'status'])
                ->orderBy('id', 'desc');

            return DataTables::of($expenses)
                ->addIndexColumn()
                ->addColumn('expense_name', function ($row) {
                    return $row->expense ? $row->expense->name : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('property_reference', function ($row) {
                    return $row->property ? $row->property->property_reference : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('date', function ($row) {
                    return $row->date ? date('d M, Y', strtotime($row->date)) : 'N/A';
                })
                ->addColumn('amount', function ($row) {
                    return '£' . number_format($row->amount, 2);
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                      data-id="'.$row->id.'" '.$checked.'>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <button class="dropdown-item" id="EditBtn" rid="'.$row->id.'">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                    </button>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item deleteBtn" 
                                            data-delete-url="' . route('expenses.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#expenseTransactionTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['expense_name', 'property_reference', 'amount', 'status', 'action'])
                ->make(true);
        }

        $expenseCategories = Expense::where('status', 1)->get();
        $properties = Property::where('status', 1)->get();
        return view('admin.expense-transaction.index', compact('expenseCategories', 'properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'property_id' => 'required|exists:properties,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $transaction = new Transaction();
            $transaction->tran_id = 'EXP-' . time() . '-' . rand(1000, 9999);
            $transaction->date = $request->date;
            $transaction->expense_id = $request->expense_id;
            $transaction->property_id = $request->property_id;
            $transaction->amount = $request->amount;
            $transaction->transaction_type = 'payable'; // Money going out
            $transaction->description = $request->description;
            $transaction->status = false;

            if ($transaction->save()) {
                DB::commit();
                return response()->json([
                    'message' => 'Expense recorded successfully!',
                    'transaction' => $transaction 
                ], 200);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Server error while recording expense: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $transaction = Transaction::with(['expense', 'property'])->where('id', $id)->whereNotNull('expense_id')->first();
        return response()->json($transaction);
    }

    public function update(Request $request)
    {
        $request->validate([
            'expense_id' => 'required|exists:expenses,id',
            'property_id' => 'required|exists:properties,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $request->codeid)->whereNotNull('expense_id')->firstOrFail();
            
            $transaction->date = $request->date;
            $transaction->expense_id = $request->expense_id;
            $transaction->property_id = $request->property_id;
            $transaction->amount = $request->amount;
            $transaction->description = $request->description;

            if ($transaction->save()) {
                DB::commit();
                return response()->json([
                    'message' => 'Expense updated successfully!'
                ], 200);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update expense: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $id)->whereNotNull('expense_id')->first();
            
            if (!$transaction) {
                return response()->json([
                    'message' => 'Expense transaction not found.'
                ], 404);
            }

            if ($transaction->delete()) {
                DB::commit();
                return response()->json([
                    'message' => 'Expense deleted successfully.'
                ], 200);
            }

            DB::rollback();
            return response()->json([
                'message' => 'Failed to delete expense.'
            ], 500);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to delete: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request)
    {
        $transaction = Transaction::where('id', $request->id)->whereNotNull('expense_id')->first();
        
        if (!$transaction) {
            return response()->json([
                'message' => 'Expense transaction not found'
            ], 404);
        }

        $transaction->status = $request->status;

        if ($transaction->save()) {
            return response()->json([
                'message' => 'Expense status updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update expense status'
        ], 500);
    }
}