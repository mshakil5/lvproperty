<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Landlord;
use DataTables;

class LandlordController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $landlords = Landlord::select([
                'id',
                'name',
                'email',
                'phone',
                'service_type',
                'management_fee',
                'agreement_due_date',
                'status',
                'reference_checked',
                'right_to_rent_status'
            ])->orderBy('id', 'desc');

            return DataTables::of($landlords)
                ->addIndexColumn()
                ->addColumn('service_type', function ($row) {
                    $badge_class = [
                        'Full Management' => 'bg-success',
                        'Rent Collection' => 'bg-info',
                        'Tenant Finding' => 'bg-warning'
                    ][$row->service_type] ?? 'bg-secondary';

                    return '<span class="badge ' . $badge_class . '">' . ($row->service_type ?? 'N/A') . '</span>';
                })
                ->addColumn('management_fee', function ($row) {
                    return $row->management_fee
                        ? number_format($row->management_fee, 0) . '%'
                        : '';
                })
                ->addColumn('agreement_due_date', function ($row) {
                    return $row->agreement_due_date ? date('d M, Y', strtotime($row->agreement_due_date)) : '';
                })
                ->addColumn('reference_status', function ($row) {
                    $badge_class = [
                        'yes' => 'bg-success',
                        'no' => 'bg-danger',
                        'processing' => 'bg-warning'
                    ][$row->reference_checked] ?? 'bg-secondary';

                    return '<span class="badge ' . $badge_class . '">' . ucfirst($row->reference_checked) . '</span>';
                })
                ->addColumn('right_to_rent', function ($row) {
                    $badge_class = [
                        'verified' => 'bg-success',
                        'not_verified' => 'bg-danger',
                        'pending' => 'bg-warning'
                    ][$row->right_to_rent_status] ?? 'bg-secondary';

                    return '<span class="badge ' . $badge_class . '">' . ucfirst(str_replace('_', ' ', $row->right_to_rent_status)) . '</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                      id="customSwitchStatus' . $row->id . '" data-id="' . $row->id . '" ' . $checked . '>
                                <label class="form-check-label" for="customSwitchStatus' . $row->id . '"></label>
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
                                    <button class="dropdown-item" id="EditBtn" rid="' . $row->id . '">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                    </button>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item deleteBtn" 
                                            data-delete-url="' . route('landlord.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#landlordTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['service_type', 'status', 'reference_status', 'right_to_rent', 'action'])
                ->make(true);
        }

        return view('admin.landlord.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:landlords,email',
            'phone' => 'required',
            'address' => 'required',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'sort_code' => 'nullable',
            'service_type' => 'nullable|in:Full Management,Rent Collection,Tenant Finding',
            'management_fee' => 'nullable|numeric|min:0|max:100',
            'agreement_date' => 'nullable|date',
            'agreement_duration' => 'nullable|integer|min:1',
            // New fields validation
            'current_address' => 'nullable',
            'previous_address' => 'nullable',
            'emergency_contact_name' => 'nullable',
            'emergency_contact_phone' => 'nullable',
            'emergency_contact_relation' => 'nullable',
            'reference_checked' => 'nullable|in:yes,no,processing',
            'credit_score' => 'nullable',
            'previous_landlord_reference' => 'nullable',
            'personal_reference' => 'nullable',
            'right_to_rent_status' => 'nullable|in:verified,not_verified,pending',
            'right_to_rent_check_date' => 'nullable|date'
        ]);

        $data = new Landlord;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->bank_name = $request->bank_name;
        $data->account_number = $request->account_number;
        $data->sort_code = $request->sort_code;
        $data->service_type = $request->service_type;
        $data->management_fee = $request->management_fee;
        $data->agreement_date = $request->agreement_date;
        $data->agreement_duration = $request->agreement_duration;

        // New tenant fields
        $data->current_address = $request->current_address;
        $data->previous_address = $request->previous_address;
        $data->emergency_contact_name = $request->emergency_contact_name;
        $data->emergency_contact_phone = $request->emergency_contact_phone;
        $data->emergency_contact_relation = $request->emergency_contact_relation;
        $data->reference_checked = $request->reference_checked;
        $data->credit_score = $request->credit_score;
        $data->previous_landlord_reference = $request->previous_landlord_reference;
        $data->personal_reference = $request->personal_reference;
        $data->right_to_rent_status = $request->right_to_rent_status;
        $data->right_to_rent_check_date = $request->right_to_rent_check_date;

        if ($request->agreement_date && $request->agreement_duration) {
            $data->agreement_due_date = date(
                'Y-m-d',
                strtotime("{$request->agreement_date} +{$request->agreement_duration} months")
            );
        } else {
            $data->agreement_due_date = null;
        }

        if ($data->save()) {
            return response()->json([
                'message' => 'Landlord created successfully!',
                'landlord' => $data
            ], 200);
        }

        return response()->json([
            'message' => 'Server error while creating landlord.'
        ], 500);
    }

    public function edit($id)
    {
        $where = [
            'id' => $id
        ];
        $info = Landlord::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:landlords,email,' . $request->codeid,
            'phone' => 'required',
            'address' => 'required',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'sort_code' => 'nullable',
            'service_type' => 'nullable|in:Full Management,Rent Collection,Tenant Finding',
            'management_fee' => 'nullable|numeric|min:0|max:100',
            'agreement_date' => 'nullable|date',
            'agreement_duration' => 'nullable|integer|min:1',
            // New fields validation
            'current_address' => 'nullable',
            'previous_address' => 'nullable',
            'emergency_contact_name' => 'nullable',
            'emergency_contact_phone' => 'nullable',
            'emergency_contact_relation' => 'nullable',
            'reference_checked' => 'nullable|in:yes,no,processing',
            'credit_score' => 'nullable',
            'previous_landlord_reference' => 'nullable',
            'personal_reference' => 'nullable',
            'right_to_rent_status' => 'nullable|in:verified,not_verified,pending',
            'right_to_rent_check_date' => 'nullable|date'
        ]);

        $data = Landlord::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->bank_name = $request->bank_name;
        $data->account_number = $request->account_number;
        $data->sort_code = $request->sort_code;
        $data->service_type = $request->service_type;
        $data->management_fee = $request->management_fee;
        $data->agreement_date = $request->agreement_date;
        $data->agreement_duration = $request->agreement_duration;

        // New tenant fields
        $data->current_address = $request->current_address;
        $data->previous_address = $request->previous_address;
        $data->emergency_contact_name = $request->emergency_contact_name;
        $data->emergency_contact_phone = $request->emergency_contact_phone;
        $data->emergency_contact_relation = $request->emergency_contact_relation;
        $data->reference_checked = $request->reference_checked;
        $data->credit_score = $request->credit_score;
        $data->previous_landlord_reference = $request->previous_landlord_reference;
        $data->personal_reference = $request->personal_reference;
        $data->right_to_rent_status = $request->right_to_rent_status;
        $data->right_to_rent_check_date = $request->right_to_rent_check_date;

        if ($request->agreement_date && $request->agreement_duration) {
            $data->agreement_due_date = date(
                'Y-m-d',
                strtotime("{$request->agreement_date} +{$request->agreement_duration} months")
            );
        } else {
            $data->agreement_due_date = null;
        }

        if ($data->save()) {
            return response()->json([
                'message' => 'Landlord updated successfully!'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update landlord. Please try again.'
        ], 500);
    }

    public function delete($id)
    {
        $data = Landlord::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Landlord not found.'
            ], 404);
        }

        if ($data->delete()) {
            return response()->json([
                'message' => 'Landlord deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete landlord.'
        ], 500);
    }

    public function toggleStatus(Request $request)
    {
        $landlord = Landlord::find($request->landlord_id);

        if (!$landlord) {
            return response()->json([
                'message' => 'Landlord not found'
            ], 404);
        }

        $landlord->status = $request->status;

        if ($landlord->save()) {
            return response()->json([
                'message' => 'Landlord status updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update landlord status'
        ], 500);
    }
}
