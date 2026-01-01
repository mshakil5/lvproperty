@extends('admin.pages.master')
@section('title', 'Income')
@section('content')

<div class="container-fluid" id="newBtnSection">
    <div class="row mb-3">
        <div class="col-auto">
            <a href="{{ route('income.create') }}" class="btn btn-primary">
                Receive Payment
            </a>
        </div>
    </div>
</div>

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

<div class="container-fluid" id="contentContainer">
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
                        <th>Transaction ID</th>
                        <th>Property</th>
                        <th>Tenant</th>
                        <th>Amount</th>
                        <th>Payment Type</th>
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

        $('#incomeTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: "{{ route('income.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'date', name: 'date', orderable: false },
                { data: 'tran_id', name: 'tran_id', orderable: false },
                { data: 'property_reference', name: 'property_reference', orderable: false },
                { data: 'tenant_name', name: 'tenant_name', orderable: false },
                { data: 'amount', name: 'amount', orderable: false },
                { data: 'payment_type', name: 'payment_type', orderable: false },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.view-income-details', function() {
            var incomeId = $(this).data('income-id');
            var url = "{{ route('income.details', ':id') }}".replace(':id', incomeId);
            
            $.get(url, function(response) {
                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Transaction ID:</strong> ${response.income.tran_id}</p>
                            <p><strong>Date:</strong> ${response.income.date}</p>
                            <p><strong>Property:</strong> ${response.income.property_reference}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Amount:</strong> ${response.income.amount}</p>
                            <p><strong>Payment Type:</strong> ${response.income.payment_type}</p>
                            <p><strong>Recorded:</strong> ${response.income.created_at}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Description:</strong> ${response.income.description || 'N/A'}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Paid Transactions (${response.paid_transactions.length}):</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Date</th>
                                    <th>Property</th>
                                    <th>Tenant</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                response.paid_transactions.forEach(transaction => {
                    content += `
                        <tr>
                            <td>${transaction.tran_id}</td>
                            <td>${transaction.date}</td>
                            <td>${transaction.property_reference}</td>
                            <td>${transaction.tenant_name}</td>
                            <td>${transaction.amount}</td>
                            <td>${transaction.description || 'N/A'}</td>
                        </tr>
                    `;
                });
                
                content += `
                            </tbody>
                        </table>
                    </div>
                `;
                
                $('#incomeDetailsContent').html(content);
                $('#incomeDetailsModal').modal('show');
            });
        });
    });
</script>
@endsection