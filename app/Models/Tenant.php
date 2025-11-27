<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function tenancies()
    {
        return $this->hasMany(Tenancy::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}