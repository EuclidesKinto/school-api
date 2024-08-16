<?php

namespace App\Actions\Subscriptions;

use App\Actions\Orders\ApplyCoupon;
use App\Models\Charge;
use App\Models\Coupon;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Stripe\Facades\Stripe;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Action;

class ImportStripeSubscription extends Action
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
    public function handle($subscription)
    {
        /**
         * 0. Criar billing profile do usuário
         * 1. Criar order localmente e discount
         * 2. Criar subscription localmente
         * 3. Criar payment method localmente
         * 4. criar invoice localmente
         * 5. criar charge localmente
         * 6. criar transaction localmente
         */
        $stripe_user = Stripe::customers()->retrieve($subscription->customer);
        $stripe_subscription_start = Carbon::createFromTimestamp($subscription->current_period_start);
        $stripe_subscription_end = Carbon::createFromTimestamp($subscription->current_period_end);

        $plan = Plan::where('identifier', 'premium')->first();
        $user = User::where('metadata->stripe_id', $subscription->customer)->orWhere('email', $stripe_user->email)->with('subscription')->first();
        // cria a subscription p/ usuário
        $user_sub = $user->subscription;
        if (is_null($user_sub)) {
            $user_sub = $user->newSubscription('main', $plan, $stripe_subscription_start);
        } else {
            $user_sub->changePlan($plan);
        }
        $user->subscription_id = $user_sub->id;
        $user->save();

        $coupon = Coupon::where('code', 'UHCLABS_MIGRATION')->first();
        if (!$user) {
            Log::debug('syncstripesubscription::Usuário não encontrado: ', [$stripe_user->email]);
            return 2;
        }
        if ($user->subscribedTo($plan->id) && ($user->subscription->is_paid && ($user->subscription->gateway_id == $subscription->id))) {
            return 3;
        }
        // associa o usuário ao cupom
        $coupon->users()->syncWithoutDetaching($user);
        // sincroniza informações da stripe
        $stripe_invoice = Stripe::invoices()->retrieve($subscription->latest_invoice);
        $subscription_status = $stripe_invoice->paid ? Order::PAID : Order::PENDING_PAYMENT;

        $payment_method = 'boleto';
        $stripe_charge = null;
        $stripe_transaction = null;
        $stripe_card = null;
        if ($stripe_invoice->collection_method != 'send_invoice') {
            $payment_method = 'credit_card';
            $stripe_charge = Stripe::charges()->retrieve($stripe_invoice->charge);
            $stripe_transaction = Stripe::transactions()->retrieve($stripe_charge->balance_transaction);
            // $stripe_card = Stripe::customers()->retrieveSource($stripe_user->id, $stripe_charge->payment_method);
            $stripe_card = data_get($stripe_charge, 'source');
        }

        // 1
        $payer = $user->billingProfiles()->firstOrCreate([
            'name' => Str::before(trim($user->name), ' '),
            'surname' => Str::after(trim($user->name), ' '),
            'email' => $user->email,
            'document' => $user->cpf,
            'document_type' => 'cpf',
        ]);

        // 2
        $order = $user->cart;
        $order->payer_id = $payer->id;
        $order->payment_method = $payment_method;
        $order->status = $subscription_status;
        $order->installments = 1;
        $order->save();
        $order->items()->firstOrCreate([
            'product_id' => $plan->product->id,
            'quantity' => 1,
        ]);

        // aplica o desconto dos usuários do uhclabs
        $discount = ApplyCoupon::make()->handle($order, $coupon);

        $discount->refresh();
        $order->refresh();

        // registra metodo de pagamento
        $pm = null;
        if ($payment_method == 'credit_card') {
            $pm = $payer->paymentMethods()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $payment_method,
                    'gateway_id' => $stripe_card->id,
                    'gateway' => 'stripe',

                ],
                [
                    'metadata' => [
                        'brand' => $stripe_card->brand,
                        'last_four_digits' => $stripe_card->last4,
                        'exp_month' => $stripe_card->exp_month,
                        'exp_year' => $stripe_card->exp_year,
                    ]
                ]
            );
        } else {
            $pm = $payer->paymentMethods()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'type' => $payment_method,
                    'gateway' => 'stripe',
                ],
                [
                    'metadata' => [
                        'brand' => '',
                        'last_four_digits' => '',
                        'exp_month' => '',
                        'exp_year' => '',
                    ]
                ]
            );
        }


        // 4 
        $invoice = Invoice::firstOrCreate(
            [
                'billing_profile_id' => $payer->id,
                'user_id' => $user->id,
                'status' => $subscription_status,
                'subtotal' => $plan->price,
                'total' => (float) number_format($stripe_invoice->total / 100, 2, '.', ''),
                'order_id' => $order->id,
                'billing_at' => Carbon::createFromTimestamp($stripe_invoice->created),
                'due_at' => Carbon::createFromTimestamp(($stripe_invoice->due_date ?: $stripe_invoice->created)),
                'total_discount' => $discount->amount,
                'total_increment' => 0,
                'gateway' => 'stripe',
                'gateway_invoice_id' => $stripe_invoice->id,
                'gateway_url' => $stripe_invoice->invoice_pdf,
                'metadata' => $stripe_invoice,
                'billing_period_start_at' => $stripe_subscription_start,
                'billing_period_end_at' => $stripe_subscription_end,
                'payment_method_id' => $pm->id,
                'code' => $stripe_invoice->number,
            ]
        );

        // 5
        $charge = Charge::firstOrCreate(
            [
                'amount' => (float) number_format($stripe_invoice->total / 100, 2, '.', ''),
                'order_id' => $order->id,
                'user_id' => $user->id,
                'payer_id' => $payer->id,
                'gateway' => 'stripe',
                'gateway_id' => optional($stripe_charge)->id,
                'status' => 'paid',
                'currency' => 'BRL',
                'due_at' => Carbon::createFromTimestamp($stripe_invoice->created),
                'count' => 1,
                'gateway_payer_id' => $stripe_user->id,
                'payment_method' => $payment_method,
                'payment_method_id' => $pm->id,
            ],
            [
                'details' => $stripe_charge

            ]
        );

        $transaction = Transaction::firstOrCreate(
            [
                'gateway_id' => optional($stripe_transaction)->id,
                'amount' => (float) number_format($stripe_invoice->total / 100, 2, '.', ''),
                'status' => $subscription_status,
                'gateway' => 'stripe',
                'user_id' => $user->id,
                'order_id' => $order->id,
                'payer_id' => $payer->id,
                'payment_method_id' => $pm->id,
            ],
            [
                'details' => $stripe_transaction
            ]
        );

        $charge->invoice_id = $invoice->id;
        $charge->gateway_id = $transaction->id;
        $transaction->charge_id = $charge->id;
        $transaction->invoice_id = $invoice->id;

        $charge->subscription_id = $user_sub->id;
        $charge->save();
        $transaction->save();

        if ($stripe_invoice->paid) {
            $user_sub->is_paid = true;
            $user_sub->gateway = 'stripe';
            $user_sub->gateway_id = $subscription->id;
            $user_sub->saveQuietly();
            return 1;
        }

        return 4;
    }
}
