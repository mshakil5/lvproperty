<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PropertyCompliance;
use App\Models\Property;
use App\Models\ComplianceType;
use DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Tenancy;

class PropertyComplianceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $propertyCompliances = PropertyCompliance::with(['property', 'complianceType'])
                ->select(['id', 'property_id', 'compliance_type_id', 'certificate_number', 'issue_date', 'expiry_date', 'status', 'cost', 'paid_by'])
                ->orderBy('id', 'desc');

            return DataTables::of($propertyCompliances)
                ->addIndexColumn()
                ->addColumn('property_name', function ($row) {
                    return $row->property ? $row->property->property_name : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('compliance_type', function ($row) {
                    return $row->complianceType ? $row->complianceType->name : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('certificate_number', function ($row) {
                    return $row->certificate_number ?: '<span class="text-muted">N/A</span>';
                })
                ->addColumn('issue_date', function ($row) {
                    return $row->issue_date ? date('d M, Y', strtotime($row->issue_date)) : 'N/A';
                })
                ->addColumn('expiry_date', function ($row) {
                    return $row->expiry_date ? date('d M, Y', strtotime($row->expiry_date)) : 'N/A';
                })
                ->addColumn('cost', function ($row) {
                    return $row->cost ? '£' . number_format($row->cost, 2) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('payment_status', function ($row) {
                    // Get payment status from transaction
                    $transaction = Transaction::where('property_compliance_id', $row->id)
                        ->where('transaction_type', 'due')
                        ->first();
                    
                    $badge_class = $transaction && $transaction->status ? 'bg-success' : 'bg-warning';
                    $text = $transaction && $transaction->status ? 'Paid' : 'Unpaid';
                    return '<span class="badge '.$badge_class.'">'.$text.'</span>';
                })
                ->addColumn('status', function ($row) {
                    $badge_class = [
                        'Active' => 'bg-success',
                        'Expired' => 'bg-danger', 
                        'Renewed' => 'bg-info',
                        'Pending' => 'bg-warning'
                    ][$row->status] ?? 'bg-secondary';
                    
                    return '<span class="badge '.$badge_class.'">'.$row->status.'</span>';
                })
                ->addColumn('action', function ($row) {
                    // Check if payment is due
                    $dueTransaction = Transaction::where('property_compliance_id', $row->id)
                        ->where('transaction_type', 'due')
                        ->first();
                    
                    $receiveBtn = '';
                    if ($dueTransaction && !$dueTransaction->status) {
                        $receiveBtn = '
                            <li>
                                <button class="dropdown-item receive-payment-btn" data-compliance-id="'.$row->id.'">
                                    <i class="ri-money-pound-circle-fill align-bottom me-2 text-muted"></i> Receive Payment
                                </button>
                            </li>
                            <li class="dropdown-divider"></li>
                        ';
                    }
                    
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                '.$receiveBtn.'
                                <li>
                                    <button class="dropdown-item" id="EditBtn" rid="'.$row->id.'">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit
                                    </button>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item deleteBtn" 
                                            data-delete-url="' . route('property-compliance.delete', $row->id) . '" 
                                            data-method="DELETE" 
                                            data-table="#propertyComplianceTable">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['property_name', 'compliance_type', 'certificate_number', 'cost', 'payment_status', 'status', 'action'])
                ->make(true);
        }

        $properties = Property::where('status', '!=', 'Maintenance')->get();
        $complianceTypes = ComplianceType::where('status', 1)->get();
        return view('admin.property-compliance.index', compact('properties', 'complianceTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'compliance_type_id' => 'required|exists:compliance_types,id',
            'certificate_number' => 'nullable',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'renewal_date' => 'nullable|date',
            'status' => 'required|in:Active,Expired,Renewed,Pending',
            'notes' => 'nullable',
            'cost' => 'nullable|numeric|min:0',
            'paid_by' => 'required|in:Landlord,Tenant'
        ]);

        DB::beginTransaction();
        try {
            $data = new PropertyCompliance;
            $data->property_id = $request->property_id;
            $data->compliance_type_id = $request->compliance_type_id;
            $data->certificate_number = $request->certificate_number;
            $data->issue_date = $request->issue_date;
            $data->expiry_date = $request->expiry_date;
            $data->renewal_date = $request->renewal_date;
            $data->status = $request->status;
            $data->notes = $request->notes;
            $data->cost = $request->cost;
            $data->paid_by = $request->paid_by;
            
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('compliance_documents', $fileName, 'public');
                $data->document_path = $filePath;
            }
            
            $data->save();

            if ($data->cost && $data->cost > 0) {
                $property = Property::find($data->property_id);
                $complianceType = ComplianceType::find($data->compliance_type_id);
                
                $transaction = new Transaction();
                $transaction->tran_id = 'COMP-REC-' . time() . '-' . $data->id;
                $transaction->date = now();
                $transaction->property_compliance_id = $data->id;
                $transaction->amount = $data->cost;
                $transaction->transaction_type = 'due';
                $transaction->status = false; // Always unpaid when created
                $transaction->description = $complianceType->name . ' Certificate - Receivable from ' . $data->paid_by;
                
                if ($data->paid_by == 'Landlord') {
                    $transaction->landlord_id = $property->landlord_id;
                } else {
                    $currentTenancy = Tenancy::where('property_id', $data->property_id)
                        ->where('status', true)->first();
                    if ($currentTenancy) {
                        $transaction->tenant_id = $currentTenancy->tenant_id;
                    }
                }
                
                $transaction->save();
            }

            DB::commit();
            
            return response()->json([
                'message' => 'Property Compliance created successfully!',
                'propertyCompliance' => $data 
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Server error while creating property compliance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $where = [
            'id'=>$id
        ];
        $info = PropertyCompliance::with(['property', 'complianceType'])->where($where)->get()->first();
        return response()->json($info);
    }

    public function update(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'compliance_type_id' => 'required|exists:compliance_types,id',
            'certificate_number' => 'nullable',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'renewal_date' => 'nullable|date',
            'status' => 'required|in:Active,Expired,Renewed,Pending',
            'notes' => 'nullable',
            'cost' => 'nullable|numeric|min:0',
            'paid_by' => 'required|in:Landlord,Tenant'
        ]);

        DB::beginTransaction();
        try {
            $data = PropertyCompliance::findOrFail($request->codeid);
            $oldCost = $data->cost;
            $oldPaidBy = $data->paid_by;
            
            $data->property_id = $request->property_id;
            $data->compliance_type_id = $request->compliance_type_id;
            $data->certificate_number = $request->certificate_number;
            $data->issue_date = $request->issue_date;
            $data->expiry_date = $request->expiry_date;
            $data->renewal_date = $request->renewal_date;
            $data->status = $request->status;
            $data->notes = $request->notes;
            $data->cost = $request->cost;
            $data->paid_by = $request->paid_by;

            if ($request->hasFile('document')) {
                if ($data->document_path && file_exists(public_path('storage/' . $data->document_path))) {
                    unlink(public_path('storage/' . $data->document_path));
                }
                
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('compliance_documents', $fileName, 'public');
                $data->document_path = $filePath;
            }

            $data->save();

            // Handle transaction updates
            $existingTransaction = Transaction::where('property_compliance_id', $data->id)
                ->where('transaction_type', 'due')
                ->first();

            if ($data->cost && $data->cost > 0) {
                $property = Property::find($data->property_id);
                $complianceType = ComplianceType::find($data->compliance_type_id);
                
                if ($existingTransaction) {
                    $existingTransaction->amount = $data->cost;
                    $existingTransaction->description = $complianceType->name . ' Certificate - Receivable from ' . $data->paid_by;
                    
                    if ($data->paid_by != $oldPaidBy) {
                        if ($data->paid_by == 'Landlord') {
                            $existingTransaction->landlord_id = $property->landlord_id;
                            $existingTransaction->tenant_id = null;
                        } else {
                            $currentTenancy = Tenancy::where('property_id', $data->property_id)
                                ->where('status', true)->first();
                            $existingTransaction->tenant_id = $currentTenancy ? $currentTenancy->tenant_id : null;
                            $existingTransaction->landlord_id = null;
                        }
                    }
                    
                    $existingTransaction->save();
                } else {
                    // Create new transaction if didn't exist
                    $transaction = new Transaction();
                    $transaction->tran_id = 'COMP-REC-' . time() . '-' . $data->id;
                    $transaction->date = now();
                    $transaction->property_compliance_id = $data->id;
                    $transaction->amount = $data->cost;
                    $transaction->transaction_type = 'due';
                    $transaction->status = false; // Always unpaid when created
                    $transaction->description = $complianceType->name . ' Certificate - Receivable from ' . $data->paid_by;
                    
                    if ($data->paid_by == 'Landlord') {
                        $transaction->landlord_id = $property->landlord_id;
                    } else {
                        $currentTenancy = Tenancy::where('property_id', $data->property_id)
                            ->where('status', true)->first();
                        if ($currentTenancy) {
                            $transaction->tenant_id = $currentTenancy->tenant_id;
                        }
                    }
                    
                    $transaction->save();
                }
            } else {
                if ($existingTransaction) {
                    $existingTransaction->delete();
                }
            }

            DB::commit();
            
            return response()->json([
                'message' => 'Property Compliance updated successfully!'
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update property compliance: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $data = PropertyCompliance::find($id);
            
            if (!$data) {
                return response()->json([
                    'message' => 'Property Compliance not found.'
                ], 404);
            }

            $transaction = Transaction::where('property_compliance_id', $data->id)
                ->where('transaction_type', 'due')
                ->first();
            if ($transaction) {
                $transaction->delete();
            }

            if ($data->document_path && file_exists(public_path('storage/' . $data->document_path))) {
                unlink(public_path('storage/' . $data->document_path));
            }

            if ($data->delete()) {
                DB::commit();
                return response()->json([
                    'message' => 'Property Compliance deleted successfully.'
                ], 200);
            }

            DB::rollback();
            return response()->json([
                'message' => 'Failed to delete property compliance.'
            ], 500);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to delete: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getComplianceTypeDetails($id)
    {
        $complianceType = ComplianceType::find($id);
        if ($complianceType) {
            return response()->json([
                'validity_months' => $complianceType->validity_months,
                'alert_before_days' => $complianceType->alert_before_days
            ]);
        }
        return response()->json(null, 404);
    }

    public function receivePayment(Request $request)
    {
        $request->validate([
            'compliance_id' => 'required|exists:property_compliances,id',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:cash,bank,card,online',
            'notes' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $compliance = PropertyCompliance::findOrFail($request->compliance_id);
            
            // Find the due transaction
            $dueTransaction = Transaction::where('property_compliance_id', $compliance->id)
                ->where('transaction_type', 'due')
                ->first();

            if (!$dueTransaction) {
                return response()->json([
                    'message' => 'No due transaction found for this compliance.'
                ], 404);
            }

            if ($dueTransaction->status) {
                return response()->json([
                    'message' => 'Payment already received for this compliance.'
                ], 400);
            }

            // Mark due transaction as paid
            $dueTransaction->update(['status' => true]);

            // Create received transaction
            $receivedTransaction = new Transaction();
            $receivedTransaction->tran_id = 'COMP-PAY-' . time() . '-' . $compliance->id;
            $receivedTransaction->date = $request->payment_date;
            $receivedTransaction->property_compliance_id = $compliance->id;
            $receivedTransaction->amount = $dueTransaction->amount;
            $receivedTransaction->payment_type = $request->payment_type;
            $receivedTransaction->transaction_type = 'received';
            $receivedTransaction->status = true;
            $receivedTransaction->description = 'Payment received for ' . $dueTransaction->description;
            $receivedTransaction->description = $request->notes;
            
            // Copy landlord/tenant from due transaction
            $receivedTransaction->landlord_id = $dueTransaction->landlord_id;
            $receivedTransaction->tenant_id = $dueTransaction->tenant_id;
            
            $receivedTransaction->save();

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