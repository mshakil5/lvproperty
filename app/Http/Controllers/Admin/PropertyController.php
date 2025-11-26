<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Landlord;
use DataTables;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $properties = Property::with('landlord')->select([
                'id',
                'landlord_id', 
                'property_reference',
                'address_first_line',
                'city',
                'postcode',
                'property_type', 
                'status',
                'status_until_date',
                'representative_name',
                'representative_contact',
                'representative_authorisation',
                'technicians',
                'service_type',
                'management_fee',
                'emergency_contact'
            ])->orderBy('id', 'desc');

            return DataTables::of($properties)
                ->addIndexColumn()

                /* ---------------------------
                PROPERTY COLUMN
                ---------------------------- */
                ->addColumn('property', function ($row) {
                    $html = '';

                    if ($row->property_reference) {
                        $html .= '<strong>' . e($row->property_reference) . '</strong><br>';
                    }
                    if ($row->address_first_line) {
                        $html .= e($row->address_first_line) . '<br>';
                    }
                    if ($row->city) {
                        $html .= e($row->city) . ' - ' . e($row->postcode);
                    }

                    return $html ?: 'N/A';
                })

                /* ---------------------------
                LANDLORD COLUMN
                ---------------------------- */
                ->addColumn('landlord', function ($row) {
                    return $row->landlord ? e($row->landlord->name) : 'N/A';
                })

                /* ---------------------------
                TYPE & SERVICE COLUMN
                ---------------------------- */
                ->addColumn('type_service', function ($row) {
                    $html = '';

                    if ($row->property_type) {
                        $html .= '<strong>Type:</strong> ' . e($row->property_type) . '<br>';
                    }
                    if ($row->service_type) {
                        $html .= '<strong>Service:</strong> ' . e($row->service_type);
                    }
                    if ($row->management_fee) {
                        $html .= '<br><strong>Fee:</strong> ' . e($row->management_fee) . '%';
                    }

                    return $html ?: 'N/A';
                })

                /* ---------------------------
                REPRESENTATIVE COLUMN
                ---------------------------- */
                ->addColumn('representative', function ($row) {
                    $html = '';

                    if ($row->representative_name) {
                        $html .= '<strong>' . e($row->representative_name) . '</strong><br>';
                    }
                    if ($row->representative_contact) {
                        $html .= e($row->representative_contact) . '<br>';
                    }
                    if ($row->representative_authorisation) {
                        $authBadge = $row->representative_authorisation == 'Yes' ? 'bg-success' : 'bg-secondary';
                        $html .= '<span class="badge ' . $authBadge . '">' . e($row->representative_authorisation) . '</span>';
                    }

                    return $html ?: 'N/A';
                })

                /* ---------------------------
                TECHNICIANS COLUMN
                ---------------------------- */
                ->addColumn('technicians', function ($row) {
                    if ($row->technicians) {
                        $technicians = json_decode($row->technicians, true);
                        if (is_array($technicians) && !empty($technicians)) {
                            $techNames = [];
                            foreach ($technicians as $tech) {
                                if (isset($tech['technician_name']) && !empty($tech['technician_name'])) {
                                    $techNames[] = e($tech['technician_name']);
                                }
                            }
                            if (!empty($techNames)) {
                                return implode(', ', array_slice($techNames, 0, 2)) . 
                                    (count($techNames) > 2 ? '...' : '');
                            }
                        }
                    }
                    return 'N/A';
                })

                /* ---------------------------
                STATUS COLUMN
                ---------------------------- */
                ->addColumn('status', function ($row) {
                    $badge_class = [
                        'Vacant' => 'bg-danger',
                        'Occupied' => 'bg-success', 
                        'Maintenance' => 'bg-warning'
                    ][$row->status] ?? 'bg-secondary';
                    
                    $statusText = e($row->status);
                    if ($row->status_until_date && in_array($row->status, ['Occupied', 'Maintenance'])) {
                        $statusText .= '<br><small>Until: ' . \Carbon\Carbon::parse($row->status_until_date)->format('d/m/Y') . '</small>';
                    }
                    
                    return '<span class="badge ' . $badge_class . '">' . $statusText . '</span>';
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
                                            data-delete-url="' . route('property.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#propertyTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })

                ->rawColumns(['property', 'landlord', 'type_service', 'representative', 'technicians', 'status', 'action'])
                ->make(true);
        }

        $landlords = Landlord::where('status', 1)->get();
        return view('admin.property.index', compact('landlords'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'landlord_id' => 'required|exists:landlords,id',
            'address_first_line' => 'required',
            'city' => 'required',
            'postcode' => 'required',
            'property_type' => 'required|in:House,Flat,Apartment,Commercial',
            'status' => 'required|in:Vacant,Occupied,Maintenance',
            'emergency_contact' => 'required',

            // Representative Details
            'representative_name' => 'nullable',
            'representative_contact' => 'nullable', // Fixed: changed from representative_emergency_contact
            'representative_authorisation' => 'nullable|in:Yes,No,NA',

            // Service Agreement
            'service_type' => 'nullable|in:Full Management,Rent Collection,Tenant Finding',
            'management_fee' => 'nullable|numeric|min:0|max:100',
            'agreement_date' => 'nullable|date',
            'agreement_duration' => 'nullable|integer|min:0',

            // File validation
            'representative_authorisation_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',

            // Status until date validation
            'status_until_date' => 'nullable|date',
        ]);

        // Add conditional validation for status_until_date
        if (in_array($request->status, ['Occupied', 'Maintenance']) && !$request->status_until_date) {
            return response()->json([
                'message' => 'The status until date field is required when status is Occupied or Maintenance.'
            ], 422);
        }

        // Add conditional validation for representative_authorisation_file
        if ($request->representative_authorisation === 'Yes' && !$request->hasFile('representative_authorisation_file')) {
            return response()->json([
                'message' => 'The authorisation letter file is required when representative authorisation is Yes.'
            ], 422);
        }

        $data = new Property();
        
        // Basic Information
        $data->landlord_id = $request->landlord_id;
        $data->property_type = $request->property_type;
        $data->status = $request->status;
        
        $data->status_until_date = $request->status_until_date;
        
        $data->address_first_line = $request->address_first_line;
        $data->city = $request->city;
        $data->postcode = $request->postcode;
        $data->emergency_contact = $request->emergency_contact;

        // Representative Details
        $data->representative_name = $request->representative_name;
        $data->representative_contact = $request->representative_contact; // Fixed
        $data->representative_authorisation = $request->representative_authorisation;

        // Service Agreement
        $data->service_type = $request->service_type;
        $data->management_fee = $request->management_fee;
        $data->agreement_date = $request->agreement_date;
        $data->agreement_duration = $request->agreement_duration;

        // Generate Property Reference
        $propertyReference = $this->generatePropertyReference(
            $request->address_first_line,
            $request->postcode
        );
        $data->property_reference = $propertyReference;

        // Service Technician Details (JSON)
        if ($request->has('technicians') && !empty($request->technicians)) {
            $technicians = json_decode($request->technicians, true);
            if (is_array($technicians) && !empty($technicians)) {
                $data->technicians = json_encode($technicians); 
            } else {
                $data->technicians = null;
            }
        } else {
            $data->technicians = null;
        }

        /* ---------------------------------------------------
            FILE UPLOADS (store as: /uploads/properties/xxxx.pdf)
        --------------------------------------------------- */
        $uploadPath = 'uploads/properties/';

        // Process representative authorisation file
        if ($request->representative_authorisation === 'Yes' && $request->hasFile('representative_authorisation_file')) {
            $file = $request->file('representative_authorisation_file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->representative_authorisation_file = '/' . $uploadPath . $filename;
        } else {
            // Clear the file if authorisation is not 'Yes' or no file uploaded
            $data->representative_authorisation_file = null;
        }

        // Save
        $data->save();

        return response()->json([
            'message' => 'Property created successfully!',
            'property' => $data
        ], 200);
    }

    private function generatePropertyReference($addressFirstLine, $postcode)
    {
        // Clean the address - remove special chars and extra spaces
        $cleanAddress = trim(preg_replace('/[^\w\s]/', '', $addressFirstLine));
        
        // Extract house number (first number sequence)
        preg_match('/\d+/', $cleanAddress, $match);
        $houseNo = $match[0] ?? '';
        
        // Remove house number and get the road part
        $roadPart = trim(preg_replace('/^\d+\s*/', '', $cleanAddress));
        
        // Split into words and filter out empty ones
        $words = array_filter(preg_split('/\s+/', $roadPart));
        
        // Get first letters of first two meaningful words
        $initials = '';
        $wordCount = 0;
        
        foreach ($words as $word) {
            if ($wordCount >= 2) break; // We only need first two words
            
            // Skip very short words (like 'a', 'the', etc.) if you want
            if (strlen($word) > 1) {
                $initials .= strtoupper(substr($word, 0, 1));
                $wordCount++;
            }
        }
        
        // If we got less than 2 initials, pad with first letter only
        if (strlen($initials) == 1 && isset($words[1])) {
            $initials .= strtoupper(substr($words[1], 0, 1));
        }
        
        // Clean postcode
        $cleanPostcode = strtoupper(str_replace(' ', '', $postcode));
        
        return $houseNo . $initials . $cleanPostcode;
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = Property::where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $request->validate([
            'landlord_id' => 'required|exists:landlords,id',
            'address_first_line' => 'required',
            'city' => 'required',
            'postcode' => 'required',
            'property_type' => 'required|in:House,Flat,Apartment,Commercial',
            'status' => 'required|in:Vacant,Occupied,Maintenance',
            'emergency_contact' => 'required',

            // Representative Details
            'representative_name' => 'nullable',
            'representative_contact' => 'nullable',
            'representative_authorisation' => 'nullable|in:Yes,No,NA',

            // Service Agreement
            'service_type' => 'nullable|in:Full Management,Rent Collection,Tenant Finding',
            'management_fee' => 'nullable|numeric|min:0|max:100',
            'agreement_date' => 'nullable|date',
            'agreement_duration' => 'nullable|integer|min:0',

            // File validation
            'representative_authorisation_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',

            // Status until date validation
            'status_until_date' => 'nullable|date',
        ]);

        // Add conditional validation for status_until_date
        if (in_array($request->status, ['Occupied', 'Maintenance']) && !$request->status_until_date) {
            return response()->json([
                'message' => 'The status until date field is required when status is Occupied or Maintenance.'
            ], 422);
        }

        // Add conditional validation for representative_authorisation_file
        if ($request->representative_authorisation === 'Yes' && !$request->hasFile('representative_authorisation_file')) {
            // Only validate if no file exists already in database
            $existingProperty = Property::find($request->codeid);
            if (!$existingProperty->representative_authorisation_file) {
                return response()->json([
                    'message' => 'The authorisation letter file is required when representative authorisation is Yes.'
                ], 422);
            }
        }

        $data = Property::findOrFail($request->codeid);
        
        // Basic Information
        $data->landlord_id = $request->landlord_id;
        $data->property_type = $request->property_type;
        $data->status = $request->status;
        
        $data->status_until_date = $request->status_until_date;
        
        $data->address_first_line = $request->address_first_line;
        $data->city = $request->city;
        $data->postcode = $request->postcode;
        $data->emergency_contact = $request->emergency_contact;

        // Representative Details
        $data->representative_name = $request->representative_name;
        $data->representative_contact = $request->representative_contact;
        $data->representative_authorisation = $request->representative_authorisation;

        // Service Agreement
        $data->service_type = $request->service_type;
        $data->management_fee = $request->management_fee;
        $data->agreement_date = $request->agreement_date;
        $data->agreement_duration = $request->agreement_duration;

        // Service Technician Details (JSON)
        if ($request->has('technicians') && !empty($request->technicians)) {
            $technicians = json_decode($request->technicians, true);
            if (is_array($technicians) && !empty($technicians)) {
                $data->technicians = json_encode($technicians); 
            } else {
                $data->technicians = null;
            }
        } else {
            $data->technicians = null;
        }

        /* ---------------------------------------------------
            FILE UPLOADS (store as: /uploads/properties/xxxx.pdf)
        --------------------------------------------------- */
        $uploadPath = 'uploads/properties/';

        // Process representative authorisation file
        if ($request->representative_authorisation === 'Yes' && $request->hasFile('representative_authorisation_file')) {
            // Delete old file if exists
            if ($data->representative_authorisation_file && file_exists(public_path($data->representative_authorisation_file))) {
                unlink(public_path($data->representative_authorisation_file));
            }
            
            $file = $request->file('representative_authorisation_file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($uploadPath), $filename);
            $data->representative_authorisation_file = '/' . $uploadPath . $filename;
        } elseif ($request->representative_authorisation !== 'Yes') {
            // Clear the file if authorisation is not 'Yes' - delete existing file
            if ($data->representative_authorisation_file && file_exists(public_path($data->representative_authorisation_file))) {
                unlink(public_path($data->representative_authorisation_file));
            }
            $data->representative_authorisation_file = null;
        }
        // If authorisation is 'Yes' but no new file uploaded, keep the existing file

        // Save
        if ($data->save()) {
            return response()->json([
                'message' => 'Property updated successfully!',
                'property' => $data
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update property. Please try again.'
        ], 500);
    }

    public function delete($id)
    {
        $data = Property::find($id);
        
        if (!$data) {
            return response()->json([
                'message' => 'Property not found.'
            ], 404);
        }

        // Optional: Delete associated files
        if ($data->representative_authorisation_file && file_exists(public_path($data->representative_authorisation_file))) {
            unlink(public_path($data->representative_authorisation_file));
        }

        if ($data->delete()) {
            return response()->json([
                'message' => 'Property deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete property.'
        ], 500);
    }
}