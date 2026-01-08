<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statement - {{ $property->property_reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .info-column h5 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }

        .info-column p {
            font-size: 10px;
            margin-bottom: 3px;
        }

        .tenancy-table h5 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }

        .tenancy-table table {
            font-size: 10px;
        }

        .tenancy-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
        }

        .main-table {
            margin-bottom: 20px;
            font-size: 9px;
        }

        .main-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
        }

        .main-table td {
            text-align: right;
            padding: 8px;
        }

        .main-table .category-col {
            text-align: left;
            font-weight: normal;
        }

        .main-table .section-header {
            background-color: #e8e8e8;
            font-weight: bold;
            text-align: left;
        }

        .main-table .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
            border-top: 2px solid #000;
        }

        .main-table .income-section {
            background-color: #e8f4f8;
        }

        .main-table .expense-section {
            background-color: #f8e8e8;
        }

        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 9px;
            color: #666;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-info {
            color: #17a2b8;
        }

        .page-break {
            page-break-after: always;
        }

        .summary-table {
            font-size: 10px;
        }

        .summary-table td {
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h2>{{ $property->property_reference }} - End of Year Rental and Charges Statement</h2>
        </div>

        <!-- Landlord & Property Info -->
        <div class="row mb-4">
            <div class="col-6 info-column">
                <h5>Landlord Details</h5>
                <p><strong>Name:</strong> {{ $landlord->name }}</p>
                @if ($landlord->company_name)
                    <p><strong>Company:</strong> {{ $landlord->company_name }}</p>
                @endif
                @if ($landlord->correspondence_address)
                    <p><strong>Address:</strong> {{ $landlord->correspondence_address }}</p>
                @endif
                @if ($landlord->postcode)
                    <p><strong>Postcode:</strong> {{ $landlord->postcode }}</p>
                @endif
                @if ($landlord->email)
                    <p><strong>Email:</strong> {{ $landlord->email }}</p>
                @endif
                @if ($landlord->phone)
                    <p><strong>Phone:</strong> {{ $landlord->phone }}</p>
                @endif
            </div>

            <div class="col-6 info-column">
                <h5>Statement Details</h5>
                <p><strong>Property Ref:</strong> {{ $property->property_reference }}</p>
                <p><strong>Address:</strong> {{ $property->address_first_line }}, {{ $property->city }}, {{ $property->postcode }}</p>
                <p><strong>Statement Period:</strong> {{ $startDate->format('d F Y') }} to {{ $endDate->format('d F Y') }}</p>
                <p><strong>Prepared By:</strong> Admin</p>
                <p><strong>Date Issued:</strong> {{ now()->format('d F Y') }}</p>
            </div>
        </div>

        <!-- Tenancy Information -->
        @if ($tenancies->count() > 0)
            <div class="tenancy-table mb-4">
                <h5>Tenancy Information</h5>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tenant Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Monthly Rent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tenancies as $tenancy)
                            <tr>
                                <td>{{ $tenancy->tenant->name ?? 'N/A' }}</td>
                                <td>{{ Carbon\Carbon::parse($tenancy->start_date)->format('d/m/Y') }}</td>
                                <td>{{ Carbon\Carbon::parse($tenancy->end_date)->format('d/m/Y') }}</td>
                                <td>£{{ number_format($tenancy->amount, 2) }}</td>
                                <td>{{ ucfirst($tenancy->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Main Statement Table -->
        <table class="table table-sm table-bordered main-table">
            <thead class="table-light">
                <tr>
                    <th style="text-align: left; width: 20%;">Category</th>
                    @foreach ($months as $month)
                        <th style="width: 6.5%;">{{ $month['display'] }}</th>
                    @endforeach
                    <th style="width: 6.5%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- INCOME SECTION -->
                <tr class="section-header income-section">
                    <td colspan="{{ count($months) + 2 }}">INCOME</td>
                </tr>

                <!-- Rent Row -->
                <tr>
                    <td class="category-col text-success"><strong>Rent Received</strong></td>
                    @php $rentTotal = 0; @endphp
                    @foreach ($months as $month)
                        @php
                            $rentAmount = $statementData['rent']['values'][$month['key']] ?? 0;
                            $rentTotal += $rentAmount;
                        @endphp
                        <td>{{ $rentAmount > 0 ? '£' . number_format($rentAmount, 2) : '' }}</td>
                    @endforeach
                    <td><strong>{{ $rentTotal > 0 ? '£' . number_format($rentTotal, 2) : '' }}</strong></td>
                </tr>

                <!-- Other Income Categories -->
                @php $otherIncomeTotal = 0; @endphp
                @foreach ($statementData as $key => $row)
                    @if ($row['type'] === 'income' && $key !== 'rent')
                        <tr>
                            <td class="category-col text-success">{{ $row['category'] }}</td>
                            @php $categoryTotal = 0; @endphp
                            @foreach ($months as $month)
                                @php
                                    $amount = $row['values'][$month['key']] ?? 0;
                                    $categoryTotal += $amount;
                                    $otherIncomeTotal += $amount;
                                @endphp
                                <td>{{ $amount > 0 ? '£' . number_format($amount, 2) : '' }}</td>
                            @endforeach
                            <td><strong>{{ $categoryTotal > 0 ? '£' . number_format($categoryTotal, 2) : '' }}</strong></td>
                        </tr>
                    @endif
                @endforeach

                <!-- Total Income Row -->
                <tr class="total-row income-section">
                    <td class="category-col text-success"><strong>TOTAL INCOME</strong></td>
                    @php $totalIncome = 0; @endphp
                    @foreach ($months as $month)
                        @php
                            $monthIncome = $statementData['rent']['values'][$month['key']] ?? 0;
                            foreach ($statementData as $key => $row) {
                                if ($row['type'] === 'income' && $key !== 'rent') {
                                    $monthIncome += $row['values'][$month['key']] ?? 0;
                                }
                            }
                            $totalIncome += $monthIncome;
                        @endphp
                        <td><strong class="text-success">{{ $monthIncome > 0 ? '£' . number_format($monthIncome, 2) : '' }}</strong></td>
                    @endforeach
                    <td><strong class="text-success">{{ $totalIncome > 0 ? '£' . number_format($totalIncome, 2) : '' }}</strong></td>
                </tr>

                <!-- EXPENSES SECTION -->
                <tr class="section-header expense-section">
                    <td colspan="{{ count($months) + 2 }}">EXPENSES</td>
                </tr>

                <!-- Expense Categories -->
                @php $totalExpenses = 0; @endphp
                @foreach ($statementData as $key => $row)
                    @if ($row['type'] === 'expense')
                        <tr>
                            <td class="category-col text-danger">{{ $row['category'] }}</td>
                            @php $categoryTotal = 0; @endphp
                            @foreach ($months as $month)
                                @php
                                    $amount = $row['values'][$month['key']] ?? 0;
                                    $categoryTotal += $amount;
                                    $totalExpenses += $amount;
                                @endphp
                                <td>{{ $amount > 0 ? '£' . number_format($amount, 2) : '' }}</td>
                            @endforeach
                            <td><strong>{{ $categoryTotal > 0 ? '£' . number_format($categoryTotal, 2) : '' }}</strong></td>
                        </tr>
                    @endif
                @endforeach

                <!-- Total Expenses Row -->
                <tr class="total-row expense-section">
                    <td class="category-col text-danger"><strong>TOTAL EXPENSES</strong></td>
                    @php $totalExpensesMonth = 0; @endphp
                    @foreach ($months as $month)
                        @php
                            $monthExpense = 0;
                            foreach ($statementData as $key => $row) {
                                if ($row['type'] === 'expense') {
                                    $monthExpense += $row['values'][$month['key']] ?? 0;
                                }
                            }
                            $totalExpensesMonth += $monthExpense;
                        @endphp
                        <td><strong class="text-danger">{{ $monthExpense > 0 ? '£' . number_format($monthExpense, 2) : '' }}</strong></td>
                    @endforeach
                    <td><strong class="text-danger">{{ $totalExpenses > 0 ? '£' . number_format($totalExpenses, 2) : '' }}</strong></td>
                </tr>

                <!-- NET INCOME ROW -->
                <tr class="total-row">
                    <td class="category-col"><strong>NET INCOME / (LOSS)</strong></td>
                    @php $grandNetIncome = 0; @endphp
                    @foreach ($months as $month)
                        @php
                            $monthIncome = 0;
                            $monthExpense = 0;

                            $monthIncome += $statementData['rent']['values'][$month['key']] ?? 0;
                            foreach ($statementData as $key => $row) {
                                if ($row['type'] === 'income' && $key !== 'rent') {
                                    $monthIncome += $row['values'][$month['key']] ?? 0;
                                }
                                if ($row['type'] === 'expense') {
                                    $monthExpense += $row['values'][$month['key']] ?? 0;
                                }
                            }

                            $netMonth = $monthIncome - $monthExpense;
                            $grandNetIncome += $netMonth;
                        @endphp
                        <td><strong class="{{ $netMonth >= 0 ? 'text-success' : 'text-danger' }}">{{ $netMonth != 0 ? '£' . number_format($netMonth, 2) : '' }}</strong></td>
                    @endforeach
                    <td><strong class="{{ $grandNetIncome >= 0 ? 'text-success' : 'text-danger' }}">{{ $grandNetIncome != 0 ? '£' . number_format($grandNetIncome, 2) : '' }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Financial Summary -->
        <div class="mt-4 mb-4">
            <h5 class="mb-3" style="font-size:12px;font-weight:bold;">Financial Summary</h5>
            <table class="table table-sm table-bordered summary-table">
                <tbody>
                    <tr class="table-light">
                        <td style="font-weight: bold; width: 60%;">Total Rent Received</td>
                        <td style="font-weight: bold; text-align: right; width: 40%;">£{{ number_format($summary['totalRent'], 2) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Total Other Income</td>
                        <td style="font-weight: bold; text-align: right;">£{{ number_format($summary['totalOtherIncome'], 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td style="font-weight: bold;">Total Expenses</td>
                        <td style="font-weight: bold; text-align: right;">£{{ number_format($summary['totalExpenses'], 2) }}</td>
                    </tr>
                    <tr class="table-success">
                        <td style="font-weight: bold;">Net Income</td>
                        <td style="font-weight: bold; text-align: right; color: {{ $summary['netIncome'] >= 0 ? '#28a745' : '#dc3545' }};">£{{ number_format($summary['netIncome'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This statement has been generated by the Property Management System. For queries, please contact the property manager.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>