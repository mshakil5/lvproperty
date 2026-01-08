<!DOCTYPE html>
<html lang="en">
<head>
@php
    use Carbon\Carbon;
@endphp
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<title>{{ $company->company_name }} - Invoice</title>

<style>
    body {
        font-family: Arial, Helvetica;
        font-size: 12px;
        margin: 0;
        padding: 20px;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .invoice-body {
        max-width: 794px;
        margin: 0 auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    .table td, .table th {
        padding: 8px;
    }
    @media print {
        body { padding: 0; }
    }
</style>
</head>

<body>
<section class="invoice">
<div class="invoice-body">

<br><br><br><br><br><br>

{{-- HEADER --}}
<table>
    <tr>
        <td width="50%">
            <img src="{{ asset('uploads/company/' . $company->company_logo) }}" width="120">
        </td>
        <td width="50%" class="text-right">
            <h1 style="font-size:30px;color:blue;margin:0">INVOICE</h1>
        </td>
    </tr>
</table>

<br><br><br><br><br><br>

{{-- BILL INFO --}}
<table>
<tr>
<td width="40%">
    <h5 style="margin:5px;line-height:10px">Bill To</h5>
    <p style="margin:5px;line-height:10px">{{ $trx->tenant?->name ?? 'N/A' }}</p>
    @if($trx->tenant?->email)
        <p style="margin:5px;line-height:10px">{{ $trx->tenant->email }}</p>
    @endif
    @if($trx->tenant?->phone)
        <p style="margin:5px;line-height:10px">{{ $trx->tenant->phone }}</p>
    @endif
</td>

<td width="30%" class="text-center" style="vertical-align:middle">
    <p style="margin:5px;font-weight:bold">
        Bill For: {{ $trx->income?->name ?? 'Income' }}
    </p>
</td>

<td width="30%" class="text-right">
    <p style="margin:5px">Invoice No: {{ $trx->tran_id }}</p>
    <p style="margin:5px">Date: {{ Carbon::parse($trx->date)->format('d/m/Y') }}</p>
</td>
</tr>
</table>

<br>

{{-- PAYMENT TABLE (SAME AS MODAL DATA) --}}
<table class="table" style="border:1px solid #dee2e6">
    <thead>
        <tr>
            <th style="border:1px solid #dee2e6;text-align:center">TXN</th>
            <th style="border:1px solid #dee2e6;text-align:center">Date</th>
            <th style="border:1px solid #dee2e6;text-align:center">Payment Type</th>
            <th style="border:1px solid #dee2e6;text-align:center">Paid Amount</th>
        </tr>
    </thead>
    <tbody>
@php
$rows = [];

if (!empty($trx->received_ids)) {
    foreach ($trx->received_ids as $r) {
        $rt = \App\Models\Transaction::find($r['id']);
        if ($rt) {
            $rows[] = [
                'tran_id' => $rt->tran_id,
                'date' => \Carbon\Carbon::parse($rt->date)->format('d M, Y'),
                'amount' => $r['amount'],
                'payment_type' => ucfirst($rt->payment_type),
            ];
        }
    }
} else {
    $rows[] = [
        'tran_id' => $trx->tran_id,
        'date' => \Carbon\Carbon::parse($trx->date)->format('d M, Y'),
        'amount' => $trx->received_amount,
        'payment_type' => ucfirst($trx->payment_type),
    ];
}
@endphp

@foreach($rows as $row)
<tr>
    <td style="border:1px solid #dee2e6;text-align:center">{{ $row['tran_id'] }}</td>
    <td style="border:1px solid #dee2e6;text-align:center">{{ $row['date'] }}</td>
    <td style="border:1px solid #dee2e6;text-align:center">
        {{ $row['payment_type'] }}
    </td>
        <td style="border:1px solid #dee2e6;text-align:right">
        £{{ number_format($row['amount'],2) }}
    </td>
</tr>
@endforeach
    </tbody>
</table>


{{-- TOTAL --}}
<table style="margin-top:20px">
<tr>
    <td width="70%"></td>
    <td><strong>TOTAL</strong></td>
    <td class="text-right" style="padding-right:8px">
        <strong>£{{ number_format($trx->received_amount,2) }}</strong>
    </td>
</tr>
</table>

<br><br><br><br><br><br>

{{-- PAID STAMP --}}
@if($trx->status && $paidImageBase64)
<table>
<tr>
    <td width="60%"></td>
    <td width="40%" class="text-right">
        <img src="{{ $paidImageBase64 }}" width="120">
    </td>
</tr>
</table>
@endif

{{-- FOOTER --}}
<div style="position:fixed;bottom:0;left:50%;transform:translateX(-50%);
max-width:794px;width:100%;padding:10px 20px;border-top:1px solid #ddd">

<table>
<tr>
<td width="50%">
    <b>{{ $company->business_name ?? $company->company_name }}</b><br>
    Registration Number: {{ $company->company_reg_number ?? '' }}<br>
    Vat Number: {{ $company->vat_number ?? '' }}<br>
    {{ $company->address1 ?? '' }}
</td>
<td width="50%" class="text-right">
    {{ $company->phone1 ?? '' }}<br>
    {{ $company->email1 ?? '' }}<br>
    {{ $company->website ?? '' }}
</td>
</tr>
</table>

</div>

</div>
</section>

<script>
window.onload = () => setTimeout(() => window.print(), 1000);
</script>

</body>
</html>
