<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Models\MailingSubscription;
use Illuminate\Http\Request;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Support\Facades\Log;
use App\Models\MailingList;

class MailingSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($mailingContact)
    {

        try {
            
            //update list status in active campaign
            $updateListInfo = ['contactList' => [
                'list' => '32',
                'contact' => $mailingContact->contact_id,
                'status' => '1',
            ]];

            $listToUpdate = ['body' => json_encode($updateListInfo)];

            ActiveCampaign::addContactToList($listToUpdate);
        } catch (\Throwable $th) {

            Log::error("Erro ao adicionar usuário a lista de contatos do active campaign", ['ctx' => $th]);

            $contactQuery = ['query' => ['id' => $mailingContact->contact_id,]];

            ActiveCampaign::deleteContact($contactQuery);

            $mailingContact->destroy($mailingContact->id);

            return;
        }

        //create list status on db
        $list = MailingList::where('list_id', '32')->first();

        $mailingSubscription = new MailingSubscription();

        $mailingSubscription->contact_id = $mailingContact->id;
        $mailingSubscription->provider = 'active_campaign';
        $mailingSubscription->status = 'Active';
        $mailingSubscription->list_id = $list->id;
        $mailingSubscription->save();

        return $mailingSubscription;
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
        $mailingSubscription = MailingSubscription::findOrFail($id);

        $mailingSubscription->delete();
    }
}
