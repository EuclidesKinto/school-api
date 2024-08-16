<?php

namespace App\Actions\Subscriptions;

use App\Actions\Orders\CreateCharge;
use App\Actions\Orders\CreateInvoice;
use App\Exceptions\Pagarme\OrderCreationException;
use App\Exceptions\Subscriptions\FailedToCreateRecurrenceException;
use App\Models\Order;
use App\Services\Pagarme\V2\Facades\Pagarme as PagarmeV2;
use App\Services\Webhook\Pagarme\Support\Models\Discount;
use App\Services\Webhook\Pagarme\Support\Models\Subscription as PagarmeSubscription;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Action;

class CreateBoletoSubscription extends Action
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
     * @return mixed
     */
    public function handle(Order $order)
    {
        $user = $order->user;
        $payer = $order->payer;
        $plan = $order->plan();

        // parametros da subscription que será enviada para o webservice da pagarme
        $params = [
            'code' => $order->code,
            'description' => "HackingClub Assinatura do {$plan->description}",
            'payment_method' => $order->payment_method,
            'plan_id' => $plan->pagarme_plan_id,
            'customer_id' => $payer->metadata['pagarme_id'],
        ];

        // adiciona descontos ao plano
        if ($order->discounts->count() > 0) {
            $params['discounts'] = $this->makeDiscountList($order->discounts);
        }

        $pagarme_sub = $this->createPagarmeSubscription($params);
        // atualiza o pedido
        $order->setStatus(Order::PENDING_PAYMENT);
        $order->refresh();
        // atualiza a subscription do usuário
        $subscription = $user->subscription;
        $subscription->gateway = 'pagarme';
        $subscription->gateway_id = $pagarme_sub->id;
        $subscription->raw = $pagarme_sub;
        $subscription->saveQuietly();

        // gera cobrança
        $charge = CreateCharge::make()->handle($order, $subscription);
        // gera fatura
        CreateInvoice::make()->handle($order, $subscription, $charge);

        $order->refresh();
        return $order;
    }

    private function makeDiscountList($discounts)
    {
        $discount_list = [];
        foreach ($discounts as $discount) {
            $obj = new Discount([
                'value' => $discount->coupon->value,
                'discount_type' => $discount->coupon->type,
            ]);
            $discount_list[] = $obj;
        }
        return $discount_list;
    }


    private function createPagarmeSubscription($params)
    {
        try {
            // cria a subscription na pagarme
            $subscription = PagarmeV2::subscribe(new PagarmeSubscription($params));
            return $subscription;
        } catch (FailedToCreateRecurrenceException $e) {
            throw new OrderCreationException($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            Log::debug(__CLASS__ . ':' . __LINE__ . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
