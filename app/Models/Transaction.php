<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Order;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    const PENDING = 'pending';
    const FAILED = 'failed';
    const CANCELED = 'canceled';
    const PAID = 'paid';
    const CAPTURED = 'captured';
    const PROCESSING = 'processing';
    const OVERPAID = 'overpaid';
    const UNDERPAID = 'underpaid';

    protected $fillable = [
        'gateway_id',
        'amount',
        'status',
        'payer_id',
        'gateway',
        'user_id',
        'order_id',
        'payment_method_id',
        'details',
        'invoice_id',
        'charge_id',
    ];

    protected $attributes = [
        'details' => '{}',
        'gateway' => 'pagarme',
        'status' => 'pending'
    ];

    protected $casts = [
        'details' => 'object'
    ];

    /**
     * Retorna a última cobrança registrada para a transação
     */
    public function lastCharge()
    {
        return $this->hasOne(Charge::class)->latest();
    }

    /**
     * Retorna todas as cobranças registradas para a transação
     */
    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payer()
    {
        return $this->belongsTo(BillingProfile::class, 'payer_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
