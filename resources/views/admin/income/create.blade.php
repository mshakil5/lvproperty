@extends('admin.pages.master')
@section('title', 'Receive Payment')
@section('content')

    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-auto">
                <a href="{{ route('income.index') }}" class="btn btn-primary">
                    <i class="ri-arrow-left-line"></i>Back
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
                            <div class="col-md-4">
                                <label class="form-label">Income Category <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="income_id" name="income_id" required>
                                    <option value="">Select Income Category</option>
                                    @foreach ($incomes as $income)
                                        <option value="{{ $income->id }}">{{ $income->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="payment_type" name="payment_type" required>
                                    <option value="">Select Payment Type</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank Transfer</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount Received (£) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="total_amount"
                                    name="total_amount" placeholder="0.00" required>
                                <small class="text-muted d-block mt-1">Selected Total: <strong
                                        id="selectedTotal">£0.00</strong></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Select Property <span class="text-danger" id="propertyRequired"></span></label>
                                <select class="form-control select2" id="property_id" name="property_id">
                                    <option value="">Select Property</option>
                                    @foreach ($properties as $property)
                                        <option value="{{ $property->id }}">{{ $property->property_reference }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Payment description (optional)"></textarea>
                            </div>

                            <div class="col-12" id="dueTransactionsSection" style="display: none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Select Due Transactions</h5>
                                        <small class="text-muted">Select transactions. Payment will be applied intelligently
                                            from first to last.</small>
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
        let selectedTransactions = [];

        $(document).ready(function() {
            
            // When income category changes
            $('#income_id').change(function() {
                var incomeId = $(this).val();
                var selectedOption = $(this).find('option:selected');
                var incomeName = selectedOption.text().toLowerCase();

                selectedTransactions = [];
                updateSelectedTotal();

                if (!incomeId) {
                    $('#dueTransactionsSection').hide();
                    $('#property_id').prop('required', false);
                    $('#propertyRequired').html('');
                    return;
                }

                // Only show dues selector and make property required if income is "rent"
                if (incomeName.includes('rent')) {
                    $('#dueTransactionsSection').show();
                    $('#property_id').prop('required', true);
                    $('#propertyRequired').html('<span class="text-danger">*</span>');
                    $('#dueTransactionsContainer').html(`
                        <div class="text-center text-muted py-4">
                            <i class="ri-search-line display-4"></i>
                            <p class="mt-2">Select a property to view due transactions</p>
                        </div>
                    `);
                } else {
                    $('#dueTransactionsSection').hide();
                    $('#property_id').prop('required', false);
                    $('#propertyRequired').html('');
                    $('#dueTransactionsContainer').html('');
                }
            });

            // When property changes (only for rent)
            $('#property_id').change(function() {
                var propertyId = $(this).val();
                var incomeId = $('#income_id').val();
                var selectedOption = $('#income_id').find('option:selected');
                var incomeName = selectedOption.text().toLowerCase();

                if (incomeName.includes('rent') && propertyId) {
                    loadDueTransactions(propertyId, incomeId);
                } else {
                    $('#dueTransactionsContainer').html(`
                        <div class="text-center text-muted py-4">
                            <i class="ri-search-line display-4"></i>
                            <p class="mt-2">Select a property to view due transactions</p>
                        </div>
                    `);
                    selectedTransactions = [];
                    updateSelectedTotal();
                }
            });

            $("#addBtn").click(function() {
                var incomeId = $('#income_id').val();
                var selectedOption = $('#income_id').find('option:selected');
                var incomeName = selectedOption.text().toLowerCase();

                if (!incomeId) {
                    showError('Please select an income category');
                    return;
                }

                if (!$("#total_amount").val() || parseFloat($("#total_amount").val()) <= 0) {
                    showError('Please enter a valid payment amount');
                    return;
                }

                if (incomeName.includes('rent')) {
                    if (selectedTransactions.length === 0) {
                        showError('Please select at least one due transaction');
                        return;
                    }

                    const enteredAmount = parseFloat($("#total_amount").val());
                    const selectedTotal = parseFloat($('#selectedTotal').text().replace('£', ''));

                    if (enteredAmount > selectedTotal) {
                        showError('Payment amount cannot be more than the total selected transactions (£' + selectedTotal.toFixed(2) + ')');
                        return;
                    }
                }

                var form_data = new FormData();
                form_data.append("_token", "{{ csrf_token() }}");
                form_data.append("income_id", incomeId);
                form_data.append("property_id", $("#property_id").val());
                form_data.append("date", $("#date").val());
                form_data.append("payment_type", $("#payment_type").val());
                form_data.append("total_amount", $("#total_amount").val());
                form_data.append("description", $("#description").val());

                selectedTransactions.forEach(function(transactionId) {
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
                            let errors = xhr.responseJSON.errors;
                            let firstError = Object.values(errors)[0][0];
                            showError(firstError);
                        } else {
                            showError(xhr.responseJSON?.message ?? "Something went wrong!");
                        }
                        console.error(xhr.responseText);
                    }
                });
            });

            function loadDueTransactions(propertyId, incomeId) {
                $.get("{{ route('income.due-transactions') }}", {
                    property_id: propertyId,
                    income_id: incomeId
                }, function(response) {
                    console.log(response);
                    if (response.length > 0) {
                        let html = '<div class="row">';
                        response.forEach((transaction, index) => {
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
                                                        <small class="text-muted">${transaction.description || 'Monthly rent'}</small>
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
                    updateSelectedTotal();
                });
            }

            $(document).on('change', '.transaction-checkbox', function() {
                const transactionId = parseInt($(this).val());
                const card = $(this).closest('.due-transaction-card');

                if ($(this).is(':checked')) {
                    if (!selectedTransactions.includes(transactionId)) {
                        selectedTransactions.push(transactionId);
                    }
                    card.addClass('border-primary bg-light');
                } else {
                    selectedTransactions = selectedTransactions.filter(id => id !== transactionId);
                    card.removeClass('border-primary bg-light');
                }

                updateSelectedTotal();
            });

            function updateSelectedTotal() {
                let total = 0;
                selectedTransactions.forEach(transactionId => {
                    let amount = parseFloat($(`[data-transaction-id="${transactionId}"]`).data('amount'));
                    total += amount;
                });
                $('#selectedTotal').text('£' + total.toFixed(2));
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