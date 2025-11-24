@extends('admin.pages.master')
@section('title', 'Tenant')
@section('content')

<div class="container-fluid" id="newBtnSection">
    <div class="row mb-3">
        <div class="col-auto">
            <button type="button" class="btn btn-primary" id="newBtn">
                Add New Tenant
            </button>
        </div>
    </div>
</div>

<div class="container-fluid" id="addThisFormContainer">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Tenant</h4>
                </div>
                <div class="card-body">
                    <form id="createThisForm">
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
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Permanent Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder=""></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Current Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="current_address" name="current_address" rows="2" placeholder=""></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Previous Address</label>
                                <textarea class="form-control" id="previous_address" name="previous_address" rows="2" placeholder=""></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Document</label>
                                <input type="file" class="form-control" id="document" name="document">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Bank Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" id="account_number" name="account_number" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sort Code</label>
                                <input type="text" class="form-control" id="sort_code" name="sort_code" placeholder="">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Emergency Contact</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact Phone</label>
                                <input type="text" class="form-control" id="emergency_contact_phone" name="emergency_contact_phone" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Relation</label>
                                <input type="text" class="form-control" id="emergency_contact_relation" name="emergency_contact_relation" placeholder="">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">References & Verification</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Reference Checked <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="reference_checked" name="reference_checked">
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                    <option value="Processing">Processing</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Immigration Status <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="immigration_status" name="immigration_status">
                                    <option value="Not Checked">Not Checked</option>
                                    <option value="Checked">Checked</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Right to Rent Status <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="right_to_rent_status" name="right_to_rent_status">
                                    <option value="Pending">Pending</option>
                                    <option value="Verified">Verified</option>
                                    <option value="Not Verified">Not Verified</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Credit Score</label>
                                <input type="text" class="form-control" id="credit_score" name="credit_score" placeholder="">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Right to Rent Check Date</label>
                                <input type="date" class="form-control" id="right_to_rent_check_date" name="right_to_rent_check_date">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Previous Landlord Reference</label>
                                <textarea class="form-control" id="previous_landlord_reference" name="previous_landlord_reference" rows="2" placeholder=""></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Personal Reference</label>
                                <textarea class="form-control" id="personal_reference" name="personal_reference" rows="2" placeholder=""></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Tenancy Agreement</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Monthly Amount (£) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="0.00">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" readonly>
                                <small class="text-muted">Auto-calculated: Start Date + 1 month</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Auto Renew</label>
                                <select class="form-control" id="auto_renew" name="auto_renew">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Any special notes or terms..."></textarea>
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

<!-- Tenancies Modal -->
<div class="modal fade" id="tenanciesModal" tabindex="-1" aria-labelledby="tenanciesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tenanciesModalLabel">Tenant Transactions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Due</h6>
                                <h4 id="totalDueAmount">£0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Received</h6>
                                <h4 id="totalReceivedAmount">£0.00</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Balance</h6>
                                <h4 id="balanceAmount">£0.00</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="transactionsTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                {{-- <th>Status</th> --}}
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="transactionsTableBody">
                            <!-- Transactions will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Receive Payment Modal -->
<div class="modal fade" id="receivePaymentModal" tabindex="-1" aria-labelledby="receivePaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receivePaymentModalLabel">Receive Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="receivePaymentForm">
                    @csrf
                    <input type="hidden" id="transaction_id" name="transaction_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Type</label>
                        <select class="form-control" id="payment_type" name="payment_type" required>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Amount</label>
                        <input type="text" class="form-control" id="payment_amount" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" id="payment_description" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmReceivePayment">Receive Payment</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid" id="contentContainer">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Tenants</h4>
        </div>
        <div class="card-body">
            <table id="tenantTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Property</th>
                        <th>Reference Check</th>
                        <th>Immigration Status</th>
                        <th>Right to Rent</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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

        $('#tenantTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: "{{ route('alltenant') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'property_name',
                    name: 'property_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'reference_checked',
                    name: 'reference_checked',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'immigration_status',
                    name: 'immigration_status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'right_to_rent',
                    name: 'right_to_rent',
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

        $(document).on('change', '.toggle-status', function() {
            var tenant_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/tenant-status',
                method: "POST",
                data: {
                    tenant_id: tenant_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(d) {
                    reloadTable('#tenantTable');
                    showSuccess(d.message);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    showError('Failed to update status');
                }
            });
        });

        $(document).on('change', '#start_date', function() {
            var startDate = $(this).val();
            if (startDate) {
                // Calculate end date (start date + 1 month)
                var start = new Date(startDate);
                var end = new Date(start);
                end.setMonth(end.getMonth() + 1);
                
                // Format to YYYY-MM-DD
                var endFormatted = end.toISOString().split('T')[0];
                $('#end_date').val(endFormatted);
            }
        });

        if ($('#start_date').val()) {
            $('#start_date').trigger('change');
        }

        // Handle Tenancies button click
        $(document).on('click', '.view-tenancies-btn', function() {
            var tenantId = $(this).data('tenant-id');
            loadTenantTransactions(tenantId);
        });

        // Load tenant transactions
        function loadTenantTransactions(tenantId) {
            $.ajax({
                url: '/admin/tenant/' + tenantId + '/transactions',
                method: 'GET',
                success: function(response) {
                    populateTransactionsTable(response.transactions);
                    updateSummaryCards(response.total_due, response.total_received);
                    $('#tenanciesModal').modal('show');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    showError('Failed to load transactions');
                }
            });
        }

        // Populate transactions table
        function populateTransactionsTable(transactions) {
            var tbody = $('#transactionsTableBody');
            tbody.empty();
            
            if (transactions.length === 0) {
                tbody.append('<tr><td colspan="6" class="text-center">No transactions found</td></tr>');
                return;
            }
            
            transactions.forEach(function(transaction) {
                var statusBadge = transaction.status ? 
                    '<span class="badge bg-success">Active</span>' : 
                    '<span class="badge bg-secondary">Paid</span>';
                    
                var typeBadge = transaction.transaction_type === 'due' ? 
                    '<span class="badge bg-warning">Due</span>' : 
                    '<span class="badge bg-success">Received</span>';
                    
                var actionBtn = '';
                // Only show receive button for active due transactions
                if (transaction.transaction_type === 'due' && transaction.status) {
                    actionBtn = '<button class="btn btn-sm btn-primary receive-payment-btn" ' +
                            'data-transaction-id="' + transaction.id + '" ' +
                            'data-amount="' + transaction.amount + '" ' +
                            'data-description="' + transaction.description + '">Receive</button>';
                } else if (transaction.transaction_type === 'due' && !transaction.status) {
                    actionBtn = '<span class="text-muted">Paid</span>';
                } else {
                    actionBtn = '<span class="text-muted">-</span>';
                }
                
                var row = '<tr>' +
                    '<td>' + transaction.date + '</td>' +
                    '<td>' + transaction.description + '</td>' +
                    '<td>' + typeBadge + '</td>' +
                    '<td>£' + parseFloat(transaction.amount).toFixed(2) + '</td>' +
                    '<td class="d-none">' + statusBadge + '</td>' +
                    '<td>' + actionBtn + '</td>' +
                    '</tr>';
                    
                tbody.append(row);
            });
        }

        // Update summary cards
        function updateSummaryCards(totalDue, totalReceived) {
            var balance = totalDue - totalReceived;
            
            $('#totalDueAmount').text('£' + parseFloat(totalDue).toFixed(2));
            $('#totalReceivedAmount').text('£' + parseFloat(totalReceived).toFixed(2));
            $('#balanceAmount').text('£' + parseFloat(balance).toFixed(2));
        }

        // Handle Receive Payment button click
        $(document).on('click', '.receive-payment-btn', function() {
            var transactionId = $(this).data('transaction-id');
            var amount = $(this).data('amount');
            var description = $(this).data('description');
            
            $('#transaction_id').val(transactionId);
            $('#payment_amount').val('£' + parseFloat(amount).toFixed(2));
            $('#payment_description').val(description);
            
            $('#receivePaymentModal').modal('show');
        });

        // Confirm receive payment
        $('#confirmReceivePayment').click(function() {
            var formData = new FormData($('#receivePaymentForm')[0]);
            
            $.ajax({
                url: '/admin/tenant/receive-payment',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showSuccess(response.message);
                    $('#receivePaymentModal').modal('hide');
                    $('#tenanciesModal').modal('hide');
                    reloadTable('#tenantTable');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                        showError(firstError);
                    } else {
                        showError(xhr.responseJSON?.message ?? "Failed to process payment");
                    }
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

        var url = "{{ URL::to('/admin/tenant') }}";
        var upurl = "{{ URL::to('/admin/tenant-update') }}";

        $("#addBtn").click(function() {
            //create
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                // Basic Information
                form_data.append("property_id", $("#property_id").val());
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("address", $("#address").val());
                form_data.append("current_address", $("#current_address").val());
                form_data.append("previous_address", $("#previous_address").val());
                
                // Bank Details
                form_data.append("bank_name", $("#bank_name").val());
                form_data.append("account_number", $("#account_number").val());
                form_data.append("sort_code", $("#sort_code").val());
                
                // Emergency Contact
                form_data.append("emergency_contact_name", $("#emergency_contact_name").val());
                form_data.append("emergency_contact_phone", $("#emergency_contact_phone").val());
                form_data.append("emergency_contact_relation", $("#emergency_contact_relation").val());
                
                // References & Verification
                form_data.append("reference_checked", $("#reference_checked").val());
                form_data.append("previous_landlord_reference", $("#previous_landlord_reference").val());
                form_data.append("personal_reference", $("#personal_reference").val());
                form_data.append("credit_score", $("#credit_score").val());
                form_data.append("immigration_status", $("#immigration_status").val());
                form_data.append("right_to_rent_status", $("#right_to_rent_status").val());
                form_data.append("right_to_rent_check_date", $("#right_to_rent_check_date").val());

                form_data.append("amount", $("#amount").val());
                form_data.append("start_date", $("#start_date").val());
                form_data.append("end_date", $("#end_date").val());
                form_data.append("auto_renew", $("#auto_renew").val());
                form_data.append("note", $("#note").val());

                if ($("#document")[0].files[0]) {
                    form_data.append("document", $("#document")[0].files[0]);
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
                        reloadTable('#tenantTable');
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
                // Basic Information
                form_data.append("property_id", $("#property_id").val());
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("address", $("#address").val());
                form_data.append("current_address", $("#current_address").val());
                form_data.append("previous_address", $("#previous_address").val());
                
                // Bank Details
                form_data.append("bank_name", $("#bank_name").val());
                form_data.append("account_number", $("#account_number").val());
                form_data.append("sort_code", $("#sort_code").val());
                
                // Emergency Contact
                form_data.append("emergency_contact_name", $("#emergency_contact_name").val());
                form_data.append("emergency_contact_phone", $("#emergency_contact_phone").val());
                form_data.append("emergency_contact_relation", $("#emergency_contact_relation").val());
                
                // References & Verification
                form_data.append("reference_checked", $("#reference_checked").val());
                form_data.append("previous_landlord_reference", $("#previous_landlord_reference").val());
                form_data.append("personal_reference", $("#personal_reference").val());
                form_data.append("credit_score", $("#credit_score").val());
                form_data.append("immigration_status", $("#immigration_status").val());
                form_data.append("right_to_rent_status", $("#right_to_rent_status").val());
                form_data.append("right_to_rent_check_date", $("#right_to_rent_check_date").val());
                
                // TENANCY AGREEMENT FIELDS - ADD THESE
                form_data.append("amount", $("#amount").val());
                form_data.append("start_date", $("#start_date").val());
                form_data.append("end_date", $("#end_date").val());
                form_data.append("auto_renew", $("#auto_renew").val());
                form_data.append("note", $("#note").val());

                if ($("#document")[0].files[0]) {
                    form_data.append("document", $("#document")[0].files[0]);
                }
                
                form_data.append("codeid", $("#codeid").val());

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
                        reloadTable('#tenantTable');
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
                if ($('#start_date').val()) {
                    $('#start_date').trigger('change');
                }
            });
        });
        //Edit  end 

        function populateForm(data) {
            // Basic Information
            $("#property_id").val(data.property_id).trigger('change');
            $("#name").val(data.name);
            $("#email").val(data.email);
            $("#phone").val(data.phone);
            $("#address").val(data.address);
            $("#current_address").val(data.current_address);
            $("#previous_address").val(data.previous_address);
            
            // Bank Details
            $("#bank_name").val(data.bank_name);
            $("#account_number").val(data.account_number);
            $("#sort_code").val(data.sort_code);
            
            // Emergency Contact
            $("#emergency_contact_name").val(data.emergency_contact_name);
            $("#emergency_contact_phone").val(data.emergency_contact_phone);
            $("#emergency_contact_relation").val(data.emergency_contact_relation);
            
            // References & Verification
            $("#reference_checked").val(data.reference_checked).trigger('change');
            $("#previous_landlord_reference").val(data.previous_landlord_reference);
            $("#personal_reference").val(data.personal_reference);
            $("#credit_score").val(data.credit_score);
            $("#immigration_status").val(data.immigration_status).trigger('change');
            $("#right_to_rent_status").val(data.right_to_rent_status).trigger('change');
            $("#right_to_rent_check_date").val(data.right_to_rent_check_date);
            
            // TENANCY AGREEMENT FIELDS - ADD THESE
            $("#amount").val(data.amount);
            $("#start_date").val(data.start_date);
            $("#end_date").val(data.end_date);
            $("#auto_renew").val(data.auto_renew);
            $("#note").val(data.note);
            
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
            $("#cardTitle").text('Add new Tenant');
            
            // Clear Select2
            $('.select2').val(null).trigger('change');
            
            // Clear tenancy fields specifically
            $("#amount").val('');
            $("#start_date").val('');
            $("#end_date").val('');
            $("#auto_renew").val('1');
            $("#note").val('');
            $("#document").val('');
        }
    });
</script>
@endsection