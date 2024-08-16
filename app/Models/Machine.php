<?php

namespace App\Models;

use App\Models\Instance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Flag;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\User;
use App\Models\Tag;
use App\ValueClasses\UserProgress;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Machine extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'ami_id',
        'name',
        'description',
        'os_name',
        'tournament_id',
        'type',
        'dificulty',
        'blooder_id',
        'creator_id',
        'active',
        'is_freemium',
        'photo_path',
        'release_at',
        'retire_at',
        'remote_resource_id'
    ];

    // type castings
    protected $casts = [
        'active' => 'boolean',
        'is_freemium' => 'boolean',
        'release_at' => 'datetime',
        'retire_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];


    protected $attributes = [
        'is_freemium' => true
    ];


    /**
     * Attachments relationship
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function achievements()
    {
        return $this->morphMany(Achievement::class, 'achievable');
    }

    /**
     * Retorna se a máquina é free
     */
    public function isFree()
    {
        return $this->is_freemium;
    }

    public function scopeDificulty($query, $dificulty)
    {
        return $query->where('dificulty', $dificulty);
    }

    public function scopeCompletedBy($query, $user)
    {
        return $query->whereHas('owns', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('progress', 100);
        });
    }

    public function scopeNotStartedBy($query, $user)
    {
        return $query->whereDoesntHave('owns', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }

    public function scopeStartedBy($query, $user)
    {
        return $query->whereHas('owns', function ($query) use ($user) {
            $query->where('progress', '<', 100)->where('user_id', $user->id)->groupBy('machine_id');
        });
    }

    public function scopeUserDoesntHaveProgress($query, $user_id)
    {
        return $query->whereDoesntHave('owns', function (Builder $q) use ($user_id) {
            $q->where('user_id', $user_id);
        });
    }

    public function scopeHasTag($query, $tag)
    {
        return $query->whereHas('flags', function ($flags) use ($tag) {
            $flags->whereHas('tags', function ($tags) use ($tag) {
                $tags->where('slug', 'LIKE', '%' . Str::slug($tag, '-') . '%');
            });
        });
    }

    /**
     * Instances relationship
     */
    public function instances()
    {
        return $this->hasMany(Instance::class);
    }

    public function instanceActive()
    {
        return $this->hasOne(Instance::class)->where('is_active', true);
    }

    /**
     * Flags relationship
     */
    public function flags()
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }

    /**
     * Retorna as tags
     */
    public function tags()
    {
        return $this->hasManyThrough(Tag::class, Flag::class)->where('taggable_type', static::class);
    }

    /**
     * Quizzes relationship
     */
    public function quizzes()
    {
        return $this->morphMany(Quiz::class, 'quizzable');
    }

    /**
     * Owns relationship
     */
    public function owns()
    {
        return $this->hasMany(Own::class);
    }

    /**
     * Retorna os usuários que já ownaram a máquina
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'owns'); //->withPivot('points');
    }

    /**
     * Machine creator relationship
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    /**
     * First Blood relationship
     */
    public function blood()
    {
        return $this->belongsTo(User::class, 'blooder_id', 'id');
    }

    public function scoreHistorable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTagsAttribute()
    {
        return $this->flags->pluck('tags')->flatten()->pluck('name')->unique()->toArray();
    }

    /**
     * Get current user total owns of this machine
     */
    public function getUserTotalOwns(User $user)
    {
        if (!$user) {
            $user = Auth::user();
        }
        return $this->owns()->byUser($user)->count();
    }

    public function getUserProgress(User $user)
    {
        return (int) $this->owns()->byUser($user)->max('progress');
    }

    /**
     * Tournament relationship
     */
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * certification_user relationship
     */
    public function certificationUser(): BelongsToMany
    {
        return $this->belongsToMany(CertificationUser::class);
    }

    /**
     * Total points attribute
     */
    public function getTotalPointsAttribute()
    {
        return $this->flags->sum('points');
    }

    /**
     * Total flags attribute
     */
    public function getTotalFlags()
    {
        return $this->flags->count();
    }

    public function getFirstBloodPoints()
    {
        return $this->total_points / 10;
    }

    public function setFirstBlooder($userId)
    {
        $this->blooder_id = $userId;

        $this->save();
    }

    public function getUserOwnedMachines()
    {

        $user = Auth::user();

        return $user->owns()->get();
    }

    public function isMachineCompleted($userId)
    {
        $totalFlags = $this->getTotalFlags();

        $totalOwns = Own::where('machine_id', $this->id)->where('user_id', $userId)->count();

        if ($totalFlags == $totalOwns) {
            return true;
        }

        return false;
    }

    public function getMachineByTags($tags, $reverse = false, $ids = null)
    {

        if (!sizeof($tags)){
            return Machine::all()->random(5);
        }

        if ($reverse) {

            if (array_key_exists(1, $tags) && array_key_exists(2, $tags)) {

                $machines = Machine::with('flags.tags')->where('id', '!=', $tags[0])->where('id', '!=', $tags[1])->where('id', '!=', $tags[2])->whereNotIn('id', $ids)->take(5)->get();
            } elseif (array_key_exists(1, $tags)) {

                $machines = Machine::with('flags.tags')->where('id', '!=', $tags[0])->where('id', '!=', $tags[1])->whereNotIn('id', $ids)->take(5)->get();
            } else {

                $machines = Machine::with('flags.tags')->where('id', '!=', $tags[0])->whereNotIn('id', $ids)->take(5)->get();
            }
        } else {

            if (array_key_exists(1, $tags) && array_key_exists(2, $tags)) {

                $machines = Machine::with('flags.tags')->where('id', $tags[0])->where('id', $tags[1])->where('id', $tags[2])->take(5)->get();
            } elseif (array_key_exists(1, $tags)) {

                $machines = Machine::with('flags.tags')->where('id', $tags[0])->where('id', $tags[1])->take(5)->get();
            } else {

                $machines = Machine::with('flags.tags')->where('id', $tags[0])->take(5)->get();
            }
        }
        ;

        //if there is no machine to return get a random one
        if ($machines->isEmpty()) {

            return $machines = Machine::all()->random(5);
        }

        return $machines;
    }
}
