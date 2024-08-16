<?php

namespace App\Models;

use App\Actions\Orders\ApplyCoupon;
use App\Actions\Orders\CreateOrderUpdate;
use App\Actions\Orders\ValidateInstallments;
use App\Actions\Orders\ValidateOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderItem;
use App\Models\OrderUpdate;
use Illuminate\Support\Facades\Log;
use App\Traits\HasPagarme;
use App\ValueClasses\OrderPaymentMethods;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasPagarme;

    protected $fillable = [
        'user_id',
        'status',
        'subtotal', // valor total dos itens do pedido
        'total', // valor total dos itens - descontos aplicados
        'payer_id',
        'installments',
        'payment_method', // [credit_card, boleto, pix]
        'code', // código único do pedido - usado para identificar o pedido nos gateways de pagamento
        'paid_at', // data em que o pedido foi pago
        'refunded_at', // data em que o pedido foi reembolsado
        'payment_method_id', // id do método de pagamento usado
    ];

    protected $casts = [
        'subtotal' => 'float',
        'total' => 'float',
        'installments' => 'integer',
        'paid_at' => 'datetime',
    ];

    protected $attributes = [
        'installments' => 1,
    ];

    /**
     * Loads relationship by default
     * 
     * @var array
     */
    protected $with = ['items'];

    const PENDING = 'checkout_pending';

    const PENDING_PAYMENT = 'payment_pending';

    const CANCELED = 'order_canceled';

    const FAILED = 'failed';

    const PAID = 'paid';

    const REFUNDED = 'refunded';

    const PAYMENT_FAILED = 'failed';

    const CLOSED = 'closed';

    const PROCESSING = 'processing';


    /**
     * Retorna o pedido mais recente com status pending_checkout
     * também conhecido como "carrinho", já que não foi processado ainda.
     */
    public function scopeWithStatus($query, $status)
    {
        $query->where('status', $status);
    }

    /**
     * retorna os cupons de desconto usados no pedido
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'discounts');
    }

    /**
     * Retorna os descontos aplicados ao pedido
     */
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    /**
     * Retorna todas as cobranças executadas no pedido
     */
    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    /**
     * Retorna a última cobrança do pedido
     */
    public function charge()
    {
        return $this->hasOne(Charge::class)->latestOfMany();
    }

    /**
     * Retorna a última cobrança registrada
     */
    public function lastCharge()
    {
        return $this->hasOne(Charge::class)->latest();
    }

    /**
     * Retorna o dono do pedido
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Retorna os itens do pedido
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Retorna a atualização mais recente do pedido
     */
    public function latestUpdate()
    {
        return $this->hasOne(OrderUpdate::class, 'order_id')->latestOfMany();
    }

    /**
     * Retorna todas as atualizações do pedido
     */
    public function updates()
    {
        return $this->hasMany(OrderUpdate::class);
    }

    /**
     * retorna a transação mais recente deste pedido
     * 
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class)->latestOfMany();
    }

    /**
     * Retorna todas as transações (e tentativas do pedido)
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * retorna o usuário que de fato pagou o pedido
     * 
     */
    public function payer()
    {
        return $this->belongsTo(BillingProfile::class, 'payer_id', 'id');
    }

    /**
     * Retorna todos os perfis de pagamento utilizados 
     * ao tentar pagar o pedido
     */
    public function payers()
    {
        return $this->hasManyThrough(BillingProfile::class, Transaction::class, 'payer_id', 'id');
    }

    /**
     * Retorna a última fatura do pedido
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }

    /**
     * Retorna as faturas do pedido
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Aplica um cupom de desconto ao pedido
     * @param Coupon $coupon
     * @return Discount
     */
    public function applyCoupon(Coupon $coupon)
    {
        return ApplyCoupon::make()->handle($this, $coupon);
    }


    /**
     * Define o método de pagamento do pedido
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->payment_method_id = $paymentMethod->id;
        $this->payment_method = OrderPaymentMethods::getMethodName($paymentMethod->type);
        $this->saveQuietly();
    }

    /**
     * Define o comprador do pedido
     */
    public function setPayer(BillingProfile $payer)
    {
        $this->payer_id = $payer->id;
        $this->saveQuietly();
    }

    /**
     * Define as parcelas do pedido
     */
    public function setInstallments($installments)
    {
        ValidateInstallments::make()->handle($this, $installments);
        $this->installments = $installments;
        $this->saveQuietly();
    }

    /**
     * Valida o pedido
     */
    public function validate()
    {
        ValidateOrder::make()->handle($this);
    }

    /**
     * Realiza a contagem de quantos planos tem na order
     */
    public function plansCount()
    {
        return $this->items()->whereHas('product', function ($query) {
            $query->whereHasMorph('productable', [Plan::class]);
        })->count();
    }

    /**
     * Identifica se o pedido é de uma subscription e 
     * retorna o plano selecionado
     */
    public function plan()
    {
        return $this->items()->whereHas('product', function ($query) {
            $query->whereHasMorph('productable', [Plan::class]);
        })->first()->product->productable;
    }

    /**
     * Retorna se o pedido está pago
     */
    public function isPaid()
    {
        return $this->status === self::PAID;
    }

    /**
     * Define os estados de um pedido
     */
    public function setStatus($status)
    {
        $status = Str::camel($status);
        if (method_exists($this, $status)) {
            return $this->{$status}();
        }
        Log::error("Pedido com status inexistente: ", ['pedido' => $this, 'status' => $status]);
        return true;
    }

    public function processing()
    {
        return CreateOrderUpdate::make()->processing($this);
    }

    public function active()
    {
        return $this->paid();
    }

    public function captured()
    {
        return $this->paid();
    }

    public function pending()
    {
        return $this->paymentPending();
    }

    public function paid()
    {
        return CreateOrderUpdate::make()->paid($this);
    }

    public function canceled()
    {
        return CreateOrderUpdate::make()->canceled($this);
    }

    public function refunded()
    {
        return CreateOrderUpdate::make()->refunded($this);
    }

    public function failed()
    {
        return $this->paymentFailed();
    }

    public function paymentFailed()
    {
        return CreateOrderUpdate::make()->paymentFailed($this);
    }

    public function paymentPending()
    {
        return CreateOrderUpdate::make()->paymentPending($this);
    }

    public function closed()
    {
        return CreateOrderUpdate::make()->closed($this);
    }

    /**
     * Accessors 
     */

    /**
     * Retorna o valor do pedido em centavos
     * 
     * @return int $cents_total
     */
    public function getCentsTotalAttribute()
    {
        return (int) bcmul($this->total, 100);
    }
}
