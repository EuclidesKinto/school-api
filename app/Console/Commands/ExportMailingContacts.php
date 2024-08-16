<?php

namespace App\Console\Commands;

use App\Models\MailingContact;
use App\Models\MailingList;
use App\Models\MailingSubscription;
use App\Models\User;
use Illuminate\Console\Command;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;

class ExportMailingContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:exportmailingcontacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'export all mailing contacts to active campaign';

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

        foreach (User::all() as $user) {

            $contactsQuery = ['query' =>
            [
                'email' => $user->email,
            ]];

            $maxAttempts = 5;
            $attempts = 0;

            do {

                try {
                    $contact = ActiveCampaign::getContacts($contactsQuery);
                } catch (\Throwable $th) {
                    $attempts++;
                    sleep(2);
                    continue;
                }

                break;
            } while ($attempts < $maxAttempts);

            if ($user->is_premium()) {
                $listId = '37';
            } else {
                $listId = '32';
            }

            if (!$contact) {

                #cria o contato no active campaign

                $contactInfo = ['contact' => [
                    'email' => $user->email,
                    'firstName' => $user->name,
                ]];

                $contactInfo = json_encode($contactInfo);

                $contactToCreate = ['body' => $contactInfo];

                do {

                    try {
                        $createdContact = ActiveCampaign::createContact($contactToCreate);
                    } catch (\Throwable $th) {
                        $attempts++;
                        sleep(2);
                        continue;
                    }

                    break;
                } while ($attempts < $maxAttempts);

                $contactId = $createdContact->contact->id;
            } else {

                $contactId = $contact[0]->id;
            }

            #adiciona o contato a lista do active campaign

            $updateListInfo = ['contactList' => [
                'list' => $listId,
                'contact' => $contactId,
                'status' => '1',
            ]];

            $updateListInfo = json_encode($updateListInfo);

            $listToUpdate = ['body' => $updateListInfo];

            do {

                try {
                    ActiveCampaign::addContactToList($listToUpdate);
                } catch (\Throwable $th) {
                    $attempts++;
                    sleep(2);
                    continue;
                }

                break;
            } while ($attempts < $maxAttempts);

            #cria o contato no db mailingContact

            $mailingContact = MailingContact::firstOrNew(['user_id' =>  $user->id]);

            $mailingContact->user_id = $user->id;
            $mailingContact->contact_id = $contactId;
            $mailingContact->provider = 'active_campaign';

            $mailingContact->save();

            #cria o tipo de inscrição no db mailingSubscription
            if ($user->is_premium()) {
                $list = MailingList::where('list_id', '37')->first();
            } else {
                $list = MailingList::where('list_id', '32')->first();
            }

            $mailingSubscription = new MailingSubscription;

            $mailingSubscription->contact_id = $mailingContact->id;
            $mailingSubscription->provider = 'active_campaign';
            $mailingSubscription->status = 'active';
            $mailingSubscription->subscription_id = '';
            $mailingSubscription->list_id = $list->id;

            $mailingSubscription->save();
        }
    }
}
