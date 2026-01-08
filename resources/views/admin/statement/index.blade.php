@extends('admin.pages.master')
@section('title', 'Yearly Statement')
@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-3">Generate Yearly Statement</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Statement Criteria</h4>
                </div>
                <div class="card-body">
                    <form id="statementForm" method="POST" action="{{ route('statement.generate') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Select Landlord <span class="text-danger">*</span></label>
                                <select class="form-control form-select" id="landlord_id" name="landlord_id" required>
                                    <option value="">-- Choose Landlord --</option>
                                    @foreach($landlords as $landlord)
                                        <option value="{{ $landlord->id }}">{{ $landlord->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Select Property <span class="text-danger">*</span></label>
                                <select class="form-control form-select" id="property_id" name="property_id" required>
                                    <option value="">-- Choose Property --</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" form="statementForm" class="btn btn-primary">
                        <i class="ri-file-chart-line me-2"></i> Generate Statement
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Load properties when landlord is selected
        $('#landlord_id').on('change', function() {
            var landlord_id = $(this).val();
            
            if (landlord_id) {
                $.ajax({
                    url: "{{ route('statement.index') }}",
                    method: "GET",
                    data: {
                        landlord_id: landlord_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#property_id').html('<option value="">-- Choose Property --</option>');
                        $.each(data, function(key, property) {
                            $('#property_id').append(
                                '<option value="' + property.id + '">' + property.property_reference + ' - ' + property.address_first_line + '</option>'
                            );
                        });
                    },
                    error: function() {
                        showError('Failed to load properties');
                    }
                });
            } else {
                $('#property_id').html('<option value="">-- Choose Property --</option>');
            }
        });

        // Set default dates (current year)
        var today = new Date();
        var currentYear = today.getFullYear();
        
        var startDate = new Date(currentYear, 0, 1);
        var endDate = new Date(currentYear, 11, 31);
        
        $('#start_date').val(startDate.toISOString().split('T')[0]);
        $('#end_date').val(endDate.toISOString().split('T')[0]);
    });
</script>
@endsection