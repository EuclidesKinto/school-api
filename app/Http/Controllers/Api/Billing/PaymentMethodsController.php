<?php

namespace App\Http\Controllers\Api\Billing;

use App\Actions\Orders\CreateCard;
use App\Actions\Orders\DeleteCard;
use App\Http\Controllers\Controller;
use App\Http\Resources\Billing\BillingProfileResource;
use App\Http\Resources\Billing\PaymentMethodResource;
use App\Http\Resources\Billing\PaymentMethodsCollection;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paymentMethods = $request->user()->paymentMethods()->paginate();
        $paymentMethods = new PaymentMethodsCollection($paymentMethods);
        return response()->json($paymentMethods, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $payer = $request->user()->billingProfiles()->findOrFail($request->input('billing_profile_id'));
        $card = $request->input('card');
        $card = CreateCard::make()->handle($payer, $card);
        return response()->json(['payment_method' => new PaymentMethodResource($card), 'success' => true], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $paymentMethod = $request->user()->paymentMethods()->findOrFail($id);
        $paymentMethod = new PaymentMethodResource($paymentMethod);
        return response()->json(['payment_method' => $paymentMethod]);
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
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $pm = $request->user()->paymentMethods()->findOrFail($id);
        $payer = $pm->billingProfile;
        $sub = $request->user()->subscription;
        $sub_pm = $sub->charge->paymentMethod;
        if ($pm->is($sub_pm)) {
            // método de pagamento é o padrão da subscription
            return response()->json(['success' => false, 'message' => 'Não é possível remover o cartão de crédito padrão da assinatura. Insira outro cartão de crédito para que este possa ser removido.']);
        }
        DeleteCard::make()->handle($pm);
        $pm->delete();
        $payer->refresh();
        return response()->json(['success' => true, 'message' => 'Cartão de crédito removido com sucesso!']);
    }
}
