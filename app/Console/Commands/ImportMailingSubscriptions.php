<?php

namespace App\Console\Commands;

use App\Models\MailingContact;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\MailingSubscription;

class ImportMailingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:importmailingsubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import all mailing subscriptions to hackingclub db';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mailingLists = DB::table('mailing_lists')->where('provider', 'active_campaign')->get();

        $limit = 100; //numero de resultados por cada pagina

        $offset = 0; //o ponto de inicio para cada pagina (começando em 0)

        foreach ($mailingLists as $mailingList) {

            $mailingListQuery = ['query' =>
            [
                'listid' => $mailingList->list_id
            ]];

            $total  = ActiveCampaign::getTotalContactsInList($mailingListQuery);

            for ($offset = 0; $offset < $total; $offset += 100) {

                $contactsQuery = ['query' =>
                [
                    'listid' => $mailingList->list_id,
                    'limit' => $limit,
                    'offset' => $offset
                ]];

                $contacts  = ActiveCampaign::getContacts($contactsQuery);

                foreach ($contacts as $contact) {

                    $contactLists = ActiveCampaign::getContactListMembership($contact->id);

                    foreach ($contactLists as $lists) {

                        foreach ($lists as $list) {

                            if ($list->list == '32' || $list->list == '37') {

                                $mailingSubscription = new MailingSubscription;

                                $mailingContact = MailingContact::where('contact_id', $contact->id)->first();

                                switch ($list->status) {
                                    case -1:
                                        $status = 'any';
                                        break;
                                    case 0:
                                        $status = 'unconfirmed';
                                        break;
                                    case 1:
                                        $status = 'active';
                                        break;
                                    case 2:
                                        $status = 'unsubscribed';
                                        break;
                                    case 3:
                                        $status = 'bounced';
                                        break;
                                }

                                $mailingSubscription->contact_id = $mailingContact->id;
                                $mailingSubscription->provider = 'active_campaign';
                                $mailingSubscription->status = $status;
                                $mailingSubscription->subscription_id = $list->id;
                                $mailingSubscription->list_id = $mailingList->id;

                                $mailingSubscription->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
