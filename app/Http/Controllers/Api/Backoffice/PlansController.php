<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rinvex\Subscriptions\Models\Plan;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::all();
        return response()->json(compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $plan = $request->only([
            'name',
            'description',
            'price',
            'signup_fee',
            'invoice_period',
            'invoice_interval',
            'trial_period',
            'trial_interval', 'day',
            'sort_order',
            'currency'
        ]);

        $plan = Plan::create($plan);
        return response()->json(compact('plan'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plan = Plan::findOrFail($id);
        return response()->json(compact('plan'), 200);
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
        $plan = Plan::findOrFail($id);

        $planData = $request->only([
            'name',
            'description',
            'price',
            'signup_fee',
            'invoice_period',
            'invoice_interval',
            'trial_period',
            'trial_interval', 'day',
            'sort_order',
            'currency'
        ]);
        $plan->fill($planData);
        $plan->save();
        return response()->json(compact('plan'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
        return response()->json([], 204);
    }
}
