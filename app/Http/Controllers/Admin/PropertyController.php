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
                'property_name', 
                'address', 
                'property_type', 
                'rent_amount', 
                'status',
                'representative_name',
                'technician_name'
            ])->orderBy('id', 'desc');
            
            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('landlord_name', function ($row) {
                    return $row->landlord ? $row->landlord->name : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('property_type', function ($row) {
                    $badge_class = [
                        'House' => 'bg-primary',
                        'Flat' => 'bg-info', 
                        'Apartment' => 'bg-success',
                        'Commercial' => 'bg-warning'
                    ][$row->property_type] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badge_class.'">'.$row->property_type.'</span>';
                })
                ->addColumn('rent_amount', function ($row) {
                    return $row->rent_amount ? '£' . number_format($row->rent_amount, 2) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('representative', function ($row) {
                    return $row->representative_name ?: '<span class="text-muted">N/A</span>';
                })
                ->addColumn('technician', function ($row) {
                    return $row->technician_name ?: '<span class="text-muted">N/A</span>';
                })
                ->addColumn('status', function ($row) {
                    $badge_class = [
                        'Vacant' => 'bg-danger',
                        'Occupied' => 'bg-success', 
                        'Maintenance' => 'bg-warning'
                    ][$row->status] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badge_class.'">'.$row->status.'</span>';
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
                ->rawColumns(['landlord_name', 'property_type', 'rent_amount', 'status', 'representative', 'technician', 'action'])
                ->make(true);
        }

        $landlords = Landlord::where('status', 1)->get();
        return view('admin.property.index', compact('landlords'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'landlord_id' => 'required|exists:landlords,id',
            'property_name' => 'required',
            'address' => 'required',
            'city' => 'nullable',
            'postcode' => 'nullable',
            'property_type' => 'required|in:House,Flat,Apartment,Commercial',
            'rent_amount' => 'nullable|numeric|min:0',
            'representative_name' => 'nullable',
            'representative_authorisation' => 'nullable',
            'representative_emergency_contact' => 'nullable',
            'technician_name' => 'nullable',
            'technician_phone' => 'nullable',
            'technician_email' => 'nullable',
            'status' => 'required|in:Vacant,Occupied,Maintenance'
        ]);

        $data = new Property;
        $data->landlord_id = $request->landlord_id;
        $data->property_name = $request->property_name;
        $data->address = $request->address;
        $data->city = $request->city;
        $data->postcode = $request->postcode;
        $data->property_type = $request->property_type;
        $data->rent_amount = $request->rent_amount;
        $data->representative_name = $request->representative_name;
        $data->representative_authorisation = $request->representative_authorisation;
        $data->representative_emergency_contact = $request->representative_emergency_contact;
        $data->technician_name = $request->technician_name;
        $data->technician_phone = $request->technician_phone;
        $data->technician_email = $request->technician_email;
        $data->status = $request->status;
        
        if ($data->save()) {
            return response()->json([
                'message' => 'Property created successfully!',
                'property' => $data 
            ], 200);
        }

        return response()->json([
            'message' => 'Server error while creating property.'
        ], 500);
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
            'property_name' => 'required',
            'address' => 'required',
            'city' => 'nullable',
            'postcode' => 'nullable',
            'property_type' => 'required|in:House,Flat,Apartment,Commercial',
            'rent_amount' => 'nullable|numeric|min:0',
            'representative_name' => 'nullable',
            'representative_authorisation' => 'nullable',
            'representative_emergency_contact' => 'nullable',
            'technician_name' => 'nullable',
            'technician_phone' => 'nullable',
            'technician_email' => 'nullable',
            'status' => 'required|in:Vacant,Occupied,Maintenance'
        ]);

        $data = Property::findOrFail($request->codeid);
        $data->landlord_id = $request->landlord_id;
        $data->property_name = $request->property_name;
        $data->address = $request->address;
        $data->city = $request->city;
        $data->postcode = $request->postcode;
        $data->property_type = $request->property_type;
        $data->rent_amount = $request->rent_amount;
        $data->representative_name = $request->representative_name;
        $data->representative_authorisation = $request->representative_authorisation;
        $data->representative_emergency_contact = $request->representative_emergency_contact;
        $data->technician_name = $request->technician_name;
        $data->technician_phone = $request->technician_phone;
        $data->technician_email = $request->technician_email;
        $data->status = $request->status;

        if ($data->save()) {
            return response()->json([
                'message' => 'Property updated successfully!'
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