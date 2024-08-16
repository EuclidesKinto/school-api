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

class UpdateContactListStatusActiveCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $mailingListId;
    public $status;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $mailingListId, $status)
    {
        $this->user = $user;
        $this->mailingListId = $mailingListId;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailingContact = MailingContact::where('user_id', $this->user->id)->first();

        $mailingSubscription = MailingSubscription::where('contact_id', $mailingContact->id)->first();

        switch ($this->status) {
            case 'any':
                $status = '-1';
                break;
            case 'unconfirmed':
                $status = '0';
                break;
            case 'active':
                $status = '1';
                break;
            case 'unsubscribed':
                $status = '2';
                break;
            case 'bounced':
                $status = '3';
                break;
        }

        $updateListInfo = ['contactList' => [
            'list' => $this->mailingListId,
            'contact' => $mailingContact->contact_id,
            'status' => $status,
        ]];

        $updateListInfo = json_encode($updateListInfo);

        $listToUpdate = ['body' => $updateListInfo];

        ActiveCampaign::addContactToList($listToUpdate);

        //update list status on db

        $list = MailingList::where('list_id', $this->mailingListId)->first();

        MailingSubscription::updateOrCreate(
            ['contact_id' => $mailingSubscription->contact_id, 'list_id' => $list->id],
            ['contact_id' =>  $mailingSubscription->contact_id, 'list_id' => $list->id, 'provider' =>  'active_campaign', 'status' => $this->status],
        );

        return;
    }
}
