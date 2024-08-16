<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Lesson extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'metadata',
        'active',
        'image_url',
        'video_unique_id',
        'links',
    ];

    protected $dates = ['deleted_at'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Tags relationship
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function quizzes()
    {
        return $this->morphMany(Quizz::class, 'quizzable');
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    public function questions()
    {
        return $this->hasManyThrough(Question::class, Quizz::class, 'quizzable_id', 'quizz_id')->where('quizzable_type', static::class);
    }

    public function hacktivities(): MorphMany
    {
        return $this->morphMany(Hacktivity::class, 'subject')->orderBy('created_at', 'desc');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function generateTempUrl()
    {

        return Storage::disk('s3')->temporaryUrl('courses/videos/' . $this->video_unique_id, now()->addMinutes(60));
    }

    public function getMostCommonLessonTags($lesson)
    {

        $lessonTags = array();

        $challenges = $lesson->challenges;

        foreach ($challenges as $challenge) {
            $flags = $challenge->flags;

            foreach ($flags as $flag) {

                $tags = $flag->tags;

                foreach ($tags as $tag) {

                    array_push($lessonTags, $tag->id);
                }
            }
        }

        return $lessonTags;
    }

    public function getRecommendedMachine($lesson)
    {
        $lesson->load(['tags', 'challenges.flags.tags']);
        $lessonTags = $lesson->tags->pluck('id')->flatten()->unique();
        $lessonChallengesTags = $lesson->challenges->pluck('flags.*.tags.*.id')->flatten()->unique();
        $lessonTags = $lessonTags->merge($lessonChallengesTags)->unique();
        if (! sizeOf($lessonTags)) {
            return Machine::inRandomOrder()->limit(5)->get();
        }
        return Machine::whereHas('flags.tags', function($query) use($lessonTags){
            $query->wherein('tags.id', $lessonTags);
        })->inRandomOrder()->limit(5)->get();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }


    public function old_getRecommendedMachine($lesson)
    {

        $lessonTags = $this->getMostCommonLessonTags($lesson);

        $values = array_count_values($lessonTags);

        arsort($values);

        $popular = array_slice(array_keys($values), 0, 3, true);

        $machine = new Machine;

        $machines = $machine->getMachineByTags($popular);

        return $machines;
    }

    public function getLessonStartedByUser($lessonId)
    {
        $user = Auth::user();

        return $user->lessons->contains($lessonId);
    }
}
