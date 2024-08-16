<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Facades\IuguCharge;
use App\Models\Charge;
use App\Models\PaymentMethod;
use App\Facades\IuguPaymentMethod;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Facades\IuguCustomer;
use Carbon\Carbon;
use Helper;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $installmentsNumber = null;

        if (!$user->payment_gw_id) {

            try {

                $customerToCreate = json_encode([
                    'email' => $user->email,
                    'name' => $user->name,
                    'cpf_cnpj' => $request->cpf
                ]);

                $userToCreate = IuguCustomer::createCustomer($customerToCreate);
                $user->payment_gw_id = $userToCreate->id;
                $user->save();
            } catch (\Throwable $th) {

                Log::error("Erro ao adicionar usuário no iugu.", ['ctx' => $th]);

                return response()->json(['message' => 'Erro ao criar cobrança [IG].'], 500);
            }
        }

        IuguCustomer::updateCustomer($user->payment_gw_id, json_encode([
            'cpf_cnpj' => $request->cpf,
        ]));

        $plan = Plan::findOrFail($request->get('plan_id'));

        $value = $plan->value_cents;
        if ($user->is_trooper()) {
            $value /= 2;
        }

        $json = [
            'items' => [
                'description' => $plan->identifier,
                'quantity' => 1,
                'price_cents' => (int) ($value)
            ],

            'restrict_payment_method' => true,
            'customer_id' => $user->payment_gw_id,
            'email' => $user->email,
            'months' => $request->get('months')
        ];

        if ($request->get('method') == 'bank_slip') {
            $paidWith = 'boleto';
            $json['items']['price_cents'] = $value;
            $json['months'] = 1;
            //check if user has payer
            if (!$user->payer) {
                return response()->json(['message' => 'Por favor adicione informações se quiser pagar via boleto.'], 500);
            }
            $json['payer'] = [
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
            ];
        }

        if ($request->get('method')) {

            $json['method'] = $request->get('method');
        }

        if ($request->get('card')) {

            $value = Helper::getInstallments($value, $request->get('months'));
            $json['items']['price_cents'] = (int) ($value);
            $paidWith = 'card';
            $installmentsNumber = $request->get('months');

            $cardInfo = $request->get('card');

            $params = json_encode([
                'token' => $cardInfo['token'],
                'description' => $cardInfo['description'],
                'set_as_default' => $cardInfo['set_as_default'],
                'item_type' => 'credit_card'
            ]);

            try {

                $responseBody = IuguPaymentMethod::createPaymentMethod($user->payment_gw_id, $params);

                if (PaymentMethod::where('payment_gw_user_id', $user->payment_gw_id)->exists()) {
                    PaymentMethod::where('payment_gw_user_id', $user->payment_gw_id)
                        ->update(['default' => 0]);
                }
            } catch (\Throwable $th) {

                Log::error("Erro ao criar forma de pagamento.", ['ctx' => $th]);
                Log::error($th->getMessage());

                return response()->json(['message' => 'Erro ao criar forma de pagamento [IG].'], 500);
            }

            try {

                DB::beginTransaction();

                $paymentMethod = new PaymentMethod();

                $paymentMethod->payment_method_id = $responseBody->id;
                $paymentMethod->payment_gw_user_id = $user->payment_gw_id;
                $paymentMethod->user_id = $user->id;
                $paymentMethod->default = $cardInfo['set_as_default'];
                $paymentMethod->brand = $responseBody->data->brand;
                $paymentMethod->holder_name = $responseBody->data->holder_name;
                $paymentMethod->display_number = $responseBody->data->display_number;
                $paymentMethod->bin = $responseBody->data->bin;
                $paymentMethod->year = $responseBody->data->year;
                $paymentMethod->month = $responseBody->data->month;

                $paymentMethod->save();

                DB::commit();

                $json['customer_payment_method_id'] = $paymentMethod->payment_method_id;

            } catch (\Throwable $th) {

                Log::error("Erro ao criar forma de pagamento no banco de dados", ['ctx' => $th]);

                IuguPaymentMethod::deletePaymentMethod($paymentMethod->payment_gw_user_id, $paymentMethod->payment_method_id);

                DB::rollBack();

                return response()->json(['message' => 'Erro ao criar forma de pagamento [HC].'], 500);
            }
        }

        $params = json_encode($json);

        //cria cobrança na iugu
        try {

            $responseBody = IuguCharge::createCharge($params);
        } catch (\Throwable $th) {

            Log::error("Erro ao criar cobrança.", ['ctx' => $th]);

            return response()->json(['message' => 'Erro ao criar cobrança [IG].'], 500);
        }



        if ($request->get('method') == 'bank_slip') {

            $status = "pending";
        } elseif ($responseBody->message == "Autorizado") {
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

        //cria a fatura
        $invoice = (new InvoiceController)->store($plan, (int) ($value), $responseBody->invoice_id, $status, $paidWith, $installmentsNumber);

        //cria a cobrança no db
        try {

            DB::beginTransaction();

            $charge = new Charge();

            $charge->user_id = $user->id;
            $charge->invoice_id = $invoice->id;
            $charge->value_cents = (int) ($value);

            $charge->save();

            DB::commit();
        } catch (\Throwable $th) {

            Log::error("Erro ao criar cobrança no banco de dados", ['ctx' => $th]);

            DB::rollBack();

            return response()->json(['message' => 'Erro ao criar cobrança [DB].'], 500);
        }

        if ($request->get('method') == 'bank_slip') {

            return response()->json(['message' => 'Cobrança criada com sucesso', 'url' => $responseBody->url, 'pdf' => $responseBody->pdf], 200);
        }

        if ($responseBody->message == "Autorizado") {

            return response()->json(['message' => 'Cobrança criada com sucesso, autorizado', 'url' => $responseBody->url], 200);
        }

        return response()->json(['message' => 'Cobrança criada com sucesso, transação negada', 'url' => $responseBody->url], 402);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function installments($planId)
    {
        $response = array();

        $plan = Plan::find($planId);

        $value = $plan->value_cents;

        $user = Auth::user();

        if ($user->is_trooper()) {

            $value = $value / 2;
        }

        $response = [
            'is_trooper' => $user->is_trooper(),
            'installments' => [
                '1' => number_format($value, 0, '', ''),
                '2' => number_format(($value / 2) + ($value * 0.036), 0, '', ''),
                '3' => number_format(($value / 3) + ($value * 0.036), 0, '', ''),
                '4' => number_format(($value / 4) + ($value * 0.036), 0, '', ''),
                '5' => number_format(($value / 5) + ($value * 0.036), 0, '', ''),
                '6' => number_format(($value / 6) + ($value * 0.036), 0, '', ''),
                '7' => number_format(($value / 7) + ($value * 0.041), 0, '', ''),
                '8' => number_format(($value / 8) + ($value * 0.041), 0, '', ''),
                '9' => number_format(($value / 9) + ($value * 0.041), 0, '', ''),
                '10' => number_format(($value / 10) + ($value * 0.041), 0, '', ''),
                '11' => number_format(($value / 11) + ($value * 0.041), 0, '', ''),
                '12' => number_format(($value / 12) + ($value * 0.041), 0, '', ''),
            ]
        ];

        return $response;
    }
}
