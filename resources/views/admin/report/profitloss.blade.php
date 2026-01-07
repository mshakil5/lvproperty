@extends('admin.pages.master')
@section('title', 'Profit & Loss Report')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Profit & Loss Report</h4>
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

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light border-success">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Total Income</h6>
                            <h3 class="text-success fw-bold" id="totalIncome">£0.00</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card bg-light border-danger">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Total Expense</h6>
                            <h3 class="text-danger fw-bold" id="totalExpense">£0.00</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card" id="profitLossCard">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Net Profit / Loss</h6>
                            <h3 class="fw-bold" id="profitLoss">£0.00</h3>
                            <p id="profitLossStatus" class="mt-2"></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income Breakdown -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Income Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Transaction ID</th>
                                            <th>Description</th>
                                            <th>Property</th>
                                            <th>Tenant</th>
                                            <th class="text-end">Debit (£)</th>
                                            <th class="text-end">Credit (£)</th>
                                            <th class="text-end">Balance (£)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="incomeDetailsBody">
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary fw-bold">
                                            <td colspan="6">Total Income</td>
                                            <td id="incomeTotalDebit" class="text-end">£0.00</td>
                                            <td id="incomeTotalCredit" class="text-end">£0.00</td>
                                            <td id="incomeTotalBalance" class="text-end">£0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expense Breakdown -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">Expense Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Transaction ID</th>
                                            <th>Description</th>
                                            <th>Property</th>
                                            <th>Expense</th>
                                            <th class="text-end">Debit (£)</th>
                                            <th class="text-end">Credit (£)</th>
                                            <th class="text-end">Balance (£)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="expenseDetailsBody">
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary fw-bold">
                                            <td colspan="6">Total Expense</td>
                                            <td id="expenseTotalDebit" class="text-end">£0.00</td>
                                            <td id="expenseTotalCredit" class="text-end">£0.00</td>
                                            <td id="expenseTotalBalance" class="text-end">£0.00</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
    loadProfitLossReport();

    function loadProfitLossReport() {
        $.ajax({
            url: "{{ route('report.profitloss') }}",
            type: 'GET',
            data: {
                start_date: $('#startDate').val(),
                end_date: $('#endDate').val()
            },
            dataType: 'json',
            success: function(data) {
                var income = data.total_income;
                var expense = data.total_expense;
                var profitLoss = data.profit_loss;
                var isProfit = data.is_profit;

                $('#totalIncome').text('£' + income.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#totalExpense').text('£' + expense.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#profitLoss').text('£' + Math.abs(profitLoss).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                // Populate income details
                var incomeBody = $('#incomeDetailsBody');
                incomeBody.empty();
                var index = 1;
                if (data.income_details && data.income_details.length > 0) {
                    data.income_details.forEach(function(detail) {
                        incomeBody.append(`
                            <tr>
                                <td>${index++}</td>
                                <td>${detail.date}</td>
                                <td>${detail.tran_id}</td>
                                <td>${detail.description}</td>
                                <td>${detail.property}</td>
                                <td>${detail.tenant}</td>
                                <td class="text-end">${detail.debit}</td>
                                <td class="text-end">${detail.credit}</td>
                                <td class="text-end fw-bold text-success">${detail.balance}</td>
                            </tr>
                        `);
                    });
                } else {
                    incomeBody.append('<tr><td colspan="9" class="text-center text-muted">No income transactions</td></tr>');
                }

                // Calculate income totals
                var incomeDebitTotal = 0;
                var incomeCreditTotal = 0;
                data.income_details.forEach(function(detail) {
                    incomeDebitTotal += parseFloat(detail.debit.replace('£', '').replace(',', '')) || 0;
                    incomeCreditTotal += parseFloat(detail.credit.replace('£', '').replace(',', '')) || 0;
                });
                
                $('#incomeTotalDebit').text('£' + incomeDebitTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#incomeTotalCredit').text('£' + incomeCreditTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#incomeTotalBalance').text('£' + income.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                // Populate expense details
                var expenseBody = $('#expenseDetailsBody');
                expenseBody.empty();
                index = 1;
                if (data.expense_details && data.expense_details.length > 0) {
                    data.expense_details.forEach(function(detail) {
                        expenseBody.append(`
                            <tr>
                                <td>${index++}</td>
                                <td>${detail.date}</td>
                                <td>${detail.tran_id}</td>
                                <td>${detail.description}</td>
                                <td>${detail.property}</td>
                                <td>${detail.expense}</td>
                                <td class="text-end">${detail.debit}</td>
                                <td class="text-end">${detail.credit}</td>
                                <td class="text-end fw-bold text-danger">${detail.balance}</td>
                            </tr>
                        `);
                    });
                } else {
                    expenseBody.append('<tr><td colspan="9" class="text-center text-muted">No expense transactions</td></tr>');
                }

                // Calculate expense totals
                var expenseDebitTotal = 0;
                var expenseCreditTotal = 0;
                data.expense_details.forEach(function(detail) {
                    expenseDebitTotal += parseFloat(detail.debit.replace('£', '').replace(',', '')) || 0;
                    expenseCreditTotal += parseFloat(detail.credit.replace('£', '').replace(',', '')) || 0;
                });
                
                $('#expenseTotalDebit').text('£' + expenseDebitTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#expenseTotalCredit').text('£' + expenseCreditTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                $('#expenseTotalBalance').text('£' + Math.abs(expense).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));

                var profitLossCard = $('#profitLossCard');
                var statusText = '';

                if (isProfit) {
                    profitLossCard.removeClass('border-danger').addClass('border-success bg-light');
                    $('#profitLoss').removeClass('text-danger').addClass('text-success');
                    statusText = '<span class="badge bg-success">PROFIT</span>';
                } else {
                    profitLossCard.removeClass('border-success').addClass('border-danger bg-light');
                    $('#profitLoss').removeClass('text-success').addClass('text-danger');
                    statusText = '<span class="badge bg-danger">LOSS</span>';
                }

                $('#profitLossStatus').html(statusText);
            },
            error: function() {
                alert('Failed to load profit/loss report');
            }
        });
    }

    $('#filterBtn').click(function() {
        loadProfitLossReport();
    });

    $('#resetBtn').click(function() {
        var today = new Date();
        var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        $('#startDate').val(firstDay.toISOString().split('T')[0]);
        $('#endDate').val(today.toISOString().split('T')[0]);
        loadProfitLossReport();
    });

    $('#startDate, #endDate').keypress(function(e) {
        if (e.which == 13) {
            loadProfitLossReport();
        }
    });
});
</script>
@endsection