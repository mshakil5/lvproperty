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

        /* HEADER WITH LOGO */
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
        }

        .logo-section {
            flex: 0 0 150px;
            text-align: right;
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
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #999;
            text-align: center;
        }

        /* MAIN CONTENT */
        .content {
            padding: 40px;
        }

        /* TWO COLUMN LAYOUT */
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

        /* STATEMENT TABLE */
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

        /* SUMMARY TABLE */
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

        /* COMPLIANCE SECTION */
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
            display: flex;
            align-items: center;
        }

        .compliance-header i {
            margin-right: 8px;
            color: #1e7bc4;
        }

        .compliance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .compliance-table th {
            background: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #999;
            font-weight: bold;
        }

        .compliance-table td {
            padding: 10px;
            border: 1px solid #999;
        }

        .compliance-table .label-col {
            font-weight: bold;
            width: 50%;
        }

        /* CONTACT SECTION */
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
            display: flex;
            align-items: center;
        }

        .contact-header i {
            margin-right: 8px;
            color: #ff9800;
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

        .contact-left a {
            color: #1e7bc4;
            text-decoration: none;
        }

        .contact-right {
            text-align: right;
            font-size: 11px;
        }

        .contact-right p {
            margin-bottom: 5px;
        }

        .authorized-label {
            font-weight: bold;
            margin-bottom: 30px;
        }

        /* DATE AND SIGNATURE */
        .date-signature {
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            text-align: center;
            font-size: 11px;
        }

        .date-field {
            text-align: center;
        }

        .date-field p {
            margin-bottom: 5px;
        }

        .date-field strong {
            display: block;
            margin-top: 5px;
        }

        /* FOOTER */
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
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER WITH LOGO -->
        <div class="header">
            <div class="header-title">
                <h1>LV Property: End of Year Rental and Charges Statement</h1>
            </div>
            <div class="logo-section">
                <div class="logo-placeholder">LV PROPERTY<br>LOGO</div>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="content">
            <!-- TWO COLUMN SECTION -->
            <div class="two-column">
                <!-- LEFT COLUMN: LANDLORD -->
                <div class="left-column">
                    <h3>Landlord</h3>
                    <p>
                        <strong>Mrs. J Begum</strong><br>
                        J Begum Properties<br>
                        97 Erskine Road<br>
                        Sutton<br>
                        SM1 3AT<br>
                        <br>
                        Tel: 020 1234 5678<br>
                        Email: j.begum@property.com
                    </p>
                </div>

                <!-- RIGHT COLUMN: STATEMENT DETAILS -->
                <div class="right-column">
                    <div class="row">
                        <div class="label">Statement Period</div>
                        <div class="value">1 January 2024 to 31 December 2024</div>
                    </div>

                    <div class="row">
                        <div class="label">Property Address</div>
                        <div class="value">18 Killick House, Sutton, SM1 1SA</div>
                    </div>

                    <div class="row">
                        <div class="label">Property Reference</div>
                        <div class="value">18KHSM11SA</div>
                    </div>

                    <div class="row">
                        <div class="label">Statement Reference</div>
                        <div class="value">18KHSM11SA-2024-YR</div>
                    </div>

                    <div class="row">
                        <div class="label">Prepared By</div>
                        <div class="value">Admin User</div>
                    </div>

                    <div class="row">
                        <div class="label">Date Issued</div>
                        <div class="value">15 January 2025</div>
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
                            <th>Jan</th>
                            <th>Feb</th>
                            <th>Mar</th>
                            <th>Apr</th>
                            <th>May</th>
                            <th>Jun</th>
                            <th>Jul</th>
                            <th>Aug</th>
                            <th>Sep</th>
                            <th>Oct</th>
                            <th>Nov</th>
                            <th>Dec</th>
                            <th style="background: #1e7bc4; color: white;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- INCOME HEADER -->
                        <tr class="section-header">
                            <td colspan="14">INCOME</td>
                        </tr>

                        <!-- RENT RECEIVED -->
                        <tr>
                            <td>Rent Received</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td class="total-row">£24,000</td>
                        </tr>

                        <!-- TOTAL INCOME -->
                        <tr class="total-row">
                            <td>TOTAL INCOME</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£2,000</td>
                            <td>£24,000</td>
                        </tr>

                        <!-- EXPENSES HEADER -->
                        <tr class="section-header">
                            <td colspan="14">EXPENSES</td>
                        </tr>

                        <!-- MAINTENANCE -->
                        <tr>
                            <td>Maintenance</td>
                            <td>£100</td>
                            <td>£150</td>
                            <td>-</td>
                            <td>£200</td>
                            <td>-</td>
                            <td>£75</td>
                            <td>-</td>
                            <td>£100</td>
                            <td>-</td>
                            <td>£150</td>
                            <td>-</td>
                            <td>£125</td>
                            <td>£900</td>
                        </tr>

                        <!-- MANAGEMENT FEE -->
                        <tr>
                            <td>Management Fee (5%)</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£100</td>
                            <td>£1,200</td>
                        </tr>

                        <!-- TOTAL EXPENSES -->
                        <tr class="total-row">
                            <td>TOTAL EXPENSES</td>
                            <td>£200</td>
                            <td>£250</td>
                            <td>£100</td>
                            <td>£300</td>
                            <td>£100</td>
                            <td>£175</td>
                            <td>£100</td>
                            <td>£200</td>
                            <td>£100</td>
                            <td>£250</td>
                            <td>£100</td>
                            <td>£225</td>
                            <td>£2,100</td>
                        </tr>

                        <!-- NET INCOME -->
                        <tr class="net-row">
                            <td>NET INCOME / (LOSS)</td>
                            <td>£1,800</td>
                            <td>£1,750</td>
                            <td>£1,900</td>
                            <td>£1,700</td>
                            <td>£1,900</td>
                            <td>£1,825</td>
                            <td>£1,900</td>
                            <td>£1,800</td>
                            <td>£1,900</td>
                            <td>£1,750</td>
                            <td>£1,900</td>
                            <td>£1,775</td>
                            <td>£21,900</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- SUMMARY TABLE -->
            <div class="summary-section">
                <h3>Annual Financial Summary</h3>
                <p style="font-size: 11px; margin-bottom: 15px; color: #666;">
                    Income reflects Total Rent Received minus Agency Fees and Landlord-Paid Charges. Deposit Deductions are itemised separately and reconciled via TDS.
                </p>
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
                            <td class="amount">£24,000.00</td>
                        </tr>
                        <tr>
                            <td class="label">Less: Agency Fees</td>
                            <td class="amount">£1,200.00</td>
                        </tr>
                        <tr>
                            <td class="label">Less: Landlord-Paid Charges</td>
                            <td class="amount">£900.00</td>
                        </tr>
                        <tr class="total-summary">
                            <td class="label">Net Income (After Fees & Charges)</td>
                            <td class="amount">£21,900.00</td>
                        </tr>
                        <tr>
                            <td class="label">Deposit Deductions (TDS)</td>
                            <td class="amount">£0.00</td>
                        </tr>
                        <tr class="total-summary">
                            <td class="label"><strong>Total Payable to Landlord</strong></td>
                            <td class="amount"><strong>£21,900.00</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- COMPLIANCE & NOTES -->
            <div class="compliance-section">
                <div class="compliance-header">
                    ✓ Compliance & Notes
                </div>
                <table class="compliance-table">
                    <tbody>
                        <tr>
                            <td class="label-col">Gas Safety Certificate Expiration:</td>
                            <td>YES</td>
                        </tr>
                        <tr>
                            <td class="label-col">EICR (Electrical Inspection) Expiration:</td>
                            <td>YES</td>
                        </tr>
                        <tr>
                            <td class="label-col">Deposit Registered With:</td>
                            <td>TDS</td>
                        </tr>
                        <tr>
                            <td class="label-col">Tenancy Status:</td>
                            <td style="color: #27ae60; font-weight: bold;">RENEWED</td>
                        </tr>
                        <tr>
                            <td class="label-col">Next Rent Review Date:</td>
                            <td></td>
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
                <div class="contact-header">
                    ☎ Contact & Queries
                </div>
                <div class="contact-content">
                    <div class="contact-left">
                        <p><strong>📧 hello@londonvalleyproperty.co.uk</strong></p>
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
                    <div class="date-field">
                        <p></p>
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