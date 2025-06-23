<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'document_type',
        'document_number',
        'company_name',
        'company_ruc',
        'billing_address',
        'shipping_address'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function getFullNameAttribute()
    {
        return $this->company_name ? "{$this->name} ({$this->company_name})" : $this->name;
    }
}
