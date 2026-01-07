@extends('admin.pages.master')
@section('title','Ledger')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Ledger (Debit / Credit)</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Tran ID</th>
                    <th>Description</th>
                    <th>Debit (£)</th>
                    <th>Credit (£)</th>
                    <th>Balance (£)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ledger as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['tran_id'] }}</td>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['debit'] ? number_format($row['debit'],2) : '-' }}</td>
                    <td>{{ $row['credit'] ? number_format($row['credit'],2) : '-' }}</td>
                    <td><strong>{{ number_format($row['balance'],2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
