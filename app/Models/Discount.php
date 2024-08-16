<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'coupon_id',
        'order_id',
    ];


    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public static function calculateDiscount(Coupon $coupon, $valueToDiscount)
    {
        if ($coupon->type == 'flat') {
            return $coupon->value;
        } else {
            return $coupon->value * ($valueToDiscount / 100);
        }
    }
}
