<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\PropertyCompliance;
use App\Models\Transaction;
use App\Models\Expense;
use App\Models\Tenancy;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function dashboard()
    {
        if (Auth::check()) {
            $user = auth()->user();

            if ($user->user_type == '1') {
                return redirect()->route('admin.dashboard');
            } else if ($user->user_type == '0') {
                return redirect()->route('user.dashboard');
            }
        } else {
            return redirect()->route('login');
        }
    }

    public function adminHome()
    {
        // CORRECTED FINANCIAL CALCULATIONS
        
        // Total Receivable - Only pending rent dues (from tenants)
        // $totalReceivable = Transaction::where('transaction_type', 'due')
        //     ->where('status', false) // false = pending/unpaid
        //     ->whereNotNull('tenant_id') // Only tenant rent dues
        //     ->sum('amount');

        // // Total Received - Only completed rent payments (from tenants)
        // $totalReceived = Transaction::where('transaction_type', 'received')
        //     ->where('status', true) // true = completed/paid
        //     ->whereNotNull('tenant_id') // Only tenant rent payments
        //     ->sum('amount');

        // // Total Expenses - Only completed expense payments
        // $totalExpenses = Transaction::where('transaction_type', 'payable')
        //     ->where('status', true) // true = paid out
        //     ->whereNotNull('expense_id') // Only expense transactions
        //     ->sum('amount');

        // // Pending Expenses - Only pending expense payments
        // $pendingExpenses = Transaction::where('transaction_type', 'payable')
        //     ->where('status', false) // false = pending
        //     ->whereNotNull('expense_id') // Only expense transactions
        //     ->sum('amount');

        // // Compliance Receivables - Pending compliance payments
        // $complianceReceivable = Transaction::where('transaction_type', 'due')
        //     ->where('status', false)
        //     ->whereNotNull('property_compliance_id')
        //     ->sum('amount');

        // // Property Statistics
        // $stats = [
        //     'total_landlords' => Landlord::count(),
        //     'total_properties' => Property::count(),
        //     'occupied_properties' => Property::where('status', 'Occupied')->count(),
        //     'vacant_properties' => Property::where('status', 'Vacant')->count(),
        //     'maintenance_properties' => Property::where('status', 'Maintenance')->count(),
        //     'total_tenants' => Tenant::count(),
        //     'active_tenants' => Tenant::where('status', 1)->count(),
        //     'expiring_compliances' => PropertyCompliance::where('expiry_date', '<=', now()->addDays(30))
        //         ->where('expiry_date', '>=', now())
        //         ->where('status', 'Active')
        //         ->count(),
        //     'expired_compliances' => PropertyCompliance::where('expiry_date', '<', now())
        //         ->where('status', 'Active')
        //         ->count(),
            
        //     // Financial Stats - CORRECTED
        //     'total_receivable' => $totalReceivable + $complianceReceivable, // Both rent and compliance receivables
        //     'total_received' => $totalReceived,
        //     'total_expenses' => $totalExpenses,
        //     'pending_expenses' => $pendingExpenses,
        //     'net_income' => $totalReceived - $totalExpenses,
            
        //     // Transaction Counts - CORRECTED
        //     'pending_rents' => Transaction::where('transaction_type', 'due')
        //         ->where('status', false)
        //         ->whereNotNull('tenant_id')
        //         ->count(),
        //     'pending_compliance_payments' => Transaction::where('transaction_type', 'due')
        //         ->where('status', false)
        //         ->whereNotNull('property_compliance_id')
        //         ->count(),
        //     'total_compliance_receivable' => $complianceReceivable,
        // ];

        // // Recent activities
        // $recentTenants = Tenant::with(['currentProperty'])->latest()->take(5)->get();
        
        // $expiringCompliances = PropertyCompliance::with(['property', 'complianceType'])
        //     ->where('expiry_date', '<=', now()->addDays(30))
        //     ->where('expiry_date', '>=', now())
        //     ->where('status', 'Active')
        //     ->orderBy('expiry_date')
        //     ->take(5)
        //     ->get();

        // // Recent Transactions - CORRECTED to show meaningful transactions
        // $recentTransactions = Transaction::with(['tenant', 'landlord', 'propertyCompliance.complianceType', 'expense'])
        //     ->where(function($query) {
        //         $query->where('transaction_type', 'received')
        //               ->orWhere(function($q) {
        //                   $q->where('transaction_type', 'due')
        //                     ->where('status', false); // Only show pending dues
        //               })
        //               ->orWhere('transaction_type', 'payable');
        //     })
        //     ->latest()
        //     ->take(5)
        //     ->get();

        // // Top Properties by Rent
        // $topProperties = Property::with(['landlord'])
        //     ->where('rent_amount', '>', 0)
        //     ->orderBy('rent_amount', 'desc')
        //     ->take(5)
        //     ->get();

        return view('admin.pages.dashboard');
        // return view('admin.pages.dashboard', compact(
        //     'stats', 
        //     'recentTenants', 
        //     'expiringCompliances', 
        //     'recentTransactions',
        //     'topProperties'
        // ));
    }

    public function cleanDB()
    {
        $tables = [
            'compliance_types',
            'expenses',
            'incomes',
            'landlords',
            'properties',
            'property_compliances',
            'tenancies',
            'tenants',
            'transactions',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return "Cleaned.";
    }

    public function managerHome()
    {
        return 'manager';
    }

    public function userHome()
    {
        return 'user';
    }
}