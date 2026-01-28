<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #f0f0f0;
        }

        .print-button-section {
            margin-bottom: 20px;
            text-align: right;
            padding: 0 20px;
        }

        .print-button-section button {
            padding: 10px 20px;
            margin-left: 10px;
            background: #1e7bc4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .print-button-section button:hover {
            background: #1565a0;
        }

        @media print {
            .print-button-section {
                display: none !important;
            }

            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .page-container {
                width: 842px !important;
                margin: 0 auto !important;
                padding: 40px 45px !important;
                background: white !important;
                page-break-after: always;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .page-container:last-child {
                page-break-after: avoid;
            }

            h1 {
                color: #2e5a88 !important;
                -webkit-print-color-adjust: exact;
            }

            .section-header {
                background-color: #2e5a88 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
            }

            .bg-grey {
                background-color: #e6e6e6 !important;
                -webkit-print-color-adjust: exact;
            }

            .bg-blue-tint {
                background-color: #d9e1f2 !important;
                -webkit-print-color-adjust: exact;
            }

            .label-box {
                background-color: #d9e1f2 !important;
                -webkit-print-color-adjust: exact;
            }

            .status-green {
                color: #28a745 !important;
                -webkit-print-color-adjust: exact;
            }

            table {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .footer {
                position: absolute;
                bottom: 35px;
            }
        }

        /* Rest of non-print styles */
        .page-container {
            width: 842px;
            background: white;
            margin: 20px auto;
            padding: 40px 45px;
            box-sizing: border-box;
            position: relative;
            min-height: 1191px;
            page-break-after: always;
        }

        h1 {
            color: #2e5a88;
            font-size: 19px;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 35px;
            font-weight: bold;
        }

        .header-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 10px;
        }

        .left-col {
            width: 45%;
        }

        .right-col {
            width: 50%;
        }

        .logo-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .logo-img {
            height: 50px;
            width: auto;
        }

        .statement-box {
            border: 1.5px solid black;
            padding: 3px 8px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .info-row {
            display: flex;
            margin-bottom: 3px;
            line-height: 1.2;
        }

        .label {
            font-weight: bold;
            width: 135px;
            flex-shrink: 0;
        }

        .value {
            flex-grow: 1;
        }

        .section-header {
            background-color: #2e5a88;
            color: white;
            padding: 3px 10px;
            font-size: 10.5px;
            font-weight: bold;
            border: 1.5px solid black;
            display: flex;
            align-items: center;
            margin-bottom: -1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 0;
        }

        th,
        td {
            border: 1px solid black;
            font-size: 8.5px;
            padding: 2px 4px;
            height: 16px;
            text-align: left;
        }

        .bg-grey {
            background-color: #e6e6e6;
        }

        .bg-blue-tint {
            background-color: #d9e1f2;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .separated-row {
            border: 1.5px solid black;
            display: flex;
            margin-top: 5px;
        }

        .label-box {
            background-color: #d9e1f2;
            font-weight: bold;
            padding: 4px 10px;
            border-right: 1.5px solid black;
            width: 192px;
            font-size: 9.5px;
        }

        .total-spacer {
            width: 108px;
            border-right: 1.5px solid black;
        }

        .data-grid {
            display: flex;
            flex-grow: 1;
        }

        .data-cell {
            flex: 1;
            border-right: 1px solid black;
            padding: 4px 2px;
            font-size: 8px;
            text-align: right;
        }

        .data-cell:last-child {
            border-right: none;
        }

        .summary-wrapper {
            width: 340px;
            margin-top: 25px;
        }

        .summary-table td:nth-child(2) {
            border-right: none;
            width: 15px;
            text-align: center;
            font-weight: bold;
        }

        .summary-table td:last-child {
            border-left: none;
            text-align: right;
            font-weight: bold;
        }

        .disclaimer {
            border: 1.5px solid black;
            padding: 5px;
            text-align: center;
            font-style: italic;
            font-size: 8.5px;
            margin-top: 15px;
        }

        .compliance-table td:first-child {
            width: 40%;
            font-weight: bold;
        }

        .compliance-table td:last-child {
            width: 60%;
        }

        .status-green {
            color: #28a745;
            font-weight: bold;
        }

        .footer {
            position: absolute;
            bottom: 35px;
            width: 752px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 8px;
            color: #2e5a88;
            line-height: 1.3;
        }
    </style>
</head>

<body>

    <div class="print-button-section">
        <button onclick="window.print()">🖨️ Print</button>
        <button onclick="window.history.back()">← Back</button>
    </div>
    <div class="page-container">
        <h1>LV Property: End-of-Year Rental and Charges Statement</h1>
        <div class="header-grid">
            <div class="left-col">
                <div class="info-row"><span class="label">Landlord</span><span
                        class="value">{{ $landlord->name }}</span></div>
                <div class="info-row"><span class="label">Landlord Address</span><span
                        class="value">{{ $landlord->correspondence_address }}, {{ $landlord->city }},
                        {{ $landlord->postcode }}</span></div>
                <div style="height: 35px;"></div>
                <div class="info-row"><span class="label">Property Type/ Occupancy</span><span
                        class="value">Residential</span></div>
                <div class="info-row"><span class="label">Service Type</span><span class="value">Full
                        Management</span></div>
            </div>
            <div class="right-col">
                <div class="logo-wrapper">
                    @php $companyDetails = \App\Models\CompanyDetails::first(); @endphp
                    @if ($companyDetails && $companyDetails->company_logo)
                        <img src="{{ asset('uploads/company/' . $companyDetails->company_logo) }}" alt="LV Logo"
                            class="logo-img">
                    @else
                        <div
                            style="height: 50px; width: 100px; background: #f0f0f0; border: 1px dashed #999; display: flex; align-items: center; justify-content: center; font-size: 8px;">
                            LOGO</div>
                    @endif
                </div>
                <div class="statement-box"><span>Statement Period:</span><span>{{ $startDate->format('d M Y') }} -
                        {{ $endDate->format('d M Y') }}</span></div>
                <div class="info-row"><span class="label">Property Address:</span><span
                        class="value"><b>{{ $property->address_first_line }}, {{ $property->city }},
                            {{ $property->postcode }}</b></span></div>
                <div class="info-row"><span class="label">Property Reference:</span><span
                        class="value">{{ $property->property_reference }}</span></div>
                <div class="info-row"><span class="label">Tenant Name(s):</span><span
                        class="value">{{ $currentTenancy->tenant_name ?? 'N/A' }}</span></div>
                <div class="info-row"><span class="label">Prepared By:</span><span class="value">LV Property -
                        Management Team</span></div>
                <div class="info-row"><span class="label">Date Issued:</span><span
                        class="value">{{ now()->format('d M Y') }}</span></div>
            </div>
        </div>

        <div class="section-header">Rental Income, Charges & Deductions Summary</div>
        <table>
            <tr class="bg-grey text-center">
                <th style="width: 193px;">Income</th>
                <th style="width: 109px;">Total</th>
                @foreach ($months as $month)
                    <th>{{ $month['display'] }}</th>
                @endforeach
            </tr>
            <tr>
                <td class="bg-grey">Rent Received</td>
                <td class="bg-blue-tint">£ {{ number_format($summary['totalRent'], 2) }}</td>
                @php $rentTotal = 0; @endphp
                @foreach ($months as $month)
                    @php
                        $rentAmount = $statementData['rent']['values'][$month['key']] ?? 0;
                        $rentTotal += $rentAmount;
                    @endphp
                    <td class="text-right">{{ $rentAmount > 0 ? '£ ' . number_format($rentAmount, 2) : '£ -' }}</td>
                @endforeach
            </tr>
            @foreach ($statementData as $key => $row)
                @if ($row['type'] === 'income' && $key !== 'rent')
                    <tr>
                        <td>{{ $row['category'] }}</td>
                        <td></td>
                        @php $categoryTotal = 0; @endphp
                        @foreach ($months as $month)
                            @php
                                $amount = $row['values'][$month['key']] ?? 0;
                                $categoryTotal += $amount;
                            @endphp
                            <td class="text-right">{{ $amount > 0 ? '£ ' . number_format($amount, 2) : '' }}</td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </table>

        <div class="separated-row">
            <div class="label-box">Total expenses</div>
            <div class="total-spacer"></div>
            <div class="data-grid">
                @php $totalExpenses = 0; @endphp
                @foreach ($months as $month)
                    @php
                        $monthExpense = 0;
                        foreach ($statementData as $key => $row) {
                            if ($row['type'] === 'expense') {
                                $monthExpense += $row['values'][$month['key']] ?? 0;
                            }
                        }
                        $totalExpenses += $monthExpense;
                    @endphp
                    <div class="data-cell">{{ $monthExpense > 0 ? '£ ' . number_format($monthExpense, 2) : '£ -' }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="separated-row">
            <div class="label-box">Net Income</div>
            <div class="total-spacer"></div>
            <div class="data-grid">
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
                    <div class="data-cell">{{ $netMonth != 0 ? '£ ' . number_format($netMonth, 2) : '£ -' }}</div>
                @endforeach
            </div>
        </div>

        <div class="summary-wrapper">
            <div class="section-header">Financial Summary</div>
            <table class="summary-table">
                <tr class="bg-grey">
                    <td>Item</td>
                    <td></td>
                    <td>Amount (£)</td>
                </tr>
                <tr>
                    <td>Total Rent Received</td>
                    <td>£</td>
                    <td>{{ number_format($summary['totalRent'], 2) }}</td>
                </tr>
                <tr>
                    <td>Total Charges/Deductions</td>
                    <td>£</td>
                    <td>{{ number_format($summary['totalExpenses'], 2) }}</td>
                </tr>
                <tr class="bg-blue-tint">
                    <td>Net Income to Landlord</td>
                    <td>£</td>
                    <td>{{ number_format($summary['netIncome'], 2) }}</td>
                </tr>
                <tr>
                    <td>Outstanding Arrears (if any)</td>
                    <td>£</td>
                    <td>{{ number_format($summary['netIncome'], 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="disclaimer">Net income reflects total rent received minus agency fees and landlord-paid charges.
        </div>

        <div class="footer">
            <div style="width: 30%;"></div>
            <div style="text-align: center; width: 40%;"><b>LV PROPERTY</b><br>Registered Office: Suite-2.10 Abbey
                Parade. SW19 1DG. UK</div>
            <div style="text-align: right; width: 30%;">Email: <a href="/cdn-cgi/l/email-protection"
                    class="__cf_email__"
                    data-cfemail="f69e939a9a99b69a999892999880979a9a938f868499869384828fd89599d8839d">[email&#160;protected]</a><br>www.londonvalleyproperty.co.uk
            </div>
        </div>
    </div>

    <div class="page-container" style="padding: 40px;">
        <h1 style="color: #2e5a88; font-size: 20px; text-align: center; margin-bottom: 40px; font-weight: bold;">
            LV Property: End-of-Year Rental and Charges Statement
        </h1>

        <div class="section-header"
            style="width: 75%; border: 1.5px solid black; padding: 4px 8px; font-weight: bold; font-size: 11px; color: #2e5a88; display: flex; align-items: center; margin-bottom: 10px; background: none; box-sizing: border-box;">
            <span style="margin-right: 8px;">📌</span> Compliance & Notes
        </div>

        <table class="compliance-table"
            style="width: 75%; border-collapse: collapse; border: 1.5px solid black; table-layout: fixed; box-sizing: border-box;">
            @if ($propertyCompliances->count() > 0)
                @foreach ($propertyCompliances as $compliance)
                    <tr>
                        <td
                            style="border: 1px solid black; padding: 4px 8px; font-size: 10px; font-weight: bold; width: 60%;">
                            {{ $compliance->complianceType->name ?? 'Compliance' }}:</td>
                        <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px;">
                            @if ($compliance->status === 'Active')
                                <span class="status-green">✓ YES</span>
                            @elseif($compliance->status === 'Expired')
                                <span style="color: #c0392b; font-weight: bold;">✗
                                    EXPIRED</span>@else{{ $compliance->status }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px; font-weight: bold;">No
                        Compliance Records</td>
                    <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px;"></td>
                </tr>
            @endif
            <tr>
                <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px; font-weight: bold;">Deposit
                    Registered With:</td>
                <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px;">TDS</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px; font-weight: bold;">Tenancy
                    Status:</td>
                <td
                    style="border: 1px solid black; padding: 4px 8px; font-size: 10px; color: #28a745; font-weight: bold;">
                    {{ $currentTenancy->status ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px; font-weight: bold;">Next Rent
                    Review Date:</td>
                <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px;">
                    {{ $currentTenancy && $currentTenancy->renewal_date ? \Carbon\Carbon::parse($currentTenancy->renewal_date)->format('d M Y') : '' }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px; font-weight: bold;">Property
                    Inspections Conducted:</td>
                <td style="border: 1px solid black; padding: 4px 8px; font-size: 10px;"></td>
            </tr>
        </table>

        <div class="section-header"
            style="width: 75%; border: 1.5px solid black; padding: 4px 8px; font-weight: bold; font-size: 11px; color: #2e5a88; display: flex; align-items: center; margin-top: 40px; margin-bottom: 10px; background: none; box-sizing: border-box;">
            <span style="margin-right: 8px;">📞</span> Contact & Queries
        </div>
        <div style="padding: 20px; display: flex; justify-content: space-between; width: 75%; box-sizing: border-box;">
            <div style="font-size: 10px; line-height: 1.6;">
                <p style="margin: 0;">📧 <a href="/cdn-cgi/l/email-protection" class="__cf_email__"
                        data-cfemail="e189848d8d8ea18d8e8f858e8f97808d8d849891938e9184939598cf828ecf948a">[email&#160;protected]</a>
                </p>
                <p style="margin: 0;">📞 07523 959582 | 0208 287 4037</p>
            </div>
            <div style="text-align: right; margin-top: 20px; font-size: 10px;">
                <p style="margin: 0;"><b>Authorised Signature:</b> ____________________</p>
            </div>
        </div>

        <div
            style="display: flex; justify-content: space-between; width: 75%; margin-left: auto; padding: 30px; box-sizing: border-box;">
            <div style="font-size: 10px; line-height: 1.6;">
                <p style="font-weight: 700;">LV Property</p>
                <p style="margin: 0;">Date: {{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="footer"
            style="position: absolute; bottom: 40px; width: calc(100% - 80px); display: flex; justify-content: space-between; align-items: flex-end; font-size: 8.5px; color: #2e5a88;">
            <div style="width: 30%;"></div>
            <div style="text-align: center; width: 40%;"><b>LV PROPERTY</b><br>Registered Office: Suite-2.10 Abbey
                Parade. SW19 1DG. UK</div>
            <div style="text-align: right; width: 30%;">Email:
                hello@londonvalleyproperty.co.uk<br>www.londonvalleyproperty.co.uk</div>
        </div>
    </div>

</body>

</html>