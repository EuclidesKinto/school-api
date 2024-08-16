<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payer extends Model
{
    use HasFactory;

    protected $fillable = [
        'cpf_cnpj',
        'name',
        'phone_prefix',
        'phone',
        'email',
        'street',
        'number',
        'district',
        'city',
        'state',
        'zip_code',
        'complement'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
