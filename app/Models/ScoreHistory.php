<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ScoreHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'score_id',
        'user_id',
        'origin_type',
        'origin_id',
        'type',
        'value',
        'previous_score',
        'current_score'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function score(): BelongsTo
    {
        return $this->belongsTo(Score::class);
    }

    public function machines(): MorphMany
    {
        return $this->morphMany(Machine::class, 'machineable');
    }

    public function challenges(): MorphMany
    {
        return $this->morphMany(Machine::class, 'challengeable');
    }
}