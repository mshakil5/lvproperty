@extends('admin.pages.master')
@section('title', 'Income')
@section('content')

<div class="container-fluid mb-3">
    <div class="row">
        <div class="col-auto">
            <a href="{{ route('income.create') }}" class="btn btn-primary">
                Receive Payment
            </a>
        </div>
    </div>
</div>

<!-- Income Details Modal -->
<div class="modal fade" id="incomeDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Income Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="incomeDetailsContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Income Table -->
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">Income Records</h4>
        </div>
        <div class="card-body">
            <table id="incomeTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Date</th>
                        <th>Property</th>
                        <th>Tenant</th>
                        <th>Amount Received</th>
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

    // DataTable
    $('#incomeTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: "{{ route('income.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'date', name: 'date' },
            { data: 'property', name: 'property' },
            { data: 'tenant', name: 'tenant' },
            { data: 'amount', name: 'amount' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    // View income details
    $(document).on('click', '.view-income-details', function() {
        var incomeId = $(this).data('income-id');
        var url = "{{ route('income.details', ':id') }}".replace(':id', incomeId);

        $.get(url, function(res) {

            const list = res.received_transactions || [];

            var content = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Transaction ID:</strong> ${res.income.tran_id}</p>
                        <p><strong>Date:</strong> ${res.income.date}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Amount Received:</strong>
                            <strong class="text-success">${res.income.received_amount}</strong>
                        </p>
                        <p><strong>Status:</strong> ${res.income.status}</p>
                    </div>
                </div>
                <hr>
                <h6>Received Payments (${list.length}):</h6>
                <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>TXN</th>
                            <th>Date</th>
                            <th>Paid Amount</th>
                            <th>Payment Type</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            list.forEach(r => {
                content += `
                    <tr>
                        <td>${r.tran_id}</td>
                        <td>${r.date}</td>
                        <td><strong>${r.amount}</strong></td>
                        <td>${r.payment_type}</td>
                    </tr>
                `;
            });

            content += `</tbody></table></div>`;

            $('#incomeDetailsContent').html(content);
            $('#incomeDetailsModal').modal('show');
        });
    });
});
</script>
@endsection