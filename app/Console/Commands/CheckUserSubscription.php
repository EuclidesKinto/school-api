<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\Billing\InvoiceController;
use App\Mail\SubscriptionExpired;
use App\Mail\SubscriptionExpiredPix;
use App\Mail\SubscriptionExpiredBoleto;
use App\Mail\SubscriptionRenewed;
use App\Facades\IuguInvoice;
use App\Facades\IuguCharge;
use App\Models\PaymentMethod;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\Charge;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Helper;


class CheckUserSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:check_user_subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if users subscription is still enabled';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $subscriptions = Subscription::all();

        foreach ($subscriptions as $subscription) {

            if (Carbon::now()->diffInDays($subscription->expires_at, false) == 10) {
                //send mail to user if subscription is about to expire
                $subscription->load(['user', 'user.invoices']);

                $lastInvoice = $subscription->user->invoices->where('status', 'paid')->sortByDesc('updated_at')->first();

                $user = $subscription->user;

                $plan = Plan::where('identifier', 'premium')->first();

                if ($lastInvoice->paid_with == 'pix') {

                    $plan = Plan::findOrFail($lastInvoice->plan_id);

                    $value = $plan->value_cents;
                    if ($user->is_trooper()) {
                        $value = $plan->value_cents / 2;
                    }

                    $paidWith = 'pix';
                    $status = 'pending';

                    $params = json_encode([
                        'items' => [
                            [
                                'description' => $plan->identifier,
                                'quantity' => 1,
                                'price_cents' => (int) $value
                            ],
                        ],
                        'ensure_workday_due_date' => true,
                        'payable_with' => 'pix',
                        'email' => $user->email,
                        'due_date' => Carbon::now()->addDays(3),
                        'fines' => false,
                        'ignore_canceled_email' => true,
                        'customer_id' => $user->payment_gw_id,
                        'ignore_due_email' => true,
                    ]);

                    try {

                        $responseBody = IuguInvoice::createInvoice($params);
                    } catch (\Throwable $th) {

                        Log::error("Erro ao criar fatura.", ['ctx' => $th]);
                    }

                    $invoice = (new InvoiceController)->store($plan, (int) ($value), $responseBody->id, $status, $paidWith, $installments = null, $user);

                    Mail::to($subscription->user->email)->send((new SubscriptionExpiredPix($subscription->expires_at, $responseBody->pix->qrcode, $responseBody->pix->qrcode_text, $plan->value_cents)));

                } elseif ($lastInvoice->paid_with == 'boleto') {

                    $plan = Plan::findOrFail($lastInvoice->plan_id);

                    $value = $plan->value_cents;
                    if ($user->is_trooper()) {
                        $value = $plan->value_cents / 2;
                    }

                    $paidWith = 'boleto';
                    $status = 'pending';

                    $json = [
                        'items' => [
                            'description' => $plan->identifier,
                            'quantity' => 1,
                            'price_cents' => $value
                        ],
                        'restrict_payment_method' => true,
                        'customer_id' => $user->payment_gw_id,
                        'email' => $user->email,
                        'months' => 1,
                        'payer' => [
                            'address' => [
                                'street' => $user->payer->street,
                                'number' => $user->payer->number,
                                'district' => $user->payer->district,
                                'city' => $user->payer->city,
                                'state' => $user->payer->state,
                                'zip_code' => wordwrap((string) $user->payer->zip_code, 5, "-", true),
                                'complement' => $user->payer->complement,
                            ],
                            'cpf_cnpj' => $user->payer->cpf_cnpj,
                            'name' => $user->payer->name,
                            'phone_prefix' => $user->payer->phone_prefix,
                            'phone' => $user->payer->phone,
                            'email' => $user->payer->email,

                        ],
                        'method' => 'bank_slip'
                    ];

                    $params = json_encode($json);

                    try {

                        $responseBody = IuguCharge::createCharge($params);
                    } catch (\Throwable $th) {

                        Log::error("Erro ao criar cobrança.", ['ctx' => $th]);
                    }

                    $status = "pending";

                    $invoice = (new InvoiceController)->store($plan, (int) ($value), $responseBody->invoice_id, $status, $paidWith, $installments = null, $user);

                    try {

                        DB::beginTransaction();

                        $charge = new Charge();

                        $charge->user_id = $user->id;
                        $charge->invoice_id = $invoice->id;
                        $charge->value_cents = $plan->value_cents;

                        $charge->save();

                        DB::commit();
                    } catch (\Throwable $th) {

                        Log::error("Erro ao criar cobrança no banco de dados", ['ctx' => $th]);

                        DB::rollBack();
                    }

                    Mail::to($subscription->user->email)->send((new SubscriptionExpiredBoleto($subscription->expires_at, $responseBody->url, $responseBody->pdf, $plan->value_cents)));

                } else {

                    Mail::to($subscription->user->email)->send((new SubscriptionExpired($subscription->expires_at, $plan->value_cents)));
                }

            } elseif (Carbon::now()->diffInDays($subscription->expires_at, false) == 0) {
                //auto renew if user paid with card and is renewable

                $installmentsNumber = 1;

                $subscription->load(['user', 'user.invoices']);

                $lastInvoice = $subscription->user->invoices->where('status', 'paid')->sortByDesc('updated_at')->first();

                $user = $subscription->user;

                if ($lastInvoice->paid_with == 'card' && $subscription->renewable == 1) {

                    $plan = Plan::findOrFail($lastInvoice->plan_id);

                    $installmentsNumber = $lastInvoice->installments;

                    $value = $plan->value_cents;
                    if ($user->is_trooper()) {
                        $value = $plan->value_cents / 2;
                    }

                    $paidWith = 'card';

                    $value = Helper::getInstallments($value, $installmentsNumber);

                    $paymentMethod = PaymentMethod::where('user_id', $user->id)->latest('created_at')->first();

                    $json = [
                        'items' => [
                            'description' => $plan->identifier,
                            'quantity' => 1,
                            'price_cents' => (int) ($value)
                        ],

                        'restrict_payment_method' => true,
                        'customer_id' => $user->payment_gw_id,
                        'email' => $user->email,
                        'months' => $lastInvoice->installments,
                        'customer_payment_method_id' => $paymentMethod->payment_method_id,
                    ];

                    $params = json_encode($json);

                    try {

                        $responseBody = IuguCharge::createCharge($params);
                    } catch (\Throwable $th) {

                        Log::error("Erro ao criar cobrança.", ['ctx' => $th]);

                        return response()->json(['message' => 'Erro ao criar cobrança [IG].'], 500);
                    }

                    if ($responseBody->message == "Autorizado") {
                        //cria a assinatura premium se for pago com cartão e der tudo certo
                        $subscription = Subscription::where('user_id', '=', $user->id)->first();

                        $subscription->plan_id = $plan->id;
                        $subscription->expires_at = Carbon::now()->addYear();
                        $subscription->started_at = Carbon::now();
                        $subscription->renewable = 1;
                        $subscription->save();

                        $status = "paid";
                    } else {

                        $status = "denied";
                    }

                    $invoice = (new InvoiceController)->store($plan, (int) ($value), $responseBody->invoice_id, $status, $paidWith, $installmentsNumber, $user);

                    try {

                        DB::beginTransaction();

                        $charge = new Charge();

                        $charge->user_id = $user->id;
                        $charge->invoice_id = $invoice->id;
                        $charge->value_cents = $value;

                        $charge->save();

                        DB::commit();
                    } catch (\Throwable $th) {

                        Log::error("Erro ao criar cobrança no banco de dados", ['ctx' => $th]);

                        DB::rollBack();
                    }

                    Mail::to($subscription->user->email)->send((new SubscriptionRenewed($subscription->expires_at->addYear(), $value, $installmentsNumber)));
                } elseif ($lastInvoice->status == 'paid') {

                    $plan = Plan::where('identifier', 'premium')->first();

                    Mail::to($subscription->user->email)->send((new SubscriptionRenewed($subscription->expires_at->addYear(), $value, $installmentsNumber)));
                }

            } elseif (Carbon::now()->diffInDays($subscription->expires_at, false) == 0 && $subscription->renewable == 1) {
                # code...
            } elseif (Carbon::now()->subDays(4)->gt($subscription->expires_at)) {
                //give 5 extra days to user before turning him freemium
                $plan = DB::table('plans')->where('identifier', 'freemium')->get();

                $subscription->update([
                    'plan_id' => $plan[0]->id,
                    'user_id' => $subscription->user_id,
                    'status' => 'active',
                    'expires_at' => Carbon::maxValue(),
                    'started_at' => Carbon::now(),
                    'renewable' => null,
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
