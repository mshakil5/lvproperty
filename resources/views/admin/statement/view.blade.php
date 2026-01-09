<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Statement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 950px;
            margin: 0 auto;
            background: white;
        }

        .print-button-section {
            margin-bottom: 20px;
            text-align: right;
            padding: 0 20px;
        }

        .print-button-section button,
        .print-button-section a {
            padding: 10px 20px;
            margin-left: 10px;
            background: #1e7bc4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 13px;
        }

        .print-button-section button:hover {
            background: #1565a0;
        }

        .print-button-section a {
            background: #666;
        }

        .print-button-section a:hover {
            background: #555;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px 40px;
            border-bottom: 2px solid #ddd;
        }

        .header-title {
            flex: 1;
            text-align: center;
        }

        .header-title h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1e7bc4;
            margin: 0;
        }

        .logo-section {
            flex: 0 0 150px;
            text-align: right;
        }

        .logo-placeholder {
            width: 120px;
            height: 80px;
            background: #f0f0f0;
            border: 2px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #999;
            text-align: center;
        }

        .content {
            padding: 40px;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 40px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 40px;
        }

        .left-column h3 {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .left-column p {
            font-size: 12px;
            line-height: 1.8;
            margin-bottom: 10px;
            color: #333;
        }

        .right-column {
            font-size: 12px;
            line-height: 1.8;
            color: #333;
        }

        .right-column .row {
            margin-bottom: 12px;
        }

        .right-column .label {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .right-column .value {
            color: #555;
        }

        .statement-section {
            margin-bottom: 40px;
        }

        .statement-section h3 {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 8px;
        }

        .statement-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 30px;
        }

        .statement-table th {
            background: #f0f0f0;
            color: #333;
            padding: 10px;
            text-align: right;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }

        .statement-table th:first-child {
            text-align: left;
        }

        .statement-table td {
            padding: 10px;
            border: 1px solid #bdc3c7;
            text-align: right;
        }

        .statement-table td:first-child {
            text-align: left;
            font-weight: bold;
        }

        .statement-table .section-header {
            background: #1e7bc4;
            color: white;
            font-weight: bold;
        }

        .statement-table .total-row {
            background: #ecf0f1;
            font-weight: bold;
        }

        .statement-table .net-row {
            background: #d5f4e6;
            font-weight: bold;
            color: #27ae60;
        }

        .summary-section {
            margin-bottom: 40px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 40px;
        }

        .summary-section h3 {
            font-size: 13px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 8px;
        }

        .summary-section > p {
            font-size: 11px;
            margin-bottom: 15px;
            color: #666;
        }

        .summary-table {
            width: 60%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .summary-table th {
            background: #f0f0f0;
            color: #333;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }

        .summary-table td {
            padding: 12px;
            border: 1px solid #bdc3c7;
        }

        .summary-table .label {
            font-weight: bold;
            width: 70%;
        }

        .summary-table .amount {
            text-align: right;
            font-weight: bold;
            color: #1e7bc4;
        }

        .summary-table .total-summary {
            background: #ecf0f1;
            font-weight: bold;
        }

        .compliance-section {
            margin-bottom: 40px;
            margin-top: 40px;
            border: 2px solid #999;
        }

        .compliance-header {
            background: #f0f0f0;
            padding: 10px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #999;
        }

        .compliance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .compliance-table td {
            padding: 10px;
            border: 1px solid #999;
        }

        .compliance-table .label-col {
            font-weight: bold;
            width: 50%;
        }

        .contact-section {
            margin-bottom: 40px;
            margin-top: 40px;
            border: 2px solid #999;
        }

        .contact-header {
            background: #f0f0f0;
            padding: 10px;
            font-weight: bold;
            font-size: 12px;
            border-bottom: 1px solid #999;
        }

        .contact-content {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .contact-left {
            font-size: 11px;
        }

        .contact-left p {
            margin-bottom: 5px;
        }

        .contact-right {
            text-align: right;
            font-size: 11px;
        }

        .authorized-label {
            font-weight: bold;
            margin-bottom: 30px;
        }

        .date-signature {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            text-align: center;
            font-size: 11px;
        }

        .date-field p {
            margin-bottom: 5px;
        }

        .footer {
            background: #f0f0f0;
            padding: 20px 40px;
            text-align: center;
            border-top: 2px solid #ddd;
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }

        .footer p {
            margin-bottom: 8px;
        }

        .footer-separator {
            border-top: 1px solid #999;
            margin: 10px 0;
        }

        .footer a {
            color: #1e7bc4;
            text-decoration: none;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .container {
                box-shadow: none;
            }
            .print-button-section {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- PDF Download Button -->
    <div class="print-button-section">
        <form id="pdfForm" method="POST" action="{{ route('statement.pdf') }}" style="display: inline;">
            @csrf
            <input type="hidden" name="landlord_id" value="{{ $landlord->id }}">
            <input type="hidden" name="property_id" value="{{ $property->id }}">
            <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            <button type="submit" class="print-button-section" style="padding: 10px 20px; background: #1e7bc4; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">📥 Download PDF</button>
        </form>
        <a href="{{ route('statement.index') }}" style="padding: 10px 20px; margin-left: 10px; background: #666; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 13px;">← Back</a>
    </div>

    <div class="container">
        <!-- HEADER WITH LOGO -->
        <div class="header">
            <div class="header-title">
                <h1>LV Property: End of Year Rental and Charges Statement</h1>
            </div>
            <div class="logo-section">
                @php $companyDetails = \App\Models\CompanyDetails::first(); @endphp
                @if($companyDetails && $companyDetails->company_logo)
                    <img src="{{ asset('uploads/company/' . $companyDetails->company_logo) }}" alt="Company Logo" style="max-width: 120px; height: auto;">
                @else
                    <div class="logo-placeholder">LV PROPERTY<br>LOGO</div>
                @endif
            </div>
        </div>

        <div class="content">
            <!-- TWO COLUMN SECTION -->
            <div class="two-column">
                <!-- LEFT COLUMN: LANDLORD -->
                <div class="left-column">
                    <h3>Landlord</h3>
                    <p>
                        <strong>{{ $landlord->name }}</strong><br>
                        @if($landlord->company_name){{ $landlord->company_name }}<br>@endif
                        @if($landlord->correspondence_address){{ $landlord->correspondence_address }}<br>@endif
                        @if($landlord->city){{ $landlord->city }}<br>@endif
                        @if($landlord->postcode){{ $landlord->postcode }}<br>@endif
                        <br>
                        @if($landlord->phone)Tel: {{ $landlord->phone }}<br>@endif
                        @if($landlord->email)Email: {{ $landlord->email }}@endif
                    </p>
                </div>

                <!-- RIGHT COLUMN: STATEMENT DETAILS -->
                <div class="right-column">
                    <div class="row">
                        <div class="label">Statement Period</div>
                        <div class="value">{{ $startDate->format('d F Y') }} to {{ $endDate->format('d F Y') }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Property Address</div>
                        <div class="value">{{ $property->address_first_line }}, {{ $property->city }}, {{ $property->postcode }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Property Reference</div>
                        <div class="value">{{ $property->property_reference }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Statement Reference</div>
                        <div class="value">{{ $property->property_reference }}-{{ $startDate->format('Y') }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Prepared By</div>
                        <div class="value">{{ Auth::user()->name ?? '' }}</div>
                    </div>

                    <div class="row">
                        <div class="label">Date Issued</div>
                        <div class="value">{{ now()->format('d F Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- STATEMENT TABLE -->
            <div class="statement-section">
                <h3>Financial Summary - Months vs Categories</h3>
                <table class="statement-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Category</th>
                            @foreach($months as $month)
                                <th style="width: 6.5%;">{{ $month['display'] }}</th>
                            @endforeach
                            <th style="width: 6.5%; background: #1e7bc4; color: white;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- INCOME HEADER -->
                        <tr class="section-header">
                            <td colspan="{{ count($months) + 2 }}">INCOME</td>
                        </tr>

                        <!-- RENT RECEIVED -->
                        <tr>
                            <td>Rent Received</td>
                            @php $rentTotal = 0; @endphp
                            @foreach($months as $month)
                                @php
                                    $rentAmount = $statementData['rent']['values'][$month['key']] ?? 0;
                                    $rentTotal += $rentAmount;
                                @endphp
                                <td>{{ $rentAmount > 0 ? '£' . number_format($rentAmount, 2) : '' }}</td>
                            @endforeach
                            <td class="total-row">{{ $rentTotal > 0 ? '£' . number_format($rentTotal, 2) : '' }}</td>
                        </tr>

                        <!-- OTHER INCOME CATEGORIES -->
                        @foreach($statementData as $key => $row)
                            @if($row['type'] === 'income' && $key !== 'rent')
                                <tr>
                                    <td>{{ $row['category'] }}</td>
                                    @php $categoryTotal = 0; @endphp
                                    @foreach($months as $month)
                                        @php
                                            $amount = $row['values'][$month['key']] ?? 0;
                                            $categoryTotal += $amount;
                                        @endphp
                                        <td>{{ $amount > 0 ? '£' . number_format($amount, 2) : '' }}</td>
                                    @endforeach
                                    <td>{{ $categoryTotal > 0 ? '£' . number_format($categoryTotal, 2) : '' }}</td>
                                </tr>
                            @endif
                        @endforeach

                        <!-- TOTAL INCOME -->
                        <tr class="total-row">
                            <td>TOTAL INCOME</td>
                            @php $totalIncome = 0; @endphp
                            @foreach($months as $month)
                                @php
                                    $monthIncome = ($statementData['rent']['values'][$month['key']] ?? 0);
                                    foreach($statementData as $key => $row) {
                                        if($row['type'] === 'income' && $key !== 'rent') {
                                            $monthIncome += ($row['values'][$month['key']] ?? 0);
                                        }
                                    }
                                    $totalIncome += $monthIncome;
                                @endphp
                                <td>{{ $monthIncome > 0 ? '£' . number_format($monthIncome, 2) : '' }}</td>
                            @endforeach
                            <td>{{ $totalIncome > 0 ? '£' . number_format($totalIncome, 2) : '' }}</td>
                        </tr>

                        <!-- EXPENSES HEADER -->
                        <tr class="section-header">
                            <td colspan="{{ count($months) + 2 }}">EXPENSES</td>
                        </tr>

                        <!-- EXPENSE CATEGORIES -->
                        @foreach($statementData as $key => $row)
                            @if($row['type'] === 'expense')
                                <tr>
                                    <td>{{ $row['category'] }}</td>
                                    @php $categoryTotal = 0; @endphp
                                    @foreach($months as $month)
                                        @php
                                            $amount = $row['values'][$month['key']] ?? 0;
                                            $categoryTotal += $amount;
                                        @endphp
                                        <td>{{ $amount > 0 ? '£' . number_format($amount, 2) : '' }}</td>
                                    @endforeach
                                    <td>{{ $categoryTotal > 0 ? '£' . number_format($categoryTotal, 2) : '' }}</td>
                                </tr>
                            @endif
                        @endforeach

                        <!-- TOTAL EXPENSES -->
                        <tr class="total-row">
                            <td>TOTAL EXPENSES</td>
                            @php $totalExpenses = 0; @endphp
                            @foreach($months as $month)
                                @php
                                    $monthExpense = 0;
                                    foreach($statementData as $key => $row) {
                                        if($row['type'] === 'expense') {
                                            $monthExpense += ($row['values'][$month['key']] ?? 0);
                                        }
                                    }
                                    $totalExpenses += $monthExpense;
                                @endphp
                                <td>{{ $monthExpense > 0 ? '£' . number_format($monthExpense, 2) : '' }}</td>
                            @endforeach
                            <td>{{ $totalExpenses > 0 ? '£' . number_format($totalExpenses, 2) : '' }}</td>
                        </tr>

                        <!-- NET INCOME -->
                        <tr class="net-row">
                            <td>NET INCOME / (LOSS)</td>
                            @php $grandNetIncome = 0; @endphp
                            @foreach($months as $month)
                                @php
                                    $monthIncome = 0;
                                    $monthExpense = 0;
                                    
                                    $monthIncome += ($statementData['rent']['values'][$month['key']] ?? 0);
                                    foreach($statementData as $key => $row) {
                                        if($row['type'] === 'income' && $key !== 'rent') {
                                            $monthIncome += ($row['values'][$month['key']] ?? 0);
                                        }
                                        if($row['type'] === 'expense') {
                                            $monthExpense += ($row['values'][$month['key']] ?? 0);
                                        }
                                    }
                                    
                                    $netMonth = $monthIncome - $monthExpense;
                                    $grandNetIncome += $netMonth;
                                @endphp
                                <td>{{ $netMonth != 0 ? '£' . number_format($netMonth, 2) : '' }}</td>
                            @endforeach
                            <td>{{ $grandNetIncome != 0 ? '£' . number_format($grandNetIncome, 2) : '' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- SUMMARY TABLE -->
            <div class="summary-section">
                <h3>Annual Financial Summary</h3>
                <p>Income reflects Total Rent Received minus Agency Fees and Landlord-Paid Charges. Deposit Deductions are itemised separately and reconciled via TDS.</p>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="label">Total Rent Received</td>
                            <td class="amount">£{{ number_format($summary['totalRent'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Less: Agency Fees</td>
                            <td class="amount">£{{ number_format($summary['totalExpenses'] * 0.05, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Less: Landlord-Paid Charges</td>
                            <td class="amount">£{{ number_format($summary['totalExpenses'] * 0.05, 2) }}</td>
                        </tr>
                        <tr class="total-summary">
                            <td class="label">Net Income (After Fees & Charges)</td>
                            <td class="amount">£{{ number_format($summary['netIncome'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Deposit Deductions (TDS)</td>
                            <td class="amount">£0.00</td>
                        </tr>
                        <tr class="total-summary">
                            <td class="label"><strong>Total Payable to Landlord</strong></td>
                            <td class="amount"><strong>£{{ number_format($summary['netIncome'], 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- COMPLIANCE & NOTES -->
            <div class="compliance-section">
                <div class="compliance-header">✓ Compliance & Notes</div>
                <table class="compliance-table">
                    <tbody>
                        @if($propertyCompliances->count() > 0)
                            @foreach($propertyCompliances as $compliance)
                                <tr>
                                    <td class="label-col">{{ $compliance->complianceType->name ?? 'Compliance' }}:</td>
                                    <td>
                                        @if($compliance->status === 'Active')
                                            <span style="color: #27ae60; font-weight: bold;">✓ YES</span>
                                        @elseif($compliance->status === 'Expired')
                                            <span style="color: #c0392b; font-weight: bold;">✗ EXPIRED</span>
                                        @else
                                            <span style="color: #f39c12; font-weight: bold;">{{ $compliance->status }}</span>
                                        @endif
                                        @if($compliance->expiry_date)
                                            <br><small style="color: #666;">Expires: {{ Carbon\Carbon::parse($compliance->expiry_date)->format('d M Y') }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td class="label-col">No Compliance Records:</td>
                                <td></td>
                            </tr>
                        @endif
                        
                        <tr>
                            <td class="label-col">Deposit Registered With:</td>
                            <td></td>
                        </tr>

                        <tr>
                            <td class="label-col">Tenancy Status:</td>
                            <td>
                                @if($currentTenancy)
                                    <span style="color: #27ae60; font-weight: bold;">{{ ucfirst($currentTenancy->status) }}</span>
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td class="label-col">Next Rent Review Date:</td>
                            <td>
                                @if($currentTenancy && $currentTenancy->renewal_date)
                                    {{ Carbon\Carbon::parse($currentTenancy->renewal_date)->format('d M Y') }}
                                @else
                                    <span style="color: #999;"></span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td class="label-col">Property Inspections Conducted:</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- CONTACT & QUERIES -->
            <div class="contact-section">
                <div class="contact-header">☎ Contact & Queries</div>
                <div class="contact-content">
                    <div class="contact-left">
                        <p>
                            <strong>📧
                                <a href="mailto:hello@londonvalleyproperty.co.uk">
                                hello@londonvalleyproperty.co.uk
                                </a>
                            </strong>
                            </p>
                        <p><strong>☎ 0752 3959582 | 0208 287 4037</strong></p>
                    </div>
                    <div class="contact-right">
                        <p class="authorized-label">Authorised Signature:</p>
                        <div style="min-height: 50px; border-bottom: 1px solid #999;"></div>
                    </div>
                </div>
                <div class="date-signature">
                    <div class="date-field">
                        <p>LV Property</p>
                        <p>Date: ___________</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
                <!-- FOOTER -->
        <div class="footer">
            <p><strong>LV PROPERTY</strong></p>
            <p>Registered Office: Suite 2, 10 Abbey Parade, Wimbledon, SW19 1DG, United Kingdom</p>
            <p>Email: <a href="mailto:hello@londonvalleyproperty.co.uk">hello@londonvalleyproperty.co.uk</a></p>
            <p>Phone: 020 8287 4037 | 075 2395 9582</p>
        </div>
    </div>
</body>
</html>