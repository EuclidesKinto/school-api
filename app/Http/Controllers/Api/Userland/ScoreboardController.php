<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\UserResource;
use App\Models\Tournament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ScoreboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($tournamentId)
    {
        // get and order user scores by value and date created
        $tournamentScores = Tournament::with([
            'scores' => function ($query) {
                $query->select('scores.*')
                    ->selectRaw('(SELECT MAX(created_at) FROM score_histories WHERE score_id = scores.id AND type = "add") AS latest_history_created_at')
                    ->orderBy('value', 'desc')
                    ->orderByRaw('latest_history_created_at ASC');
            },
        ])->find($tournamentId);

        $tournamentScores = $tournamentScores->toArray();

        $user = Auth::user();

        //get the authenticated user position in the scoreboard
        foreach ($tournamentScores['scores'] as $tournamentKey => $tournamentValue) {

            foreach ($tournamentValue as $scoreKey => $scoreValue) {

                if ($scoreKey == 'user_id' && $scoreValue == $user->id) {

                    $userPos = $tournamentKey;
                    $userInfo = $tournamentScores['scores'][$tournamentKey];
                    break;
                }
            }
            if (isset($userPos)) {
                break;
            }
        }

        $topTen = array_slice($tournamentScores['scores'], 0, 10);

        //check if authenticated user is out of the top 10, if he is then add him at the end of the array
        if (isset($userPos) && $userPos >= 10) {
            $topTen += [$userPos => $userInfo];
            $topTen[$userPos]['user_position'] = $userPos + 1;
        }

        //count users first bloods
        foreach ($topTen as $position => $user) {

            if ($position <= 10) {
                $topTen[$position]['user_position'] = $position + 1;
            }

            $topTen[$position]['first_blood_count'] = DB::table("machines")->where('blooder_id', $user['user_id'])->where('tournament_id', $tournamentId)->count();

            $user = User::find($topTen[$position]['user_id']);

            $user->load(['scoreGeneral','getCompetitivePatent']);

            $user = new UserResource($user);

            $topTen[$position]['user'] = $user;

            unset($topTen[$position]['user_id']);
        }

        return $topTen;
    }
}
