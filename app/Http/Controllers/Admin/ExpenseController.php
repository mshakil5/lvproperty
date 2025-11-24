<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use DataTables;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $expenses = Expense::orderBy('id', 'desc');

            return DataTables::of($expenses)
                ->addIndexColumn()
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
                                            data-delete-url="' . route('expense.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#expenseTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.expense.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:expenses,name',
            'description' => 'nullable'
        ]);

        $data = new Expense;
        $data->name = $request->name;
        $data->description = $request->description;
        
        if ($data->save()) {
            return response()->json([
                'message' => 'Expense created successfully!',
                'expense' => $data 
            ], 200);
        }

        return response()->json([
            'message' => 'Server error while creating expense.'
        ], 500);
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Expense::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:expenses,name,' . $request->codeid,
            'description' => 'nullable'
        ]);

        $data = Expense::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->description = $request->description;

        if ($data->save()) {
            return response()->json([
                'message' => 'Expense updated successfully!'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update expense. Please try again.'
        ], 500);
    }

    public function delete($id)
    {
        $data = Expense::find($id);
        
        if (!$data) {
            return response()->json([
                'message' => 'Expense not found.'
            ], 404);
        }

        if ($data->delete()) {
            return response()->json([
                'message' => 'Expense deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete expense.'
        ], 500);
    }

    public function toggleStatus(Request $request)
    {
        $expense = Expense::find($request->id);
        
        if (!$expense) {
            return response()->json([
                'message' => 'Expense not found'
            ], 404);
        }

        $expense->status = $request->status;

        if ($expense->save()) {
            return response()->json([
                'message' => 'Expense status updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update expense status'
        ], 500);
    }
}