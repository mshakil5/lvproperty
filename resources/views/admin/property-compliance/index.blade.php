@extends('admin.pages.master')
@section('title', 'Property Compliance')
@section('content')

<div class="container-fluid" id="newBtnSection">
    <div class="row mb-3">
        <div class="col-auto">
            <button type="button" class="btn btn-primary" id="newBtn">
                Add New Compliance
            </button>
        </div>
    </div>
</div>

<div class="container-fluid" id="addThisFormContainer">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Compliance</h4>
                </div>
                <div class="card-body">
                    <form id="createThisForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="codeid" name="codeid">

                        <h5 class="mb-3">Basic Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Property <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="property_id" name="property_id">
                                    <option value="">Select Property</option>
                                    @foreach ($properties as $property)
                                        <option value="{{ $property->id }}">{{ $property->property_name }} - {{ $property->address }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Compliance Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="compliance_type_id" name="compliance_type_id">
                                    <option value="">Select Compliance Type</option>
                                    @foreach ($complianceTypes as $type)
                                        <option value="{{ $type->id }}" data-validity="{{ $type->validity_months }}" data-alert="{{ $type->alert_before_days }}">
                                            {{ $type->name }} ({{ $type->validity_months }} months)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Certificate Number</label>
                                <input type="text" class="form-control" id="certificate_number" name="certificate_number" placeholder="e.g., GS123456">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Expired">Expired</option>
                                    <option value="Renewed">Renewed</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="issue_date" name="issue_date">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Renewal Date</label>
                                <input type="date" class="form-control" id="renewal_date" name="renewal_date">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cost (£)</label>
                                <input type="number" step="0.01" class="form-control" id="cost" name="cost" placeholder="0.00">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Paid By <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="paid_by" name="paid_by">
                                    <option value="Landlord">Landlord</option>
                                    <option value="Tenant">Tenant</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Certificate Document</label>
                                <input type="file" class="form-control" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Upload PDF, JPG, or PNG files</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about this certificate"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" id="addBtn" class="btn btn-primary">
                        Create
                    </button>
                    <button type="button" id="FormCloseBtn" class="btn btn-light">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid" id="contentContainer">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Property Compliances</h4>
        </div>
        <div class="card-body">
            <table id="propertyComplianceTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Property</th>
                        <th>Compliance Type</th>
                        <th>Certificate No</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Cost</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Receive Payment Modal -->
<div class="modal fade" id="receivePaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receive Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="receivePaymentForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="compliance_id" name="compliance_id">
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="payment_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                            <select class="form-control" name="payment_type" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" placeholder="Any additional notes"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Receive Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize Select2 for all select elements
        $('.select2').select2({
            placeholder: "Select option",
            allowClear: true,
            width: '100%'
        });

        $('#propertyComplianceTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: "{{ route('allproperty-compliance') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'property_name',
                    name: 'property_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'compliance_type',
                    name: 'compliance_type',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'certificate_number',
                    name: 'certificate_number',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'issue_date',
                    name: 'issue_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'expiry_date',
                    name: 'expiry_date',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'cost',
                    name: 'cost',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Auto-calculate expiry date when issue date changes
        $(document).on('change', '#compliance_type_id, #issue_date', function() {
            var complianceTypeId = $('#compliance_type_id').val();
            var issueDate = $('#issue_date').val();
            
            if (complianceTypeId && issueDate) {
                var selectedOption = $('#compliance_type_id option:selected');
                var validityMonths = selectedOption.data('validity');
                
                if (validityMonths && issueDate) {
                    var issueDateObj = new Date(issueDate);
                    var expiryDateObj = new Date(issueDateObj);
                    expiryDateObj.setMonth(expiryDateObj.getMonth() + parseInt(validityMonths));
                    
                    var expiryDate = expiryDateObj.toISOString().split('T')[0];
                    $('#expiry_date').val(expiryDate);
                }
            }
        });

        // Receive Payment
        $(document).on('click', '.receive-payment-btn', function() {
            var complianceId = $(this).data('compliance-id');
            $('#compliance_id').val(complianceId);
            $('#receivePaymentModal').modal('show');
        });

        // Submit Payment Form
        $('#receivePaymentForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('property-compliance.receive-payment') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#receivePaymentModal').modal('hide');
                    reloadTable('#propertyComplianceTable');
                    showSuccess(response.message);
                },
                error: function(xhr) {
                    let error = xhr.responseJSON?.message || 'Failed to process payment';
                    showError(error);
                }
            });
        });

    });
</script>

<script>
    $(document).ready(function() {
        $("#addThisFormContainer").hide();
        $("#newBtn").click(function() {
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
            
            // Re-initialize Select2 when form is shown
            $('.select2').select2({
                placeholder: "Select option",
                allowClear: true,
                width: '100%'
            });
        });

        $("#FormCloseBtn").click(function() {
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = "{{ URL::to('/admin/property-compliance') }}";
        var upurl = "{{ URL::to('/admin/property-compliance-update') }}";

        $("#addBtn").click(function() {
            //create
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                form_data.append("property_id", $("#property_id").val());
                form_data.append("compliance_type_id", $("#compliance_type_id").val());
                form_data.append("certificate_number", $("#certificate_number").val());
                form_data.append("issue_date", $("#issue_date").val());
                form_data.append("expiry_date", $("#expiry_date").val());
                form_data.append("renewal_date", $("#renewal_date").val());
                form_data.append("status", $("#status").val());
                form_data.append("notes", $("#notes").val());
                form_data.append("cost", $("#cost").val());
                form_data.append("paid_by", $("#paid_by").val());
                
                // Append file if selected
                var documentFile = $('#document')[0].files[0];
                if (documentFile) {
                    form_data.append("document", documentFile);
                }

                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        showSuccess(d.message);
                        $("#addThisFormContainer").slideUp(300);
                        setTimeout(() => {
                            $("#newBtn").show(200);
                        }, 300);
                        reloadTable('#propertyComplianceTable');
                        clearform();
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                            showError(firstError);
                        } else {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                        console.error(xhr.responseText);
                    }
                });
            }
            //create  end

            //Update
            if ($(this).val() == 'Update') {
                var form_data = new FormData();
                form_data.append("property_id", $("#property_id").val());
                form_data.append("compliance_type_id", $("#compliance_type_id").val());
                form_data.append("certificate_number", $("#certificate_number").val());
                form_data.append("issue_date", $("#issue_date").val());
                form_data.append("expiry_date", $("#expiry_date").val());
                form_data.append("renewal_date", $("#renewal_date").val());
                form_data.append("status", $("#status").val());
                form_data.append("notes", $("#notes").val());
                form_data.append("cost", $("#cost").val());
                form_data.append("paid_by", $("#paid_by").val());
                form_data.append("codeid", $("#codeid").val());
                
                // Append file if selected
                var documentFile = $('#document')[0].files[0];
                if (documentFile) {
                    form_data.append("document", documentFile);
                }

                $.ajax({
                    url: upurl,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function(d) {
                        showSuccess(d.message);
                        $("#addThisFormContainer").hide();
                        $("#addThisFormContainer").slideUp(300);
                        setTimeout(() => {
                            $("#newBtn").show(200);
                        }, 300);
                        reloadTable('#propertyComplianceTable');
                        clearform();
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                            showError(firstError);
                        } else {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                        console.error(xhr.responseText);
                    }
                });
            }
            //Update  end
        });

        //Edit
        $("#contentContainer").on('click', '#EditBtn', function() {
            $("#cardTitle").text('Update this data');
            codeid = $(this).attr('rid');
            info_url = url + '/' + codeid + '/edit';
            $.get(info_url, {}, function(d) {
                populateForm(d);
                pagetop();
            });
        });
        //Edit  end 

        function populateForm(data) {
            $("#property_id").val(data.property_id).trigger('change');
            $("#compliance_type_id").val(data.compliance_type_id).trigger('change');
            $("#certificate_number").val(data.certificate_number);
            $("#issue_date").val(data.issue_date);
            $("#expiry_date").val(data.expiry_date);
            $("#renewal_date").val(data.renewal_date);
            $("#status").val(data.status).trigger('change');
            $("#notes").val(data.notes);
            $("#cost").val(data.cost);
            $("#paid_by").val(data.paid_by).trigger('change');
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#addBtn").html('Create');
            $("#cardTitle").text('Add new Compliance');
            
            // Clear Select2
            $('.select2').val(null).trigger('change');
        }
    });
</script>
@endsection