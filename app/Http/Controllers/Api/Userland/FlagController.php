<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Userland\AchievementResource;
use App\Models\Achievement;
use App\Models\User;
use App\Models\Own;
use App\Models\ChallengeOwn;
use App\Models\Machine;
use App\Models\Challenge;
use App\Models\Tournament;
use App\Services\ScoreService;
use Illuminate\Support\Facades\DB;

class FlagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function own($flag, $resource)
    {

        $user = auth()->user();

        if (!$user->is_premium() && !$resource->is_freemium) {
            return response()->json([
                "success" => false,
                "message" => 'Você precisa ser premium para enviar flags!',
            ]);
        }

        $flag = $resource->flags()->where('flag', $flag)->first();

        if ($flag) {

            if ($resource instanceof Machine) {

                $instance = $user->instances()->active()->firstOrFail();

                $exists = Own::where([
                    ['flag_id', $flag->id],
                    ['user_id', $user->id]
                ])->first();

                if (!$exists) {

                    DB::beginTransaction();
                    $own = (new Own)->sharedLock()->firstOrCreate([
                        'flag_id' => $flag->id,
                        'user_id' => $user->id,
                        'points' => $flag->points,
                        'instance_id' => $instance->id,
                        'tournament_id' => $resource->tournament_id,
                        'machine_id' => $resource->id,
                    ]);
                    DB::commit();

                    $firstBloodPoints = 0;

                    $isResourceCompleted = $resource->isMachineCompleted($user->id);

                    $userNick = $user->nick;

                    if ($user->nick == null) {
                        $userNick = $user->name;
                    };

                    $machineInfo = [
                        'user_nick' => $userNick,
                        'resource_name' => $resource->name,
                        'type' => 'own_flag',
                        'subject_type' => get_class($resource),
                        'subject_id' => $resource->id,
                        'user_id' => $user->id,
                        'flag_dificulty' => $flag->dificulty,
                    ];

                    $achievement = null;

                    if ($isResourceCompleted) {

                        $typeOfActivity = ['type' => 'own_machine'];

                        $machineInfo = array_replace($machineInfo, $typeOfActivity);
                    }

                    if ($resource->blooder_id == null && $isResourceCompleted && $resource->type != 'certification') {

                        $firstBloodPoints = $resource->getFirstBloodPoints();

                        $resource->setFirstBlooder($user->id);

                        $typeOfActivity = ['type' => 'first_blood'];

                        $machineInfo = array_replace($machineInfo, $typeOfActivity);
                    }

                    if ($resource->type != 'certification') {

                        $hacktivity = new HacktivityController;

                        $hacktivity->create($machineInfo);
                    }

                    if ($resource->type == 'default') {

                        $scoreService = new ScoreService;

                        $scoreService->addScore($resource->tournament_id, $flag->points, $firstBloodPoints, $resource);
                    }

                    if ($isResourceCompleted) {
                        return response()->json([
                            "success" => true,
                            "message" => 'A flag enviada está correta!'
                        ]);
                    }

                    return response()->json([
                        "success" => true,
                        "message" => 'A flag enviada está correta!',
                    ]);
                }

                return response()->json([
                    "success" => true,
                    "message" => 'Você já enviou esta flag!',
                ]);
            } elseif ($resource instanceof Challenge) {

                $instance = $user->instancesChallenge()->active()->firstOrFail();

                $exists = ChallengeOwn::where([
                    ['flag_id', $flag->id],
                    ['user_id', $user->id]
                ])->first();

                if (!$exists) {

                    $course = Challenge::find($resource->id)->lesson->module->course;

                    DB::beginTransaction();
                    ChallengeOwn::sharedLock()->firstOrCreate([
                        'flag_id' => $flag->id,
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'points' => $flag->points,
                        'challenge_instance_id' => $instance->id,
                        'tournament_id' => $resource->tournament_id,
                        'challenge_id' => $resource->id,
                        'lesson_id' => $resource->lesson_id
                    ]);
                    DB::commit();

                    $firstBloodPoints = 0;

                    $isResourceCompleted = $resource->isChallengeCompleted($user->id);

                    $userNick = $user->nick;

                    if ($user->nick == null) {
                        $userNick = $user->name;
                    };

                    $challengeInfo = [
                        'user_nick' => $userNick,
                        'resource_name' => $resource->name,
                        'type' => 'own_flag',
                        'subject_type' => get_class($resource),
                        'subject_id' => $resource->id,
                        'user_id' => $user->id,
                        'flag_dificulty' => $flag->dificulty,
                    ];

                    $achievement = null;

                    if ($isResourceCompleted) {

                        $typeOfActivity = ['type' => 'own_machine'];

                        $challengeInfo = array_replace($challengeInfo, $typeOfActivity);
                    }

                    if ($resource->blooder_id == null && $isResourceCompleted && $resource->type != 'certification') {

                        $firstBloodPoints = $resource->getFirstBloodPoints();

                        $resource->setFirstBlooder($user->id);

                        $typeOfActivity = ['type' => 'first_blood'];

                        $challengeInfo = array_replace($challengeInfo, $typeOfActivity);
                    }

                    if ($resource->type != 'certification') {

                        $hacktivity = new HacktivityController;

                        $hacktivity->create($challengeInfo);
                    }

                    if ($resource->type == 'default') {

                        $scoreService = new ScoreService;
                        $scoreService->addScore($resource->tournament_id, $flag->points, $firstBloodPoints, $resource);
                    }



                    if ($isResourceCompleted) {
                        return response()->json([
                            "success" => true,
                            "message" => 'A flag enviada está correta!'
                        ]);
                    }
                    return response()->json([
                        "success" => true,
                        "message" => 'A flag enviada está correta!',
                    ]);
                }

                return response()->json([
                    "success" => true,
                    "message" => 'Você já enviou esta flag!',
                ]);
            }
        } else {

            return response()->json([
                "success" => false,
                "message" => 'A flag enviada está incorreta!',
            ]);
        }
    }
}
