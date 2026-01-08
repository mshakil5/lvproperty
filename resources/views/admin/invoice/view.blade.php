@extends('admin.pages.master')
@section('title', 'Monthly Invoice')
@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 text-end">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="ri-printer-line me-2"></i> Print Invoice
            </button>
            <a href="{{ route('invoice.index') }}" class="btn btn-secondary ms-2">
                <i class="ri-arrow-left-line me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-5" style="background: white;">
                    <!-- Header Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">Property Reference</h6>
                            <p style="font-size: 20px; font-weight: bold; margin: 0;">{{ $property->property_reference }}</p>
                            <p style="font-size: 11px; margin: 5px 0 0 0;">{{ $property->address_first_line }}<br>{{ $property->city }}<br>{{ $property->postcode }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p style="font-size: 12px; margin: 0;"><strong>Statement Date</strong><br>{{ now()->format('l, j F Y') }}</p>
                            <p style="font-size: 12px; margin: 10px 0 0 0;"><strong>Statement Number</strong><br>{{ $property->property_reference }}-{{ $month->format('m-Y') }}</p>
                        </div>
                    </div>

                    <hr style="border: 1px solid #ddd; margin: 20px 0;">

                    <!-- Landlord & Tenant Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p style="font-size: 11px; margin: 0;"><strong>Landlord</strong></p>
                            <p style="font-size: 12px; font-weight: bold; margin: 5px 0;">{{ $landlord->name }}</p>
                            @if($landlord->company_name)
                                <p style="font-size: 11px; margin: 0;">{{ $landlord->company_name }}</p>
                            @endif
                            <p style="font-size: 11px; margin: 0;">Portfolio address: {{ $property->address_first_line }}, {{ $property->city }}, {{ $property->postcode }}</p>
                        </div>
                        <div class="col-md-6">
                            <p style="font-size: 11px; margin: 0;"><strong>Tenant</strong></p>
                            <p style="font-size: 12px; font-weight: bold; margin: 5px 0;">{{ $currentTenant->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <hr style="border: 1px solid #ddd; margin: 20px 0;">

                    <!-- Rent Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p style="font-size: 11px; margin: 0;"><strong>Rent</strong></p>
                            <p style="font-size: 12px; margin: 5px 0;">£{{ number_format($monthlyRent > 0 ? $monthlyRent : 0, 2) }} P/M</p>
                            <p style="font-size: 11px; margin: 0;"><strong>Rent Period</strong></p>
                            <p style="font-size: 12px; margin: 5px 0;">{{ $startDate->format('l, j F Y') }} to {{ $endDate->format('l, j F Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p style="font-size: 11px; margin: 0;"><strong>Service</strong></p>
                            <p style="font-size: 12px; margin: 5px 0;">{{ $property->service_type ?? 'Full Management Service' }}</p>
                            <p style="font-size: 11px; margin: 10px 0 0 0;"><strong>Current Reserve Balance</strong></p>
                            <p style="font-size: 12px; margin: 5px 0;">£ -</p>
                        </div>
                    </div>

                    <hr style="border: 1px solid #ddd; margin: 20px 0;">

                    <!-- Transactions Table -->
                    <div class="mb-4">
                        <h6 style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">Transactions</h6>
                        <table class="table table-sm table-bordered" style="font-size: 11px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 12%;">Date</th>
                                    <th style="width: 50%;">Description</th>
                                    <th class="text-end" style="width: 19%;">Receipts</th>
                                    <th class="text-end" style="width: 19%;">Deductions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                        <td>
                                            @if($transaction->income)
                                                {{ $transaction->income->name }}
                                            @elseif($transaction->expense)
                                                {{ $transaction->expense->name }}
                                            @else
                                                {{ $transaction->description ?? 'Transaction' }}
                                            @endif
                                        </td>
                                        <td class="text-end">
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
                                        <td class="text-end">
                                            @if($transaction->expense_id && $transaction->amount > 0)
                                                £{{ number_format($transaction->amount, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center" style="padding: 20px;">
                                            <p style="margin: 0; color: #999;">No transactions found for this period</p>
                                        </td>
                                    </tr>
                                @endforelse

                                <tr class="table-light" style="font-weight: bold;">
                                    <td colspan="2">Total</td>
                                    <td class="text-end">£{{ number_format($summary['totalReceipts'], 2) }}</td>
                                    <td class="text-end">£{{ number_format($summary['totalDeductions'], 2) }}</td>
                                </tr>
                                <tr style="background-color: #fff3cd; font-weight: bold;">
                                    <td colspan="2">Balance</td>
                                    <td colspan="2" class="text-end" style="color: {{ $summary['balance'] >= 0 ? '#28a745' : '#dc3545' }};">
                                        {{ $summary['balance'] >= 0 ? '+' : '' }}£{{ number_format($summary['balance'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <hr style="border: 1px solid #ddd; margin: 20px 0;">

                    <!-- Bank Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">Bank Details</h6>
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
                        </div>
                        <div class="col-md-6">
                            <h6 style="font-size: 12px; font-weight: bold; margin-bottom: 10px;">Summary</h6>
                            <table class="table table-sm" style="font-size: 10px; margin-bottom: 0;">
                                <tr>
                                    <td><strong>Total Receipts:</strong></td>
                                    <td class="text-end"><strong>£{{ number_format($summary['totalReceipts'], 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Deductions:</strong></td>
                                    <td class="text-end"><strong>£{{ number_format($summary['totalDeductions'], 2) }}</strong></td>
                                </tr>
                                <tr style="border-top: 1px solid #ddd;">
                                    <td><strong>Balance:</strong></td>
                                    <td class="text-end" style="color: {{ $summary['balance'] >= 0 ? '#28a745' : '#dc3545' }}; font-weight: bold;">{{ $summary['balance'] >= 0 ? '+' : '' }}£{{ number_format($summary['balance'], 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr style="border: 1px solid #ddd; margin: 20px 0;">

                    <!-- Footer/Notes -->
                    <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px;">
                        <p style="font-size: 10px; margin: 0; line-height: 1.6;">
                            <strong>NOTE:</strong> This invoice has been generated by the Property Management System. 
                            For any queries regarding your account or these charges, please contact us immediately.
                        </p>
                    </div>

                    <!-- Company Info -->
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center;">
                        <p style="font-size: 9px; color: #666; margin: 0;">
                            Property Management System | Invoice Generated on {{ now()->format('j F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, a.btn { display: none !important; }
        body { background: white; }
        .card { border: none; box-shadow: none; }
        .container-fluid { max-width: 100%; margin: 0; padding: 0; }
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .table {
        margin-bottom: 0;
    }

    .table td, .table th {
        padding: 8px;
        vertical-align: middle;
    }

    .table-sm td, .table-sm th {
        padding: 6px 8px;
    }

    .text-end {
        text-align: right !important;
    }
</style>

@endsection