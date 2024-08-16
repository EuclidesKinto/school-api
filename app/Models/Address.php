<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use App\Scopes\MostRecentScope;

class Address extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'line_1',
        'line_2',
        'state',
        'city',
        'zip_code',
        'type',
        'user_id',
        'billing_profile_id',
        'country',
        'metadata'
    ];


    protected $casts = [
        'metadata' => AsArrayObject::class,
    ];

    protected $attributes = [
        'country' => 'BR',
        'metadata' => '{"pagarme_id": null}'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function billingProfile()
    {
        return $this->belongsTo(BillingProfile::class);
    }
}
