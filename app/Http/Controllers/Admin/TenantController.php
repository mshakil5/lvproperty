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
        $this->generateAllPendingTransactions();

        if ($request->ajax()) {
            $tenants = Tenant::with(['currentProperty', 'transactions' => function($query) {
                $query->where('transaction_type', 'due')->where('status', true);
            }])->select([
                'id', 
                'name', 
                'email', 
                'phone', 
                'reference_checked', 
                'immigration_status',
                'right_to_rent_status',
                'status'
            ])->orderBy('id', 'desc');
            
            return DataTables::of($tenants)
                ->addIndexColumn()
                ->addColumn('property_name', function ($row) {
                    return $row->currentProperty 
                        ? $row->currentProperty->property_name 
                        : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('due_amount', function ($row) {
                    $totalDue = $row->transactions->sum('amount');
                    return '£' . number_format($totalDue, 2);
                })
                ->addColumn('reference_checked', function ($row) {
                    $badge_class = [
                        'Yes' => 'bg-success',
                        'No' => 'bg-danger', 
                        'Processing' => 'bg-warning'
                    ][$row->reference_checked] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badge_class.'">'.$row->reference_checked.'</span>';
                })
                ->addColumn('immigration_status', function ($row) {
                    $badge_class = [
                        'Checked' => 'bg-success',
                        'Pending' => 'bg-warning', 
                        'Not Checked' => 'bg-danger'
                    ][$row->immigration_status] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badge_class.'">'.$row->immigration_status.'</span>';
                })
                ->addColumn('right_to_rent', function ($row) {
                    $badge_class = [
                        'Verified' => 'bg-success',
                        'Pending' => 'bg-warning', 
                        'Not Verified' => 'bg-danger'
                    ][$row->right_to_rent_status] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badge_class.'">'.$row->right_to_rent_status.'</span>';
                })
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
                                <li>
                                    <button class="dropdown-item view-tenancies-btn" data-tenant-id="'.$row->id.'">
                                        <i class="ri-bill-fill align-bottom me-2 text-muted"></i> Tenancies
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
                ->rawColumns(['property_name', 'due_amount', 'reference_checked', 'immigration_status', 'right_to_rent', 'status', 'action'])
                ->make(true);
        }

        $properties = Property::where('status', 'Vacant')->orWhere('status', 'Occupied')->get();
        return view('admin.tenant.index', compact('properties'));
    }

    private function generateAllPendingTransactions()
    {
        $activeTenancies = Tenancy::where('status', true)
            ->where('start_date', '<=', now())
            ->get();

        foreach ($activeTenancies as $tenancy) {
            $this->generateMissingMonthlyTransactions($tenancy);
        }
    }

    private function generateMissingMonthlyTransactions($originalTenancy)
    {
        $startDate = new \DateTime($originalTenancy->start_date);
        $today = new \DateTime();
        $generatedCount = 0;
        
        // If start date is in future, no need to generate transactions
        if ($startDate > $today) {
            return 0;
        }
        
        $month = $startDate;
        
        while ($month <= $today) {
            $monthFormatted = $month->format('Y-m-01'); // First day of the month
            $monthName = $month->format('F Y');
            $monthEnd = (clone $month)->modify('+1 month -1 day')->format('Y-m-d'); // Last day of the month
            
            // Check if transaction already exists for this specific month
            $existingTransaction = Transaction::where('tenant_id', $originalTenancy->tenant_id)
                ->where('transaction_type', 'due')
                ->whereYear('date', $month->format('Y'))
                ->whereMonth('date', $month->format('m'))
                ->first();
                
            if (!$existingTransaction) {
                // Check if tenancy exists for this month, if not create one
                $tenancy = Tenancy::where('tenant_id', $originalTenancy->tenant_id)
                    ->whereYear('start_date', $month->format('Y'))
                    ->whereMonth('start_date', $month->format('m'))
                    ->first();
                    
                if (!$tenancy) {
                    // Create new tenancy for this month
                    $tenancy = new Tenancy();
                    $tenancy->property_id = $originalTenancy->property_id;
                    $tenancy->tenant_id = $originalTenancy->tenant_id;
                    $tenancy->landlord_id = $originalTenancy->landlord_id;
                    $tenancy->amount = $originalTenancy->amount;
                    $tenancy->due_date = $originalTenancy->due_date;
                    $tenancy->start_date = $monthFormatted;
                    $tenancy->end_date = $monthEnd;
                    $tenancy->auto_renew = $originalTenancy->auto_renew;
                    $tenancy->note = 'Monthly tenancy for ' . $monthName;
                    $tenancy->status = true;
                    $tenancy->save();
                }
                
                // Create transaction for this specific month
                $transaction = new Transaction();
                $transaction->tran_id = 'TXN' . $month->format('Ymd') . str_pad($tenancy->id, 4, '0', STR_PAD_LEFT);
                $transaction->date = $monthFormatted;
                $transaction->tenancy_id = $tenancy->id;
                $transaction->tenant_id = $originalTenancy->tenant_id;
                $transaction->landlord_id = $originalTenancy->landlord_id;
                $transaction->amount = $originalTenancy->amount;
                $transaction->payment_type = 'cash';
                $transaction->transaction_type = 'due';
                $transaction->status = true;
                $transaction->description = 'Monthly rent due for ' . $monthName;
                $transaction->save();
                
                $generatedCount++;
            }
            
            // Move to next month
            $month->modify('+1 month');
        }
        
        return $generatedCount;
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'required',
            'address' => 'nullable',
            'current_address' => 'required',
            'previous_address' => 'nullable',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'sort_code' => 'nullable',
            'emergency_contact_name' => 'nullable',
            'emergency_contact_phone' => 'nullable',
            'emergency_contact_relation' => 'nullable',
            'reference_checked' => 'required|in:Yes,No,Processing',
            'previous_landlord_reference' => 'nullable',
            'personal_reference' => 'nullable',
            'credit_score' => 'nullable',
            'immigration_status' => 'required|in:Checked,Pending,Not Checked',
            'right_to_rent_status' => 'required|in:Verified,Not Verified,Pending',
            'right_to_rent_check_date' => 'nullable|date',
            
            // Tenancy validation
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'auto_renew' => 'required|boolean',
            'note' => 'nullable|string'
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // 1. Create Tenant
            $tenant = new Tenant;
            $tenant->name = $request->name;
            $tenant->email = $request->email;
            $tenant->phone = $request->phone;
            $tenant->address = $request->address;
            $tenant->current_address = $request->current_address;
            $tenant->previous_address = $request->previous_address;
            $tenant->bank_name = $request->bank_name;
            $tenant->account_number = $request->account_number;
            $tenant->sort_code = $request->sort_code;
            $tenant->emergency_contact_name = $request->emergency_contact_name;
            $tenant->emergency_contact_phone = $request->emergency_contact_phone;
            $tenant->emergency_contact_relation = $request->emergency_contact_relation;
            $tenant->reference_checked = $request->reference_checked;
            $tenant->previous_landlord_reference = $request->previous_landlord_reference;
            $tenant->personal_reference = $request->personal_reference;
            $tenant->credit_score = $request->credit_score;
            $tenant->immigration_status = $request->immigration_status;
            $tenant->right_to_rent_status = $request->right_to_rent_status;
            $tenant->right_to_rent_check_date = $request->right_to_rent_check_date;

            if ($request->hasFile('document')) {
                $document = $request->file('document');
                $documentName = time() . '_' . uniqid() . '.' . $document->getClientOriginalExtension();
                $document->move(public_path('images/tenant-file'), $documentName);
                $tenant->document = $documentName;
            }

            $tenant->save();

            // 2. Get Property and Landlord info
            $property = Property::find($request->property_id);
            $landlord_id = $property->landlord_id;

            // 3. Create Tenancy
            $tenancy = new Tenancy;
            $tenancy->property_id = $request->property_id;
            $tenancy->tenant_id = $tenant->id;
            $tenancy->landlord_id = $landlord_id;
            $tenancy->amount = $request->amount;
            $tenancy->due_date = 20; // Default due date
            $tenancy->start_date = $request->start_date;
            $tenancy->end_date = $request->end_date;
            $tenancy->auto_renew = $request->auto_renew;
            $tenancy->note = $request->note;
            $tenancy->status = true;
            $tenancy->save();

            $transaction = new Transaction();
            $transaction->tran_id = 'TXN' . date('Ymd') . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);
            $transaction->date = $request->start_date;
            $transaction->tenancy_id = $tenancy->id;
            $transaction->tenant_id = $tenant->id;
            $transaction->landlord_id = $landlord_id;
            $transaction->amount = $request->amount;
            $transaction->payment_type = 'cash';
            $transaction->transaction_type = 'due';
            $transaction->status = true;
            $transaction->description = 'Monthly rent due for ' . date('F Y', strtotime($request->start_date));
            $transaction->save();

            Property::where('id', $request->property_id)->update(['status' => 'Occupied']);

            DB::commit();

            return response()->json([
                'message' => 'Tenant and Tenancy created successfully!',
                'tenant' => $tenant,
                'tenancy' => $tenancy
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Server error while creating tenant and tenancy: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $tenant = Tenant::with(['tenancies' => function($query) {
            $query->where('status', true)->latest()->first();
        }])->find($id);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        // Get current active tenancy or create empty object
        $currentTenancy = $tenant->tenancies->first();
        
        $responseData = $tenant->toArray();
        
        // Add tenancy data to response
        if ($currentTenancy) {
            $responseData['amount'] = $currentTenancy->amount;
            $responseData['start_date'] = $currentTenancy->start_date;
            $responseData['end_date'] = $currentTenancy->end_date;
            $responseData['auto_renew'] = $currentTenancy->auto_renew;
            $responseData['note'] = $currentTenancy->note;
        } else {
            // Default values if no tenancy exists
            $responseData['amount'] = '';
            $responseData['start_date'] = '';
            $responseData['end_date'] = '';
            $responseData['auto_renew'] = 1;
            $responseData['note'] = '';
        }

        return response()->json($responseData);
    }

    public function update(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'name' => 'required',
            'email' => 'required|email|unique:tenants,email,' . $request->codeid,
            'phone' => 'required',
            'address' => 'nullable',
            'current_address' => 'required',
            'previous_address' => 'nullable',
            'bank_name' => 'nullable',
            'account_number' => 'nullable',
            'sort_code' => 'nullable',
            'emergency_contact_name' => 'nullable',
            'emergency_contact_phone' => 'nullable',
            'emergency_contact_relation' => 'nullable',
            'reference_checked' => 'required|in:Yes,No,Processing',
            'previous_landlord_reference' => 'nullable',
            'personal_reference' => 'nullable',
            'credit_score' => 'nullable',
            'immigration_status' => 'required|in:Checked,Pending,Not Checked',
            'right_to_rent_status' => 'required|in:Verified,Not Verified,Pending',
            'right_to_rent_check_date' => 'nullable|date',
            
            // Tenancy validation
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'auto_renew' => 'required|boolean',
            'note' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $tenant = Tenant::findOrFail($request->codeid);
            
            // Handle property status when changing properties
            $oldPropertyId = $tenant->property_id;
            $newPropertyId = $request->property_id;
            
            if ($oldPropertyId != $newPropertyId) {
                Property::where('id', $oldPropertyId)->update(['status' => 'Vacant']);
                Property::where('id', $newPropertyId)->update(['status' => 'Occupied']);
            }
            
            // Update Tenant
            $tenant->property_id = $request->property_id;
            $tenant->name = $request->name;
            $tenant->email = $request->email;
            $tenant->phone = $request->phone;
            $tenant->address = $request->address;
            $tenant->current_address = $request->current_address;
            $tenant->previous_address = $request->previous_address;
            $tenant->bank_name = $request->bank_name;
            $tenant->account_number = $request->account_number;
            $tenant->sort_code = $request->sort_code;
            $tenant->emergency_contact_name = $request->emergency_contact_name;
            $tenant->emergency_contact_phone = $request->emergency_contact_phone;
            $tenant->emergency_contact_relation = $request->emergency_contact_relation;
            $tenant->reference_checked = $request->reference_checked;
            $tenant->previous_landlord_reference = $request->previous_landlord_reference;
            $tenant->personal_reference = $request->personal_reference;
            $tenant->credit_score = $request->credit_score;
            $tenant->immigration_status = $request->immigration_status;
            $tenant->right_to_rent_status = $request->right_to_rent_status;
            $tenant->right_to_rent_check_date = $request->right_to_rent_check_date;

            if ($request->hasFile('document')) {
                // Delete old document if exists
                if ($tenant->document && file_exists(public_path('images/tenant-file/' . $tenant->document))) {
                    unlink(public_path('images/tenant-file/' . $tenant->document));
                }
                
                $document = $request->file('document');
                $documentName = time() . '_' . uniqid() . '.' . $document->getClientOriginalExtension();
                $document->move(public_path('images/tenant-file'), $documentName);
                $tenant->document = $documentName;
            }

            $tenant->save();

            // Get new property and landlord info
            $property = Property::find($request->property_id);
            $landlord_id = $property->landlord_id;

            // Update or Create Tenancy
            $tenancy = Tenancy::where('tenant_id', $tenant->id)
                            ->where('status', true)
                            ->first();

            $tenancyCreated = false;
            if ($tenancy) {
                // Update existing tenancy
                $tenancy->property_id = $request->property_id;
                $tenancy->amount = $request->amount;
                $tenancy->start_date = $request->start_date;
                $tenancy->end_date = $request->end_date;
                $tenancy->auto_renew = $request->auto_renew;
                $tenancy->note = $request->note;
                $tenancy->save();
            } else {
                // Create new tenancy if none exists
                $tenancy = new Tenancy;
                $tenancy->property_id = $request->property_id;
                $tenancy->tenant_id = $tenant->id;
                $tenancy->landlord_id = $landlord_id;
                $tenancy->amount = $request->amount;
                $tenancy->due_date = 20;
                $tenancy->start_date = $request->start_date;
                $tenancy->end_date = $request->end_date;
                $tenancy->auto_renew = $request->auto_renew;
                $tenancy->note = $request->note;
                $tenancy->status = true;
                $tenancy->save();
                $tenancyCreated = true;
            }

            // Handle Transaction Update/Creation
            $transaction = Transaction::where('tenancy_id', $tenancy->id)
                                    ->where('transaction_type', 'due')
                                    ->where('status', true)
                                    ->first();

            if ($transaction) {
                // Update existing due transaction if amount changed or property changed
                if ($transaction->amount != $request->amount || $transaction->landlord_id != $landlord_id) {
                    $transaction->amount = $request->amount;
                    $transaction->landlord_id = $landlord_id;
                    $transaction->description = 'Monthly rent due for ' . date('F Y', strtotime($request->start_date));
                    $transaction->save();
                }
            } else {
                // Create new due transaction if none exists (for existing tenants)
                $transaction = new Transaction();
                $transaction->tran_id = 'TXN' . date('Ymd') . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);
                $transaction->date = $request->start_date;
                $transaction->tenancy_id = $tenancy->id;
                $transaction->tenant_id = $tenant->id;
                $transaction->landlord_id = $landlord_id;
                $transaction->amount = $request->amount;
                $transaction->payment_type = 'cash';
                $transaction->transaction_type = 'due';
                $transaction->status = true;
                $transaction->description = 'Monthly rent due for ' . date('F Y', strtotime($request->start_date));
                $transaction->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Tenant, Tenancy and Transaction updated successfully!'
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

    public function getTenantTransactions($tenantId)
    {
        // Get all transactions
        $transactions = Transaction::with('tenancy.property')
            ->where('tenant_id', $tenantId)
            ->orderBy('date', 'desc')
            ->get();

        // Filter out due transactions that have a corresponding received transaction
        $filteredTransactions = $transactions->filter(function($transaction) use ($transactions) {
            if ($transaction->transaction_type === 'due') {
                // Check if there's a received transaction for the same description/month
                $hasReceived = $transactions->where('description', 'Payment received for ' . $transaction->description)
                    ->where('transaction_type', 'received')
                    ->isNotEmpty();
                return !$hasReceived;
            }
            return true;
        });

        return response()->json([
            'transactions' => $filteredTransactions->values()->toArray(), // Convert to array
            'total_due' => $transactions->where('transaction_type', 'due')->where('status', true)->sum('amount'),
            'total_received' => $transactions->where('transaction_type', 'received')->where('status', true)->sum('amount')
        ]);
    }
    
    public function receivePayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'payment_type' => 'required|in:cash,bank,card,online',
            'payment_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            $dueTransaction = Transaction::findOrFail($request->transaction_id);
            
            if ($dueTransaction->transaction_type !== 'due') {
                return response()->json(['message' => 'Invalid transaction type'], 400);
            }

            // Create received transaction
            $receivedTransaction = new Transaction();
            $receivedTransaction->tran_id = 'TXN' . date('YmdHis') . mt_rand(100, 999);
            $receivedTransaction->date = $request->payment_date;
            $receivedTransaction->tenancy_id = $dueTransaction->tenancy_id;
            $receivedTransaction->tenant_id = $dueTransaction->tenant_id;
            $receivedTransaction->landlord_id = $dueTransaction->landlord_id;
            $receivedTransaction->amount = $dueTransaction->amount;
            $receivedTransaction->payment_type = $request->payment_type;
            $receivedTransaction->transaction_type = 'received';
            $receivedTransaction->status = true;
            $receivedTransaction->description = 'Payment received for ' . $dueTransaction->description;
            $receivedTransaction->save();

            // Mark the due transaction as inactive/paid
            $dueTransaction->status = false;
            $dueTransaction->save();

            DB::commit();

            return response()->json([
                'message' => 'Payment received successfully!',
                'transaction' => $receivedTransaction
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }
}