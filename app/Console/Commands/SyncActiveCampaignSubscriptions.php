<?php

namespace App\Console\Commands;

use App\Models\MailingContact;
use App\Models\MailingList;
use App\Models\MailingSubscription;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncActiveCampaignSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:syncactivecampaignsubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all active campaign subscriptions (id and status) with hc db';

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
        $subscriptions = MailingSubscription::all();

        foreach ($subscriptions as $subscription) {

            $mailingContact = MailingContact::find($subscription->contact_id);

            $attempt = 0;
            $maxAttempt = 5;

            while ($attempt <= $maxAttempt) {

                try {

                    $response = ActiveCampaign::getContactListMembership($mailingContact->contact_id);

                    foreach ($response->contactLists as $list) {

                        if ($list->list == '32' || $list->list == '37') {

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

                            $mailingList = MailingList::where('list_id', $list->list)->first();

                            $subscription->where('list_id', $mailingList->id,)->where('contact_id', $mailingContact->id)->update(['subscription_id' => $list->id, 'status' => $status]);
                        }
                    }
                } catch (\Throwable $th) {

                    $errorCodes = ['401', '404', '500', '503', '504'];

                    if (in_array($th->getCode(), $errorCodes)) {

                        $attempt++;

                        sleep(2);

                        continue;
                    } else {

                        Log::error("Erro resgatar informações da inscrição do usuário. contato: " . $mailingContact->id, ['ctx' => $th]);
                    }
                }
                break;
            }
        }
    }
}
