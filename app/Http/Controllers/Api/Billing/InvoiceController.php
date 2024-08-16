<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Facades\IuguInvoice;
use App\Facades\IuguCustomer;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $invoices = $request->user()->invoices()->paginate(10);
        return response()->json($invoices, 200);
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
    public function store($plan, $value, $invoiceId, $status = 'pending', $paidWith, $installments = null, $user = null)
    {

        if ($user == null) {

            $user = Auth::user();
        }

        try {

            DB::beginTransaction();

            $invoice = new Invoice();

            $invoice->user_id = $user->id;
            $invoice->status = $status;
            $invoice->value_cents = $value;
            $invoice->payment_gw_id = $invoiceId;
            $invoice->plan_id = $plan->id;
            $invoice->paid_with = $paidWith;
            $invoice->installments = $installments;

            $invoice->save();

            DB::commit();

            return $invoice;
        } catch (\Throwable $th) {

            Log::error("Erro ao criar fatura no banco de dados", ['ctx' => $th]);

            DB::rollBack();

            return;
        }
    }

    public function iuguInvoiceStore(Request $request)
    {
        $user = Auth::user();

        $plan = Plan::findOrFail($request->get('plan_id'));

        //cria se não existir a inscrição
        $subscription = Subscription::where('user_id', '=', $user->id)->first();
        if (!$subscription) {
            $subscription = (new SubscriptionController)->store($plan);
        }

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

            IuguCustomer::updateCustomer($user->payment_gw_id, json_encode([
                'cpf_cnpj' => $request->cpf,
            ]));

            $responseBody = IuguInvoice::createInvoice($params);
        } catch (\Throwable $th) {

            Log::error("Erro ao criar fatura.", ['ctx' => $th]);

            return response()->json(['message' => 'Erro ao criar cobrança'], 500);
        }

        //cria a invoice no db
        $this->store($plan, (int) ($value), $responseBody->id, $status, $paidWith, $installments = null);

        return response()->json(['message' => 'Cobrança criada com sucesso', 'pix' => $responseBody->pix->qrcode, 'text' => $responseBody->pix->qrcode_text], 200);
    }

    public function iuguRefund(Request $request)
    {

        $user = Auth::user();

        $invoice = Invoice::findOrFail($request->get('invoice_id'));

        if ($user->id != $invoice->user_id) {
            return response()->json(['message' => 'forbidden.'], 403);
        }

        if (Carbon::now()->diffInDays($invoice->updated_at) >= 7) {

            return response()->json(['message' => 'O reembolso só pode ser solicitado em até 7 dias após confirmação do pagamento'], 403);
        }

        try {

            $responseBody = IuguInvoice::refund($invoice->payment_gw_id);
        } catch (\Throwable $th) {

            Log::error("Erro ao criar fatura.", ['ctx' => $th]);

            return response()->json(['message' => 'Erro ao solicitar o reembolso'], 500);
        }

        if ($responseBody->status == "refunded") {

            $plan = DB::table('plans')->where('identifier', 'freemium')->get();

            $subscription = Subscription::where('user_id', '=', $invoice->user_id)->first();

            $subscription->plan_id = $plan[0]->id;
            $subscription->status = 'active';
            $subscription->expires_at = Carbon::maxValue();
            $subscription->started_at = Carbon::now();
            $subscription->renewable = null;

            $subscription->save();

            $invoice->status = 'refunded';
            $invoice->save();

            return response()->json(['message' => 'Reembolso solicitado com sucesso'], 200);
        } else {

            return response()->json(['message' => 'Erro ao solicitar o reembolso'], 500);
        }
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

    public function changeStatus(Request $request)
    {

        $invoice = Invoice::where('payment_gw_id', $request->get('data')['id'])->first();

        $invoice->status = $request->get('data')['status'];

        $invoice->save();

        return response()->json('ok');
    }

    public function fail()
    {
    }

    public function expire()
    {
    }

    public function reimbursement()
    {
    }
}
