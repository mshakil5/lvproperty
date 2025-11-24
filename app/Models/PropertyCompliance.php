<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCompliance extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'compliance_type_id',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'renewal_date',
        'status',
        'notes',
        'document_path',
        'cost',
        'paid_by',
        'is_paid'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'cost' => 'decimal:2',
        'is_paid' => 'boolean'
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function complianceType()
    {
        return $this->belongsTo(ComplianceType::class);
    }

    public function landlord()
    {
        return $this->through('property')->has('landlord');
    }
}