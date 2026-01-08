@extends('admin.pages.master')
@section('title', 'Yearly Statement')
@section('content')

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 text-end">
            <form id="pdfForm" method="POST" action="{{ route('statement.pdf') }}" style="display: inline;">
                @csrf
                <input type="hidden" name="landlord_id" value="{{ $landlord->id }}">
                <input type="hidden" name="property_id" value="{{ $property->id }}">
                <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-file-pdf-line me-2"></i> Download PDF
                </button>
            </form>
            <a href="{{ route('statement.index') }}" class="btn btn-secondary ms-2">
                <i class="ri-arrow-left-line me-2"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <!-- Header Section -->
                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-12">
                            <h2 class="fw-bold mb-3">{{ $property->property_reference }} - End of Year Rental and Charges Statement</h2>
                        </div>
                    </div>

                    <!-- Landlord & Property Info -->
                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Landlord Details</h5>
                            <p class="mb-1"><strong>Name:</strong> {{ $landlord->name }}</p>
                            @if($landlord->company_name)
                                <p class="mb-1"><strong>Company:</strong> {{ $landlord->company_name }}</p>
                            @endif
                            @if($landlord->correspondence_address)
                                <p class="mb-1"><strong>Address:</strong> {{ $landlord->correspondence_address }}</p>
                            @endif
                            @if($landlord->postcode)
                                <p class="mb-1"><strong>Postcode:</strong> {{ $landlord->postcode }}</p>
                            @endif
                            @if($landlord->email)
                                <p class="mb-1"><strong>Email:</strong> {{ $landlord->email }}</p>
                            @endif
                            @if($landlord->phone)
                                <p class="mb-1"><strong>Phone:</strong> {{ $landlord->phone }}</p>
                            @endif
                        </div>

                        <div class="col-md-6 text-md-end">
                            <h5 class="fw-bold mb-3">Statement Details</h5>
                            <p class="mb-1"><strong>Property Ref:</strong> {{ $property->property_reference }}</p>
                            <p class="mb-1"><strong>Address:</strong> {{ $property->address_first_line }}, {{ $property->city }}, {{ $property->postcode }}</p>
                            <p class="mb-1"><strong>Statement Period:</strong> {{ $startDate->format('d F Y') }} to {{ $endDate->format('d F Y') }}</p>
                            <p class="mb-1"><strong>Prepared By:</strong> {{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="mb-1"><strong>Date Issued:</strong> {{ now()->format('d F Y') }}</p>
                        </div>
                    </div>

                    <!-- Tenancy Information -->
                    @if($tenancies->count() > 0)
                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-12">
                            <h5 class="fw-bold mb-3">Tenancy Information</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tenant Name</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Monthly Rent</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenancies as $tenancy)
                                        <tr>
                                            <td>{{ $tenancy->tenant->name ?? 'N/A' }}</td>
                                            <td>{{ Carbon\Carbon::parse($tenancy->start_date)->format('d/m/Y') }}</td>
                                            <td>{{ Carbon\Carbon::parse($tenancy->end_date)->format('d/m/Y') }}</td>
                                            <td>£{{ number_format($tenancy->amount, 2) }}</td>
                                            <td><span class="badge bg-{{ $tenancy->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($tenancy->status) }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Main Statement Table - Categories as Rows, Months as Columns -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold mb-3">Financial Summary - Months vs Categories</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fw-bold" style="min-width: 200px;">Category</th>
                                            @foreach($months as $month)
                                                <th class="text-center fw-bold" style="min-width: 80px;">{{ $month['display'] }}</th>
                                            @endforeach
                                            <th class="text-center fw-bold bg-light" style="min-width: 80px;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- INCOME SECTION -->
                                        <tr class="table-primary">
                                            <td colspan="{{ count($months) + 2 }}" class="fw-bold">INCOME</td>
                                        </tr>

                                        <!-- Rent Row -->
                                        <tr>
                                            <td class="fw-bold text-success">Rent Received</td>
                                            @php $rentTotal = 0; @endphp
                                            @foreach($months as $month)
                                                @php 
                                                    $rentAmount = $statementData['rent']['values'][$month['key']] ?? 0;
                                                    $rentTotal += $rentAmount;
                                                @endphp
                                                <td class="text-end">{{ $rentAmount > 0 ? '£' . number_format($rentAmount, 2) : '' }}</td>
                                            @endforeach
                                            <td class="text-end fw-bold bg-light">{{ $rentTotal > 0 ? '£' . number_format($rentTotal, 2) : '' }}</td>
                                        </tr>

                                        <!-- Other Income Categories -->
                                        @php $otherIncomeTotal = 0; @endphp
                                        @foreach($statementData as $key => $row)
                                            @if($row['type'] === 'income' && $key !== 'rent')
                                                <tr>
                                                    <td class="text-success">{{ $row['category'] }}</td>
                                                    @php $categoryTotal = 0; @endphp
                                                    @foreach($months as $month)
                                                        @php 
                                                            $amount = $row['values'][$month['key']] ?? 0;
                                                            $categoryTotal += $amount;
                                                            $otherIncomeTotal += $amount;
                                                        @endphp
                                                        <td class="text-end">{{ $amount > 0 ? '£' . number_format($amount, 2) : '' }}</td>
                                                    @endforeach
                                                    <td class="text-end fw-bold bg-light">{{ $categoryTotal > 0 ? '£' . number_format($categoryTotal, 2) : '' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <!-- Total Income Row -->
                                        <tr class="table-light fw-bold">
                                            <td class="text-success fw-bold">TOTAL INCOME</td>
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
                                                <td class="text-end fw-bold text-success">{{ $monthIncome > 0 ? '£' . number_format($monthIncome, 2) : '' }}</td>
                                            @endforeach
                                            <td class="text-end fw-bold bg-light text-success">{{ $totalIncome > 0 ? '£' . number_format($totalIncome, 2) : '' }}</td>
                                        </tr>

                                        <!-- EXPENSES SECTION -->
                                        <tr class="table-danger">
                                            <td colspan="{{ count($months) + 2 }}" class="fw-bold">EXPENSES</td>
                                        </tr>

                                        <!-- Expense Categories -->
                                        @php $totalExpenses = 0; @endphp
                                        @foreach($statementData as $key => $row)
                                            @if($row['type'] === 'expense')
                                                <tr>
                                                    <td class="text-danger">{{ $row['category'] }}</td>
                                                    @php $categoryTotal = 0; @endphp
                                                    @foreach($months as $month)
                                                        @php 
                                                            $amount = $row['values'][$month['key']] ?? 0;
                                                            $categoryTotal += $amount;
                                                            $totalExpenses += $amount;
                                                        @endphp
                                                        <td class="text-end">{{ $amount > 0 ? '£' . number_format($amount, 2) : '' }}</td>
                                                    @endforeach
                                                    <td class="text-end fw-bold bg-light">{{ $categoryTotal > 0 ? '£' . number_format($categoryTotal, 2) : '' }}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <!-- Total Expenses Row -->
                                        <tr class="table-light fw-bold">
                                            <td class="text-danger fw-bold">TOTAL EXPENSES</td>
                                            @php $totalExpensesMonth = 0; @endphp
                                            @foreach($months as $month)
                                                @php 
                                                    $monthExpense = 0;
                                                    foreach($statementData as $key => $row) {
                                                        if($row['type'] === 'expense') {
                                                            $monthExpense += ($row['values'][$month['key']] ?? 0);
                                                        }
                                                    }
                                                    $totalExpensesMonth += $monthExpense;
                                                @endphp
                                                <td class="text-end fw-bold text-danger">{{ $monthExpense > 0 ? '£' . number_format($monthExpense, 2) : '' }}</td>
                                            @endforeach
                                            <td class="text-end fw-bold bg-light text-danger">{{ $totalExpenses > 0 ? '£' . number_format($totalExpenses, 2) : '' }}</td>
                                        </tr>

                                        <!-- NET INCOME ROW -->
                                        <tr class="table-success fw-bold">
                                            <td class="fw-bold">NET INCOME / (LOSS)</td>
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
                                                <td class="text-end fw-bold {{ $netMonth >= 0 ? 'text-success' : 'text-danger' }}">{{ $netMonth != 0 ? '£' . number_format($netMonth, 2) : '' }}</td>
                                            @endforeach
                                            <td class="text-end fw-bold bg-light {{ $grandNetIncome >= 0 ? 'text-success' : 'text-danger' }}">{{ $grandNetIncome != 0 ? '£' . number_format($grandNetIncome, 2) : '' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Table -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="fw-bold mb-3">Financial Summary</h5>
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr class="table-light">
                                        <td style="font-weight: bold; width: 60%;">Total Rent Received</td>
                                        <td style="font-weight: bold; text-align: right; width: 40%;">£{{ number_format($summary['totalRent'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">Total Other Income</td>
                                        <td style="font-weight: bold; text-align: right;">£{{ number_format($summary['totalOtherIncome'], 2) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td style="font-weight: bold;">Total Expenses</td>
                                        <td style="font-weight: bold; text-align: right;">£{{ number_format($summary['totalExpenses'], 2) }}</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td style="font-weight: bold;">Net Income</td>
                                        <td style="font-weight: bold; text-align: right; color: {{ $summary['netIncome'] >= 0 ? '#28a745' : '#dc3545' }};">£{{ number_format($summary['netIncome'], 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="row pt-4 border-top text-muted small">
                        <div class="col-12">
                            <p class="mb-0">This statement has been generated by the Property Management System. For queries, please contact the property manager.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, a.btn, [onclick*="print"] { display: none !important; }
        .card { border: 1px solid #dee2e6; }
        body { margin: 0; padding: 10px; }
        .container-fluid { max-width: 100%; }
        table { font-size: 0.85rem; }
        .table-responsive { overflow-x: auto; }
    }

    .table td, .table th {
        padding: 0.5rem;
        vertical-align: middle;
    }

    .text-end {
        text-align: right !important;
    }
</style>

@endsection