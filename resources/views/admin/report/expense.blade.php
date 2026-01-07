@extends('admin.pages.master')
@section('title', 'Expense Report')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">Expense Report</h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" id="startDate" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" id="endDate" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" id="filterBtn">
                        <i class="ri-filter-line"></i> Filter
                    </button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-secondary w-100" id="resetBtn">
                        <i class="ri-refresh-line"></i> Reset
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="expenseReportTable" class="table table-striped table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Description</th>
                            <th>Property</th>
                            <th>Expense</th>
                            <th>Debit (£)</th>
                            <th>Credit (£)</th>
                            <th>Balance (£)</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr class="table-secondary fw-bold">
                            <td colspan="6">TOTAL</td>
                            <td id="totalDebit">£0.00</td>
                            <td id="totalCredit">£0.00</td>
                            <td id="totalBalance">£0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
    var table = $('#expenseReportTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        ajax: {
            url: "{{ route('report.expense') }}",
            data: function(d) {
                d.start_date = $('#startDate').val();
                d.end_date = $('#endDate').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'date', name: 'date' },
            { data: 'tran_id', name: 'tran_id' },
            { data: 'description', name: 'description' },
            { data: 'property', name: 'property' },
            { data: 'expense', name: 'expense' },
            { data: 'debit', name: 'debit', className: 'text-end' },
            { data: 'credit', name: 'credit', className: 'text-end' },
            { data: 'balance', name: 'balance', className: 'text-end fw-bold text-danger' }
        ],
        drawCallback: function() {
            calculateTotals();
        }
    });

    $('#filterBtn').click(function() {
        table.draw();
    });

    $('#resetBtn').click(function() {
        var today = new Date();
        var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        $('#startDate').val(firstDay.toISOString().split('T')[0]);
        $('#endDate').val(today.toISOString().split('T')[0]);
        table.draw();
    });

    $('#startDate, #endDate').keypress(function(e) {
        if (e.which == 13) {
            table.draw();
        }
    });

    function calculateTotals() {
        var debitTotal = 0;
        var creditTotal = 0;
        var lastBalance = 0;

        table.column(6).data().each(function(value) {
            var num = parseFloat(value.replace('£', '').replace(',', '')) || 0;
            debitTotal += num;
        });

        table.column(7).data().each(function(value) {
            var num = parseFloat(value.replace('£', '').replace(',', '')) || 0;
            creditTotal += num;
        });

        table.column(8).data().each(function(value) {
            lastBalance = parseFloat(value.replace('£', '').replace(',', '')) || 0;
        });

        $('#totalDebit').text('£' + debitTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        $('#totalCredit').text('£' + creditTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        $('#totalBalance').text('£' + lastBalance.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    }
});
</script>
@endsection