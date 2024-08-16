<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Facades\IuguPaymentMethod;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    public function index(Request $request)
    {

        $paymentMethod = $request->user()->paymentMethod()->get();

        return $paymentMethod;
    }

    public function store(Request $request)
    {

        $user = Auth::user();

        $params = json_encode([
            'token' => $request->get('token'),
            'description' => $request->get('description'),
            'set_as_default' => $request->get('set_as_default'),
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
            $paymentMethod->default = $request->get('set_as_default');
            $paymentMethod->brand = $responseBody->data->brand;
            $paymentMethod->holder_name = $responseBody->data->holder_name;
            $paymentMethod->display_number = $responseBody->data->display_number;
            $paymentMethod->bin = $responseBody->data->bin;
            $paymentMethod->year = $responseBody->data->year;
            $paymentMethod->month = $responseBody->data->month;

            $paymentMethod->save();

            DB::commit();

            return response()->json(['message' => 'Forma de pagamento criada com sucesso.', 'payment_method' => $paymentMethod], 201);
        } catch (\Throwable $th) {

            Log::error("Erro ao criar forma de pagamento no banco de dados", ['ctx' => $th]);

            IuguPaymentMethod::deletePaymentMethod($paymentMethod->payment_gw_user_id, $paymentMethod->payment_method_id);

            DB::rollBack();

            return response()->json(['message' => 'Erro ao criar forma de pagamento [HC].'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        if ($request->user()->id != $paymentMethod->user_id) {
            abort(403);
        }

        $params = json_encode([
            'description' => $request->get('description'),
            'set_as_default' => $request->get('set_as_default'),
        ]);

        try {

            IuguPaymentMethod::updatePaymentMethod($paymentMethod->payment_gw_user_id, $paymentMethod->payment_method_id, $params);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function destroy(Request $request, $id)
    {

        $paymentMethod = PaymentMethod::findOrFail($id);

        if ($request->user()->id != $paymentMethod->user_id) {
            abort(403);
        }

        try {

            IuguPaymentMethod::deletePaymentMethod($paymentMethod->payment_gw_user_id, $paymentMethod->payment_method_id);
        } catch (\Throwable $th) {

            Log::error("Erro ao criar forma de pagamento no banco de dados", ['ctx' => $th]);
        }

        $paymentMethod->delete();

        return response()->json(['message' => 'Forma de pagamento deletada com sucesso.'], 200);
    }
}
