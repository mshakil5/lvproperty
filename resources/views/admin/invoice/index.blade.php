@extends('admin.pages.master')
@section('title', 'Monthly Invoice')
@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="mb-3">Generate Monthly Invoice</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Invoice Criteria</h4>
                </div>
                <div class="card-body">
                    <form id="invoiceForm" method="POST" action="{{ route('invoice.generate') }}">
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

                            <div class="col-12">
                                <label class="form-label">Select Month <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="month" name="month" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" form="invoiceForm" class="btn btn-primary">
                        <i class="ri-file-text-line me-2"></i> Generate Invoice
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
                    url: "{{ route('invoice.index') }}",
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

        // Set default month to current month
        var today = new Date();
        var year = today.getFullYear();
        var month = String(today.getMonth() + 1).padStart(2, '0');
        $('#month').val(year + '-' + month);
    });
</script>
@endsection