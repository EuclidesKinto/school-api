<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Challenge;
use App\Models\User;
use Carbon\Carbon;

class ChallengeInstance extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'user_id',
        'is_active',
        'startup',
        'shutdown',
        'ip_address',
        'docker_container_id',
        'remote_instance_id'
    ];

    protected $casts = [
        'startup' => 'datetime',
        'shutdown' => 'datetime'
    ];

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getMinutesLeftAttribute()
    {
        return "{$this->shutdown->diffInMinutes(Carbon::now())}";
    }

    // query scopes
    /**
     * returns only active machines
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeMustShutdown($query)
    {
        return $query->where([
            ['shutdown', '<=', Carbon::now()],
            ['is_active', '=', 1]
        ]);
    }
}
