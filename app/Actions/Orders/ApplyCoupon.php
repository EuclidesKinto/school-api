<?php

namespace App\Actions\Orders;

use App\Exceptions\Orders\Discounts\CouponUsageNotAllowed;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Action;

class ApplyCoupon extends Action
{
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Execute the action and return a result.
     *
     * @todo: implements discount to order's full price
     * @return mixed
     */
    public function handle(Order $order, Coupon $coupon)
    {
        // procura um produto ao qual aplicar o cupom de desconto
        $product_to_apply_discount =  $order->products()->whereHas('coupons', function ($query) use ($coupon) {
            $query->where('code', $coupon->code);
        })->first();

        // caso não encontre um produto aplicável ao cupom, retorna erro
        if (!$product_to_apply_discount) {
            throw new CouponUsageNotAllowed('Este cupom não é aplicável aos itens do seu pedido.');
        }

        // checa se o cupom já foi aplicado ao pedido
        if ($order->discounts()->where('coupon_id', $coupon->id)->exists()) {
            throw new CouponUsageNotAllowed('Cupom já aplicado ao pedido.');
        }

        // checha se o usuário já utilizou este cupom no passado
        if ($order->user->orders()->where('status', '!=', Order::PENDING)->whereHas('discounts', function ($query) use ($coupon) {
            $query->where('coupon_id', $coupon->id);
        })->exists()) {
            throw new CouponUsageNotAllowed('Você já utilizou este cupom anteriormente.');
        }

        // checa se o usuário pode usar o cupom
        if (DB::table('coupon_user')->where('user_id', $order->user->id)->where('coupon_id', $coupon->id)->exists()) {
            return Discount::create([
                'order_id' => $order->id,
                'coupon_id' => $coupon->id,
                'amount' => Discount::calculateDiscount($coupon, $product_to_apply_discount->price),
            ]);
        }

        if ($coupon->code == Coupon::COUPON_TROPA) {
            throw new CouponUsageNotAllowed('Cupom exclusivo para membros da tropa do Web Hacking.');
        }

        if ($coupon->code == Coupon::COUPON_UHCLABS) {
            throw new CouponUsageNotAllowed('Cupom exclusivo para assinantes do UHCLabs.');
        }

        throw new CouponUsageNotAllowed('Usuário não está autorizado a utilizar este cupom de desconto.');
    }
}
