<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificationUser extends Model
{
    use HasFactory;

    protected $table = 'certification_user';

    protected $fillable = [
        'certification_user_id',
        'url',
        'validation_id'
    ];

    public function machines(): BelongsToMany
    {
        return $this->belongsToMany(Machine::class);
    }

    public function certification(): BelongsTo
    {
        return $this->belongsTo(Certification::class);
    }
}
