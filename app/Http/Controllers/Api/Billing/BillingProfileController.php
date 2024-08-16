<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Billing\StoreBillingProfile;
use App\Http\Requests\Billing\UpdateBillingProfile;

class BillingProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Http\Illuminate\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $profiles = $request->user()->billingProfiles()->with('address', 'paymentMethod')->get();
        return response()->json(compact('profiles'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Billing\StoreBillingProfile $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBillingProfile $request)
    {
        $profile = $request->only(['name', 'surname', 'document', 'document_type', 'gender', 'email', 'birthdate', 'phones', 'metadata']);
        $profile = $request->user()->billingProfiles()->create($profile);
        $profile->load('address', 'paymentMethod');
        $profile->refresh();
        return response()->json(compact('profile'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $profile = $request->user()->billingProfiles()->findOrFail($id);
        $profile->load('address', 'paymentMethod');
        return response()->json(compact('profile'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Billing\UpdateBillingProfile $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBillingProfile $request, $id)
    {
        $profile = $request->user()->billingProfiles()->findOrFail($id);
        $newProfile = $request->only(['name', 'surname', 'document', 'document_type', 'gender', 'email', 'birthdate', 'phones', 'metadata']);
        $profile->fill($newProfile);
        $profile->save();
        $profile->load('address', 'paymentMethod');
        $profile->refresh();
        return response()->json(compact('profile'), 200);
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
        $profile = $request->user()->billingProfiles()->findOrFail($id);
        $profile->addresses()->delete();
        $profile->paymentMethods()->delete();
        $profile->delete();
        return response()->json([], 204);
    }
}
