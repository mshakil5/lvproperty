<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0;">
    <div style="width: 100%; margin: 0; padding: 0;">
        <!-- HEADER -->
        <table width="100%" style="margin: 0; border-collapse: collapse; border-bottom: 2px solid #ddd;">
            <tr style="vertical-align: top;">
                <td width="55%" style="padding: 30px 40px 30px 40px; padding-right: 20px; border: none;">
                    <h6 style="font-size: 14px; font-weight: bold; margin-bottom: 5px; margin: 0 0 5px 0;">Property Reference</h6>
                    <p style="font-size: 28px; font-weight: bold; margin: 0 0 15px 0;">{{ $property->property_reference }}</p>
                    <p style="font-size: 11px; margin: 0 0 15px 0;">
                        {{ $property->address_first_line }}<br>
                        {{ $property->city }}<br>
                        {{ $property->postcode }}
                    </p>
                    <p style="font-size: 11px; margin: 0 0 5px 0;"><strong>Statement Date</strong><br>{{ now()->format('l, j F Y') }}</p>
                    <p style="font-size: 11px; margin: 10px 0 0 0;"><strong>Statement Number</strong><br>{{ $property->property_reference }}-{{ $month->format('m-Y') }}</p>
                </td>
                <td width="45%" style="padding: 30px 40px 30px 20px; text-align: right; border: none; vertical-align: top;">
                    @php $companyDetails = \App\Models\CompanyDetails::first(); @endphp
                    @if($companyDetails && $companyDetails->company_logo)
                        <img src="{{ public_path('uploads/company/' . $companyDetails->company_logo) }}" alt="Company Logo" style="max-width: 140px; height: auto;">
                    @else
                        <div style="width: 140px; height: 100px; background: #f0f0f0; border: 2px dashed #999; text-align: center; font-size: 11px; color: #999; padding-top: 35px;">COMPANY<br>LOGO</div>
                    @endif
                </td>
            </tr>
        </table>

        <div style="padding: 40px;">
            <!-- LANDLORD & TENANT INFO -->
            <table width="100%" style="margin-bottom: 20px; border-collapse: collapse;">
                <tr>
                    <td width="50%" style="border: none; padding: 0; padding-right: 20px; font-size: 11px;">
                        <p style="font-size: 11px; margin: 0;"><strong>Landlord</strong></p>
                        <p style="font-size: 12px; font-weight: bold; margin: 5px 0;">{{ $landlord->name }}</p>
                        @if($landlord->company_name)
                            <p style="font-size: 11px; margin: 0;">{{ $landlord->company_name }}</p>
                        @endif
                        <p style="font-size: 11px; margin: 0;">Portfolio address: {{ $property->address_first_line }}, {{ $property->city }}, {{ $property->postcode }}</p>
                    </td>
                    <td width="50%" style="border: none; padding: 0; padding-left: 20px; font-size: 11px;">
                        <p style="font-size: 11px; margin: 0;"><strong>Tenant</strong></p>
                        <p style="font-size: 12px; font-weight: bold; margin: 5px 0;">{{ $currentTenant->name ?? 'N/A' }}</p>
                    </td>
                </tr>
            </table>

            <div style="border: 1px solid #ddd; margin: 20px 0;"></div>

            <!-- RENT DETAILS -->
            <table width="100%" style="margin-bottom: 20px; border-collapse: collapse;">
                <tr>
                    <td width="50%" style="border: none; padding: 0; padding-right: 20px; font-size: 11px;">
                        <p style="font-size: 11px; margin: 0;"><strong>Rent</strong></p>
                        <p style="font-size: 12px; margin: 5px 0;">£{{ number_format($monthlyRent > 0 ? $monthlyRent : 0, 2) }} P/M</p>
                        <p style="font-size: 11px; margin: 0;"><strong>Rent Period</strong></p>
                        <p style="font-size: 12px; margin: 5px 0;">{{ $startDate->format('l, j F Y') }} to {{ $endDate->format('l, j F Y') }}</p>
                    </td>
                    <td width="50%" style="border: none; padding: 0; padding-left: 20px; font-size: 11px;">
                        <p style="font-size: 11px; margin: 0;"><strong>Service</strong></p>
                        <p style="font-size: 12px; margin: 5px 0;">{{ $property->service_type ?? 'Full Management Service' }}</p>
                        <p style="font-size: 11px; margin: 10px 0 0 0;"><strong>Current Reserve Balance</strong></p>
                        <p style="font-size: 12px; margin: 5px 0;">£ -</p>
                    </td>
                </tr>
            </table>

            <div style="border: 1px solid #ddd; margin: 20px 0;"></div>

            <!-- TRANSACTIONS TABLE -->
            <p style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">Transactions</p>
            <table width="100%" style="margin-bottom: 20px; border-collapse: collapse; font-size: 10px;">
                <thead>
                    <tr>
                        <th style="width: 12%; background: #f0f0f0; border: 1px solid #bdc3c7; padding: 8px; font-weight: bold;">Date</th>
                        <th style="width: 50%; background: #f0f0f0; border: 1px solid #bdc3c7; padding: 8px; font-weight: bold;">Description</th>
                        <th style="width: 19%; background: #f0f0f0; border: 1px solid #bdc3c7; padding: 8px; font-weight: bold; text-align: right;">Receipts</th>
                        <th style="width: 19%; background: #f0f0f0; border: 1px solid #bdc3c7; padding: 8px; font-weight: bold; text-align: right;">Deductions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td style="border: 1px solid #bdc3c7; padding: 8px;">{{ Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                            <td style="border: 1px solid #bdc3c7; padding: 8px;">
                                @if($transaction->income)
                                    {{ $transaction->income->name }}
                                @elseif($transaction->expense)
                                    {{ $transaction->expense->name }}
                                @else
                                    {{ $transaction->description ?? 'Transaction' }}
                                @endif
                            </td>
                            <td style="border: 1px solid #bdc3c7; padding: 8px; text-align: right;">
                                @if($transaction->transaction_type === 'received')
                                    @if($transaction->received_amount > 0)
                                        £{{ number_format($transaction->received_amount, 2) }}
                                    @elseif($transaction->amount > 0)
                                        £{{ number_format($transaction->amount, 2) }}
                                    @else
                                        -
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td style="border: 1px solid #bdc3c7; padding: 8px; text-align: right;">
                                @if($transaction->expense_id && $transaction->amount > 0)
                                    £{{ number_format($transaction->amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="border: 1px solid #bdc3c7; padding: 20px; text-align: center; color: #999;">
                                No transactions found for this period
                            </td>
                        </tr>
                    @endforelse

                    <tr style="background: #ecf0f1; font-weight: bold;">
                        <td colspan="2" style="border: 1px solid #bdc3c7; padding: 8px;">Total</td>
                        <td style="border: 1px solid #bdc3c7; padding: 8px; text-align: right;">£{{ number_format($summary['totalReceipts'], 2) }}</td>
                        <td style="border: 1px solid #bdc3c7; padding: 8px; text-align: right;">£{{ number_format($summary['totalDeductions'], 2) }}</td>
                    </tr>
                    <tr style="background: #fff3cd; font-weight: bold;">
                        <td colspan="2" style="border: 1px solid #bdc3c7; padding: 8px;">Balance</td>
                        <td colspan="2" style="border: 1px solid #bdc3c7; padding: 8px; text-align: right; color: {{ $summary['balance'] >= 0 ? '#28a745' : '#dc3545' }};">
                            {{ $summary['balance'] >= 0 ? '+' : '' }}£{{ number_format($summary['balance'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div style="border: 1px solid #ddd; margin: 20px 0;"></div>

            <!-- BANK DETAILS & SUMMARY -->
            <table width="100%" style="margin-bottom: 20px; border-collapse: collapse;">
                <tr style="vertical-align: top;">
                    <td width="50%" style="border: none; padding: 0; padding-right: 20px;">
                        <p style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">Bank Details</p>
                        @if($landlord->bank_name)
                            <p style="font-size: 11px; margin: 5px 0;"><strong>Bank:</strong> {{ $landlord->bank_name }}</p>
                        @endif
                        @if($landlord->sort_code)
                            <p style="font-size: 11px; margin: 5px 0;"><strong>Sort Code:</strong> {{ $landlord->sort_code }}</p>
                        @endif
                        @if($landlord->account_number)
                            <p style="font-size: 11px; margin: 5px 0;"><strong>Account No:</strong> {{ $landlord->account_number }}</p>
                        @endif
                        @if(!$landlord->bank_name && !$landlord->sort_code && !$landlord->account_number)
                            <p style="font-size: 10px; margin: 10px 0 0 0; color: #666; font-style: italic;">Bank Details not provided</p>
                        @endif
                    </td>
                    <td width="50%" style="border: none; padding: 0; padding-left: 20px;">
                        <p style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">Summary</p>
                        <table width="100%" style="border-collapse: collapse; font-size: 10px;">
                            <tr>
                                <td style="border: none; padding: 5px 0;"><strong>Total Receipts:</strong></td>
                                <td style="border: none; padding: 5px 0; text-align: right;"><strong>£{{ number_format($summary['totalReceipts'], 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td style="border: none; padding: 5px 0;"><strong>Total Deductions:</strong></td>
                                <td style="border: none; padding: 5px 0; text-align: right;"><strong>£{{ number_format($summary['totalDeductions'], 2) }}</strong></td>
                            </tr>
                            <tr style="border-top: 1px solid #ddd;">
                                <td style="border: none; padding: 5px 0;"><strong>Balance:</strong></td>
                                <td style="border: none; padding: 5px 0; text-align: right; color: {{ $summary['balance'] >= 0 ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                                    {{ $summary['balance'] >= 0 ? '+' : '' }}£{{ number_format($summary['balance'], 2) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- NOTES -->
            <div style="background-color: #f8f9fa; padding: 15px; margin-bottom: 20px; font-size: 10px;">
                <p style="margin: 0;">
                    <strong>NOTE:</strong> This invoice has been generated by the Property Management System. 
                    For any queries regarding your account or these charges, please contact us immediately.
                </p>
            </div>
        </div>

        <!-- FOOTER -->
        <div style="background: #f0f0f0; padding: 20px 40px; text-align: center; border-top: 2px solid #ddd; font-size: 9px; color: #666; line-height: 1.5;">
            <p style="margin-bottom: 5px;"><strong>LV PROPERTY</strong></p>
            <p style="margin-bottom: 5px;">Registered Office: Suite 2, 10 Abbey Parade, Wimbledon, SW19 1DG, United Kingdom</p>
            <p style="margin-bottom: 5px;">Email: hello@londonvalleyproperty.co.uk</p>
            <p style="margin: 0;">Phone: 020 8287 4037 | 075 2395 9582</p>
        </div>
    </div>
</body>
</html>