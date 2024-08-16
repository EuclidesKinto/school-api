<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\Billing\StoreAddress;

class AddressesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->get();
        return response()->json(compact('addresses'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Billing\StoreAddress  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAddress $request)
    {
        $billing_profile = $request->input('billing_profile_id');
        $billing_profile = $request->user()->billingProfiles()->findOrFail($billing_profile);
        $address = $request->only(['line_1', 'line_2', 'state', 'city', 'zip_code', 'type',]);
        $address['billing_profile_id'] = $billing_profile->id;
        $address = $request->user()->addresses()->create($address);
        return response()->json(compact('address'), 201);
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
        $address = $request->user()->addresses()->findOrFail($id);
        return response()->json(compact('address'), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Billing\StoreAddress  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreAddress $request, $id)
    {
        $newAddress = $request->only(['line_1', 'line_2', 'state', 'city', 'zip_code', 'type']);
        $address = $request->user()->addresess()->findOrFail($id);
        $address->fill($newAddress);
        $address->save();
        $address->refresh();
        return response()->json(compact('address'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();
        return response()->json([], 204);
    }
}
