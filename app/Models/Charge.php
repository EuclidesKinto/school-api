<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Charge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_id',
        'value_cents',
    ];

    public function invoice(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
