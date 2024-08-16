<?php

namespace App\Console\Commands;

use App\Models\MailingContact;
use App\Models\User;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportMailingContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:importmailingcontacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import all mailing contacts from active campaign';

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
        //rate limit de 5 request por sec

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

                    $mailingContact = new MailingContact();

                    $user = User::where('email', $contact->email)->first();

                    if ($user) {
                        $mailingContact->user_id = $user->id;
                    }
                    $mailingContact->contact_id = $contact->id;
                    $mailingContact->provider = 'active_campaign';

                    $mailingContact->save();
                }
            }
        }
    }
}
