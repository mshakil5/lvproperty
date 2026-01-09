<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Yearly Statement</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            padding: 30px 40px;
            border-bottom: 2px solid #ddd;
            display: table;
            table-layout: fixed;
        }

        .header-title {
            width: 70%;
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .header-title h1 {
            font-size: 20px;
            font-weight: bold;
            color: #1e7bc4;
            margin: 0;
        }

        .logo-section {
            width: 30%;
            text-align: right;
            display: table-cell;
            vertical-align: middle;
        }

        .logo-section img {
            max-width: 120px;
            height: auto;
        }

        .logo-placeholder {
            width: 120px;
            height: 80px;
            background: #f0f0f0;
            border: 2px dashed #999;
            text-align: center;
            padding: 20px 0;
        }

        .content {
            padding: 40px;
        }

        .two-column {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 40px;
            table-layout: fixed;
        }

        .left-column, .right-column {
            display: table-cell;
            width: 50%;
            padding-right: 40px;
            vertical-align: top;
        }

        .right-column {
            padding-right: 0;
            padding-left: 40px;
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
            display: block;
        }

        .right-column .label {
            font-weight: bold;
            margin-bottom: 2px;
            display: block;
        }

        .right-column .value {
            color: #555;
            display: block;
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
            font-size: 9px;
            margin-bottom: 30px;
        }

        .statement-table th {
            background: #f0f0f0;
            color: #333;
            padding: 8px;
            text-align: right;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }

        .statement-table th:first-child {
            text-align: left;
        }

        .statement-table td {
            padding: 8px;
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
            font-size: 10px;
            margin-bottom: 15px;
            color: #666;
        }

        .summary-table {
            width: 60%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .summary-table th {
            background: #f0f0f0;
            color: #333;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }

        .summary-table td {
            padding: 10px;
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
            font-size: 11px;
            border-bottom: 1px solid #999;
        }

        .compliance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
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
            font-size: 11px;
            border-bottom: 1px solid #999;
        }

        .contact-content {
            padding: 20px;
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .contact-left {
            display: table-cell;
            width: 50%;
            font-size: 10px;
        }

        .contact-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            font-size: 10px;
        }

        .authorized-label {
            font-weight: bold;
            margin-bottom: 30px;
        }

        .signature-line {
            min-height: 30px;
            border-bottom: 1px solid #999;
            margin-top: 20px;
        }

        .date-signature {
            padding: 20px;
            display: table;
            width: 100%;
            font-size: 10px;
            table-layout: fixed;
        }

        .date-field {
            display: table-cell;
            width: 50%;
            text-align: center;
        }

        .date-field p {
            margin-bottom: 5px;
        }

        .footer {
            background: #f0f0f0;
            padding: 20px 40px;
            text-align: center;
            border-top: 2px solid #ddd;
            font-size: 9px;
            color: #666;
            line-height: 1.5;
        }

        .footer p {
            margin-bottom: 5px;
        }

        .footer-separator {
            border-top: 1px solid #999;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER WITH LOGO -->
        <table width="100%" style="padding: 30px 40px; border-bottom: 2px solid #ddd;">
            <tr>
                <td width="70%" style="text-align: center; vertical-align: middle;">
                    <h1 style="font-size: 20px; font-weight: bold; color: #1e7bc4; margin: 0;">LV Property: End of Year Rental and Charges Statement</h1>
                </td>
                <td width="30%" style="text-align: right; vertical-align: middle;">
                    @php $companyDetails = \App\Models\CompanyDetails::first(); @endphp
                    @if($companyDetails && $companyDetails->company_logo)
                        <img src="{{ public_path('uploads/company/' . $companyDetails->company_logo) }}" alt="Company Logo" style="max-width: 120px; height: auto;">
                    @else
                        <div style="width: 120px; height: 80px; background: #f0f0f0; border: 2px dashed #999; text-align: center; padding-top: 20px; font-size: 10px; color: #999;">LV PROPERTY<br>LOGO</div>
                    @endif
                </td>
            </tr>
        </table>

        <div class="content">
            <!-- TWO COLUMN SECTION -->
            <table width="100%" style="border-bottom: 2px solid #ddd; margin-bottom: 40px; padding-bottom: 40px; margin-top: 0;">
                <tr>
                    <!-- LEFT COLUMN: LANDLORD -->
                    <td width="50%" valign="top" style="padding-right: 40px;">
                        <h3 style="font-size: 13px; font-weight: bold; color: #333; margin-bottom: 15px;">Landlord</h3>
                        <p style="font-size: 12px; line-height: 1.8; color: #333;">
                            <strong>{{ $landlord->name }}</strong><br>
                            @if($landlord->company_name){{ $landlord->company_name }}<br>@endif
                            @if($landlord->correspondence_address){{ $landlord->correspondence_address }}<br>@endif
                            @if($landlord->city){{ $landlord->city }}<br>@endif
                            @if($landlord->postcode){{ $landlord->postcode }}<br>@endif
                            <br>
                            @if($landlord->phone)Tel: {{ $landlord->phone }}<br>@endif
                            @if($landlord->email)Email: {{ $landlord->email }}@endif
                        </p>
                    </td>

                    <!-- RIGHT COLUMN: STATEMENT DETAILS -->
                    <td width="50%" valign="top" style="padding-left: 40px; font-size: 12px; line-height: 1.8; color: #333;">
                        <div style="margin-bottom: 12px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">Statement Period</div>
                            <div style="color: #555;">{{ $startDate->format('d F Y') }} to {{ $endDate->format('d F Y') }}</div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">Property Address</div>
                            <div style="color: #555;">{{ $property->address_first_line }}, {{ $property->city }}, {{ $property->postcode }}</div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">Property Reference</div>
                            <div style="color: #555;">{{ $property->property_reference }}</div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">Statement Reference</div>
                            <div style="color: #555;">{{ $property->property_reference }}-{{ $startDate->format('Y') }}</div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">Prepared By</div>
                            <div style="color: #555;">{{ Auth::user()->name ?? '' }}</div>
                        </div>

                        <div style="margin-bottom: 12px;">
                            <div style="font-weight: bold; margin-bottom: 2px;">Date Issued</div>
                            <div style="color: #555;">{{ now()->format('d F Y') }}</div>
                        </div>
                    </td>
                </tr>
            </table>

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
                                            ✓ YES
                                        @elseif($compliance->status === 'Expired')
                                            ✗ EXPIRED
                                        @else
                                            {{ $compliance->status }}
                                        @endif
                                        @if($compliance->expiry_date)
                                            (Expires: {{ Carbon\Carbon::parse($compliance->expiry_date)->format('d M Y') }})
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
                                    {{ ucfirst($currentTenancy->status) }}
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td class="label-col">Next Rent Review Date:</td>
                            <td>
                                @if($currentTenancy && $currentTenancy->renewal_date)
                                    {{ Carbon\Carbon::parse($currentTenancy->renewal_date)->format('d M Y') }}
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
                        <p><strong>📧 <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="1f777a7373705f7370717b7071697e73737a666f6d706f7a6d6b66317c70316a74">[email&#160;protected]</a></strong></p>
                        <p><strong>☎ 0752 3959582 | 0208 287 4037</strong></p>
                    </div>
                    <div class="contact-right">
                        <p class="authorized-label">Authorised Signature:</p>
                        <div class="signature-line"></div>
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
        <div class="footer">
            <p><strong>LV PROPERTY</strong></p>
            <p>Registered Office: Suite 2, 10 Abbey Parade, Wimbledon, SW19 1DG, United Kingdom</p>
            <p>Email: <a href="mailto:hello@londonvalleyproperty.co.uk">hello@londonvalleyproperty.co.uk</a></p>
            <p>Phone: 020 8287 4037 | 075 2395 9582</p>
        </div>
    </div>
</body>
</html>