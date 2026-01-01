@extends('admin.pages.master')
@section('title', 'Receive Payment')
@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-auto">
            <a href="{{ route('income.index') }}" class="btn btn-primary">
                <i class="ri-arrow-left-line"></i> Back to Income
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Receive Payment</h4>
            </div>
            <div class="card-body">
                <form id="createThisForm">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Select Property <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="property_id" name="property_id" required>
                                <option value="">Select Property</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->property_reference }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="payment_type" name="payment_type" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Total Amount <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="total_amount" name="total_amount" readonly style="background-color: #f8f9fa; font-weight: bold; font-size: 1.1rem;">
                            <input type="hidden" id="total_amount_raw" name="total_amount">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Payment description (optional)"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Select Due Transactions</h5>
                                    <small class="text-muted">Select one or more unpaid transactions to receive payment</small>
                                </div>
                                <div class="card-body">
                                    <div id="dueTransactionsContainer">
                                        <div class="text-center text-muted py-4">
                                            <i class="ri-search-line display-4"></i>
                                            <p class="mt-2">Select a property to view due transactions</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer text-end">
                <button type="submit" id="addBtn" class="btn btn-primary">
                    Receive Payment
                </button>
                <a href="{{ route('income.index') }}" class="btn btn-light">
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    let selectedTransactions = new Set();
    let selectedAmounts = new Map();

    $(document).ready(function() {
        $('#property_id').change(function() {
            var propertyId = $(this).val();
            if (propertyId) {
                loadDueTransactions(propertyId);
            } else {
                $('#dueTransactionsContainer').html(`
                    <div class="text-center text-muted py-4">
                        <i class="ri-search-line display-4"></i>
                        <p class="mt-2">Select a property to view due transactions</p>
                    </div>
                `);
                updateTotalAmount();
            }
        });

        $("#addBtn").click(function() {
            if (selectedTransactions.size === 0) {
                showError('Please select at least one due transaction');
                return;
            }

            var form_data = new FormData();
            form_data.append("_token", "{{ csrf_token() }}");  
            form_data.append("property_id", $("#property_id").val());
            form_data.append("date", $("#date").val());
            form_data.append("payment_type", $("#payment_type").val());
            form_data.append("total_amount", $("#total_amount_raw").val());
            form_data.append("description", $("#description").val());
            
            Array.from(selectedTransactions).forEach(function(transactionId) {
                form_data.append("selected_transactions[]", transactionId);
            });

            $.ajax({
                url: "{{ route('income.store') }}",
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function(d) {
                    showSuccess(d.message);
                    setTimeout(() => {
                        window.location.href = "{{ route('income.index') }}";
                    }, 1500);
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

        function loadDueTransactions(propertyId) {
            $.get("{{ route('income.due-transactions') }}", { property_id: propertyId }, function(response) {
                if (response.length > 0) {
                    let html = '<div class="row">';
                    response.forEach(transaction => {
                        html += `
                            <div class="col-md-6 mb-3">
                                <div class="card due-transaction-card" data-transaction-id="${transaction.id}" data-amount="${transaction.raw_amount}">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input transaction-checkbox" type="checkbox" value="${transaction.id}" id="transaction_${transaction.id}">
                                            <label class="form-check-label w-100" for="transaction_${transaction.id}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>${transaction.tran_id}</strong><br>
                                                        <small class="text-muted">${transaction.date}</small><br>
                                                        <small>${transaction.property_reference} - ${transaction.tenant_name}</small>
                                                    </div>
                                                    <div class="text-end">
                                                        <strong class="text-primary">${transaction.amount}</strong><br>
                                                        <small class="text-muted">${transaction.description}</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    $('#dueTransactionsContainer').html(html);
                } else {
                    $('#dueTransactionsContainer').html(`
                        <div class="text-center text-muted py-4">
                            <i class="ri-checkbox-multiple-blank-line display-4"></i>
                            <p class="mt-2">No due transactions found for this property</p>
                        </div>
                    `);
                }
                updateTotalAmount();
            });
        }

        $(document).on('change', '.transaction-checkbox', function() {
            const transactionId = $(this).val();
            const amount = parseFloat($(this).closest('.due-transaction-card').data('amount'));
            const card = $(this).closest('.due-transaction-card');

            if ($(this).is(':checked')) {
                selectedTransactions.add(transactionId);
                selectedAmounts.set(transactionId, amount);
                card.addClass('border-primary bg-light');
            } else {
                selectedTransactions.delete(transactionId);
                selectedAmounts.delete(transactionId);
                card.removeClass('border-primary bg-light');
            }
            
            updateTotalAmount();
        });

        function updateTotalAmount() {
            const total = Array.from(selectedAmounts.values()).reduce((sum, amount) => sum + amount, 0);
            $('#total_amount').val('£' + total.toFixed(2));
            $('#total_amount_raw').val(total);
        }
    });
</script>

<style>
.due-transaction-card {
    transition: all 0.3s ease;
    cursor: pointer;
}
.due-transaction-card:hover {
    border-color: #0d6efd;
}
.due-transaction-card.border-primary {
    border-width: 2px;
}
</style>
@endsection