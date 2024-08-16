<?php

namespace App\Traits\Mailing;

use App\Models\MailingList;
use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasMailing
{

    public function mailingContact(): HasOne
    {
        return $this->hasOne(MailingContact::class);
    }

    /**
     * Retorna as listas de mailing do usuario.
     */
    public function mailingSubscriptions(): HasManyThrough
    {
        return $this->hasManyThrough(MailingContact::class, MailingSubscription::class, 'contact_id', 'id', 'id', 'list_id');
    }

    /**
     * Checa se o usuário pode ou não ser inscrito em uma lista de mailing.
     * caso o status seja 'unsubscribed', o usuário não pode ser inscrito novamente,
     * pois ele optou por ser removido da lista anteriormente.
     * 
     * @param MailingList $list
     * @return boolean
     */
    public function canSubscribeToMailingList(MailingList $list): bool
    {
        return !$this->mailingSubscriptions()->where('list_id', $list->id)->exists();
    }

    /**
     * Inscreve o usuário em uma lista de mailing.
     * 
     * @param MailingList $list
     * @return Boolean
     */
    public function subscribeToMailingList(MailingList $mailing_list): bool
    {
        if ($this->canSubscribeToMailingList($mailing_list)) {
            $this->mailingSubscriptions()->create([
                'list_id' => $mailing_list->id,
                'status' => 'subscribed',
            ]);
            return true;
        }
        return false;
    }

    /**
     * Retorna o contato do mailing provider baseado no contact_id do usuário.
     * 
     * @param string $contact_id
     * @return 
     */
    public function getMailingContact($contact_id)
    {
        return ActiveCampaign::getContact($contact_id);
    }

    public function getMailingContactByEmail($email)
    {
        return ActiveCampaign::getContactByEmail($email);
    }

    public function createMailingContact($details)
    {
        return ActiveCampaign::createContact($details);
    }
}
