<?php

namespace App\Services\ActiveCampaign;

use App\Models\MailingContact;
use App\Models\MailingList;
use App\Models\MailingSubscription;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ActiveCampaignService
{

    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('ACTIVE_CAMPAIGN_URL'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Api-Token' => env('ACTIVE_CAMPAIGN_KEY'),
            ]
        ]);
        return $this;
    }

    public function getLists()
    {
        $response = $this->client->get('lists');
        $lists = json_decode($response->getBody()->getContents());
        return $lists;
    }

    public function getContact($contact_id)
    {
        $response = $this->client->get('contacts/' . $contact_id);
        $contact = json_decode($response->getBody()->getContents());
        return $contact;
    }

    public function getTotalContactsInList($params)
    {
        $response = $this->client->get('contacts', $params);
        $total = json_decode($response->getBody()->getContents())->meta->total;
        return $total;
    }

    public function getContacts($params)
    {
        $response = $this->client->get('contacts', $params);
        $contacts = json_decode($response->getBody()->getContents())->contacts;
        return $contacts;
    }

    public function createContact($params)
    {
        $response = $this->client->post('contacts', $params);
        $contact = json_decode($response->getBody()->getContents());
        return $contact;
    }

    public function deleteContact($params)
    {
        $response = $this->client->delete('contacts', $params);
        $contact = json_decode($response->getBody()->getContents());
        return $contact;
    }

    public function addContactToList($params)
    {
        $response = $this->client->post('contactLists', $params);
        $response = json_decode($response->getBody()->getContents());
        return $response;
    }

    public function getContactListMembership($contactId)
    {
        $response = $this->client->get('contacts/' . $contactId . '/contactLists');
        $response = json_decode($response->getBody()->getContents());
        return $response;
    }

    public function handleContactList($user, $listId, $status)
    {

        $mailingContact = new MailingContact;
        $mailingContact = $mailingContact->getMailingContactByUser($user);

        $mailingList = new MailingList;
        $activeCampaignListId = $mailingList->getListId($listId);

        switch ($status) {
            case 'any':
                $activeCampaignStatus = '-1';
                break;
            case 'unconfirmed':
                $activeCampaignStatus = '0';
                break;
            case 'active':
                $activeCampaignStatus = '1';
                break;
            case 'unsubscribed':
                $activeCampaignStatus = '2';
                break;
            case 'bounced':
                $activeCampaignStatus = '3';
                break;
            default:
                $activeCampaignStatus = '1';
        }

        $updateListInfo = ['contactList' => [
            'list' => $activeCampaignListId->list_id,
            'contact' => $mailingContact->contact_id,
            'status' => $activeCampaignStatus,
        ]];

        $updateListInfo = json_encode($updateListInfo);

        $listToUpdate = ['body' => $updateListInfo];

        try {

            $this->addContactToList($listToUpdate);
        } catch (\Throwable $th) {

            Log::error("Erro ao alterar o status do usuário no active campaign.", ['ctx' => $th]);
        }

        //update list status on db
        MailingSubscription::updateOrCreate(
            ['contact_id' => $mailingContact->id, 'list_id' => $listId],
            ['contact_id' =>  $mailingContact->id, 'list_id' => $listId, 'provider' =>  'active_campaign', 'status' => $status],
        );
    }
}
