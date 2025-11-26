@extends('admin.pages.master')
@section('title', 'Property')
@section('content')

<div class="container-fluid" id="newBtnSection">
    <div class="row mb-3">
        <div class="col-auto">
            <button type="button" class="btn btn-primary" id="newBtn">
                Add New Property
            </button>
        </div>
    </div>
</div>

<div class="container-fluid" id="addThisFormContainer">
    <div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1" id="cardTitle">Add New Property</h4>
                </div>
                <div class="card-body">
                    <form id="createThisForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="codeid" name="codeid">

                        <h5 class="mb-3">Basic Information</h5>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Landlord <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="landlord_id" name="landlord_id">
                                    <option value="">Select Landlord</option>
                                    @foreach ($landlords as $landlord)
                                        <option value="{{ $landlord->id }}">{{ $landlord->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Property Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="property_type" name="property_type">
                                    <option value="House">House</option>
                                    <option value="Flat">Flat</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="Vacant">Vacant</option>
                                    <option value="Occupied">Occupied</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status_until_date" class="form-label">Until Date</label>
                                <input type="date" class="form-control" id="status_until_date" name="status_until_date">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address First Line <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address_first_line" name="address_first_line" placeholder="">
                                <small class="form-text text-muted">
                                    Enter the house number followed by the street name (e.g., "8 Oxford Road"). This will be used to generate the property reference.
                                </small>
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
                        <h5 class="mb-3">Representative Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Representative Name</label>
                                <input type="text" class="form-control" id="representative_name" name="representative_name" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Representative Contact Number</label>
                                <input type="text" class="form-control" id="representative_contact" name="representative_contact" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Representative Authorisation</label>
                                <select class="form-select" id="representative_authorisation" name="representative_authorisation">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    <option value="NA">NA</option>
                                </select>

                                <div id="representative_authorisation_file_container" class="mt-2" style="display:none;">
                                    <input type="file" class="form-control" id="representative_authorisation_file" name="representative_authorisation_file" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted">Upload Authorisation Letter (if Yes) (Max. 5MB)</small>
                                    <div id="representative_authorisation_file_link" class="mt-1"></div>
                                </div>
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

                        <hr class="my-4">
                        <h5 class="mb-3">Service Technician Details</h5>
                        <div id="technicianContainer">
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
            <h4 class="card-title mb-0">Properties</h4>
        </div>
        <div class="card-body">
            <table id="propertyTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Property</th>
                        <th>Landlord</th>
                        <th>Type & Service</th>
                        <th>Representative</th>
                        <th>Technicians</th>
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

        $('#representative_authorisation').on('change', function() {
            if ($(this).val() === 'Yes') {
                $('#representative_authorisation_file_container').show();
            } else {
                $('#representative_authorisation_file_container').hide();
                $('#representative_authorisation_file').val('');
            }
        });

        $('#technicianContainer').on('click', '.add-technician', function() {
            // Clone current row
            let newRow = $(this).closest('.technician-row').clone();
            // Clear input values
            newRow.find('input').val('');
            // Change buttons: + becomes -
            newRow.find('.add-technician')
                .removeClass('btn-success add-technician')
                .addClass('btn-danger remove-technician')
                .html('<i class="ri-subtract-line"></i>');
            // Append new row
            $('#technicianContainer').append(newRow);
        });

        $('#technicianContainer').on('click', '.remove-technician', function() {
            $(this).closest('.technician-row').remove();
        });

        $('#propertyTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
                ajax: {
                url: "{{ route('allproperty') }}",
                type: 'GET',
                error: function(xhr, status, error) {
                    console.error("Response Text:", xhr.responseText);
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    width: '5%'
                },
                {
                    data: 'property',
                    name: 'property_reference',
                    orderable: true,
                    searchable: true,
                    width: '20%'
                },
                {
                    data: 'landlord',
                    name: 'landlord.name',
                    orderable: true,
                    searchable: true,
                    width: '15%'
                },
                {
                    data: 'type_service',
                    name: 'property_type',
                    orderable: true,
                    searchable: true,
                    width: '15%'
                },
                {
                    data: 'representative',
                    name: 'representative_name',
                    orderable: false,
                    searchable: true,
                    width: '15%'
                },
                {
                    data: 'technicians',
                    name: 'technicians',
                    orderable: false,
                    searchable: false,
                    width: '10%'
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: true,
                    width: '10%'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '10%'
                }
            ]
        });
    });
</script>

<script>
    $(document).ready(function() {
        addTechnicianRow();
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

        var url = "{{ URL::to('/admin/property') }}";
        var upurl = "{{ URL::to('/admin/property-update') }}";

        $("#addBtn").click(function() {
            // Create FormData and log it
            var form_data = new FormData();
            
            // Basic Information
            form_data.append("landlord_id", $("#landlord_id").val());
            form_data.append("property_type", $("#property_type").val());
            form_data.append("status", $("#status").val());
            form_data.append("status_until_date", $("#status_until_date").val());
            form_data.append("address_first_line", $("#address_first_line").val());
            form_data.append("city", $("#city").val());
            form_data.append("postcode", $("#postcode").val());
            form_data.append("emergency_contact", $("#emergency_contact").val());

            // Representative Details
            form_data.append("representative_name", $("#representative_name").val());
            form_data.append("representative_contact", $("#representative_contact").val());
            form_data.append("representative_authorisation", $("#representative_authorisation").val());

            // Service Agreement
            form_data.append("service_type", $("#service_type").val());
            form_data.append("management_fee", $("#management_fee").val());
            form_data.append("agreement_date", $("#agreement_date").val());
            form_data.append("agreement_duration", $("#agreement_duration").val());

            // Add representative file if selected
            let repFile = $("#representative_authorisation_file")[0].files[0];
            if (repFile) {
                form_data.append("representative_authorisation_file", repFile);
                console.log("File attached:", repFile.name);
            }

            // Service Technician Details
            let technicianData = [];
            $('.technician-row').each(function(index) {
                let type = $(this).find('.technician_type').val();
                let name = $(this).find('.technician_name').val();
                let phone = $(this).find('.technician_phone').val();
                let email = $(this).find('.technician_email').val();
                
                if (type || name || phone || email) {
                    technicianData.push({
                        technician_type: type,
                        technician_name: name,
                        technician_phone: phone,
                        technician_email: email
                    });
                }
            });
            
            form_data.append("technicians", JSON.stringify(technicianData));
            console.log("Technicians data:", technicianData);

            // For update, add codeid
            if ($(this).val() == 'Update') {
                form_data.append("codeid", $("#codeid").val());
                console.log("Update ID:", $("#codeid").val());
                var ajaxUrl = upurl; // Use update URL
            } else {
                var ajaxUrl = url; // Use create URL
            }

            // === ADD THE MISSING AJAX CALL HERE ===
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
                    reloadTable('#propertyTable');
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
            $("#landlord_id").val(data.landlord_id).trigger('change');
            $("#property_type").val(data.property_type).trigger('change');
            $("#status").val(data.status).trigger('change');
            $("#status_until_date").val(data.status_until_date);
            $("#address_first_line").val(data.address_first_line);
            $("#city").val(data.city);
            $("#postcode").val(data.postcode);
            $("#emergency_contact").val(data.emergency_contact);

            // Representative Details - FIXED THIS LINE
            $("#representative_name").val(data.representative_name);
            $("#representative_authorisation").val(data.representative_authorisation);
            $("#representative_contact").val(data.representative_contact); // Changed from representative_emergency_contact

            // Service Agreement
            $("#service_type").val(data.service_type);
            $("#management_fee").val(data.management_fee);
            $("#agreement_date").val(data.agreement_date);
            $("#agreement_duration").val(data.agreement_duration);

            // Technician Details
            $("#technicianContainer").empty();
            if (data.technicians) {
                let technicians = JSON.parse(data.technicians);
                technicians.forEach((tech, index) => {
                    addTechnicianRow(tech.technician_type, tech.technician_name, tech.technician_phone, tech.technician_email);
                });
            } else {
                addTechnicianRow(); // Add one empty row
            }

            // Show/hide file upload based on authorisation
            if (data.representative_authorisation === 'Yes') {
                $('#representative_authorisation_file_container').show();
                // Show existing file link if available
                if (data.representative_authorisation_file) {
                    $('#representative_authorisation_file_link').html(
                        '<a href="' + data.representative_authorisation_file + '" target="_blank" class="text-primary">View Authorisation Letter</a>'
                    );
                } else {
                    $('#representative_authorisation_file_link').html('');
                }
            } else {
                $('#representative_authorisation_file_container').hide();
                $('#representative_authorisation_file_link').html('');
            }

            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        // Helper function to add technician rows
        function addTechnicianRow(type = '', name = '', phone = '', email = '') {
            // First row gets +, others get -
            let isFirstRow = $("#technicianContainer .technician-row").length === 0;
            let buttonHtml = isFirstRow 
                ? '<button type="button" class="btn btn-success add-technician"><i class="ri-add-line"></i></button>'
                : '<button type="button" class="btn btn-danger remove-technician"><i class="ri-subtract-line"></i></button>';
            
            let row = `
                <div class="row g-3 technician-row mb-2">
                    <div class="col-md-3">
                        <input type="text" class="form-control technician_type" name="technician_type[]" placeholder="Technician Type" value="${type}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control technician_name" name="technician_name[]" placeholder="Technician Name" value="${name}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control technician_phone" name="technician_phone[]" placeholder="Phone" value="${phone}">
                    </div>
                    <div class="col-md-3">
                        <input type="email" class="form-control technician_email" name="technician_email[]" placeholder="Email" value="${email}">
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        ${buttonHtml}
                    </div>
                </div>`;
            $("#technicianContainer").append(row);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#addBtn").val('Create');
            $("#addBtn").html('Create');
            $("#cardTitle").text('Add new Property');
            
            // Clear Select2
            $('.select2').val(null).trigger('change');
            
            // Clear technician container and add one empty row
            $("#technicianContainer").empty();
            addTechnicianRow(); // Automatically gets + button since container is empty
            
            // Hide file upload container
            $('#representative_authorisation_file_container').hide();
            $('#status_until_date_container').hide();
        }
    });
</script>
@endsection