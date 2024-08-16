<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Machine;
use App\Models\User;
use Carbon\Carbon;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'user_id',
        'is_active',
        'startup',
        'shutdown',
        'ip_address',
        'aws_instance_id',
        'remote_instance_id'
    ];

    protected $casts = [
        'startup' => 'datetime',
        'shutdown' => 'datetime'
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
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
