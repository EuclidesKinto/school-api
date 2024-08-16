<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\MostRecentScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'begin_at',
        'finish_at',
        'parent_id'
    ];

    protected $dates = ['deleted_at'];

    public function parent()
    {
        return $this->HasOne(Tournament::class, 'parent_id');
    }

    public function owns()
    {
        return $this->hasMany(Own::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }
}