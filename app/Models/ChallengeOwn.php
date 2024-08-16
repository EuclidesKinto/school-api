<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Flag;
use Illuminate\Notifications\Notifiable;

class ChallengeOwn extends Model
{
    use HasFactory;
    use Notifiable;

    protected static function booted()
    {
        static::created(function ($own) {

            $user_challenge_owns = ChallengeOwn::where([
                ['user_id', $own->user_id],
                ['challenge_id', $own->challenge_id],
            ])->count();

            $total_flags = $own->flags()->count();
            $own->progress = $user_challenge_owns / $total_flags * 100;

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
        'course_id',
        'points',
        'challenge_instance_id',
        'tournament_id',
        'challenge_id',
        'lesson_id',
        'created_at'
    ];

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
        return $this->challenge->flags();
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}