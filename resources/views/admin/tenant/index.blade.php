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
                    <form id="createThisForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="codeid" name="codeid">
                        <input type="hidden" id="current_tenancy_id" name="current_tenancy_id"> 

                        <h5 class="mb-3">Basic Information</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Property <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="property_id" name="property_id">
                                    <option value="">Select Property</option>
                                    @foreach ($properties as $property)
                                        <option value="{{ $property->id }}">{{ $property->property_reference }} ({{ $property->status }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" placeholder="">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address First Line <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_first_line" name="address_first_line" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Postcode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="postcode" name="postcode" placeholder="">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" placeholder="">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Douments</h5>
                        <div class="row g-3">
                            <!-- Tenancy Agreement -->
                            <div class="col-md-4">
                                <label for="tenancy_agreement_status" class="form-label">Tenancy Agreement</label>
                                <select class="form-select" id="tenancy_agreement_status" name="tenancy_agreement_status">
                                    <option value="no" selected>No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tenancy_agreement_date" class="form-label">Agreement Date</label>
                                <input type="date" class="form-control" id="tenancy_agreement_date" name="tenancy_agreement_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Agreement Document</label>
                                <input type="file" class="form-control" id="tenancy_agreement_document" name="tenancy_agreement_document">
                                <div id="tenancy_agreement_document_link" class="mt-1"></div>
                            </div>

                            <!-- Reference Check -->
                            <div class="col-md-4">
                                <label for="reference_check_status" class="form-label">Reference Check</label>
                                <select class="form-select" id="reference_check_status" name="reference_check_status">
                                    <option value="no" selected>No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="reference_check_date" class="form-label">Check Date</label>
                                <input type="date" class="form-control" id="reference_check_date" name="reference_check_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference Check Document</label>
                                <input type="file" class="form-control" id="reference_check_document" name="reference_check_document">
                                <div id="reference_check_document_link" class="mt-1"></div>
                            </div>

                            <!-- Immigration Status -->
                            <div class="col-md-4">
                                <label for="immigration_status" class="form-label">Immigration Status</label>
                                <select class="form-select" id="immigration_status" name="immigration_status">
                                    <option value="no" selected>No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="immigration_status_date" class="form-label">Check Date</label>
                                <input type="date" class="form-control" id="immigration_status_date" name="immigration_status_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Immigration Document</label>
                                <input type="file" class="form-control" id="immigration_status_document" name="immigration_status_document">
                                <div id="immigration_status_document_link" class="mt-1"></div>
                            </div>

                            <!-- Right to Rent Status -->
                            <div class="col-md-4">
                                <label for="right_to_rent_status" class="form-label">Right to Rent Status</label>
                                <select class="form-select" id="right_to_rent_status" name="right_to_rent_status">
                                    <option value="no" selected>No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="right_to_rent_date" class="form-label">Check Date</label>
                                <input type="date" class="form-control" id="right_to_rent_date" name="right_to_rent_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Right to Rent Document</label>
                                <input type="file" class="form-control" id="right_to_rent_document" name="right_to_rent_document">
                                <div id="right_to_rent_document_link" class="mt-1"></div>
                            </div>

                            <!-- Previous Landlord Reference -->
                            <div class="col-md-4">
                                <label for="previous_landlord_reference" class="form-label">Previous Landlord Reference</label>
                                <select class="form-select" id="previous_landlord_reference" name="previous_landlord_reference">
                                    <option value="no" selected>No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="previous_landlord_reference_date" class="form-label">Reference Date</label>
                                <input type="date" class="form-control" id="previous_landlord_reference_date" name="previous_landlord_reference_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Landlord Reference Document</label>
                                <input type="file" class="form-control" id="previous_landlord_reference_document" name="previous_landlord_reference_document">
                                <div id="previous_landlord_reference_document_link" class="mt-1"></div>
                            </div>

                            <!-- Personal Reference -->
                            <div class="col-md-4">
                                <label for="personal_reference" class="form-label">Personal Reference</label>
                                <select class="form-select" id="personal_reference" name="personal_reference">
                                    <option value="no" selected>No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="personal_reference_date" class="form-label">Reference Date</label>
                                <input type="date" class="form-control" id="personal_reference_date" name="personal_reference_date">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Personal Reference Document</label>
                                <input type="file" class="form-control" id="personal_reference_document" name="personal_reference_document">
                                <div id="personal_reference_document_link" class="mt-1"></div>
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
                        <h5 class="mb-3">Additional Tenants / Family Members</h5>
                        <div id="additionalTenantsContainer">
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3" id="tenancyHeading">Tenancy Agreement</h5>
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Amount (£) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="0.00">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Any special notes or terms..."></textarea>
                            </div>
                        </div>

                        <div id="terminateSection" class="mt-4" style="display: none;">
                            <hr class="my-4">
                            <h5 class="mb-3">Terminate Tenancy Agreement</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Termination Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="termination_type" name="termination_type">
                                        <option value="">Select Type</option>
                                        <option value="By Landlord">By Landlord</option>
                                        <option value="By Tenant">By Tenant</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Termination Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="termination_date" name="termination_date">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Termination Document</label>
                                    <input type="file" class="form-control" id="termination_document" name="termination_document">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Termination Reason</label>
                                    <textarea class="form-control" id="termination_reason" name="termination_reason" rows="3" placeholder="Reason for termination..."></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" id="terminateBtn" class="btn btn-danger">
                                        <i class="ri-close-line"></i> Terminate Tenancy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="renewalSection" class="mt-4" style="display: none;">
                            <hr class="my-4">
                            <h5 class="mb-3">Renew Tenancy Agreement</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">New Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="renew_start_date" name="renew_start_date">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">New End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="renew_end_date" name="renew_end_date">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">New Amount (£) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="renew_amount" name="renew_amount" placeholder="0.00">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Renewal Notes</label>
                                    <textarea class="form-control" id="renew_note" name="renew_note" rows="2" placeholder="Any notes for the renewal..."></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" id="renewBtn" class="btn btn-success">
                                        <i class="ri-refresh-line"></i> Renew Tenancy
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="previousTenancySection" style="display: none;">
                            <hr class="my-4">
                            <h5 class="mb-3">Previous Tenancy Agreements</h5>
                            <div id="previousTenanciesContainer" class="bg-light p-3 rounded">
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
            <h4 class="card-title mb-0">Tenants</h4>
        </div>
        <div class="card-body">
            <table id="tenantTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Tenant</th>
                        <th>Property & Address</th>
                        <th>Current Tenancy</th>
                        <th>Verification</th>
                        <th>Bank Details</th>
                        {{-- <th>Status</th> --}}
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
                    data: 'tenant',
                    name: 'name',
                    orderable: true,
                    searchable: true,
                },
                {
                    data: 'property_address',
                    name: 'property.property_reference',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'current_tenancy',
                    name: 'tenancies.start_date',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'verification',
                    name: 'immigration_status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'bank_details',
                    name: 'bank_name',
                    orderable: false,
                    searchable: true
                },
                // {
                //     data: 'status',
                //     name: 'status',
                //     orderable: true,
                //     searchable: false
                // },
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

        // Add initial row
        addAdditionalTenantRow();

        // Add tenant row function - MOVE THIS FUNCTION HERE
        function addAdditionalTenantRow(relation = '', name = '', email = '', phone = '') {
            let rowCount = $("#additionalTenantsContainer .additional-tenant-row").length;
            let isFirstRow = rowCount === 0;
            
            let buttonHtml = isFirstRow 
                ? '<button type="button" class="btn btn-success add-additional-tenant"><i class="ri-add-line"></i></button>'
                : '<button type="button" class="btn btn-danger remove-additional-tenant"><i class="ri-subtract-line"></i></button>';
            
            let row = `
                <div class="row g-3 additional-tenant-row mb-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control additional_tenant_relation" name="additional_tenants[${rowCount}][relation]" placeholder="Relation" value="${relation}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control additional_tenant_name" name="additional_tenants[${rowCount}][name]" placeholder="Name" value="${name}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control additional_tenant_phone" name="additional_tenants[${rowCount}][phone]" placeholder="Phone" value="${phone}">
                    </div>
                    <div class="col-md-3">
                        <input type="email" class="form-control additional_tenant_email" name="additional_tenants[${rowCount}][email]" placeholder="Email" value="${email}">
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        ${buttonHtml}
                    </div>
                </div>`;
            $("#additionalTenantsContainer").append(row);
        }

        // Dynamic add/remove functionality - MOVE THESE EVENT HANDLERS TOO
        $("#additionalTenantsContainer").on('click', '.add-additional-tenant', function() {
            addAdditionalTenantRow();
        });

        $("#additionalTenantsContainer").on('click', '.remove-additional-tenant', function() {
            $(this).closest('.additional-tenant-row').remove();
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = "{{ URL::to('/admin/tenant') }}";
        var upurl = "{{ URL::to('/admin/tenant-update') }}";

        $("#addBtn").click(function() {
            // Create FormData and log it
            var form_data = new FormData();
            
            // Basic Information
            form_data.append("property_id", $("#property_id").val());
            form_data.append("name", $("#name").val());
            form_data.append("email", $("#email").val());
            form_data.append("phone", $("#phone").val());
            form_data.append("address_first_line", $("#address_first_line").val());
            form_data.append("city", $("#city").val());
            form_data.append("postcode", $("#postcode").val());
            form_data.append("emergency_contact", $("#emergency_contact").val());

            // Tenancy Agreement
            form_data.append("tenancy_agreement_status", $("#tenancy_agreement_status").val());
            form_data.append("tenancy_agreement_date", $("#tenancy_agreement_date").val());
            let tenancyAgreementFile = $("#tenancy_agreement_document")[0].files[0];
            if (tenancyAgreementFile) {
                form_data.append("tenancy_agreement_document", tenancyAgreementFile);
            }

            // Reference Check
            form_data.append("reference_check_status", $("#reference_check_status").val());
            form_data.append("reference_check_date", $("#reference_check_date").val());
            let referenceCheckFile = $("#reference_check_document")[0].files[0];
            if (referenceCheckFile) {
                form_data.append("reference_check_document", referenceCheckFile);
            }

            // Immigration Status
            form_data.append("immigration_status", $("#immigration_status").val());
            form_data.append("immigration_status_date", $("#immigration_status_date").val());
            let immigrationStatusFile = $("#immigration_status_document")[0].files[0];
            if (immigrationStatusFile) {
                form_data.append("immigration_status_document", immigrationStatusFile);
            }

            // Right to Rent
            form_data.append("right_to_rent_status", $("#right_to_rent_status").val());
            form_data.append("right_to_rent_date", $("#right_to_rent_date").val());
            let rightToRentFile = $("#right_to_rent_document")[0].files[0];
            if (rightToRentFile) {
                form_data.append("right_to_rent_document", rightToRentFile);
            }

            // Previous Landlord Reference
            form_data.append("previous_landlord_reference", $("#previous_landlord_reference").val());
            form_data.append("previous_landlord_reference_date", $("#previous_landlord_reference_date").val());
            let previousLandlordFile = $("#previous_landlord_reference_document")[0].files[0];
            if (previousLandlordFile) {
                form_data.append("previous_landlord_reference_document", previousLandlordFile);
            }

            // Personal Reference
            form_data.append("personal_reference", $("#personal_reference").val());
            form_data.append("personal_reference_date", $("#personal_reference_date").val());
            let personalReferenceFile = $("#personal_reference_document")[0].files[0];
            if (personalReferenceFile) {
                form_data.append("personal_reference_document", personalReferenceFile);
            }

            // Bank Details
            form_data.append("bank_name", $("#bank_name").val());
            form_data.append("account_number", $("#account_number").val());
            form_data.append("sort_code", $("#sort_code").val());

            // Additional Tenants (JSON)
            let additionalTenantsData = [];
            $('.additional-tenant-row').each(function(index) {
                let relation = $(this).find('.additional_tenant_relation').val();
                let name = $(this).find('.additional_tenant_name').val();
                let phone = $(this).find('.additional_tenant_phone').val();
                let email = $(this).find('.additional_tenant_email').val();
                
                if (relation || name || phone || email) {
                    additionalTenantsData.push({
                        relation: relation,
                        name: name,
                        phone: phone,
                        email: email
                    });
                }
            });
            form_data.append("additional_tenants", JSON.stringify(additionalTenantsData));

            // Tenancy Agreement (Lease terms)
            form_data.append("start_date", $("#start_date").val());
            form_data.append("end_date", $("#end_date").val());
            form_data.append("amount", $("#amount").val());
            form_data.append("note", $("#note").val());

            // For update, add codeid
            if ($(this).val() == 'Update') {
                form_data.append("codeid", $("#codeid").val());
                var ajaxUrl = upurl; // Use update URL
            } else {
                var ajaxUrl = url; // Use create URL
            }

            // AJAX Call
            $.ajax({
                url: ajaxUrl,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    if (d.message) {
                        showSuccess(d.message);
                    }
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
            console.log(data);
            // Basic Information
            $("#property_id").val(data.property_id).trigger('change');
            $("#name").val(data.name);
            $("#email").val(data.email);
            $("#phone").val(data.phone);
            $("#address_first_line").val(data.address_first_line);
            $("#city").val(data.city);
            $("#postcode").val(data.postcode);
            $("#emergency_contact").val(data.emergency_contact);
            $("#current_tenancy_id").val(data.current_tenancy_id);

            // Tenancy Agreement
            $("#tenancy_agreement_status").val(data.tenancy_agreement_status || 'no');
            $("#tenancy_agreement_date").val(data.tenancy_agreement_date);

            // Reference Check
            $("#reference_check_status").val(data.reference_check_status || 'no');
            $("#reference_check_date").val(data.reference_check_date);

            // Immigration Status
            $("#immigration_status").val(data.immigration_status || 'no');
            $("#immigration_status_date").val(data.immigration_status_date);

            // Right to Rent
            $("#right_to_rent_status").val(data.right_to_rent_status || 'no');
            $("#right_to_rent_date").val(data.right_to_rent_date);

            // Previous Landlord Reference
            $("#previous_landlord_reference").val(data.previous_landlord_reference || 'no');
            $("#previous_landlord_reference_date").val(data.previous_landlord_reference_date);

            // Personal Reference
            $("#personal_reference").val(data.personal_reference || 'no');
            $("#personal_reference_date").val(data.personal_reference_date);

            // Bank Details
            $("#bank_name").val(data.bank_name);
            $("#account_number").val(data.account_number);
            $("#sort_code").val(data.sort_code);

            // Additional Tenants
            $("#additionalTenantsContainer").empty();
            if (data.additional_tenants) {
                let additionalTenants = JSON.parse(data.additional_tenants);
                additionalTenants.forEach((tenant, index) => {
                    addAdditionalTenantRow(tenant.relation, tenant.name, tenant.email, tenant.phone);
                });
            } else {
                addAdditionalTenantRow(); // Add one empty row
            }

            // TENANCY AGREEMENT SECTION - SHOW/HIDE BASED ON STATUS
            if (data.status === 'terminated') {
                // Hide entire tenancy agreement section including heading and inputs
                $("#tenancyHeading").hide();
                $("#tenancyHeading").next('.row.g-3').hide();
            } else {
                // Show tenancy agreement section
                $("#tenancyHeading").show();
                $("#tenancyHeading").next('.row.g-3').show();
                
                // Tenancy Agreement inputs
                $("#start_date").val(data.start_date);
                $("#end_date").val(data.end_date);
                $("#amount").val(data.amount);
                $("#note").val(data.note);
            }

            // Add status and tenancy type badges next to the heading
            if (data.status && data.tenancy_type) {
                let statusBadgeClass = 'bg-';
                switch(data.status) {
                    case 'active': statusBadgeClass += 'success'; break;
                    case 'completed': statusBadgeClass += 'secondary'; break;
                    case 'terminated': statusBadgeClass += 'danger'; break;
                    default: statusBadgeClass += 'success';
                }
                
                let typeBadgeClass = 'bg-';
                switch(data.tenancy_type) {
                    case 'In Contract': typeBadgeClass += 'primary'; break;
                    case 'Renewal Due': typeBadgeClass += 'warning'; break;
                    case 'Rolling Contract': typeBadgeClass += 'info'; break;
                    case 'Renewed': typeBadgeClass += 'success'; break;
                    default: typeBadgeClass += 'primary';
                }
                
                $("#tenancyHeading").html(
                    'Tenancy Agreement ' +
                    '<span class="badge ' + statusBadgeClass + ' ms-2">' + data.status + '</span>' +
                    '<span class="badge ' + typeBadgeClass + ' ms-1">' + data.tenancy_type + '</span>'
                );
            }

            // TERMINATION SECTION
            if (data.status === 'terminated') {
                // If already terminated, show termination details in view mode
                let terminateHtml = `
                    <div id="terminateSection" class="mt-4">
                        <hr class="my-4">
                        <h5 class="mb-3">Termination Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Terminated By</label>
                                <input type="text" class="form-control" value="${data.termination_type || 'N/A'}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Termination Date</label>
                                <input type="text" class="form-control" value="${data.termination_date || 'N/A'}" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Termination Document</label>
                                ${data.termination_document ? 
                                    `<a href="${data.termination_document}" target="_blank" class="form-control text-primary">View Document</a>` : 
                                    '<input type="text" class="form-control" value="No Document" readonly>'
                                }
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Termination Reason</label>
                                <textarea class="form-control" readonly>${data.termination_reason || 'N/A'}</textarea>
                            </div>
                        </div>
                    </div>
                `;
                // Insert after the tenancy agreement section
                if ($("#tenancyHeading").next('.row.g-3').length) {
                    $("#tenancyHeading").next('.row.g-3').after(terminateHtml);
                } else {
                    $("#tenancyHeading").after(terminateHtml);
                }
                
                // Hide renewal section if terminated
                $('#renewalSection').hide();
                
            } else if (data.status === 'active') {
                // If active, show terminate button in the heading
                $("#tenancyHeading").html(
                    'Tenancy Agreement ' +
                    '<span class="badge bg-success ms-2">' + data.status + '</span>' +
                    '<span class="badge bg-primary ms-1">' + data.tenancy_type + '</span>' +
                    '<button type="button" id="showTerminateBtn" class="btn btn-sm btn-outline-danger ms-2">Terminate</button>'
                );

                // Add terminate form (initially hidden)
                let terminateFormHtml = `
                    <div id="terminateFormSection" class="mt-4" style="display: none;">
                        <hr class="my-4">
                        <h5 class="mb-3">Terminate Tenancy Agreement</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Termination Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="termination_type" name="termination_type">
                                    <option value="">Select Type</option>
                                    <option value="By Landlord">By Landlord</option>
                                    <option value="By Tenant">By Tenant</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Termination Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="termination_date" name="termination_date" value="${new Date().toISOString().split('T')[0]}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Termination Document</label>
                                <input type="file" class="form-control" id="termination_document" name="termination_document">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Termination Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="termination_reason" name="termination_reason" rows="3" placeholder="Reason for termination..."></textarea>
                            </div>
                            <div class="col-md-12">
                                <button type="button" id="terminateBtn" class="btn btn-danger me-2">
                                    <i class="ri-close-line"></i> Confirm Termination
                                </button>
                                <button type="button" id="cancelTerminateBtn" class="btn btn-secondary">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                // Insert after the tenancy agreement inputs
                $("#tenancyHeading").next('.row.g-3').after(terminateFormHtml);
            }

            // RENEWAL SECTION
            if (data.tenancy_type && (data.tenancy_type === 'Renewal Due' || data.tenancy_type === 'Rolling Contract') && data.status === 'active') {
                $('#renewalSection').show();
                
                // Set default renewal dates
                if (data.end_date) {
                    let endDate = new Date(data.end_date);
                    let newStartDate = new Date(endDate);
                    newStartDate.setDate(newStartDate.getDate() + 1);
                    
                    $('#renew_start_date').val(newStartDate.toISOString().split('T')[0]);
                    
                    // Default end date: 1 year from new start date
                    let newEndDate = new Date(newStartDate);
                    newEndDate.setFullYear(newEndDate.getFullYear() + 1);
                    $('#renew_end_date').val(newEndDate.toISOString().split('T')[0]);
                }
                
                // Set default amount to current amount
                if (data.amount) {
                    $('#renew_amount').val(data.amount);
                }
            } else {
                $('#renewalSection').hide();
            }

            // File Links for ALL documents
            if (data.tenancy_agreement_document) {
                $('#tenancy_agreement_document_link').html(
                    '<a href="' + data.tenancy_agreement_document + '" target="_blank" class="text-primary">View Tenancy Agreement</a>'
                );
            } else {
                $('#tenancy_agreement_document_link').html('');
            }

            if (data.reference_check_document) {
                $('#reference_check_document_link').html(
                    '<a href="' + data.reference_check_document + '" target="_blank" class="text-primary">View Reference Check</a>'
                );
            } else {
                $('#reference_check_document_link').html('');
            }

            if (data.immigration_status_document) {
                $('#immigration_status_document_link').html(
                    '<a href="' + data.immigration_status_document + '" target="_blank" class="text-primary">View Immigration Document</a>'
                );
            } else {
                $('#immigration_status_document_link').html('');
            }

            if (data.right_to_rent_document) {
                $('#right_to_rent_document_link').html(
                    '<a href="' + data.right_to_rent_document + '" target="_blank" class="text-primary">View Right to Rent Document</a>'
                );
            } else {
                $('#right_to_rent_document_link').html('');
            }

            if (data.previous_landlord_reference_document) {
                $('#previous_landlord_reference_document_link').html(
                    '<a href="' + data.previous_landlord_reference_document + '" target="_blank" class="text-primary">View Landlord Reference</a>'
                );
            } else {
                $('#previous_landlord_reference_document_link').html('');
            }

            if (data.personal_reference_document) {
                $('#personal_reference_document_link').html(
                    '<a href="' + data.personal_reference_document + '" target="_blank" class="text-primary">View Personal Reference</a>'
                );
            } else {
                $('#personal_reference_document_link').html('');
            }

            loadPreviousTenancies(data.previous_tenancies);

            $("#previousTenancySection").show();
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function loadPreviousTenancies(previousTenancies) {
            let container = $('#previousTenanciesContainer');
            container.empty();

            if (!previousTenancies || previousTenancies.length === 0) {
                container.html('<p class="text-muted">No previous tenancy agreements found.</p>');
                return;
            }

            previousTenancies.forEach((tenancy, index) => {
                let tenancyHtml = `
                    <div class="previous-tenancy-item mb-3 p-3 border rounded bg-white">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Start Date:</strong><br>
                                ${tenancy.start_date ? new Date(tenancy.start_date).toLocaleDateString('en-GB') : 'N/A'}
                            </div>
                            <div class="col-md-3">
                                <strong>End Date:</strong><br>
                                ${tenancy.end_date ? new Date(tenancy.end_date).toLocaleDateString('en-GB') : 'N/A'}
                            </div>
                            <div class="col-md-2">
                                <strong>Amount:</strong><br>
                                £${tenancy.amount ? parseFloat(tenancy.amount).toFixed(2) : '0.00'}
                            </div>
                            <div class="col-md-2">
                                <strong>Status:</strong><br>
                                <span class="badge ${tenancy.status === 'completed' ? 'bg-success' : 'bg-warning'}">${tenancy.status}</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Type:</strong><br>
                                <span class="badge bg-secondary">${tenancy.tenancy_type}</span>
                            </div>
                        </div>
                        ${tenancy.note ? `<div class="row mt-2"><div class="col-12"><strong>Notes:</strong> ${tenancy.note}</div></div>` : ''}
                    </div>
                `;
                container.append(tenancyHtml);
            });
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#addBtn").html('Create');
            $("#cardTitle").text('Add new Tenant');
            
            // Clear Select2
            $('.select2').val(null).trigger('change');
            $("#previousTenancySection").hide();
            
            // Clear additional tenants container
            $("#additionalTenantsContainer").empty();
            addAdditionalTenantRow(); // Add one empty row
            
            // Show tenancy agreement section (in case it was hidden)
            $("#tenancyHeading").show();
            $("#tenancyHeading").next('.row.g-3').show();
            
            // Clear tenancy fields
            $("#start_date").val('');
            $("#end_date").val('');
            $("#amount").val('');
            $("#note").val('');
            
            // Reset tenancy heading
            $("#tenancyHeading").html('Tenancy Agreement');
            
            // Clear file links
            $('#tenancy_agreement_document_link').html('');
            $('#reference_check_document_link').html('');
            $('#previous_landlord_reference_document_link').html('');
            $('#personal_reference_document_link').html('');

            // Clear previous tenancies and renewal section
            $('#previousTenanciesContainer').empty();
            $('#renewalSection').hide();
            $('#renew_start_date').val('');
            $('#renew_end_date').val('');
            $('#renew_amount').val('');
            $('#renew_note').val('');

            // Clear termination sections - use more specific selectors
            $('div[id="terminateSection"]').remove();
            $('div[id="terminateFormSection"]').remove();
            
            // Also clear any terminate buttons that might have been added
            $('#showTerminateBtn').remove();
        }

        $("#renewBtn").click(function () {

            showConfirm("Are you sure?", "Do you want to renew this tenancy? please check all the documents?")
                .then((result) => {
                    if (!result.isConfirmed) return;

                    let form_data = new FormData();
                    form_data.append("tenant_id", $("#codeid").val());
                    form_data.append("start_date", $("#renew_start_date").val());
                    form_data.append("end_date", $("#renew_end_date").val());
                    form_data.append("amount", $("#renew_amount").val());
                    form_data.append("note", $("#renew_note").val());

                    $.ajax({
                        url: "{{ URL::to('/admin/tenant-renew') }}",
                        method: "POST",
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (d) {
                            showSuccess(d.message);
                            $("#renewalSection").hide();
                            $("#addThisFormContainer").slideUp(300);
                            reloadTable('#tenantTable');

                            // refresh edit section
                            $("#contentContainer").on("click", "#EditBtn", function () {
                                let codeid = $(this).attr("rid");
                                let info_url = url + "/" + codeid + "/edit";
                                $.get(info_url, {}, function (d) {
                                    populateForm(d);
                                });
                            });
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                                showError(firstError);
                            } else {
                                showError(xhr.responseJSON?.message ?? "Something went wrong!");
                            }
                        }
                    });
                });
        });

        // Show terminate form when terminate button is clicked
        $(document).on('click', '#showTerminateBtn', function() {
            $('#terminateFormSection').show();
            $(this).hide(); // Hide the terminate button
        });

        // Cancel termination
        $(document).on('click', '#cancelTerminateBtn', function() {
            $('#terminateFormSection').hide();
            $('#showTerminateBtn').show(); // Show the terminate button again
            // Clear termination form
            $('#termination_type').val('');
            $('#termination_reason').val('');
            $('#termination_document').val('');
        });

        // Handle termination
        $(document).on('click', '#terminateBtn', function() {
            showConfirm("Are you sure?", "Do you want to terminate this tenancy? This action cannot be undone.")
                .then((result) => {
                    if (!result.isConfirmed) return;

                    let form_data = new FormData();
                    form_data.append("tenancy_id", $("#current_tenancy_id").val());
                    form_data.append("tenant_id", $("#codeid").val());
                    form_data.append("termination_type", $("#termination_type").val());
                    form_data.append("termination_date", $("#termination_date").val());
                    form_data.append("termination_reason", $("#termination_reason").val());
                    
                    let terminationFile = $("#termination_document")[0].files[0];
                    if (terminationFile) {
                        form_data.append("termination_document", terminationFile);
                    }

                    $.ajax({
                        url: "{{ URL::to('/admin/tenant-terminate') }}",
                        method: "POST",
                        contentType: false,
                        processData: false,
                        data: form_data,
                        success: function (d) {
                            showSuccess(d.message);
                            
                            // Close the form completely instead of just sliding up
                            $("#addThisFormContainer").hide(300);
                            $("#newBtn").show(100);
                            
                            // Clear the form completely to prevent duplication
                            clearform();
                            
                            reloadTable('#tenantTable');
                        },
                        error: function (xhr) {
                            if (xhr.status === 422) {
                                let firstError = Object.values(xhr.responseJSON.errors)[0][0];
                                showError(firstError);
                            } else {
                                showError(xhr.responseJSON?.message ?? "Something went wrong!");
                            }
                        }
                    });
                });
        });

    });
</script>
@endsection