<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Landlord extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email', 
        'phone',
        'address',
        'current_address',
        'previous_address',
        'bank_name',
        'account_number',
        'sort_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'reference_checked',
        'credit_score',
        'previous_landlord_reference',
        'personal_reference',
        'right_to_rent_status',
        'right_to_rent_check_date',
        'service_type',
        'management_fee',
        'agreement_date',
        'agreement_duration',
        'agreement_due_date',
        'status'
    ];

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}