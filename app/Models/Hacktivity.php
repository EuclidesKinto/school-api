<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Hacktivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'headline',
        'type',
        'subject_id',
        'subject_type',
        'user_id',
        'is_fixed'
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function reactionsUserAuth()
    {
        return $this->morphOne(Reaction::class, 'reactable')
            ->where('user_id', Auth::user()->id);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('created_at', 'asc');
    }

    public function reactable()
    {
        return $this->morphTo();
    }
}
