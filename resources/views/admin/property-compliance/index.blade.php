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
                                        <option value="{{ $property->id }}">{{ $property->property_reference }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Compliance Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="compliance_type_id" name="compliance_type_id">
                                    <option value="">Select Compliance Type</option>
                                    @foreach ($complianceTypes as $type)
                                        <option value="{{ $type->id }}">
                                            {{ $type->name }}
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

                            <div class="col-md-12">
                                <label class="form-label">Certificate Document (Max Size: 5MB)</label>
                                <input type="file" class="form-control" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Upload PDF, JPG, or PNG files (Max: 5MB)</small>
                                <div id="document_link" class="mt-1"></div>
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
                    data: 'property_reference',
                    name: 'property_reference',
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
            
            // Show document preview link if exists
            if (data.document_path) {
                $("#document_link").html('<a href="'+data.document_path+'" target="_blank" class="btn btn-sm btn-outline-primary">View Current Document</a>');
            } else {
                $("#document_link").html('');
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
            $("#cardTitle").text('Add new Compliance');
            $("#document_link").html('');
            
            // Clear Select2
            $('.select2').val(null).trigger('change');
        }
    });
</script>
@endsection