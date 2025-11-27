<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationships
    public function tenancy()
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }

    // Scope for receivable transactions
    public function scopeReceivables($query)
    {
        return $query->where('transaction_type', 'due')->where('status', true);
    }

    // Scope for received payments
    public function scopeReceived($query)
    {
        return $query->where('transaction_type', 'received')->where('status', true);
    }

    public function property()
    {
        return $this->hasOneThrough(Property::class, Tenancy::class, 'id', 'id', 'tenancy_id', 'property_id');
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function propertyCompliance()
    {
        return $this->belongsTo(PropertyCompliance::class);
    }
}