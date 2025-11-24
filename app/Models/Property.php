<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'property_name',
        'address',
        'city',
        'postcode',
        'property_type',
        'rent_amount',
        'representative_name',
        'representative_authorisation',
        'representative_emergency_contact',
        'technician_name',
        'technician_phone',
        'technician_email',
        'status',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
    ];

    public function landlord()
    {
        return $this->belongsTo(Landlord::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}