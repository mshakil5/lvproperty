@extends('admin.pages.master')
@section('title', 'Monthly Invoice - ' . $property->property_reference)
@section('content')

<div class="container-fluid">
    <div class="row mb-3 no-print">
        <div class="col-12 text-end">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="ri-printer-line me-2"></i> Print Invoice
            </button>
            <a href="{{ route('invoice.index') }}" class="btn btn-secondary ms-2">
                <i class="ri-arrow-left-line me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="invoice-wrapper">
        <div class="content-top">
            <header class="header">
                <div class="recipient">
                    <p>
                        <strong>{{ $landlord->name }}</strong><br>
                        @if($landlord->company_name) {{ $landlord->company_name }}<br> @endif
                        {{ $property->address_first_line }}<br>
                        {{ $property->city }}<br>
                        {{ $property->postcode }}
                    </p>
                </div>
                <div class="invoice-stamp">
                    <h1>INVOICE</h1>
                </div>
                <div class="brand">
                    @php $companyDetails = \App\Models\CompanyDetails::first(); @endphp
                    @if ($companyDetails && $companyDetails->company_logo)
                        <img src="{{ asset('uploads/company/' . $companyDetails->company_logo) }}" style="max-width: 80px; height: auto;">
                    @else
                        <span class="lv-logo">LV</span>
                        <span class="brand-text">PROPERTY</span>
                    @endif
                </div>
            </header>

            <div class="meta-section">
                <div class="meta-left">
                    <table class="bordered-info">
                        <tr><td>Service</td><td><strong>{{ $property->service_type ?? 'Full Management Service' }}</strong></td></tr>
                        <tr><td>Tenant</td><td><strong>{{ $currentTenant->name ?? 'N/A' }}</strong></td></tr>
                        <tr><td>Rent</td><td><strong>£{{ number_format($monthlyRent, 2) }} P/M</strong></td></tr>
                    </table>
                    <div class="rent-box">
                        <strong>Rent Period</strong> &nbsp; {{ $startDate->format('l, j F Y') }} &nbsp; <strong>to</strong> &nbsp; {{ $endDate->format('l, j F Y') }}
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
            </div>

            <table class="main-table">
                <thead>
                    <tr>
                        <th width="12%">Date</th>
                        <th width="58%">Description</th>
                        <th width="15%" class="amt-header">Deduction</th>
                        <th width="15%" class="amt-header">Receipts</th>
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
                        <td class="amt">
                            @if ($transaction->expense_id && $transaction->amount > 0)
                                £{{ number_format($transaction->amount, 2) }}
                            @endif
                        </td>
                        <td class="amt">
                            @if ($transaction->transaction_type === 'received')
                                £{{ number_format($transaction->received_amount > 0 ? $transaction->received_amount : $transaction->amount, 2) }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-line">
                        <td colspan="2" style="text-align:right">Total</td>
                        <td class="amt">£{{ number_format($summary['totalDeductions'], 2) }}</td>
                        <td class="amt">£{{ number_format($summary['totalReceipts'], 2) }}</td>
                    </tr>
                    <tr class="landlord-pay">
                        <td colspan="3" style="text-align:right; border:none;"><strong>Paid to landlord</strong></td>
                        <td class="black-bg" style="text-align:right;">
                            {{ $summary['balance'] < 0 ? '-' : '' }}£{{ number_format(abs($summary['balance']), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="content-bottom">
            <div class="bottom-flex">
                <div class="bank-wrap">
                    <table class="bank-table">
                        <tr><td>Bank Details</td><td>{{ $landlord->bank_name ?? 'N/A' }}</td></tr>
                        <tr><td>Sort Code</td><td>{{ $landlord->sort_code ?? '******' }}</td></tr>
                        <tr><td>Account No</td><td>{{ $landlord->account_number ?? '********' }}</td></tr>
                    </table>
                    <div class="security-msg">Bank Details withheld for Security</div>
                </div>
                <div class="note-wrap">
                    <div class="note-box">NOTE: <span style="font-weight: normal; font-size: 9px;">This invoice is generated by the Property Management System.</span></div>
                </div>
            </div>

            <div class="outstanding-section">
                <div class="outstanding-title">Details of Outstanding Fees</div>
                <table class="out-table">
                    <tr>
                        <th>Date</th><th>Type</th><th>Narrative</th><th>W.O Raise</th><th>Paid</th><th>Reserve held</th><th>Due</th>
                    </tr>
                    <tr><td colspan="7" style="height:15px"></td></tr>
                </table>
            </div>

            <footer class="page-footer">
                <p>LV Property Ltd. Registered office- The Generator Business Centre, Suite 2, 10 Abbey Parade, Wimbledon, England, SW19 1DG
                    <br>
                    Phone- 0208 726 0304. Email- hello@londonvalleyproperty.co.uk</p>
            </footer>
        </div>
    </div>
</div>

<style>
    .invoice-wrapper {
        background: #fff;
        width: 210mm; /* standard A4 width */
        min-height: 290mm;
        margin: 20px auto;
        padding: 20mm; /* standard print padding */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        font-family: Arial, sans-serif;
        font-size: 10px;
        line-height: 1.1;
        color: #000;
        box-sizing: border-box; /* Ensures padding doesn't add to width */
    }

    .content-top { flex: 1; }

    /* Header & Branding */
    .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
    .invoice-stamp h1 { border: 2px solid #000; padding: 2px 30px; font-size: 18px; margin: 0; }
    .brand { text-align: center; color: #b01c1c; }
    .lv-logo { font-size: 30px; font-weight: bold; display: block; }
    .brand-text { font-size: 9px; font-weight: bold; display: block; margin-top: -5px; }

    /* Meta Info */
    .meta-section { 
        display: flex; 
        justify-content: space-between; 
        margin-bottom: 25px; 
        gap: 40px; /* Reduced from 150px to prevent squeezing the tables */
    }
    .meta-left { flex: 1; }
    .meta-right { flex: 1.2; }

    .bordered-info { border-collapse: collapse; width: 100%; }
    .bordered-info td { border: 1px solid #000; padding: 2px 5px; }
    .rent-box { border: 1px solid #000; margin-top: 5px; padding: 2px 5px; width: fit-content; white-space: nowrap; }
    
    .clean-align { border-collapse: collapse; width: 100%; }
    .clean-align td { padding: 1px 2px; }
    .clean-align td:nth-child(2) { padding: 0 5px; width: 5px; }

    .gap-row td { padding-top: 15px !important; border: none !important; }

    /* Main Table */
    .main-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    .main-table th { border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 3px; text-align: left; }
    .main-table td { padding: 3px 2px; vertical-align: top; }
    .sub-head td { font-weight: bold; text-decoration: underline; padding-top: 8px; }
    .amt { text-align: right; }
    .amt-header { text-align: right; }
    .total-line td { border-top: 1px solid #000; font-weight: bold; padding-top: 5px; }

    /* Print Color Fixes */
    .black-bg { 
        background-color: #000 !important; 
        color: #fff !important; 
        text-align: center; 
        font-weight: bold; 
        width: 80px; 
        padding: 3px;
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact;
    }

    .outstanding-title { 
        background-color: #000 !important; 
        color: #fff !important; 
        text-align: center; 
        font-weight: bold; 
        margin-top: 20px;
        -webkit-print-color-adjust: exact; 
        print-color-adjust: exact;
    }

    /* Bottom Sections */
    .bottom-flex { display: flex; justify-content: space-between; margin-top: 20px; align-items: flex-start; }
    .bank-table { border: 1px solid #000; border-collapse: collapse; width: 220px; }
    .bank-table td { border: 1px solid #000; padding: 2px 5px; }
    .bank-table td:first-child { 
        background-color: #eee !important; 
        font-weight: bold; 
        width: 80px;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .security-msg { font-size: 8px; font-weight: bold; text-align: center; width: 220px; margin-top: 2px; }
    .note-box { border: 1px solid #000; width: 280px; height: 50px; padding: 5px; font-weight: bold; }

    .out-table { width: 100%; border-collapse: collapse; border: 1px solid #000; }
    .out-table th, .out-table td { border: 1px solid #000; text-align: center; padding: 2px; font-size: 9px; }

    .page-footer { text-align: center; font-size: 9px; border-top: 1px solid #eee; padding-top: 10px; color: #555; margin-top: 20px; width: 100%; }

    @media print {
        @page {
            size: A4;
            margin: 10mm; /* Sets the physical printer margin */
        }
        .no-print { display: none !important; }
        body { background: white; margin: 0; padding: 0; }
        .invoice-wrapper { 
            margin: 0; 
            border: none; 
            width: 100% !important; /* Uses full width of the printer area */
            padding: 0; /* Let @page margin handle the space */
            height: auto;
            min-height: 95vh; /* Ensures footer stays down but allows for overflow */
        }
    }
</style>

@endsection