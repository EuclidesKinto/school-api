<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'value',
        'type', // flat or percentage
        'limit',
        'description',
        'cycles', // quantas vezes o desconto poderá ser aplicado na assinatura
        'is_active', // se o cupom está ativo ou não,
    ];

    const COUPON_TROPA = 'TROPA_DO_WEBHACKING'; // cupom de 20% de desconto para membros da TROPA DO WEBHACKING
    const COUPON_UHCLABS = 'UHCLABS_MIGRATION'; // cupom de 20 reais para assinantes do UHCLabs

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }


    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Discount::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
