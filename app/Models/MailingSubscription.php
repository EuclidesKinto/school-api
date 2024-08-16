<?php

namespace App\Models;

use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'list_id',
        'provider',
        'subscription_id', // id da subscription no provedor de mailing.
        'status', // subscribed, unsubscribed, etc.
    ];

    protected $attributes = [
        'status' => 'subscribed',
        'provider' => 'activecampaign',
    ];

    public static function scopeSubscribed($query)
    {
        return $query->where('status', 'subscribed');
    }

    public static function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    public function contact()
    {
        return $this->belongsTo(MailingContact::class);
    }

    public function list()
    {
        return $this->belongsTo(MailingList::class);
    }


    public function sync()
    {
        if (!$this->subscription_id) {
            $ac_sub = ActiveCampaign::createSubscription($contact_id, $list_id);
            $this->subscription_id = $ac_sub->id;
            $this->save();
        }
        // caso já exista lá, vc só atualiza as informações
    }
}
