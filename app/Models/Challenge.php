<?php

namespace App\Models;

use App\Models\Flag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\Achievement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Challenge extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'lesson_id',
        'name',
        'description',
        'type',
        'container_image',
        'dificulty',
        'blooder_id',
        'tournament_id',
        'creater_id',
        'difficulty',
        'release_at'
    ];

   protected $dates = ['deleted_at', 'release_at'];

    public function flags()
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }

    public function tags()
    {
        return $this->hasManyThrough(Tag::class, Flag::class)->where('taggable_type', static::class);
    }

    public function quizzes()
    {
        return $this->morphMany(Quizz::class, 'quizzable');
    }

    public function scoreHistorable(): MorphTo
    {
        return $this->morphTo();
    }

    public function questions()
    {
        return $this->hasManyThrough(Question::class, Quizz::class, 'quizzable_id', 'quizz_id')->where('quizzable_type', static::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function blood()
    {
        return $this->belongsTo(User::class, 'blooder_id', 'id');
    }

    /**
     * Total flags attribute
     */
    public function getTotalFlags()
    {
        return $this->flags->count();
    }

    public function instances()
    {
        // isso vai precisar ser revisto o quanto antes!
        // a model instance deveria ter um relacionamento morph porque a machine e a challenge faz interface com ela

        return $this->hasMany(ChallengeInstance::class);
    }

    public function instanceActive()
    {
        return $this->hasOne(ChallengeInstance::class)->where('is_active', 1);
    }


    public function achievements()
    {
        return $this->morphMany(Achievement::class, 'achievable');
    }

    public function getUserChallengeStatus($userId = null)
    {

        if ($userId == null) {

            $userId = Auth::user()->id;
        }

        $user = User::find($userId);

        $owns = DB::table("challenge_owns")->where([["user_id", "=", $user->id], ["challenge_id", "=", $this->id]])->get()->toArray();

        if ($owns) {

            $this->statusCompletion = 'completed';
        } else {

            $this->statusCompletion = 'not-started';
        }

        return $this->statusCompletion;
    }

    public function getFirstBloodPoints()
    {
        $flags = $this->flags()->get();

        $totalPoints = 0;

        foreach ($flags as $flag) {
            $totalPoints += $flag->points;
        }

        $firstBloodPoints = $totalPoints / 10;

        return $firstBloodPoints;
    }

    public function setFirstBlooder($userId)
    {
        $this->blooder_id = $userId;

        $this->save();
    }

    public function isChallengeCompleted($userId)
    {

        $totalFlags = $this->getTotalFlags();

        $totalOwns = ChallengeOwn::where('challenge_id', $this->id)->where('user_id', $userId)->count();

        if ($totalFlags == $totalOwns) {

            return true;
        }

        return false;
    }
}
