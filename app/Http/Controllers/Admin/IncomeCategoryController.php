<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Income;
use DataTables;

class IncomeCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $incomes = Income::orderBy('id', 'desc');

            return DataTables::of($incomes)
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
                                            data-delete-url="' . route('income-category.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#incomeCategoryTable">
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

        return view('admin.income-category.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:incomes,name',
            'description' => 'nullable'
        ]);

        $data = new Income;
        $data->name = $request->name;
        $data->description = $request->description;
        
        if ($data->save()) {
            return response()->json([
                'message' => 'Income category created successfully!',
                'income' => $data 
            ], 200);
        }

        return response()->json([
            'message' => 'Server error while creating income category.'
        ], 500);
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Income::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:incomes,name,' . $request->codeid,
            'description' => 'nullable'
        ]);

        $data = Income::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->description = $request->description;

        if ($data->save()) {
            return response()->json([
                'message' => 'Income category updated successfully!'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update income category. Please try again.'
        ], 500);
    }

    public function delete($id)
    {
        $data = Income::find($id);
        
        if (!$data) {
            return response()->json([
                'message' => 'Income category not found.'
            ], 404);
        }

        if ($data->delete()) {
            return response()->json([
                'message' => 'Income category deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete income category.'
        ], 500);
    }

    public function toggleStatus(Request $request)
    {
        $income = Income::find($request->id);
        
        if (!$income) {
            return response()->json([
                'message' => 'Income category not found'
            ], 404);
        }

        $income->status = $request->status;

        if ($income->save()) {
            return response()->json([
                'message' => 'Income category status updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update income category status'
        ], 500);
    }
}