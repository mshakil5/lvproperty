@extends('admin.pages.master')
@section('title', 'Landlord')
@section('content')

    <div class="container-fluid" id="newBtnSection">
        <div class="row mb-3">
            <div class="col-auto">
                <button type="button" class="btn btn-primary" id="newBtn">
                    Add New Landlord
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="addThisFormContainer">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Landlord</h4>
                    </div>
                    <div class="card-body">
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" id="codeid" name="codeid">

                            <h5 class="mb-3">Basic Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Current Address</label>
                                    <textarea class="form-control" id="current_address" name="current_address" rows="2" placeholder=""></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Permanent Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="2" placeholder=""></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Previous Address</label>
                                    <textarea class="form-control" id="previous_address" name="previous_address" rows="2" placeholder=""></textarea>
                                </div>
                            </div>

                            <hr class="my-4 d-none">
                            <h5 class="mb-3 d-none">Emergency Contact</h5>
                            <div class="row g-3 d-none">
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

                            <hr class="my-4 d-none">
                            <h5 class="mb-3 d-none">References & Verification</h5>
                            <div class="row g-3 d-none">
                                <div class="col-md-4">
                                    <label class="form-label">Reference Checked</label>
                                    <select class="form-control" id="reference_checked" name="reference_checked">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                        <option value="processing">Processing</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Credit Score</label>
                                    <input type="text" class="form-control" id="credit_score" name="credit_score" placeholder="">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Right to Rent Status</label>
                                    <select class="form-control" id="right_to_rent_status" name="right_to_rent_status">
                                        <option value="pending">Pending</option>
                                        <option value="verified">Verified</option>
                                        <option value="not_verified">Not Verified</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Right to Rent Check Date</label>
                                    <input type="date" class="form-control" id="right_to_rent_check_date" name="right_to_rent_check_date">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Previous Landlord Reference</label>
                                    <textarea class="form-control" id="previous_landlord_reference" name="previous_landlord_reference" rows="2" placeholder=""></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Personal Reference</label>
                                    <textarea class="form-control" id="personal_reference" name="personal_reference" rows="2" placeholder=""></textarea>
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
                            <h5 class="mb-3">Service Agreement</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Service Type</label>
                                    <select class="form-control" id="service_type" name="service_type">
                                        <option value="">Select Service Type</option>
                                        <option value="Full Management">Full Management</option>
                                        <option value="Rent Collection">Rent Collection</option>
                                        <option value="Tenant Finding">Tenant Finding</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Management Fee (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="management_fee" name="management_fee" placeholder="">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Agreement Date</label>
                                    <input type="date" class="form-control" id="agreement_date" name="agreement_date">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Agreement Duration (Months)</label>
                                    <input type="number" class="form-control" id="agreement_duration" name="agreement_duration" placeholder="">
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
                <h4 class="card-title mb-0">Landlords</h4>
            </div>
            <div class="card-body">
                <table id="landlordTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Service Type</th>
                            <th>Management Fee</th>
                            {{-- <th>Reference Status</th>
                            <th>Right to Rent</th> --}}
                            <th>Agreement Due Date</th>
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
        $('#landlordTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: "{{ route('alllandlord') }}",
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
                    data: 'service_type',
                    name: 'service_type',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'management_fee',
                    name: 'management_fee',
                    orderable: false,
                    searchable: false
                },
                // {
                //     data: 'reference_status',
                //     name: 'reference_status',
                //     orderable: false,
                //     searchable: false
                // },
                // {
                //     data: 'right_to_rent',
                //     name: 'right_to_rent',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'agreement_due_date',
                    name: 'agreement_due_date',
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
            var landlord_id = $(this).data('id');
            var status = $(this).prop('checked') ? 1 : 0;

            $.ajax({
                url: '/admin/landlord-status',
                method: "POST",
                data: {
                    landlord_id: landlord_id,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(d) {
                    reloadTable('#landlordTable');
                    showSuccess(d.message);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    showError('Failed to update status');
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

        var url = "{{ URL::to('/admin/landlord') }}";
        var upurl = "{{ URL::to('/admin/landlord-update') }}";

        $("#addBtn").click(function() {
            //create
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                // Basic Information
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("address", $("#address").val());
                form_data.append("current_address", $("#current_address").val());
                form_data.append("previous_address", $("#previous_address").val());
                
                // Emergency Contact
                form_data.append("emergency_contact_name", $("#emergency_contact_name").val());
                form_data.append("emergency_contact_phone", $("#emergency_contact_phone").val());
                form_data.append("emergency_contact_relation", $("#emergency_contact_relation").val());
                
                // References & Verification
                form_data.append("reference_checked", $("#reference_checked").val());
                form_data.append("credit_score", $("#credit_score").val());
                form_data.append("previous_landlord_reference", $("#previous_landlord_reference").val());
                form_data.append("personal_reference", $("#personal_reference").val());
                form_data.append("right_to_rent_status", $("#right_to_rent_status").val());
                form_data.append("right_to_rent_check_date", $("#right_to_rent_check_date").val());
                
                // Bank Details
                form_data.append("bank_name", $("#bank_name").val());
                form_data.append("account_number", $("#account_number").val());
                form_data.append("sort_code", $("#sort_code").val());
                
                // Service Agreement
                form_data.append("service_type", $("#service_type").val());
                form_data.append("management_fee", $("#management_fee").val());
                form_data.append("agreement_date", $("#agreement_date").val());
                form_data.append("agreement_duration", $("#agreement_duration").val());

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
                        reloadTable('#landlordTable');
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
                form_data.append("name", $("#name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("address", $("#address").val());
                form_data.append("current_address", $("#current_address").val());
                form_data.append("previous_address", $("#previous_address").val());
                
                // Emergency Contact
                form_data.append("emergency_contact_name", $("#emergency_contact_name").val());
                form_data.append("emergency_contact_phone", $("#emergency_contact_phone").val());
                form_data.append("emergency_contact_relation", $("#emergency_contact_relation").val());
                
                // References & Verification
                form_data.append("reference_checked", $("#reference_checked").val());
                form_data.append("credit_score", $("#credit_score").val());
                form_data.append("previous_landlord_reference", $("#previous_landlord_reference").val());
                form_data.append("personal_reference", $("#personal_reference").val());
                form_data.append("right_to_rent_status", $("#right_to_rent_status").val());
                form_data.append("right_to_rent_check_date", $("#right_to_rent_check_date").val());
                
                // Bank Details
                form_data.append("bank_name", $("#bank_name").val());
                form_data.append("account_number", $("#account_number").val());
                form_data.append("sort_code", $("#sort_code").val());
                
                // Service Agreement
                form_data.append("service_type", $("#service_type").val());
                form_data.append("management_fee", $("#management_fee").val());
                form_data.append("agreement_date", $("#agreement_date").val());
                form_data.append("agreement_duration", $("#agreement_duration").val());
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
                        reloadTable('#landlordTable');
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
            console.log(data);
            // Basic Information
            $("#name").val(data.name);
            $("#email").val(data.email);
            $("#phone").val(data.phone);
            $("#address").val(data.address);
            $("#current_address").val(data.current_address);
            $("#previous_address").val(data.previous_address);
            
            // Emergency Contact
            $("#emergency_contact_name").val(data.emergency_contact_name);
            $("#emergency_contact_phone").val(data.emergency_contact_phone);
            $("#emergency_contact_relation").val(data.emergency_contact_relation);
            
            // References & Verification
            $("#reference_checked").val(data.reference_checked);
            $("#credit_score").val(data.credit_score);
            $("#previous_landlord_reference").val(data.previous_landlord_reference);
            $("#personal_reference").val(data.personal_reference);
            $("#right_to_rent_status").val(data.right_to_rent_status);
            $("#right_to_rent_check_date").val(data.right_to_rent_check_date);
            
            // Bank Details
            $("#bank_name").val(data.bank_name);
            $("#account_number").val(data.account_number);
            $("#sort_code").val(data.sort_code);
            
            // Service Agreement
            $("#service_type").val(data.service_type);
            $("#management_fee").val(data.management_fee);
            $("#agreement_date").val(data.agreement_date);
            $("#agreement_duration").val(data.agreement_duration);
            
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
            $("#cardTitle").text('Add new Landlord');
        }
    });
</script>
@endsection