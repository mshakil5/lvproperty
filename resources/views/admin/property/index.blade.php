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
                    <form id="createThisForm">
                        @csrf
                        <input type="hidden" id="codeid" name="codeid">

                        <h5 class="mb-3">Basic Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Landlord <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="landlord_id" name="landlord_id">
                                    <option value="">Select Landlord</option>
                                    @foreach ($landlords as $landlord)
                                        <option value="{{ $landlord->id }}">{{ $landlord->name }} ({{ $landlord->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Property Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="property_name" name="property_name" placeholder="">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Property Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="property_type" name="property_type">
                                    <option value="House">House</option>
                                    <option value="Flat">Flat</option>
                                    <option value="Apartment">Apartment</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="Vacant">Vacant</option>
                                    <option value="Occupied">Occupied</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-none">
                                <label class="form-label">Rent Amount (£) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="rent_amount" name="rent_amount" placeholder="0.00">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Postcode</label>
                                <input type="text" class="form-control" id="postcode" name="postcode" placeholder="">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder=""></textarea>
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
                                <label class="form-label">Representative Authorisation</label>
                                <input type="text" class="form-control" id="representative_authorisation" name="representative_authorisation" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Representative Emergency Contact</label>
                                <input type="text" class="form-control" id="representative_emergency_contact" name="representative_emergency_contact" placeholder="">
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3">Service Technician Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Technician Name</label>
                                <input type="text" class="form-control" id="technician_name" name="technician_name" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Technician Phone</label>
                                <input type="text" class="form-control" id="technician_phone" name="technician_phone" placeholder="">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Technician Email</label>
                                <input type="email" class="form-control" id="technician_email" name="technician_email" placeholder="">
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
            <h4 class="card-title mb-0">Properties</h4>
        </div>
        <div class="card-body">
            <table id="propertyTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Property Name</th>
                        <th>Landlord</th>
                        <th>Type</th>
                        {{-- <th>Rent Amount</th> --}}
                        <th>Representative</th>
                        <th>Technician</th>
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

        $('#propertyTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: "{{ route('allproperty') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'property_name',
                    name: 'property_name'
                },
                {
                    data: 'landlord_name',
                    name: 'landlord_name',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'property_type',
                    name: 'property_type',
                    orderable: false,
                    searchable: false
                },
                // {
                //     data: 'rent_amount',
                //     name: 'rent_amount',
                //     orderable: false,
                //     searchable: false
                // },
                {
                    data: 'representative',
                    name: 'representative',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'technician',
                    name: 'technician',
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

        var url = "{{ URL::to('/admin/property') }}";
        var upurl = "{{ URL::to('/admin/property-update') }}";

        $("#addBtn").click(function() {
            //create
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                // Basic Information
                form_data.append("landlord_id", $("#landlord_id").val());
                form_data.append("property_name", $("#property_name").val());
                form_data.append("address", $("#address").val());
                form_data.append("city", $("#city").val());
                form_data.append("postcode", $("#postcode").val());
                form_data.append("property_type", $("#property_type").val());
                form_data.append("rent_amount", $("#rent_amount").val());
                form_data.append("status", $("#status").val());
                
                // Representative Details
                form_data.append("representative_name", $("#representative_name").val());
                form_data.append("representative_authorisation", $("#representative_authorisation").val());
                form_data.append("representative_emergency_contact", $("#representative_emergency_contact").val());
                
                // Technician Details
                form_data.append("technician_name", $("#technician_name").val());
                form_data.append("technician_phone", $("#technician_phone").val());
                form_data.append("technician_email", $("#technician_email").val());

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
            }
            //create  end

            //Update
            if ($(this).val() == 'Update') {
                var form_data = new FormData();
                // Basic Information
                form_data.append("landlord_id", $("#landlord_id").val());
                form_data.append("property_name", $("#property_name").val());
                form_data.append("address", $("#address").val());
                form_data.append("city", $("#city").val());
                form_data.append("postcode", $("#postcode").val());
                form_data.append("property_type", $("#property_type").val());
                form_data.append("rent_amount", $("#rent_amount").val());
                form_data.append("status", $("#status").val());
                
                // Representative Details
                form_data.append("representative_name", $("#representative_name").val());
                form_data.append("representative_authorisation", $("#representative_authorisation").val());
                form_data.append("representative_emergency_contact", $("#representative_emergency_contact").val());
                
                // Technician Details
                form_data.append("technician_name", $("#technician_name").val());
                form_data.append("technician_phone", $("#technician_phone").val());
                form_data.append("technician_email", $("#technician_email").val());
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
            // Basic Information
            $("#landlord_id").val(data.landlord_id).trigger('change');
            $("#property_name").val(data.property_name);
            $("#address").val(data.address);
            $("#city").val(data.city);
            $("#postcode").val(data.postcode);
            $("#property_type").val(data.property_type).trigger('change');
            $("#rent_amount").val(data.rent_amount);
            $("#status").val(data.status).trigger('change');
            
            // Representative Details
            $("#representative_name").val(data.representative_name);
            $("#representative_authorisation").val(data.representative_authorisation);
            $("#representative_emergency_contact").val(data.representative_emergency_contact);
            
            // Technician Details
            $("#technician_name").val(data.technician_name);
            $("#technician_phone").val(data.technician_phone);
            $("#technician_email").val(data.technician_email);
            
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
            $("#cardTitle").text('Add new Property');
            
            // Clear Select2
            $('.select2').val(null).trigger('change');
        }
    });
</script>
@endsection