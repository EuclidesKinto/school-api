<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $invoices = $request->user()->invoices()->with('payer', 'paymentMethod', 'order')->get();
        return response()->json(compact('invoices'), 200);
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
        $invoice = $request->user()->invoices()->with('payer', 'paymentMethod', 'order')->findOrFail($id);
        return response()->json(compact('invoice'), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function transactions(Request $request)
    {
        $transactions = $request->user()->transactions()->with('payer', 'paymentMethod', 'order')->get();
        return response()->json(compact('transactions'), 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function transaction(Request $request, $id)
    {
        $transaction = $request->user()->transactions()->with('payer', 'paymentMethod', 'order')->findOrFail($id);
        return response()->json(compact('transaction'), 200);
    }
}
