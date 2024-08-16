<?php

namespace App\Traits\Mailing;

use App\Services\ActiveCampaign\Facades\ActiveCampaign;

trait HasMailing
{

    public function getActiveCampaignLists()
    {
        return ActiveCampaign::getLists();
    }

    public function getActiveCampaignContacts()
    {
        return ActiveCampaign::getContacts();
    }
}
