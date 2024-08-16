<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Order;
use App\Models\OperationStatus;

class OrderUpdate extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'status',
        'description',
        'details'
    ];

    protected $attributes = [
        'details' => '{}'
    ];

    protected $casts = [
        'details' => 'object'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
