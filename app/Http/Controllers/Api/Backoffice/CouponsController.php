<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CouponsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupon::query()->orderBy('id', 'DESC')->paginate(12);
        return response()->json([
            'coupons' => $coupons,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $coupon = $request->only(['code', 'type', 'value', 'is_active', 'cycles', 'limit', 'description']);
        $users = $request->only(['users']);
        $products = $request->only(['products',]);
        $coupon['is_active'] = Arr::get($coupon, 'is_active', false);
        $coupon = Coupon::create($coupon);
        if (count($users) > 0) {
            $coupon->users()->sync($users['users']);
        }
        if (count($products) > 0) {
            $coupon->products()->sync($products['products']);
        }

        return response()->json([
            'coupon' => $coupon,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $coupon = Coupon::findOrFail($id);
        return response()->json([
            'coupon' => $coupon,
        ]);
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
        $coupon = Coupon::findOrFail($id);
        $coupon->update($request->only(['code', 'type', 'value', 'is_active', 'cycles', 'limit', 'description']));

        $users = $request->only(['users']);
        $products = $request->only(['products',]);
        if (count($users) > 0) {
            $coupon->users()->sync($users['users']);
        }
        if (count($products) > 0) {
            $coupon->products()->sync($products['products']);
        }

        return response()->json([
            'coupon' => $coupon,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();
        return response()->json([], 204);
    }
}
