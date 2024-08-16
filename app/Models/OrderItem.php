<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'amount'
    ];

    /**
     * Retorna o pedido ao qual o item pertence
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Retorna o produto que está incluso neste item
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function plan()
    {
        return $this->product()->productable();
    }
}
