<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Invoice</title>
    <style>
        /* PDF specific resets */
        @page { margin: 10mm; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }
        
        .header { width: 100%; margin-bottom: 20px; }
        .recipient { width: 40%; float: left; }
        .invoice-stamp { width: 30%; float: left; text-align: center; }
        .invoice-stamp h1 { border: 2px solid #000; padding: 5px; font-size: 18px; display: inline-block; margin: 0; }
        .brand { width: 30%; float: right; text-align: right; }

        .clear { clear: both; }

        .meta-section { width: 100%; margin-top: 20px; }
        .meta-left { width: 45%; float: left; }
        .meta-right { width: 30%; float: right; }

        table { width: 100%; border-collapse: collapse; }
        .bordered-info td { border: 1px solid #000; padding: 3px; }
        .rent-box { border: 1px solid #000; padding: 5px; margin-top: 10px; width: auto; display: inline-block; }

        .main-table { margin-top: 20px; }
        .main-table th { border-top: 2px solid #000; border-bottom: 2px solid #000; text-align: left; padding: 5px; }
        .main-table td { padding: 5px; border-bottom: 1px solid #eee; }
        
        /* THE FILL COLORS */
        .black-bg { background-color: #000 !important; color: #ffffff !important; font-weight: bold; text-align: right; }
        .grey-bg { background-color: #eeeeee !important; font-weight: bold; }
        
        .total-line td { border-top: 2px solid #000; font-weight: bold; }
        
        .bottom-section { position: absolute; bottom: 0; width: 100%; }
        .bank-table td { border: 1px solid #000; padding: 3px; }
        .note-box { border: 1px solid #000; padding: 10px; height: 40px; }

        .outstanding-title { background-color: #000; color: #fff; text-align: center; padding: 5px; font-weight: bold; margin-top: 20px; }
        .out-table { border: 1px solid #000; }
        .out-table th, .out-table td { border: 1px solid #000; text-align: center; padding: 3px; }

        .footer { text-align: center; font-size: 8px; border-top: 1px solid #ccc; margin-top: 20px; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="recipient">
            <strong>{{ $landlord->name }}</strong><br>
            @if($landlord->company_name) {{ $landlord->company_name }}<br> @endif
            {{ $property->address_first_line }}<br>
            {{ $property->city }}<br>{{ $property->postcode }}
        </div>
        <div class="invoice-stamp"><h1>INVOICE</h1></div>
        <div class="brand">
            <span style="font-size: 24px; font-weight: bold; color: #b01c1c;">LV</span><br>
            <span style="font-size: 8px; font-weight: bold;">PROPERTY</span>
        </div>
        <div class="clear"></div>
    </div>

    <div class="meta-section">
        <div class="meta-left">
            <table class="bordered-info">
                <tr><td>Service</td><td><strong>{{ $property->service_type ?? 'Full Management Service' }}</strong></td></tr>
                <tr><td>Tenant</td><td><strong>{{ $currentTenant->name ?? 'N/A' }}</strong></td></tr>
                <tr><td>Rent</td><td><strong>£{{ number_format($monthlyRent, 2) }} P/M</strong></td></tr>
            </table>
            <div class="rent-box">
                <strong>Rent Period:</strong> {{ $startDate->format('d/m/Y') }} to {{ $endDate->format('d/m/Y') }}
            </div>
        </div>
        <div class="meta-right">
            <table class="clean-align">
                <tr><td>Portfolio Reference</td><td>:</td><td><strong>{{ $property->property_reference }}</strong></td></tr>
                <tr><td>Portfolio address</td><td>:</td><td>{{ $property->address_first_line }}, {{ $property->city }}, {{ $property->postcode }}</td></tr>
                <tr><td>Statement Date</td><td>:</td><td>{{ now()->format('l, j F Y') }}</td></tr>
                <tr><td>Statement Number</td><td>:</td><td>{{ $property->property_reference }}-{{ $month->format('m-Y') }}</td></tr>
                <tr class="gap-row"><td></td><td></td><td></td></tr> <tr><td style="width: 46%;">Current Reserve Balance</td><td>:</td><td>£ -</td></tr>
                <tr><td>CNR Tax Held Balance</td><td>:</td><td>£ -</td></tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th width="15%">Date</th>
                <th width="55%">Description</th>
                <th width="15%" style="text-align: right;">Deduction</th>
                <th width="15%" style="text-align: right;">Receipts</th>
            </tr>
        </thead>
        <tbody>
            
            <tr class="sub-head"><td colspan="4">Rent Collection & Transactions</td></tr>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                <td>
                    @if ($transaction->income) {{ $transaction->income->name }}
                    @elseif($transaction->expense) {{ $transaction->expense->name }}
                    @else {{ $transaction->description ?? 'Transaction' }}
                    @endif
                </td>
                <td class="amt" style="text-align: right;">
                    @if ($transaction->expense_id && $transaction->amount > 0)
                        £{{ number_format($transaction->amount, 2) }}
                    @endif
                </td>
                <td class="amt" style="text-align: right;">
                    @if ($transaction->transaction_type === 'received')
                        £{{ number_format($transaction->received_amount > 0 ? $transaction->received_amount : $transaction->amount, 2) }}
                    @endif
                </td>
            </tr>
            @endforeach


            <tr class="total-line">
                <td colspan="2" style="text-align: right;">Total</td>
                <td style="text-align: right;">£{{ number_format($summary['totalDeductions'], 2) }}</td>
                <td style="text-align: right;">£{{ number_format($summary['totalReceipts'], 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right; border: none; padding-top: 10px;"><strong>Paid to landlord</strong></td>
                <td class="black-bg" style="padding: 5px;">£{{ number_format(abs($summary['balance']), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="bottom-section">
        <table style="width: 100%">
            <tr>
                <td style="width: 45%; border: none;">
                    <table class="bank-table">
                        <tr><td class="grey-bg">Bank Details</td><td>{{ $landlord->bank_name ?? 'N/A' }}</td></tr>
                        <tr><td class="grey-bg">Sort Code</td><td>{{ $landlord->sort_code ?? '***' }}</td></tr>
                        <tr><td class="grey-bg">Account No</td><td>{{ $landlord->account_number ?? '***' }}</td></tr>
                    </table>
                </td>
                <td style="width: 10%; border: none;"></td>
                <td style="width: 45%; border: none; vertical-align: top;">
                    <div class="note-box"><strong>NOTE:</strong> Invoice generated by System.</div>
                </td>
            </tr>
        </table>

        <div class="outstanding-title">Details of Outstanding Fees</div>
        <table class="out-table">
            <thead>
                <tr>
                    <th>Date</th><th>Type</th><th>Narrative</th><th>Paid</th><th>Due</th>
                </tr>
            </thead>
            <tbody><tr><td colspan="5" style="height: 20px;"></td></tr></tbody>
        </table>

        <div class="footer">
            LV Property Ltd. Registered office- The Generator Business Centre, Suite 2, 10 Abbey Parade, Wimbledon, SW19 1DG<br>
            Phone- 0208 726 0304. Email- hello@londonvalleyproperty.co.uk
        </div>
    </div>
</body>
</html>