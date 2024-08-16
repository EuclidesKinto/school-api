<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

use App\Models\Charge as ModelsCharge;
use App\Models\OperationStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use stdClass;

class Charge extends Model
{
    public string $id;
    public string $code;
    public string $status;
    public string $created_at;
    public string $updated_at;
    public string $due_at;
    public string $paid_at;
    public string $currency;
    public int $amount;
    public stdClass $invoice;
    public stdClass $customer;
    public stdClass $gateway_response;
    public stdClass $antifraud_response;
    public stdClass $last_transaction;

    public function created()
    {
        $charge = ModelsCharge::where('gateway_code', $this->code)->orderBY('id', 'DESC')->firstOrFail();
        $charge->load(['order', 'invoice', 'transaction', 'subscription']);
        $transaction = $charge->transaction;
        $invoice = $charge->invoice;
        $invoice->gateway_invoice_id = $this->invoice->id;
        $invoice->due_at = $this->due_at;
        $charge->gateway_id = $this->id;
        $charge->due_at = Carbon::createFromTimeString($this->due_at);
        $transaction->gateway_id = $this->last_transaction->id;
        $transaction->details = $this->last_transaction;
        $transaction->save();
        $charge->save();
        $invoice->save();
    }

    public function updated()
    {
        return true;
    }

    public function pending()
    {
        DB::beginTransaction();
        try {
            /**
             * Procura uma atualização de status do pedido adequada
             * para o retorno da API
             */
            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);
            $antifraud_score = data_get($this, 'antifraud_response.score');

            $transaction = $charge->transaction;
            $invoice = $charge->invoice;
            $invoice->gateway_invoice_id = $this->invoice->id;
            $charge->gateway_id = $this->id;
            $charge->due_at = Carbon::createFromTimeString($this->due_at);
            $transaction->gateway_id = $this->last_transaction->id;
            $transaction->details = $this->last_transaction;
            $transaction->save();
            $invoice->save();

            $charge->save();
            $charge->order->save();

            if ($antifraud_score == 'high') {
                $charge->order->updates()->create([
                    'status' => 'antifraud_reproved',
                    'description' => 'Pagamento recusado pelo antifraude',
                    'details' => data_get($this, 'antifraud_response'),
                ]);
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:paymentFailed:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function paymentFailed()
    {
        DB::beginTransaction();
        try {

            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);

            $transaction = $charge->transaction;
            $invoice = $charge->invoice;
            $invoice->gateway_invoice_id = $this->invoice->id;
            $charge->gateway_id = $this->id;
            $charge->due_at = Carbon::createFromTimeString($this->due_at);
            $transaction->gateway_id = $this->last_transaction->id;
            $transaction->details = $this->last_transaction;
            $transaction->save();
            $invoice->save();

            $charge->save();

            $antifraud_score = data_get($this, 'antifraud_response.score');

            if ($antifraud_score == 'high') {
                $charge->order->updates()->create([
                    'status' => 'antifraud_reproved',
                    'description' => 'Pagamento recusado pelo antifraude',
                    'details' => data_get($this, 'antifraud_response'),
                ]);
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:paymentFailed:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function antifraudApproved()
    {
        DB::beginTransaction();
        try {

            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);
            $charge->subscription->is_paid = true;
            $charge->save();
            $charge->subscription->save();
            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:antifraudApproved:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function antifraudPending()
    {
        DB::beginTransaction();
        try {

            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);
            $charge->save();
            $charge->order->updates()->create([
                'status' => 'antifraud_analysis',
                'description' => 'Transação em análise pelo antifraude',
                'details' => data_get($this, 'antifraud_response'),
            ]);

            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:antifraudPending:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function antifraudManual()
    {
        DB::beginTransaction();
        try {

            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);

            $charge->save();

            $charge->order->updates()->create([
                'status' => 'antifraud_manual',
                'description' => 'Transação em análise manual pelo antifraude.',
                'details' => data_get($this, 'antifraud_response'),
            ]);

            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:antifraudManual:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function antifraudReproved()
    {
        DB::beginTransaction();
        try {

            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);
            $antifraud_score = data_get($this, 'antifraud_response.score');

            $charge->save();

            if ($antifraud_score == 'high') {
                $charge->order->updates()->create([
                    'status' => 'antifraud_reproved',
                    'description' => 'Pagamento reprovado pelo antifraude',
                    'details' => data_get($this, 'antifraud_response'),
                ]);
            }
            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:antifraudReproved:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function paid()
    {
        DB::beginTransaction();
        try {
            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);
            $charge->subscription->is_paid = true;
            $charge->subscription->paid_at = Carbon::now();

            $charge->save();
            $charge->subscription->save();
            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:paid:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function refunded()
    {
        $status = $this->status;
        $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
        $charge->status = $status;
        $charge->order->setStatus($status);
        $charge->save();
    }

    public function processing()
    {
        DB::beginTransaction();
        try {

            $status = $this->status;
            $charge = ModelsCharge::where('gateway_code', $this->code)->with(['order', 'invoice', 'transaction', 'subscription'])->orderBY('id', 'DESC')->firstOrFail();
            $charge->status = $status;
            $charge->order->setStatus($status);

            $charge->save();

            DB::commit();
        } catch (ModelNotFoundException $e) {
            Log::debug("webhook:charge:processing:charge_not_found", ['code' => $this->code]);
            DB::rollBack();
        }
    }

    public function underpaid()
    {
        // logica para atualizar o status da cobrança
    }

    public function overpaid()
    {
        // logica para atualizar o status da cobrança
    }

    public function partialCanceled()
    {
        // logica para atualizar o status da cobrança
    }
}
