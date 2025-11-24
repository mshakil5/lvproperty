<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'document',
        'current_address',
        'previous_address',
        'bank_name',
        'account_number',
        'sort_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'reference_checked',
        'previous_landlord_reference',
        'personal_reference',
        'credit_score',
        'immigration_status',
        'right_to_rent_status',
        'right_to_rent_check_date',
        'status'
    ];

public function currentProperty()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function tenancies()
    {
        return $this->hasMany(Tenancy::class);
    }

    public function currentTenancy()
    {
        return $this->hasOne(Tenancy::class)->where('status', true)->latest();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}