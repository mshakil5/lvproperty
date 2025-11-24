@extends('admin.pages.master')
@section('title', 'Dashboard')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="row">
                <!-- Financial Overview Cards -->
                
                <!-- Total Receivable Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Total Receivable</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-warning fs-14 mb-0">
                                        <i class="ri-money-pound-circle-line fs-13 align-middle"></i>
                                        Pending
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        £<span class="counter-value" data-target="{{ number_format($stats['total_receivable'], 2) }}">
                                            {{ number_format($stats['total_receivable'], 2) }}
                                        </span>
                                    </h4>
                                    <div class="text-muted fs-12">
                                        <span class="text-warning">{{ $stats['pending_rents'] }} Rent Dues</span> •
                                        <span class="text-info">{{ $stats['pending_compliance_payments'] }} Compliance</span>
                                    </div>
                                    <div class="text-muted fs-12 mt-1">
                                        <small>Rent: £{{ number_format($stats['total_receivable'] - $stats['total_compliance_receivable'], 2) }} • 
                                        Compliance: £{{ number_format($stats['total_compliance_receivable'], 2) }}</small>
                                    </div>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-warning rounded fs-3">
                                        <i class="ri-money-pound-circle-line text-warning"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Received Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Total Received</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-success fs-14 mb-0">
                                        <i class="ri-arrow-right-up-line fs-13 align-middle"></i>
                                        Collected
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        £<span class="counter-value" data-target="{{ number_format($stats['total_received'], 2) }}">
                                            {{ number_format($stats['total_received'], 2) }}
                                        </span>
                                    </h4>
                                    <div class="text-muted fs-12">
                                        <span class="text-success">Rent Payments</span>
                                    </div>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-success rounded fs-3">
                                        <i class="ri-arrow-right-up-line text-success"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Expenses Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Total Expenses</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="text-danger fs-14 mb-0">
                                        <i class="ri-arrow-right-down-line fs-13 align-middle"></i>
                                        Paid Out
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                        £<span class="counter-value" data-target="{{ number_format($stats['total_expenses'], 2) }}">
                                            {{ number_format($stats['total_expenses'], 2) }}
                                        </span>
                                    </h4>
                                    <div class="text-muted fs-12">
                                        <span class="text-danger">£{{ number_format($stats['pending_expenses'], 2) }} Pending</span>
                                    </div>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-danger rounded fs-3">
                                        <i class="ri-arrow-right-down-line text-danger"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Income Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <p class="text-uppercase fw-medium text-muted text-truncate mb-0">
                                        Net Income</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <h5 class="{{ $stats['net_income'] >= 0 ? 'text-success' : 'text-danger' }} fs-14 mb-0">
                                        <i class="ri-line-chart-line fs-13 align-middle"></i>
                                        {{ $stats['net_income'] >= 0 ? 'Profit' : 'Loss' }}
                                    </h5>
                                </div>
                            </div>
                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4 {{ $stats['net_income'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        £<span class="counter-value" data-target="{{ number_format(abs($stats['net_income']), 2) }}">
                                            {{ number_format($stats['net_income'], 2) }}
                                        </span>
                                    </h4>
                                    <div class="text-muted fs-12">
                                        Received - Expenses
                                    </div>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title {{ $stats['net_income'] >= 0 ? 'bg-soft-success' : 'bg-soft-danger' }} rounded fs-3">
                                        <i class="ri-line-chart-line {{ $stats['net_income'] >= 0 ? 'text-success' : 'text-danger' }}"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Statistics Row -->
            <div class="row">
                <!-- Total Landlords Card -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card card-animate">
                        <div class="card-body text-center">
                            <div class="avatar-sm mx-auto mb-3">
                                <span class="avatar-title bg-soft-primary rounded fs-2">
                                    <i class="ri-user-3-line text-primary"></i>
                                </span>
                            </div>
                            <h4 class="fs-22 fw-semibold">{{ $stats['total_landlords'] }}</h4>
                            <p class="text-muted mb-0">Landlords</p>
                        </div>
                    </div>
                </div>

                <!-- Total Properties Card -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card card-animate">
                        <div class="card-body text-center">
                            <div class="avatar-sm mx-auto mb-3">
                                <span class="avatar-title bg-soft-info rounded fs-2">
                                    <i class="ri-home-4-line text-info"></i>
                                </span>
                            </div>
                            <h4 class="fs-22 fw-semibold">{{ $stats['total_properties'] }}</h4>
                            <p class="text-muted mb-0">Properties</p>
                        </div>
                    </div>
                </div>

                <!-- Occupied Properties Card -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card card-animate">
                        <div class="card-body text-center">
                            <div class="avatar-sm mx-auto mb-3">
                                <span class="avatar-title bg-soft-success rounded fs-2">
                                    <i class="ri-user-follow-line text-success"></i>
                                </span>
                            </div>
                            <h4 class="fs-22 fw-semibold">{{ $stats['occupied_properties'] }}</h4>
                            <p class="text-muted mb-0">Occupied</p>
                        </div>
                    </div>
                </div>

                <!-- Vacant Properties Card -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card card-animate">
                        <div class="card-body text-center">
                            <div class="avatar-sm mx-auto mb-3">
                                <span class="avatar-title bg-soft-warning rounded fs-2">
                                    <i class="ri-home-2-line text-warning"></i>
                                </span>
                            </div>
                            <h4 class="fs-22 fw-semibold">{{ $stats['vacant_properties'] }}</h4>
                            <p class="text-muted mb-0">Vacant</p>
                        </div>
                    </div>
                </div>

                <!-- Total Tenants Card -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card card-animate">
                        <div class="card-body text-center">
                            <div class="avatar-sm mx-auto mb-3">
                                <span class="avatar-title bg-soft-primary rounded fs-2">
                                    <i class="ri-group-line text-primary"></i>
                                </span>
                            </div>
                            <h4 class="fs-22 fw-semibold">{{ $stats['total_tenants'] }}</h4>
                            <p class="text-muted mb-0">Tenants</p>
                        </div>
                    </div>
                </div>

                <!-- Compliance Alerts Card -->
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card card-animate">
                        <div class="card-body text-center">
                            <div class="avatar-sm mx-auto mb-3">
                                <span class="avatar-title bg-soft-danger rounded fs-2">
                                    <i class="ri-alert-line text-danger"></i>
                                </span>
                            </div>
                            <h4 class="fs-22 fw-semibold text-danger">{{ $stats['expiring_compliances'] }}</h4>
                            <p class="text-muted mb-0">Expiring Certs</p>
                            @if($stats['expired_compliances'] > 0)
                                <small class="text-danger">({{ $stats['expired_compliances'] }} expired)</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities Section -->
            <div class="row">
                <!-- Recent Tenants -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Recent Tenants</h4>
                            <div class="flex-shrink-0">
                                <a href="{{ route('alltenant') }}" class="btn btn-soft-primary btn-sm">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th scope="col">Tenant Name</th>
                                            <th scope="col">Property</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTenants as $tenant)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-grow-1">{{ $tenant->name }}</div>
                                                    </div>
                                                </td>
                                                <td>{{ $tenant->currentProperty->property_name ?? 'N/A' }}</td>
                                                <td>{{ $tenant->phone }}</td>
                                                <td>
                                                    @if ($tenant->status)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No tenants found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expiring Compliances -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Expiring Certificates</h4>
                            <div class="flex-shrink-0">
                                <a href="{{ route('allproperty-compliance') }}"
                                    class="btn btn-soft-warning btn-sm">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th scope="col">Certificate</th>
                                            <th scope="col">Property</th>
                                            <th scope="col">Expiry Date</th>
                                            <th scope="col">Days Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($expiringCompliances as $compliance)
                                            @php
                                                $daysLeft = \Carbon\Carbon::parse($compliance->expiry_date)->diffInDays(now());
                                                $badgeClass = $daysLeft <= 7 ? 'bg-danger' : ($daysLeft <= 15 ? 'bg-warning' : 'bg-info');
                                            @endphp
                                            <tr>
                                                <td>{{ $compliance->complianceType->name ?? 'N/A' }}</td>
                                                <td>{{ $compliance->property->property_name ?? 'N/A' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($compliance->expiry_date)->format('M d, Y') }}</td>
                                                <td>
                                                    <span class="badge {{ $badgeClass }}">{{ $daysLeft }} days</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No expiring certificates</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">Recent Transactions</h4>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0)" class="btn btn-soft-info btn-sm">View All</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th scope="col">Date</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Type</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTransactions as $transaction)
                                            @php
                                                $typeBadge = match($transaction->transaction_type) {
                                                    'received' => 'bg-success',
                                                    'due' => 'bg-warning',
                                                    'payable' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                                
                                                $statusBadge = $transaction->status ? 'bg-success' : 'bg-warning';
                                                $statusText = $transaction->status ? 'Paid' : 'Pending';
                                            @endphp
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($transaction->date)->format('M d, Y') }}</td>
                                                <td>
                                                    {{ $transaction->description }}
                                                    @if($transaction->expense)
                                                        <br><small class="text-muted">Expense: {{ $transaction->expense->name }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $typeBadge }}">
                                                        {{ ucfirst($transaction->transaction_type) }}
                                                    </span>
                                                </td>
                                                <td>£{{ number_format($transaction->amount, 2) }}</td>
                                                <td>
                                                    <span class="badge {{ $statusBadge }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No transactions found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection