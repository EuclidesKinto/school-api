<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'name',
        'description',
        'list_id',
        'details',
    ];

    public function subscriptions()
    {
        return $this->hasMany(MailingSubscription::class, 'list_id');
    }

    public function getListId($id)
    {
        return MailingList::find($id);
    }
}
