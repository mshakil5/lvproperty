<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Property;
use DataTables;
use App\Models\Tenancy;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $this->updateTenancyTypes();

        if ($request->ajax()) {
            $tenants = Tenant::with(['property', 'tenancies' => function($query) {
                $query->where('status', 'active')->latest();
            }])->select([
                'id',
                'property_id',
                'name',
                'email',
                'phone',
                'address_first_line',
                'city',
                'postcode',
                'emergency_contact',
                'immigration_status',
                'right_to_rent_status',
                'previous_landlord_reference',
                'personal_reference',
                'bank_name',
                'status'
            ])->orderBy('id', 'desc');

            return DataTables::of($tenants)
                ->addIndexColumn()

                /* ---------------------------
                TENANT COLUMN
                ---------------------------- */
                ->addColumn('tenant', function ($row) {
                    $html = '';

                    if ($row->name) {
                        $html .= '<strong>' . e($row->name) . '</strong><br>';
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
                PROPERTY & ADDRESS COLUMN
                ---------------------------- */
                ->addColumn('property_address', function ($row) {
                    $html = '';

                    if ($row->property) {
                        $html .= '<strong>Property:</strong> ' . e($row->property->property_reference) . '<br>';
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
                CURRENT TENANCY COLUMN
                ---------------------------- */
                ->addColumn('current_tenancy', function ($row) {
                    $currentTenancy = $row->tenancies->first();
                    if ($currentTenancy) {
                        $html = '';
                        $html .= '<strong>Start:</strong> ' . \Carbon\Carbon::parse($currentTenancy->start_date)->format('d/m/Y') . '<br>';
                        $html .= '<strong>End:</strong> ' . \Carbon\Carbon::parse($currentTenancy->end_date)->format('d/m/Y') . '<br>';
                        $html .= '<strong>Rent:</strong> £' . number_format($currentTenancy->amount, 2);
                        return $html;
                    }
                    return '<span class="text-muted">No active tenancy</span>';
                })

                /* ---------------------------
                VERIFICATION COLUMN
                ---------------------------- */
                ->addColumn('verification', function ($row) {
                    $html = '';

                    // Immigration Status
                    $immigrationBadge = $row->immigration_status == 'yes' ? 'bg-success' : 'bg-danger';
                    $html .= '<span class="badge ' . $immigrationBadge . ' me-1 mb-1">Immigration: ' . e($row->immigration_status) . '</span><br>';

                    // Right to Rent Status
                    $rightToRentBadge = $row->right_to_rent_status == 'yes' ? 'bg-success' : 'bg-danger';
                    $html .= '<span class="badge ' . $rightToRentBadge . ' me-1 mb-1">Right to Rent: ' . e($row->right_to_rent_status) . '</span><br>';

                    // Previous Landlord Reference
                    $landlordRefBadge = $row->previous_landlord_reference == 'yes' ? 'bg-success' : 'bg-danger';
                    $html .= '<span class="badge ' . $landlordRefBadge . ' me-1 mb-1">Landlord Ref: ' . e($row->previous_landlord_reference) . '</span><br>';

                    // Personal Reference
                    $personalRefBadge = $row->personal_reference == 'yes' ? 'bg-success' : 'bg-danger';
                    $html .= '<span class="badge ' . $personalRefBadge . '">Personal Ref: ' . e($row->personal_reference) . '</span>';

                    return $html ?: 'N/A';
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
                                            data-delete-url="' . route('tenant.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#tenantTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })

                ->rawColumns(['tenant', 'property_address', 'current_tenancy', 'verification', 'bank_details', 'status', 'action'])
                ->make(true);
        }

        $properties = Property::latest()->get();
        return view('admin.tenant.index', compact('properties'));
    }

    private function updateTenancyTypes()
    {
        $today = now();
        
        // Update to Rolling Contract (end date passed)
        Tenancy::where('status', 'active')
            ->whereDate('end_date', '<', $today)
            ->update(['tenancy_type' => 'Rolling Contract']);
        
        // Update to Renewal Due (within 90 days)
        Tenancy::where('status', 'active')
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $today->copy()->addDays(90))
            ->update(['tenancy_type' => 'Renewal Due']);
        
        // Update to In Contract (more than 90 days remaining)
        Tenancy::where('status', 'active')
            ->whereDate('end_date', '>', $today->copy()->addDays(90))
            ->where('tenancy_type', '!=', 'Renewed')
            ->update(['tenancy_type' => 'In Contract']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address_first_line' => 'required',
            'city' => 'required',
            'postcode' => 'required',
            'emergency_contact' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',

            // New validation rules
            'tenancy_agreement_status' => 'required|in:yes,no',
            'tenancy_agreement_date' => 'nullable|date',
            'reference_check_status' => 'required|in:yes,no',
            'reference_check_date' => 'nullable|date',
            'immigration_status' => 'required|in:yes,no',
            'immigration_status_date' => 'nullable|date',
            'right_to_rent_status' => 'required|in:yes,no',
            'right_to_rent_date' => 'nullable|date',
            'previous_landlord_reference' => 'required|in:yes,no',
            'previous_landlord_reference_date' => 'nullable|date',
            'personal_reference' => 'required|in:yes,no',
            'personal_reference_date' => 'nullable|date',

            // File validations
            'tenancy_agreement_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'reference_check_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'immigration_status_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'right_to_rent_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'previous_landlord_reference_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'personal_reference_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        // Create Tenant
        $tenant = new Tenant();
        
        // Basic Information
        $tenant->property_id = $request->property_id;
        $tenant->name = $request->name;
        $tenant->email = $request->email;
        $tenant->phone = $request->phone;
        $tenant->address_first_line = $request->address_first_line;
        $tenant->city = $request->city;
        $tenant->postcode = $request->postcode;
        $tenant->emergency_contact = $request->emergency_contact;

        // Tenancy Agreement
        $tenant->tenancy_agreement_status = $request->tenancy_agreement_status;
        $tenant->tenancy_agreement_date = $request->tenancy_agreement_date;

        // Reference Check
        $tenant->reference_check_status = $request->reference_check_status;
        $tenant->reference_check_date = $request->reference_check_date;

        // Immigration & Right to Rent
        $tenant->immigration_status = $request->immigration_status;
        $tenant->immigration_status_date = $request->immigration_status_date;
        $tenant->right_to_rent_status = $request->right_to_rent_status;
        $tenant->right_to_rent_date = $request->right_to_rent_date;

        // References
        $tenant->previous_landlord_reference = $request->previous_landlord_reference;
        $tenant->previous_landlord_reference_date = $request->previous_landlord_reference_date;
        $tenant->personal_reference = $request->personal_reference;
        $tenant->personal_reference_date = $request->personal_reference_date;

        // Bank Details
        $tenant->bank_name = $request->bank_name;
        $tenant->account_number = $request->account_number;
        $tenant->sort_code = $request->sort_code;

        // Additional Tenants (JSON)
        if ($request->has('additional_tenants') && !empty($request->additional_tenants)) {
            $additionalTenants = json_decode($request->additional_tenants, true);
            if (is_array($additionalTenants) && !empty($additionalTenants)) {
                $tenant->additional_tenants = json_encode($additionalTenants); 
            } else {
                $tenant->additional_tenants = null;
            }
        } else {
            $tenant->additional_tenants = null;
        }

        /* ---------------------------------------------------
            FILE UPLOADS - Optimized with loop
        --------------------------------------------------- */
        $uploadPath = 'uploads/tenants/';
        
        // Document types mapping
        $documentTypes = [
            'tenancy_agreement_document',
            'reference_check_document', 
            'immigration_status_document',
            'right_to_rent_document',
            'previous_landlord_reference_document',
            'personal_reference_document'
        ];

        // Process all document uploads in loop
        foreach ($documentTypes as $documentType) {
            if ($request->hasFile($documentType)) {
                $file = $request->file($documentType);
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $filename);
                $tenant->{$documentType} = '/' . $uploadPath . $filename;
            }
        }

        // Save Tenant
        $tenant->save();

        // Get landlord_id from property
        $property = Property::find($request->property_id);
        $landlord_id = $property ? $property->landlord_id : null;

        // Create Tenancy
        $tenancy = new Tenancy();
        $tenancy->property_id = $request->property_id;
        $tenancy->tenant_id = $tenant->id;
        $tenancy->landlord_id = $landlord_id;
        $tenancy->start_date = $request->start_date;
        $tenancy->end_date = $request->end_date;
        $tenancy->amount = $request->amount;
        $tenancy->note = $request->note;
        $tenancy->status = 'active';
        $tenancy->tenancy_type = 'In Contract';
        $tenancy->save();

        // Create Transaction
        $transaction = new Transaction();
        $transaction->tran_id = 'TXN' . date('Ymd') . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);
        $transaction->date = $request->start_date;
        $transaction->tenancy_id = $tenancy->id;
        $transaction->tenant_id = $tenant->id;
        $transaction->landlord_id = $landlord_id;
        $transaction->amount = $request->amount;
        $transaction->payment_type = 'cash';
        $transaction->transaction_type = 'due';
        $transaction->status = false; // 0 = not paid
        $transaction->description = $request->note ?: 'Monthly rent due';
        $transaction->save();

        return response()->json([
            'message' => 'Tenant and tenancy created successfully!',
            'tenant' => $tenant,
            'tenancy' => $tenancy
        ], 200);
    }

    public function renewTenancy(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $tenant = Tenant::findOrFail($request->tenant_id);
            
            // Get current active tenancy
            $currentTenancy = Tenancy::where('tenant_id', $request->tenant_id)
                                    ->where('status', 'active')
                                    ->first();

            if (!$currentTenancy) {
                return response()->json([
                    'message' => 'No active tenancy found to renew'
                ], 404);
            }

            // Get landlord_id from current tenancy
            $landlord_id = $currentTenancy->landlord_id;
            $property_id = $currentTenancy->property_id;

            // Mark current tenancy as completed
            $currentTenancy->status = 'completed';
            if ($currentTenancy->tenancy_type === 'Renewal Due' || $currentTenancy->tenancy_type === 'Rolling Contract') {
                $currentTenancy->tenancy_type = 'Renewed';
            }
            $currentTenancy->save();

            // Create new tenancy
            $newTenancy = new Tenancy();
            $newTenancy->property_id = $property_id;
            $newTenancy->tenant_id = $request->tenant_id;
            $newTenancy->landlord_id = $landlord_id;
            $newTenancy->parent_id = $currentTenancy->id;
            $newTenancy->start_date = $request->start_date;
            $newTenancy->end_date = $request->end_date;
            $newTenancy->amount = $request->amount;
            $newTenancy->note = $request->note;
            $newTenancy->status = 'active';
            $newTenancy->tenancy_type = 'In Contract';
            $newTenancy->save();

            // Create Due Transaction for new tenancy
            $transaction = new Transaction();
            $transaction->tran_id = 'TXN' . date('Ymd') . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);
            $transaction->date = $request->start_date;
            $transaction->tenancy_id = $newTenancy->id;
            $transaction->tenant_id = $tenant->id;
            $transaction->landlord_id = $landlord_id;
            $transaction->amount = $request->amount;
            $transaction->payment_type = 'cash';
            $transaction->transaction_type = 'due';
            $transaction->status = false; // 0 = not paid
            $transaction->description = $request->note ?: 'Monthly rent due for renewed tenancy';
            $transaction->save();

            DB::commit();

            return response()->json([
                'message' => 'Tenancy renewed successfully!',
                'new_tenancy' => $newTenancy,
                'transaction' => $transaction
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to renew tenancy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function terminateTenancy(Request $request)
    {
        $request->validate([
            'tenancy_id' => 'required|exists:tenancies,id',
            'termination_type' => 'required|in:By Landlord,By Tenant',
            'termination_date' => 'required|date',
            'termination_reason' => 'nullable|string',
            'termination_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $tenancy = Tenancy::findOrFail($request->tenancy_id);
            
            // Update tenancy status to terminated
            $tenancy->status = 'terminated';
            $tenancy->termination_type = $request->termination_type;
            $tenancy->termination_date = $request->termination_date;
            $tenancy->termination_reason = $request->termination_reason;

            // Handle termination document upload
            if ($request->hasFile('termination_document')) {
                $uploadPath = 'uploads/tenants/';
                $file = $request->file('termination_document');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $filename);
                $tenancy->termination_document = '/' . $uploadPath . $filename;
            }

            $tenancy->save();

            // Set property status to Vacant
            Property::where('id', $tenancy->property_id)->update(['status' => 'Vacant']);

            DB::commit();

            return response()->json([
                'message' => 'Tenancy terminated successfully!',
                'tenancy' => $tenancy
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to terminate tenancy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $tenant = Tenant::with(['tenancies' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        // Get current active tenancy OR the most recent tenancy (including terminated)
        $currentTenancy = $tenant->tenancies->where('status', 'active')->first();
        
        // If no active tenancy, get the most recent one (could be terminated/completed)
        if (!$currentTenancy) {
            $currentTenancy = $tenant->tenancies->first();
        }
        
        // Get previous tenancies (completed/terminated) excluding the current one
        $previousTenancies = $tenant->tenancies;
        if ($currentTenancy) {
            $previousTenancies = $previousTenancies->where('id', '!=', $currentTenancy->id)->values();
        } else {
            $previousTenancies = $previousTenancies->values();
        }
        
        $responseData = $tenant->toArray();
        
        // Add current tenancy data to response
        if ($currentTenancy) {
            $responseData['amount'] = $currentTenancy->amount;
            $responseData['start_date'] = $currentTenancy->start_date;
            $responseData['end_date'] = $currentTenancy->end_date;
            $responseData['note'] = $currentTenancy->note;
            $responseData['tenancy_type'] = $currentTenancy->tenancy_type;
            $responseData['status'] = $currentTenancy->status; // This is the key fix!
            $responseData['current_tenancy_id'] = $currentTenancy->id;
            
            // Add termination data if status is terminated
            if ($currentTenancy->status === 'terminated') {
                $responseData['termination_type'] = $currentTenancy->termination_type;
                $responseData['termination_date'] = $currentTenancy->termination_date;
                $responseData['termination_reason'] = $currentTenancy->termination_reason;
                $responseData['termination_document'] = $currentTenancy->termination_document;
            }
        } else {
            // Default values if no tenancy exists
            $responseData['amount'] = '';
            $responseData['start_date'] = '';
            $responseData['end_date'] = '';
            $responseData['note'] = '';
            $responseData['tenancy_type'] = 'In Contract';
            $responseData['status'] = 'active'; // Default status
            $responseData['current_tenancy_id'] = null;
        }

        // Add previous tenancies data
        $responseData['previous_tenancies'] = $previousTenancies->map(function($tenancy) {
            return [
                'id' => $tenancy->id,
                'start_date' => $tenancy->start_date,
                'end_date' => $tenancy->end_date,
                'amount' => $tenancy->amount,
                'note' => $tenancy->note,
                'status' => $tenancy->status,
                'tenancy_type' => $tenancy->tenancy_type,
                'created_at' => $tenancy->created_at
            ];
        })->toArray();

        return response()->json($responseData);
    }

    public function update(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'address_first_line' => 'required',
            'city' => 'required',
            'postcode' => 'required',
            'emergency_contact' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',

            // File validations
            'tenancy_agreement_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'reference_check_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'previous_landlord_reference_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'personal_reference_document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $tenant = Tenant::findOrFail($request->codeid);
            
            // Basic Information
            $tenant->property_id = $request->property_id;
            $tenant->name = $request->name;
            $tenant->email = $request->email;
            $tenant->phone = $request->phone;
            $tenant->address_first_line = $request->address_first_line;
            $tenant->city = $request->city;
            $tenant->postcode = $request->postcode;
            $tenant->emergency_contact = $request->emergency_contact;

            // Reference Check
            $tenant->reference_check_date = $request->reference_check_date;

            // Immigration & Right to Rent
            $tenant->immigration_status = $request->immigration_status;
            $tenant->immigration_status_date = $request->immigration_status_date;
            $tenant->right_to_rent_status = $request->right_to_rent_status;
            $tenant->right_to_rent_date = $request->right_to_rent_date;

            // References
            $tenant->previous_landlord_reference = $request->previous_landlord_reference;
            $tenant->personal_reference = $request->personal_reference;

            // Bank Details
            $tenant->bank_name = $request->bank_name;
            $tenant->account_number = $request->account_number;
            $tenant->sort_code = $request->sort_code;

            // Additional Tenants (JSON)
            if ($request->has('additional_tenants') && !empty($request->additional_tenants)) {
                $additionalTenants = json_decode($request->additional_tenants, true);
                if (is_array($additionalTenants) && !empty($additionalTenants)) {
                    $tenant->additional_tenants = json_encode($additionalTenants); 
                } else {
                    $tenant->additional_tenants = null;
                }
            } else {
                $tenant->additional_tenants = null;
            }

            /* ---------------------------------------------------
                FILE UPLOADS
            --------------------------------------------------- */
            $uploadPath = 'uploads/tenants/';

            // Tenancy Agreement Document
            if ($request->hasFile('tenancy_agreement_document')) {
                // Delete old file if exists
                if ($tenant->tenancy_agreement_document && file_exists(public_path($tenant->tenancy_agreement_document))) {
                    unlink(public_path($tenant->tenancy_agreement_document));
                }
                
                $file = $request->file('tenancy_agreement_document');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $filename);
                $tenant->tenancy_agreement_document = '/' . $uploadPath . $filename;
            }

            // Reference Check Document
            if ($request->hasFile('reference_check_document')) {
                // Delete old file if exists
                if ($tenant->reference_check_document && file_exists(public_path($tenant->reference_check_document))) {
                    unlink(public_path($tenant->reference_check_document));
                }
                
                $file = $request->file('reference_check_document');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $filename);
                $tenant->reference_check_document = '/' . $uploadPath . $filename;
            }

            // Previous Landlord Reference Document
            if ($request->hasFile('previous_landlord_reference_document')) {
                // Delete old file if exists
                if ($tenant->previous_landlord_reference_document && file_exists(public_path($tenant->previous_landlord_reference_document))) {
                    unlink(public_path($tenant->previous_landlord_reference_document));
                }
                
                $file = $request->file('previous_landlord_reference_document');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $filename);
                $tenant->previous_landlord_reference_document = '/' . $uploadPath . $filename;
            }

            // Personal Reference Document
            if ($request->hasFile('personal_reference_document')) {
                // Delete old file if exists
                if ($tenant->personal_reference_document && file_exists(public_path($tenant->personal_reference_document))) {
                    unlink(public_path($tenant->personal_reference_document));
                }
                
                $file = $request->file('personal_reference_document');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($uploadPath), $filename);
                $tenant->personal_reference_document = '/' . $uploadPath . $filename;
            }

            // Save Tenant
            $tenant->save();

            // Get landlord_id from property
            $property = Property::find($request->property_id);
            $landlord_id = $property ? $property->landlord_id : null;

            // Update Tenancy
            $tenancy = Tenancy::where('tenant_id', $tenant->id)
                            ->where('status', 'active')
                            ->first();

            $tenancyCreated = false;
            if ($tenancy) {
                $tenancy->property_id = $request->property_id;
                $tenancy->landlord_id = $landlord_id;
                $tenancy->start_date = $request->start_date;
                $tenancy->end_date = $request->end_date;
                $tenancy->amount = $request->amount;
                $tenancy->note = $request->note;
                $tenancy->save();
            } else {
                // Only create new tenancy if the latest one is NOT terminated
                $latestTenancy = Tenancy::where('tenant_id', $tenant->id)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                
                // Check if latest tenancy exists and is terminated
                if (!$latestTenancy || $latestTenancy->status !== 'terminated') {
                    // Create new tenancy if none exists OR latest is not terminated
                    $tenancy = new Tenancy();
                    $tenancy->property_id = $request->property_id;
                    $tenancy->tenant_id = $tenant->id;
                    $tenancy->landlord_id = $landlord_id;
                    $tenancy->start_date = $request->start_date;
                    $tenancy->end_date = $request->end_date;
                    $tenancy->amount = $request->amount;
                    $tenancy->note = $request->note;
                    $tenancy->status = 'active';
                    $tenancy->tenancy_type = 'In Contract';
                    $tenancy->save();
                    $tenancyCreated = true;
                } else {
                    // If latest tenancy is terminated, don't create new one
                    $tenancy = null;
                }
            }

            // Update or Create Due Transaction only if tenancy exists
            if ($tenancy) {
                $transaction = Transaction::where('tenancy_id', $tenancy->id)
                                        ->where('transaction_type', 'due')
                                        ->first();

                if ($transaction) {
                    // Update existing due transaction
                    $transaction->amount = $request->amount;
                    $transaction->description = $request->note ?: 'Monthly rent due';
                    $transaction->save();
                } else {
                    // Create new due transaction
                    $transaction = new Transaction();
                    $transaction->tran_id = 'TXN' . date('Ymd') . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);
                    $transaction->date = $request->start_date;
                    $transaction->tenancy_id = $tenancy->id;
                    $transaction->tenant_id = $tenant->id;
                    $transaction->landlord_id = $landlord_id;
                    $transaction->amount = $request->amount;
                    $transaction->payment_type = 'cash';
                    $transaction->transaction_type = 'due';
                    $transaction->status = false; // 0 = not paid
                    $transaction->description = $request->note ?: 'Monthly rent due';
                    $transaction->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Tenant and tenancy updated successfully!',
                'tenant' => $tenant,
                'tenancy' => $tenancy,
                'transaction' => $transaction
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update tenant and tenancy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        $tenant = Tenant::with('currentTenancy')->find($id);
        
        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found.'
            ], 404);
        }

        if ($tenant->document && file_exists(public_path('images/tenant-file/' . $tenant->document))) {
            unlink(public_path('images/tenant-file/' . $tenant->document));
        }

        // Set current property to Vacant
        if ($tenant->currentTenancy) {
            Property::where('id', $tenant->currentTenancy->property_id)->update(['status' => 'Vacant']);
        }

        Transaction::where('tenant_id', $id)->delete();

        if ($tenant->delete()) {
            return response()->json([
                'message' => 'Tenant deleted successfully.'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete tenant.'
        ], 500);
    }

    public function toggleStatus(Request $request)
    {
        $tenant = Tenant::with('currentTenancy')->find($request->tenant_id);

        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant not found'
            ], 404);
        }

        $tenant->status = $request->status;

        if ($tenant->save()) {
            // Update property status
            if ($tenant->currentTenancy) {
                $newStatus = $request->status == 0 ? 'Vacant' : 'Occupied';
                Property::where('id', $tenant->currentTenancy->property_id)->update(['status' => $newStatus]);
            }
            
            return response()->json([
                'message' => 'Tenant status updated successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update tenant status'
        ], 500);
    }
}