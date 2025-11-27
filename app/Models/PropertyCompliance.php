<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCompliance extends Model
{
    use HasFactory;

    protected $guarded = [];

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
        return $this->belongsTo(Landlord::class);
    }
}