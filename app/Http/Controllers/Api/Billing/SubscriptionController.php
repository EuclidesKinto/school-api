<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Facades\IuguSubscription;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $plan = Plan::findOrFail($request->get('plan_id'));
        try {

            DB::beginTransaction();

            $subscription = new Subscription();

            $subscription->plan_id = $plan->id;
            $subscription->user_id = $user->id;
            $subscription->status = 'disabled';
            $subscription->expires_at = Carbon::now()->addMonth();

            $subscription->save();

            DB::commit();

            return $subscription;
        } catch (\Throwable $th) {

            Log::error("Erro ao criar inscrição no banco de dados", ['ctx' => $th]);

            DB::rollBack();

            return;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $subscription = $request->user()->subscription()->first();

        return $subscription;
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

    public function activate(Request $request)
    {

        $subscription = Subscription::find($request->get('id'));
        if ($subscription->user_id != $request->user()->id) {
            return response()->json(['message' => 'Você não tem permissão para ativar esta inscrição.'], 403);
        }

        try {

            $responseBody = IuguSubscription::activateSubscription($subscription->payment_gw_id);
        } catch (\Throwable $th) {

            Log::error("Erro ao ativar inscrição.", ['ctx' => $th]);

            return response()->json(['message' => 'Erro ao ativar inscrição [IG].'], 500);
        }

        try {

            DB::beginTransaction();

            $subscription->status = 'activated';

            $subscription->save();

            DB::commit();
        } catch (\Throwable $th) {

            Log::error("Erro ao ativar inscrição no banco de dados", ['ctx' => $th]);

            DB::rollBack();

            return response()->json(['message' => 'Erro ao ativar inscrição [DB].'], 500);
        }
    }

    public function suspend(Request $request)
    {

        $subscription = Subscription::findOrFail($request->get('id'));

        if ($request->user()->id != $subscription->user_id) {
            abort(403);
        }

        $subscription->renewable = 0;
        $subscription->save();

        return response()->json(['message' => 'Assinatura suspensa'], 200);
    }

    public function changePlan(Request $request)
    {
        $subscription = Subscription::find($request->get('subscription_id'));

        $plan = Plan::find($request->get('plan_id'));

        try {

            $responseBody = IuguSubscription::changePlanSubscription($subscription->payment_gw_id, $plan->identifier);
        } catch (\Throwable $th) {

            Log::error("Erro ao mudar plano da inscrição.", ['ctx' => $th]);

            return;
        }
    }
}