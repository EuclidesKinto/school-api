<?php

namespace App\Actions\Subscriptions;

use App\Actions\Orders\CreateCharge;
use App\Actions\Orders\CreateInvoice;
use App\Exceptions\Pagarme\FailedToCreateOrderException;
use App\Exceptions\Pagarme\OrderCreationException;
use App\Models\Order;
use App\Services\Webhook\Pagarme\Support\Models\Charge as PagarmeCharge;
use App\Services\Webhook\Pagarme\Support\Models\Order as PagarmeOrder;
use App\Services\Pagarme\V2\Facades\Pagarme as PagarmeV2;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Action;

class CreatePixSubscription extends Action
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
     * Cria um pedido (não recorrente na pagarme)
     * @todo implementar sistema de lembretes para pagamento da subscription
     * @see https://docs.pagar.me/reference#pedidos-1
     * @return mixed
     */
    public function handle(Order $order)
    {
        $user = $order->user;
        $payer = $order->payer;
        $plan = $order->plan();
        $payment_method = $order->paymentMethod;

        /**
         * Preenche os dados do pedido que serão enviados à pagarme
         */
        $params = [
            "code" => $order->code,
            "customer" => [
                "name" => $payer->full_name,
                "type" => "individual",
                "email" => $payer->email,
                "document" => $payer->document,
                "document_type" => $payer->document_type,
            ],
            // preenche os itens do pedido 
            "items" => [
                [
                    "amount" => $order->cents_total,
                    "description" => "Assinatura HackingClub $plan->description",
                    "code" => $order->code,
                    "quantity" => 1,
                    "pricing_scheme" => [
                        "price" => $order->cents_total,
                        "scheme_type" => "unit"
                    ]
                ]
            ],

            "payments" => [
                [
                    "payment_method" => "pix",
                    "pix" => [
                        "amount" => $order->cents_total,
                        "expires_in" => 3600,
                    ],
                ],
            ],
        ];

        // envia o pedido para o webservice da pagarme
        $pagarme_order = $this->createPagarmeOrder($params);
        // atualiza o status do pedido local com o retorno da API
        $order->setStatus($pagarme_order->status);
        // atualiza a subscription do usuário
        $subscription = $user->subscription;
        $subscription->gateway = 'pagarme';
        $subscription->gateway_id = $pagarme_order->id;
        $subscription->raw = $pagarme_order;
        $subscription->saveQuietly();
        // cria a cobrança localmente
        $pagarme_charge = new PagarmeCharge((array)$pagarme_order->charges[0]);
        $charge = CreateCharge::make()->handle($order, $subscription, $pagarme_charge);
        // gera a fatura e transação
        CreateInvoice::make()->handle($order, $subscription, $charge);
        $order->refresh();
        return $order;
    }



    private function createPagarmeOrder($params)
    {
        try {
            // cria a subscription na pagarme
            $order = PagarmeV2::subscribeByOrder($params);
            return $order;
        } catch (FailedToCreateOrderException $e) {
            throw new OrderCreationException($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            Log::debug(__CLASS__ . ':' . __LINE__ . ': ' . $e->getMessage());
            throw $e;
        }
    }
}
