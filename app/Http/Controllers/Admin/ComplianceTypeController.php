<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplianceType;
use DataTables;

class ComplianceTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $complianceTypes = ComplianceType::select([
                'id',
                'name',
                'description',
                'status'
            ])->orderBy('id', 'desc');

            return DataTables::of($complianceTypes)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                      id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
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
                                            data-delete-url="' . route('compliance-type.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#complianceTypeTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['is_mandatory', 'status', 'action'])
                ->make(true);
        }

        return view('admin.compliance-type.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:compliance_types,name',
            'description' => 'nullable',
        ]);

        $data = new ComplianceType;
        $data->name = $request->name;
        $data->description = $request->description;
        
        if ($data->save()) {
            return response()->json([
                'message' => 'Compliance Type created successfully!',
                'complianceType' => $data 
            ], 200);
        }

        return response()->json([
            'message' => 'Server error while creating compliance type.'
        ], 500);
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = ComplianceType::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:compliance_types,name,' . $request->codeid,
            'description' => 'nullable',
        ]);

        $data = ComplianceType::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->description = $request->description;

        if ($data->save()) {
            return response()->json([
                'message' => 'Compliance Type updated successfully!'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update compliance type. Please try again.'
        ], 500);
    }

    public function delete($id)
    {
        $data = ComplianceType::find($id);
        
        if (!$data) {
            return response()->json([
                'message' => 'Compliance Type not found.'
            ], 404);
        }

        if ($data->delete()) {
            return response()->json([
                'message' => 'Compliance Type deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete compliance type.'
        ], 500);
    }

    public function toggleStatus(Request $request)
    {
        $complianceType = ComplianceType::find($request->compliance_type_id);

        if (!$complianceType) {
            return response()->json([
                'message' => 'Compliance Type not found'
            ], 404);
        }

        $complianceType->status = $request->status;

        if ($complianceType->save()) {
            return response()->json([
                'message' => 'Compliance Type status updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update compliance type status'
        ], 500);
    }
}