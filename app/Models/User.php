<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Events\FlagPowned;
use App\Models\Own;
use App\Models\Achievement;
use App\Models\Instance;
use App\Models\ChallengeInstance;
use Spatie\Permission\Traits\HasRoles;
use Rinvex\Subscriptions\Traits\HasSubscriptions;
use App\Models\Address;
use App\Notifications\ResetPasswordNotification;
use App\Services\Tropa\Facades\Tropa;
use App\Traits\Mailing\HasMailing;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use HasSubscriptions;
    use SoftDeletes;
    use HasMailing;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'profile_photo_path',
        'cpf',
        'nick',
        'bio',
        'site',
        'lock_subscription',
        'last_login',
        'metadata',
        'subscription_id',
        // id da assinatura atual do usuario
        'payment_gw_id',
        'github_url',
        'linkedin_url',
    ];

    /**
     * Default attributes
     */
    protected $attributes = [
        'metadata' => '{}'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
        'metadata' => AsArrayObject::class
    ];

    /**
     * Retorna todos os cupons de desconto do usuário
     */
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }

    /**
     * relationships
     */
    public function owns()
    {
        return $this->hasMany(Own::class);
    }

    public function challengeOwns()
    {
        return $this->hasMany(ChallengeOwn::class);
    }

    /**
     * Retorna as conquistas do usuário
     */
    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    /**
     * Retorna as máquinas que o usuário já pegous flags
     */
    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'owns'); //->withPivot('flag_id');
    }

    public function blooders()
    {
        return $this->hasMany(Machine::class, 'blooder_id');
    }

    /**
     * Retorna máquinas que o usuário já completou
     */
    public function completedMachines()
    {
        return $this->belongsToMany(Machine::class, 'owns')->where('progress', 100);
    }


    public function instances()
    {
        return $this->hasMany(Instance::class);
    }

    public function instancesChallenge()
    {
        return $this->hasMany(ChallengeInstance::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->owns->sum('points');
    }

    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(Score::class);
    }

    public function scoreGeneral(): HasOne
    {
        return $this->hasOne(Score::class)->where('tournament_id', config('app.general_tournament'));
    }

    public function scoreCompetitive(): HasOne
    {
        return $this->hasOne(Score::class)->where('tournament_id', config('app.competitive_tournament'));
    }
    public function scoreHistories(): HasMany
    {
        return $this->hasMany(ScoreHistory::class);
    }

    public function certificates(): BelongsToMany
    {
        return $this->belongsToMany(Certificate::class);
    }

    public function certifications(): BelongsToMany
    {
        return $this->belongsToMany(Certification::class)->withPivot('user_report', 'comment', 'grade', 'approved', 'deadline', 'timeout');
    }


    public function getAvatarAttribute()
    {
        if ($this->profile_photo_path) {
            $this->profile_photo_path = str_replace("s96-c", "s1025-c", $this->profile_photo_path);
            return $this->profile_photo_path;
        } else {
            return "https://robohash.org/" . md5($this->email);
        }
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    public function getInstanceAttribute()
    {
        $instance = $this->instances()->select(['machine_id', 'is_active', 'shutdown', 'ip_address'])->active()->first();
        if ($instance) {
            return [
                'machine_id' => $instance->machine_id,
                'machine_name' => $instance->machine->name,
                'machine_type' => $instance->machine->type,
                'machine_avatar' => $instance->machine->photo_path,
                'ip_address' => $instance->ip_address,
                'is_active' => $instance->is_active,
                'shutdown' => $instance->shutdown
            ];
        } else {
            return null;
        }
    }

    /**
     * Retorna o perfil do usuário
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Retorna o endereço mais recente do usuário
     */
    public function address()
    {
        return $this->hasOne(Address::class)->latestOfMany();
    }

    /**
     * Retorna todos os endereços do usuário
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function paymentMethod(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function subscription(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscriptionPremium(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->whereRelation('plan', 'identifier', 'premium');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payer(): HasOne
    {
        return $this->hasOne(Payer::class);
    }

    /**
     * Envia notificação de reset de senhas aos usuários
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $frontendUrl = env('APP_FRONTEND_URL') . '/password/reset/?token=' . $token;

        $this->notify(new ResetPasswordNotification($frontendUrl));
    }

    /**
     * Retorna se o usuário possui um plano ativo
     * @return bool
     */
    public function is_premium()
    {

        if ($this->hasRole('admin')) {
            return true;
        }

        $freePlan = DB::table('plans')->where('identifier', 'freemium')->get();

        if ($this->subscription && $this->subscription[0]->plan_id != $freePlan[0]->id && $this->subscription[0]->status == 'active') {

            return true;
        } else {

            return false;
        }
    }


    /**
     * Retorna se o usuário é membro da tropa da blackhatagem
     * Oops! quer dizer... tropa do webhacking!
     * @return bool
     */
    public function is_trooper()
    {
        return Tropa::isMember($this->email);
    }

    /**
     * Retorna quantas maquinas o usuario completou
     * @return int
     */
    public function getOwnedMachinesAttribute()
    {
        return $this->owns()->where('progress', '=', '100')->count();
    }

    public function getOwnedChallengesAttribute()
    {

        return $this->challengeOwns()->where('progress', '=', '100')->count();
    }

    public function getOwnedTagsAttribute()
    {
        return $this->owns->flatMap(function ($own) {
            return $own->flag->tags->pluck('name');
        })
            ->unique();
    }

    public function getUserCompetitiveScoreboardPosition()
    {

        $tournamentScores = Tournament::with([
            'scores' => function ($query) {
                $query->select('scores.*')
                    ->selectRaw('(SELECT MAX(created_at) FROM score_histories WHERE score_id = scores.id AND type = "add") AS latest_history_created_at')
                    ->orderBy('value', 'desc')
                    ->orderByRaw('latest_history_created_at ASC');
            },
        ])->find(2);

        $tournamentScores = $tournamentScores->toArray();

        foreach ($tournamentScores['scores'] as $tournamentKey => $tournamentValue) {
            //$tournamentKey = user position on ranking
            if ($tournamentValue['user_id'] == $this->id) {

                $userPos = $tournamentKey;
                return $userPos + 1;
            }
        }

        //if user has no score in database returns last position;
        return array_key_last($tournamentScores['scores']) + 1;
    }

    public function getUserFirstBloodCount()
    {
        $totalFirstBloods = DB::table("machines")->where('blooder_id', $this->id)->count();

        return $totalFirstBloods;
    }

    public function getUserLessonsFinished()
    {
        return $this->lessons->count();
    }

    public function getUserModulesFinished()
    {
        $modules = Module::withCount([
            'lessons',
            'lessons as finished_lessons_count' => function ($query) {
                $query->whereHas('users', function ($subQuery) {
                    $subQuery->where('id', $this->id);
                });
            }
        ])->get();

        $finishedModules = $modules->filter(function ($module) {
            return $module->lessons_count == $module->finished_lessons_count;
        })->count();

        return $finishedModules;
    }

    public function getUserTimeline()
    {

        $timeline = array();

        $ownnedMachines = $this->owns()
            ->join('machines', 'owns.machine_id', '=', 'machines.id')
            ->join('flags', 'owns.flag_id', '=', 'flags.id')
            ->select('machines.name', 'flags.points', 'owns.created_at')
            ->get()->toArray();


        foreach ($ownnedMachines as $key => $value) {

            $value['type'] = 'ownned_machine';

            $ownnedMachines[$key] = $value;
        }

        $ownneChallenges = $this->challengeOwns()
            ->join('challenges', 'challenge_owns.challenge_id', '=', 'challenges.id')
            ->join('flags', 'challenge_owns.flag_id', '=', 'flags.id')
            ->select('challenges.name', 'flags.points', 'challenge_owns.created_at')
            ->get()->toArray();

        foreach ($ownneChallenges as $key => $value) {

            $value['type'] = 'ownned_challenge';

            $ownneChallenges[$key] = $value;
        }

        $checkedLessons = $this->lessons()
            ->select('lessons.title', 'lesson_user.created_at')
            ->get()->toArray();

        foreach ($checkedLessons as $key => $value) {

            $value['type'] = 'finished_lesson';

            $checkedLessons[$key] = $value;
        }

        $timeline = array_merge($timeline, $ownnedMachines, $ownneChallenges, $checkedLessons);

        $key_values = array_column($timeline, 'created_at');
        array_multisort($key_values, SORT_DESC, $timeline);

        return $timeline;
    }

    public function getPatent()
    {
        return $this->hasOne(Score::class)
            ->where('tournament_id', config('app.general_tournament'));
    }

    public function getCompetitivePatent()
    {
        return $this->hasOne(Score::class)
            ->where('tournament_id', config('app.competitive_tournament'));
    }

    public function getTagsOwned()
    {
        $user = $this;
        $user->load(['machines.flags.tags', 'challengeOwns.challenge.flags.tags']);

        $allTags = Tag::with(['machines', 'challenges'])->whereHas('machines')
            ->orWhereHas('challenges')
            ->get();

        $result = $allTags->map(function ($tag) use ($user) {
            $total = $tag->machines->count() + $tag->challenges->count();

            $ownedMachines = $user->machines()->whereHas('flags.tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })->count();

            $ownedChallenges = $user->challengeOwns()->whereHas('challenge.flags.tags', function ($query) use ($tag) {
                $query->where('tags.id', $tag->id);
            })->count();

            $owned = $ownedMachines + $ownedChallenges;

            return [
                'name' => $tag->name,
                'total' => $total,
                'owned' => $owned
            ];
        });

        $result = $result->sortByDesc('owned')->values();
        return $result->toArray();
    }

    public function getUserPatent($current_score): array
    {
        if (!$current_point = $current_score->value ?? 0) {
            return [
                'name' => 'Recruta',
                'icon' => 'recruta.png',
                'value' => 0
            ];
        }

        switch (intval($current_point / 2000)) {
            case 0:
                $patent['icon'] = 'recruta.png';
                $patent['name'] = 'Recruta';
                break;
            case 1:
                $patent['icon'] = 'soldado.png';
                $patent['name'] = 'Soldado';
                break;
            case 2:
                $patent['icon'] = 'cabo.png';
                $patent['name'] = 'Cabo';
                break;
            case 3:
                $patent['icon'] = '3-sargento.png';
                $patent['name'] = '3° sargento';
                break;
            case 4:
                $patent['icon'] = '2-sargento.png';
                $patent['name'] = '2° sargento';
                break;
            case 5:
                $patent['icon'] = '1-sargento.png';
                $patent['name'] = '1° sargento';
                break;
            case 6:
                $patent['icon'] = 'subtenente.png';
                $patent['name'] = 'Subtenente';
                break;
            case 7:
                $patent['icon'] = 'aspirante.png';
                $patent['name'] = 'Aspirante';
                break;
            case 8:
                $patent['icon'] = '2-tenente.png';
                $patent['name'] = '2° Tenente';
                break;
            case 9:
                $patent['icon'] = '1-tenente.png';
                $patent['name'] = '1° Tenente';
                break;
            case 10:
                $patent['icon'] = 'capitao.png';
                $patent['name'] = 'Capitão';
                break;
            case 11:
                $patent['icon'] = 'major.png';
                $patent['name'] = 'Major';
                break;
            case 12:
                $patent['icon'] = 'tenente-coronel.png';
                $patent['name'] = 'Tenente Coronel';
                break;
            case 13:
                $patent['icon'] = 'coronel.png';
                $patent['name'] = 'Coronel';
                break;
            case 14:
                $patent['icon'] = 'general-de-ataque.png';
                $patent['name'] = 'General de ataque';
                break;
            case 15:
                $patent['icon'] = 'general-de-furtividade.png';
                $patent['name'] = 'General de Furtividade';
                break;
            case 16:
                $patent['icon'] = 'general-de-inteligencia.png';
                $patent['name'] = 'general de Inteligencia';
                break;
            default:
                $patent['icon'] = 'marechal.png';
                $patent['name'] = 'Marechal';
                break;
        }
        $patent['value'] = $current_point;
        return $patent;
    }


}
