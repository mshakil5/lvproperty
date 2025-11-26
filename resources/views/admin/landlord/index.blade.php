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
                                    <label class="form-label">Company Name </label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Postcode <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="postcode" name="postcode" placeholder="">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correspondence Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="correspondence_address" name="correspondence_address" rows="2" placeholder=""></textarea>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="mb-3">Compliance</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Proof of ID (Max Size: 5MB)</label>
                                    <input type="file" class="form-control" id="proof_of_id" name="proof_of_id" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="proof_of_id_link" class="mt-1"></div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Authorisation Letter (Max Size: 5MB)</label>
                                    <input type="file" class="form-control" id="authorisation_letter" name="authorisation_letter" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="authorisation_letter_link" class="mt-1"></div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Agreement Between Landlord and Agent (Max Size: 5MB)</label>
                                    <input type="file" class="form-control" id="landlord_agent_agreement" name="landlord_agent_agreement" accept=".pdf,.jpg,.jpeg,.png">
                                    <div id="landlord_agent_agreement_link" class="mt-1"></div>
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
                            <th>Landlord</th>
                            <th>Address</th>
                            <th>Compliance</th>
                            <th>Bank Details</th>
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
            ajax: "{{ route('alllandlord') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'landlord', name: 'name' }, 
                { data: 'address', name: 'postcode' },
                { data: 'compliance', name: 'proof_of_id' },
                { data: 'bank_details', name: 'bank_name' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
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
            // Create
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                // Basic Information
                form_data.append("name", $("#name").val());
                form_data.append("company_name", $("#company_name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("postcode", $("#postcode").val());
                form_data.append("correspondence_address", $("#correspondence_address").val());
                
                // Compliance - File uploads
                if ($("#proof_of_id")[0].files[0]) {
                    form_data.append("proof_of_id", $("#proof_of_id")[0].files[0]);
                }
                if ($("#authorisation_letter")[0].files[0]) {
                    form_data.append("authorisation_letter", $("#authorisation_letter")[0].files[0]);
                }
                if ($("#landlord_agent_agreement")[0].files[0]) {
                    form_data.append("landlord_agent_agreement", $("#landlord_agent_agreement")[0].files[0]);
                }
                
                // Bank Details
                form_data.append("bank_name", $("#bank_name").val());
                form_data.append("account_number", $("#account_number").val());
                form_data.append("sort_code", $("#sort_code").val());

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
            // Create end

            // Update
            if ($(this).val() == 'Update') {
                var form_data = new FormData();
                // Basic Information
                form_data.append("name", $("#name").val());
                form_data.append("company_name", $("#company_name").val());
                form_data.append("email", $("#email").val());
                form_data.append("phone", $("#phone").val());
                form_data.append("postcode", $("#postcode").val());
                form_data.append("correspondence_address", $("#correspondence_address").val());
                
                // Compliance - File uploads
                if ($("#proof_of_id")[0].files[0]) {
                    form_data.append("proof_of_id", $("#proof_of_id")[0].files[0]);
                }
                if ($("#authorisation_letter")[0].files[0]) {
                    form_data.append("authorisation_letter", $("#authorisation_letter")[0].files[0]);
                }
                if ($("#landlord_agent_agreement")[0].files[0]) {
                    form_data.append("landlord_agent_agreement", $("#landlord_agent_agreement")[0].files[0]);
                }
                
                // Bank Details
                form_data.append("bank_name", $("#bank_name").val());
                form_data.append("account_number", $("#account_number").val());
                form_data.append("sort_code", $("#sort_code").val());
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
            // Update end
        });

        // Edit
        $("#contentContainer").on('click', '#EditBtn', function() {
            $("#cardTitle").text('Update this data');
            codeid = $(this).attr('rid');
            info_url = url + '/' + codeid + '/edit';
            $.get(info_url, {}, function(d) {
                populateForm(d);
                pagetop();
            });
        });
        // Edit end 

        function populateForm(data) {
            // console.log(data);
            // Basic Information
            $("#name").val(data.name);
            $("#company_name").val(data.company_name);
            $("#email").val(data.email);
            $("#phone").val(data.phone);
            $("#postcode").val(data.postcode);
            $("#correspondence_address").val(data.correspondence_address);
            
            // Bank Details
            $("#bank_name").val(data.bank_name);
            $("#account_number").val(data.account_number);
            $("#sort_code").val(data.sort_code);
            
            // Compliance
            if (data.proof_of_id) {
                $("#proof_of_id_link").html('<a href="'+data.proof_of_id+'" target="_blank">View Proof of ID</a>');
            } else {
                $("#proof_of_id_link").html('');
            }
            if (data.authorisation_letter) {
                $("#authorisation_letter_link").html('<a href="'+data.authorisation_letter+'" target="_blank">View Authorisation Letter</a>');
            } else {
                $("#authorisation_letter_link").html('');
            }
            if (data.landlord_agent_agreement) {
                $("#landlord_agent_agreement_link").html('<a href="'+data.landlord_agent_agreement+'" target="_blank">View Landlord Agent Agreement</a>');
            } else {
                $("#landlord_agent_agreement_link").html('');
            }
            
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
            $("#proof_of_id_link").html('');
            $("#authorisation_letter_link").html('');
            $("#landlord_agent_agreement_link").html('');
        }
    });
</script>
@endsection