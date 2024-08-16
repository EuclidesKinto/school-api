<?php

namespace App\Jobs;

use App\Models\MailingContact;
use App\Models\MailingList;
use App\Models\MailingSubscription;
use App\Models\User;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateContactActiveCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            //create contact on active campaign
            $contactInfo = ['contact' => [
                'email' => $this->user->email,
                'firstName' => $this->user->name,
            ]];

            $contactInfo = json_encode($contactInfo);

            $contactToCreate = ['body' => $contactInfo];

            $createdContact = ActiveCampaign::createContact($contactToCreate);
        } catch (\Throwable $th) {

            Log::error("Erro ao adicionar usuário no active campaign. [JOB-CREATE-CONTACT]", ['ctx' => $th]);

            return;
        }

        DB::beginTransaction();

        try {

            //create contact on db
            $mailingContact = new MailingContact();

            $mailingContact->user_id = $this->user->id;
            $mailingContact->contact_id = $createdContact->contact->id;
            $mailingContact->provider = 'active_campaign';

            $mailingContact->save();
            DB::commit();
        } catch (\Throwable $th) {

            $contactQuery = ['query' => ['id' => $createdContact->contact->id,]];

            ActiveCampaign::deleteContact($contactQuery);

            DB::rollBack();

            return;
        }

        try {

            //update list status for active campaign

            $updateListInfo = ['contactList' => [
                'list' => '32',
                'contact' => $createdContact->contact->id,
                'status' => '1',
            ]];

            $updateListInfo = json_encode($updateListInfo);

            $listToUpdate = ['body' => $updateListInfo];

            ActiveCampaign::addContactToList($listToUpdate);
        } catch (\Throwable $th) {

            Log::error("Erro ao adicionar usuário no active campaign. [JOB-CREATE-AC]", ['ctx' => $th]);

            $contactQuery = ['query' => ['id' => $createdContact->contact->id,]];

            ActiveCampaign::deleteContact($contactQuery);

            $mailingContact->delete();

            return;
        }

        DB::beginTransaction();

        try {
            //create list status on db
            $list = MailingList::where('list_id', '32')->first();

            $mailingSubscription = new MailingSubscription();

            $mailingSubscription->contact_id = $mailingContact->id;
            $mailingSubscription->provider = 'active_campaign';
            $mailingSubscription->status = 'Active';
            $mailingSubscription->list_id = $list->id;
            $mailingSubscription->save();

            DB::commit();
        } catch (\Throwable $th) {

            $contactQuery = ['query' => ['id' => $createdContact->contact->id,]];

            ActiveCampaign::deleteContact($contactQuery);

            $mailingContact->delete();

            DB::rollBack();

            return;
        }
    }
}
