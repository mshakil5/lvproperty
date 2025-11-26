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
                'company_name',
                'email',
                'phone',
                'postcode',
                'correspondence_address',
                'proof_of_id',
                'authorisation_letter',
                'landlord_agent_agreement',
                'bank_name',
                'account_number',
                'sort_code',
                'status'
            ])->orderBy('id', 'desc');

            return DataTables::of($landlords)
                ->addIndexColumn()

                /* ---------------------------
                LANDLORD COLUMN
                ---------------------------- */
                ->addColumn('landlord', function ($row) {
                    $html = '';

                    if ($row->name) {
                        $html .= '<strong>' . e($row->name) . '</strong><br>';
                    }
                    if ($row->company_name) {
                        $html .= e($row->company_name) . '<br>';
                    }
                    if ($row->email) {
                        $html .= e($row->email) . '<br>';
                    }
                    if ($row->phone) {
                        $html .= e($row->phone);
                    }

                    return $html ?: 'N/A';
                })

                /* ---------------------------
                ADDRESS COLUMN
                ---------------------------- */
                ->addColumn('address', function ($row) {
                    $html = '';

                    if ($row->postcode) {
                        $html .= '<strong>Post:</strong> ' . e($row->postcode) . '<br>';
                    }
                    if ($row->correspondence_address) {
                        $html .= '<strong>Address:</strong> ' . e($row->correspondence_address);
                    }

                    return $html ?: 'N/A';
                })

                /* ---------------------------
                COMPLIANCE COLUMN
                ---------------------------- */
                ->addColumn('compliance', function ($row) {

                    $output = '';

                    if ($row->proof_of_id) {
                        $output .= '<a href="' . asset($row->proof_of_id) . '" target="_blank" class="d-block text-primary">
                                        Proof of ID
                                    </a>';
                    }

                    if ($row->authorisation_letter) {
                        $output .= '<a href="' . asset($row->authorisation_letter) . '" target="_blank" class="d-block text-primary">
                                        Authorization Letter
                                    </a>';
                    }

                    if ($row->landlord_agent_agreement) {
                        $output .= '<a href="' . asset($row->landlord_agent_agreement) . '" target="_blank" class="d-block text-primary">
                                        Agreement Document
                                    </a>';
                    }

                    return $output ?: 'N/A';
                })

                /* ---------------------------
                BANK DETAILS COLUMN
                ---------------------------- */
                ->addColumn('bank_details', function ($row) {

                    $html = '';

                    if ($row->bank_name) {
                        $html .= '<strong>Bank:</strong> ' . e($row->bank_name) . '<br>';
                    }
                    if ($row->account_number) {
                        $html .= '<strong>Account:</strong> ' . e($row->account_number) . '<br>';
                    }
                    if ($row->sort_code) {
                        $html .= '<strong>Sort Code:</strong> ' . e($row->sort_code);
                    }

                    return $html ?: 'N/A';
                })

                /* ---------------------------
                STATUS SWITCH
                ---------------------------- */
                ->addColumn('status', function ($row) {
                    $checked = $row->status ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                    data-id="' . $row->id . '" ' . $checked . '>
                            </div>';
                })

                /* ---------------------------
                ACTIONS
                ---------------------------- */
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

                ->rawColumns(['landlord', 'address', 'compliance', 'bank_details', 'status', 'action'])
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
            'postcode' => 'required',
            'correspondence_address' => 'required',

            // File validations (max 5MB)
            'proof_of_id' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'authorisation_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'landlord_agent_agreement' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',

            // Bank
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'sort_code' => 'nullable',
        ]);

        $data = new Landlord();
        $data->name = $request->name;
        $data->company_name = $request->company_name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->postcode = $request->postcode;
        $data->correspondence_address = $request->correspondence_address;

        // Bank
        $data->bank_name = $request->bank_name;
        $data->account_number = $request->account_number;
        $data->sort_code = $request->sort_code;

        /* ---------------------------------------------------
            FILE UPLOADS (store as: /uploads/landlords/xxxx.pdf)
        --------------------------------------------------- */
        $uploadPath = 'uploads/landlords/';

        if ($request->hasFile('proof_of_id')) {
            $file = $request->file('proof_of_id');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->proof_of_id = '/' . $uploadPath . $filename;
        }

        if ($request->hasFile('authorisation_letter')) {
            $file = $request->file('authorisation_letter');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->authorisation_letter = '/' . $uploadPath . $filename;
        }

        if ($request->hasFile('landlord_agent_agreement')) {
            $file = $request->file('landlord_agent_agreement');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->landlord_agent_agreement = '/' . $uploadPath . $filename;
        }

        // Save
        $data->save();

        return response()->json([
            'message' => 'Landlord created successfully!',
            'landlord' => $data
        ], 200);
    }

    public function edit($id)
    {
        return response()->json(Landlord::find($id));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:landlords,email,' . $request->codeid,
            'phone' => 'required',
            'postcode' => 'required',
            'correspondence_address' => 'required',
            'proof_of_id' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'authorisation_letter' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'landlord_agent_agreement' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'sort_code' => 'nullable',
        ]);

        $data = Landlord::findOrFail($request->codeid);
        $data->name = $request->name;
        $data->company_name = $request->company_name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->postcode = $request->postcode;
        $data->correspondence_address = $request->correspondence_address;

        // Bank
        $data->bank_name = $request->bank_name;
        $data->account_number = $request->account_number;
        $data->sort_code = $request->sort_code;

        $uploadPath = 'uploads/landlords/';

        // Proof of ID
        if ($request->hasFile('proof_of_id')) {
            if ($data->proof_of_id && file_exists(public_path($data->proof_of_id))) {
                unlink(public_path($data->proof_of_id));
            }
            $file = $request->file('proof_of_id');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->proof_of_id = '/' . $uploadPath . $filename;
        }

        // Authorisation Letter
        if ($request->hasFile('authorisation_letter')) {
            if ($data->authorisation_letter && file_exists(public_path($data->authorisation_letter))) {
                unlink(public_path($data->authorisation_letter));
            }
            $file = $request->file('authorisation_letter');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->authorisation_letter = '/' . $uploadPath . $filename;
        }

        // Landlord-Agent Agreement
        if ($request->hasFile('landlord_agent_agreement')) {
            if ($data->landlord_agent_agreement && file_exists(public_path($data->landlord_agent_agreement))) {
                unlink(public_path($data->landlord_agent_agreement));
            }
            $file = $request->file('landlord_agent_agreement');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->landlord_agent_agreement = '/' . $uploadPath . $filename;
        }

        $data->save();

        return response()->json([
            'message' => 'Landlord updated successfully!',
            'landlord' => $data
        ], 200);
    }

    public function delete($id)
    {
        $data = Landlord::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Landlord not found.'
            ], 404);
        }

        // Remove uploaded files if they exist
        foreach (['proof_of_id', 'authorisation_letter', 'landlord_agent_agreement'] as $fileField) {
            if ($data->$fileField && file_exists(public_path($data->$fileField))) {
                unlink(public_path($data->$fileField));
            }
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
