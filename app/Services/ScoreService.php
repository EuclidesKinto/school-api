<?php

namespace App\Services;

use App\Events\FlagPowned;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScoreService
{
    public function addScore($tournament_id, $score, $firstBloodPoints = 0, $origin)
    {

        $user = Auth::user();

        $score += $firstBloodPoints;

        DB::table('scores')
            ->updateOrInsert(
                ['tournament_id' => $tournament_id, 'user_id' => $user->id],
                ['value' => DB::raw('value + ' . $score),]
            );

        $newScore = DB::table('scores')
            ->where('tournament_id', $tournament_id)
            ->where('user_id', $user->id)
            ->first();

        DB::table('score_histories')->insert([
            'score_id' => $newScore->id,
            'user_id' => $user->id,
            'origin_id' => $origin->id,
            'origin_type' => get_class($origin),
            'type' => 'add',
            'value' => $score,
            'previous_score' => $newScore->value - $score,
            'current_score' => $newScore->value,
            'created_at' => now()
        ]);

        //if tournament is competitive it also adds score to general tournament
        if ($tournament_id == 2) {

            DB::table('scores')
                ->updateOrInsert(
                    ['tournament_id' => 1, 'user_id' => $user->id],
                    ['value' => DB::raw('value + ' . $score),]
                );

            $newScore = DB::table('scores')
                ->where('tournament_id', 1)
                ->where('user_id', $user->id)
                ->first();

            DB::table('score_histories')->insert([
                'score_id' => $newScore->id,
                'user_id' => $user->id,
                'origin_id' => $origin->id,
                'origin_type' => get_class($origin),
                'type' => 'add',
                'value' => $score,
                'previous_score' => $newScore->value - $score,
                'current_score' => $newScore->value,
                'created_at' => now()
            ]);
        }

        event(new FlagPowned($user, $tournament_id));
    }

    public function subtractScore($tournament_id, $score, $origin, $user)
    {

        DB::table('scores')
            ->updateOrInsert(
                ['tournament_id' => $tournament_id, 'user_id' => $user->id],
                ['value' => DB::raw('value - ' . $score),]
            );

        $newScore = DB::table('scores')
            ->where('tournament_id', $tournament_id)
            ->where('user_id', $user->id)
            ->first();

        DB::table('score_histories')->insert([
            'score_id' => $newScore->id,
            'user_id' => $user->id,
            'origin_id' => $origin->id,
            'origin_type' => get_class($origin),
            'type' => 'subtract',
            'value' => $score,
            'previous_score' => $newScore->value + $score,
            'current_score' => $newScore->value,
            'created_at' => now()
        ]);
    }
}
