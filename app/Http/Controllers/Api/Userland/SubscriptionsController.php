<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Billing\SubscriptionResource;
use App\Http\Resources\Billing\SubscriptionsCollection;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subscriptions = $request->user()->subscriptions()->orderBy('id', 'DESC')->paginate();
        $subscriptions = new SubscriptionsCollection($subscriptions);
        return response()->json($subscriptions, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $subscription = $request->user()->subscriptions()->findOrFail($id);
        $subscription = new SubscriptionResource($subscription);
        return response()->json($subscription, 200);
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

    /**
     * Create a new Stripe Billing Portal's Session for the current user
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function current(Request $request)
    {
        $subscription = $request->user()->subscription;
        $subscription = new SubscriptionResource($subscription);
        return response()->json(['subscription' => $subscription, 'success' => true], 200);
    }

    public function cancel(Request $request)
    {
        $subscription = $request->user()->subscription;
        if ($subscription->canceled_at) {
            return response()->json(['message' => 'Esta assinatura já foi cancelada!', 'success' => false], 200);
        }
        $subscription->cancel();
        return response()->json(['message' => 'Assinatura cancelada com sucesso!', 'success' => true], 200);
    }
}
