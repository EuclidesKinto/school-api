<?php

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Models\Payer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $subscription = $request->user()->payer()->paginate(10);

        return $subscription;
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

        $payer = new Payer();

        $payer->user_id = $user->id;
        $payer->cpf_cnpj = $request->cpf_cnpj;
        $payer->name = $request->name;
        $payer->phone_prefix = $request->phone_prefix;
        $payer->phone = $request->phone;
        $payer->email = $user->email;
        $payer->street = $request->street;
        $payer->number = $request->number;
        $payer->district = $request->district;
        $payer->city = $request->city;
        $payer->state = $request->state;
        $payer->zip_code = $request->zip_code;
        $payer->complement = $request->complement;

        $payer->save();

        return response()->json(['message' => 'Informações adicionais criadas com sucesso', 'payer_info' => $payer], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

        $payer = Payer::findOrFail($id);

        if ($request->user()->id != $payer->user_id) {
            abort(403);
        }

        $payer->cpf_cnpj = $request->cpf_cnpj;
        $payer->name = $request->name;
        $payer->phone_prefix = $request->phone_prefix;
        $payer->phone = $request->phone;
        $payer->email = $request->email;
        $payer->street = $request->street;
        $payer->number = $request->number;
        $payer->district = $request->district;
        $payer->city = $request->city;
        $payer->state = $request->state;
        $payer->zip_code = $request->zip_code;

        $payer->save();

        return response()->json(['message' => 'Informações adicionais atualizadas com sucesso'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        $payer = Payer::findOrFail($id);

        if ($request->user()->id != $payer->user_id) {
            abort(403);
        }

        $payer->delete();

        return response()->json(['message' => 'Informações adicionais deletadas com sucesso'], 200);
    }
}
