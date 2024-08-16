<?php

namespace App\Models;

use App\Events\FlagPowned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Flag;
use Illuminate\Notifications\Notifiable;

class Own extends Model
{
    use HasFactory;
    use Notifiable;


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function ($own) {

            $user_owns = Own::where([
                ['user_id', $own->user_id],
                ['machine_id', $own->machine_id],
            ])->count();

            $total_flags = $own->flags()->count();
            $own->progress = $user_owns / $total_flags * 100;

            $own->saveQuietly();
        });
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $fillable = [
        'flag_id',
        'user_id',
        'points',
        'instance_id',
        'tournament_id',
        'machine_id',
        'progress',
        'created_at'
    ];

    /**
     * filters owns by the specified user_id
     * 
     * @param Builder $query
     * @param \App\Models\User $user
     * @return Builder
     */
    public function scopeByUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flag()
    {
        return $this->belongsTo(Flag::class);
    }

    public function flags()
    {
        return $this->machine->flags();
    }

    public function instance()
    {
        return $this->belongsTo(Instance::class);
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
