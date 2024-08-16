<?php

namespace App\Console\Commands;

use App\Models\ChallengeOwn;
use App\Models\Flag;
use App\Models\OldScore;
use App\Models\Own;
use App\Models\Score;
use App\Models\ScoreHistory;
use App\Models\User;
use App\Models\Challenge;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateScoresUsers extends Command
{

    const TOURNAMENT_GENERAL = 1;
    const TOURNAMENT_COMPETITIVE = 2;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('calculated_scores', null)->get();
        $progressBar = $this->output->createProgressBar($users->count());
        foreach ($users as $user) {
            Auth::loginUsingId($user->id);
            $old = OldScore::where('email', $user->email)->get();
            if ($old) {
                $old->update(['user_id' => $user->id]);
            }
            $this->deleteAllScores($user->id);
            $old = OldScore::where([['imported', '!=', null], ['user_id', $user->id]])->update(['imported' => null]);
            $this->generateScore($user);
            if ($old) {
                $old->update(['imported' => true]);
            }
            $user->calculated_scores = true;
            $user->save();
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->output->writeln('');
    }

    protected function generateScore($user): void
    {
        $machine_owns = Own::with('machine')->where('user_id', $user->id)->get();
        $challenge_owns = ChallengeOwn::where('user_id', $user->id)->get();
        $old_score = OldScore::where('email', $user->email)->where('imported', null)->get();

        $scoresMachine = $this->generateScoreActual($machine_owns, 'machine');
        $scoresChallenge = $this->generateScoreActual($challenge_owns, 'challenge');
        $scoresOld = $this->generateOldScore($old_score);

        foreach ($scoresChallenge as $key => $challengeScore) {
            foreach ($scoresOld as $oldScore) {
                if (
                    $challengeScore['origin_type'] == $oldScore['origin_type'] &&
                    $challengeScore['origin_id'] == $oldScore['origin_id'] &&
                    $challengeScore['flag_id'] == $oldScore['flag_id']
                ) {
                    unset($scoresChallenge[$key]);
                }
            }
        }

        $allScores = collect($scoresMachine)
            ->merge($scoresChallenge)
            ->merge($scoresOld)
            ->sortBy('created_at');

        $userScores = [];
        $allScores = $allScores->map(function ($score) use (&$userScores) {
            $key = $score['user_id'] . '_' . $score['tournaments_id'];
            if (!isset($userScores[$key])) {
                $userScores[$key] = [
                    'value' => 0,
                ];
            }
            $score['previous_score'] = $userScores[$key]['value'] ?? 0;
            $userScores[$key]['value'] += $score['value'];
            $score['current_score'] = $userScores[$key]['value'];

            return $score;
        });

        $score_general = Score::updateOrCreate(
            ['user_id' => $user->id, 'tournament_id' => self::TOURNAMENT_GENERAL],
            ['value' => $userScores[$user->id . '_' . self::TOURNAMENT_GENERAL]['value'] ?? 0]
        );
        $score_competitive = Score::updateOrCreate(
            ['user_id' => $user->id, 'tournament_id' => self::TOURNAMENT_COMPETITIVE],
            ['value' => $userScores[$user->id . '_' . self::TOURNAMENT_COMPETITIVE]['value'] ?? 0]
        );

        $allScores = $allScores->map(function ($score) use (&$userScores, $score_general, $score_competitive) {
            if ($score['tournaments_id'] == self::TOURNAMENT_GENERAL) {
                $score['score_id'] = $score_general->id;
            }
            if ($score['tournaments_id'] == self::TOURNAMENT_COMPETITIVE) {
                $score['score_id'] = $score_competitive->id;
            }
            unset($score['tournaments_id']);
            return $score;
        });
        ScoreHistory::insert($allScores->toArray());

        if (!empty($allScores->toArray())) {
            $this->generateOwn($allScores->toArray());
        }
    }

    protected function generateOwn($allScores)
    {
        foreach ($allScores as $score) {

            if ($score['origin_type'] == 'App\Models\Machine') {

                $own = Own::where([
                    ['user_id', $score['user_id']],
                    ['machine_id', $score['origin_id']],
                    ['flag_id', $score['flag_id']]
                ])->first();

                if (!$own) {

                    $own = [
                        'flag_id' => $score['flag_id'],
                        'user_id' => $score['user_id'],
                        'points' => $score['value'],
                        'tournament_id' => self::TOURNAMENT_COMPETITIVE,
                        'machine_id' => $score['origin_id'],
                        'instance_id' => 1,
                        'created_at' => $score['created_at'],
                    ];
                    Own::create($own);
                } else {
                    if ($own->created_at > $score['created_at']) {

                        $ownData = [
                            'flag_id' => $score['flag_id'],
                            'user_id' => $score['user_id'],
                            'points' => $score['value'],
                            'tournament_id' => self::TOURNAMENT_COMPETITIVE,
                            'machine_id' => $score['origin_id'],
                            'instance_id' => 1,
                            'created_at' => $score['created_at'],
                        ];

                        $own->fill($ownData);

                        $own->save();
                    }
                }

            } else {

                $challengeOwn = ChallengeOwn::where([
                    ['user_id', $score['user_id']],
                    ['challenge_id', $score['origin_id']],
                    ['flag_id', $score['flag_id']]
                ])->first();

                if (!$challengeOwn) {
                    try {

                        $course = Challenge::find($score['origin_id'])->lesson->module->course;

                        $challengeOwn = [
                            'flag_id' => $score['flag_id'],
                            'user_id' => $score['user_id'],
                            'points' => $score['value'],
                            'tournament_id' => self::TOURNAMENT_GENERAL,
                            'challenge_id' => $score['origin_id'],
                            'course_id' => $course->id,
                            'challenge_instance_id' => 1,
                            'created_at' => $score['created_at'],
                        ];
                        ChallengeOwn::create($challengeOwn);
                    } catch (\Throwable $th) {

                        $challengeOwn = [
                            'flag_id' => $score['flag_id'],
                            'user_id' => $score['user_id'],
                            'points' => $score['value'],
                            'tournament_id' => self::TOURNAMENT_GENERAL,
                            'challenge_id' => $score['origin_id'],
                            'course_id' => 1,
                            'challenge_instance_id' => 1,
                            'created_at' => $score['created_at'],
                        ];
                        ChallengeOwn::create($challengeOwn);
                    }
                } else {
                    try {
                        $course = Challenge::find($score['origin_id'])->lesson->module->course;

                        if ($challengeOwn->created_at > $score['created_at']) {

                            $challengeOwnData = [
                                'flag_id' => $score['flag_id'],
                                'user_id' => $score['user_id'],
                                'points' => $score['value'],
                                'tournament_id' => self::TOURNAMENT_GENERAL,
                                'challenge_id' => $score['origin_id'],
                                'course_id' => $course->exists() ? null : $course->id,
                                'challenge_instance_id' => 1,
                                'created_at' => $score['created_at'],
                            ];

                            $challengeOwn->fill($challengeOwnData);

                            $challengeOwn->save();
                        }
                    } catch (\Throwable $th) {

                        if ($challengeOwn->created_at > $score['created_at']) {

                            $challengeOwnData = [
                                'flag_id' => $score['flag_id'],
                                'user_id' => $score['user_id'],
                                'points' => $score['value'],
                                'tournament_id' => self::TOURNAMENT_GENERAL,
                                'challenge_id' => $score['origin_id'],
                                'course_id' => 1,
                                'challenge_instance_id' => 1,
                                'created_at' => $score['created_at'],
                            ];

                            $challengeOwn->fill($challengeOwnData);

                            $challengeOwn->save();
                        }
                    }
                }
            }
        }
    }

    protected function generateScoreActual($owns, $type): array
    {
        $scores = [];
        foreach ($owns as $own) {

            if ($type == 'challenge') {
                $score = [
                    'tournaments_id' => 1,
                    'user_id' => $own->user_id,
                    'origin_type' => ($type == 'machine' ? 'App\Models\Machine' : 'App\Models\Challenge'),
                    'origin_id' => ($type == 'machine' ? $own->machine_id : $own->challenge_id),
                    'flag_id' => $own->flag_id,
                    'type' => 'add',
                    'value' => $own->points,
                    'created_at' => $own->created_at,
                ];
                $scores[] = $score;
            }

            if (!$own->machine) {
                continue;
            }
            $score = [
                'tournaments_id' => 1,
                'user_id' => $own->user_id,
                'origin_type' => ($type == 'machine' ? 'App\Models\Machine' : 'App\Models\Challenge'),
                'origin_id' => ($type == 'machine' ? $own->machine_id : $own->challenge_id),
                'flag_id' => $own->flag_id,
                'type' => 'add',
                'value' => $own->points,
                'created_at' => $own->created_at,
            ];
            $scores[] = $score;

            if ($own->machine->type == 'default') {
                $score = [
                    'tournaments_id' => 2,
                    'user_id' => $own->user_id,
                    'origin_type' => ($type == 'machine' ? 'App\Models\Machine' : 'App\Models\Challenge'),
                    'origin_id' => ($type == 'machine' ? $own->machine_id : $own->challenge_id),
                    'flag_id' => $own->flag_id,
                    'type' => 'add',
                    'value' => $own->points,
                    'created_at' => $own->created_at,
                ];
                $scores[] = $score;
            }

        }
        return $scores;
    }

    protected function generateOldScore($old_scores): array
    {
        $scores = [];
        foreach ($old_scores as $old_score) {
            $flag = Flag::find($old_score->flag_id);
            $score = [
                'tournaments_id' => 1,
                'user_id' => $old_score->user_id,
                'origin_type' => $old_score->origin_type,
                'origin_id' => $old_score->origin_id,
                'flag_id' => $old_score->flag_id,
                'type' => 'add',
                'value' => ($old_score->first_blood ? ($flag->points + ($flag->points * 0.1)) : $flag->points),
                'created_at' => $old_score->event_at,
            ];
            $scores[] = $score;
        }
        return $scores;
    }

    protected function deleteAllScores($user_id): bool
    {
        try {
            DB::transaction(function () use ($user_id) {
                ScoreHistory::where('user_id', $user_id)->delete();
                Score::where(['user_id' => $user_id])->update(['value' => 0]);
            });
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }
}
