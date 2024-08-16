<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\MailingContact;
use Illuminate\Http\Request;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Support\Facades\Log;

class MailingContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($user)
    {

        try {

            //create on active campaign
            $contactInfo = ['contact' => [
                'email' => $user->email,
                'firstName' => $user->name,
            ]];

            $contactToCreate = ['body' => json_encode($contactInfo)];

            $createdContact = ActiveCampaign::createContact($contactToCreate);
        } catch (\Throwable $th) {

            Log::error("Erro ao adicionar usuário no active campaign [BACKOFFICE].", ['ctx' => $th]);

            //$user->delete($user->id);
            // se não conseguir criar o contato no active campaign, DELETA o usuário ? WTF?
            // se não conseguir deixar ele logar ué...

            return;
        }

        //create on local db
        $mailingContact = new MailingContact();

        $mailingContact->user_id = $user->id;
        $mailingContact->contact_id = $createdContact->contact->id;
        $mailingContact->provider = 'active_campaign';

        $mailingContact->save();

        return $mailingContact;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
        $mailingContact = MailingContact::findOrFail($id);

        $mailingContact->delete();
    }
}
