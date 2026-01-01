<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['remaining_due', 'is_rent'];

    protected $casts = [
        'received_ids' => 'array',
    ];

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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Total received for this due (sum of all related payments)
    public function getReceivedAmountAttribute($value)
    {
        return $value ?? 0;
    }

    // Remaining due for this transaction
    public function getRemainingDueAttribute()
    {
        return max(0, $this->amount - $this->received_amount);
    }

    // For view: all dues linked to this received transaction
    public function paidDues()
    {
        return $this->hasMany(Transaction::class, 'received_ids'); // JSON handled in controller
    }

    public function income()
    {
        return $this->belongsTo(Income::class);
    }

    public function getIsRentAttribute()
    {
        return strtolower($this->income?->name ?? '') === 'rent';
    }
}