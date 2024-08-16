<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'details',
        'provider', // activecampaign, mailchimp, etc.
        'user_id',
        'contact_id',  // contact_id on mailing provider's system.
    ];

    public function subscriptions()
    {
        return $this->hasMany(MailingSubscription::class, 'contact_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lists()
    {
        return $this->belongsToMany(MailingList::class, 'mailing_subscriptions', 'contact_id', 'list_id');
    }

    public function getMailingContactByUser($user)
    {
        return MailingContact::where('user_id', $user->id)->first();
    }
}
