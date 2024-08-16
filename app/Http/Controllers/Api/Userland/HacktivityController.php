<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Collections\HacktivityCollection;
use App\Http\Resources\Userland\HacktivityResource;
use Illuminate\Http\Request;
use App\Models\Hacktivity;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;

class HacktivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $query = Hacktivity::orderBy('created_at', 'desc')->where('subject_type', '!=', 'App\Models\Lesson');
        return HacktivityCollection::collection($query->with(['reactions.user', 'reactionsUserAuth', 'user.getPatent', 'user.scoreGeneral', 'comments.user.getPatent', 'comments.reactions.user', 'subject'])->paginate(20));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($resource)
    {

        //if its lesson
        if (is_string($resource)) {

            if (Lesson::where('id', $resource)->exists()) {

                Hacktivity::create([
                    'headline' => request()->headline,
                    'type' => 'lesson',
                    'subject_type' => 'App\Models\Lesson',
                    'subject_id' => $resource,
                    'user_id' => auth()->user()->id,
                ]);

                return response(['hacktivity created'], 200);
            } else {

                return response(['lesson not found'], 404);
            }
        }

        switch ($resource['type']) {
            case 'release':
                $headline = 'A máquina ' . $resource['resource_name'] . ' criada por ' . $resource['user_nick'] . ' acaba de ser lançada!';
                break;
            case 'first_blood':
                $headline = 'O usuário ' . $resource['user_nick'] . ' pegou o first blood na ' . $resource['resource_name'];
                break;
            case 'own_machine':
                $headline = 'O usuário ' . $resource['user_nick'] . ' ownow a máquina ' . $resource['resource_name'];
                break;
            case 'own_challenge':
                $headline = 'O usuário ' . $resource['user_nick'] . ' ownow o desafio ' . $resource['resource_name'];
                break;
            case 'own_flag':
                $headline = 'O usuário ' . $resource['user_nick'] . ' ownow uma flag ' . $resource['flag_dificulty'] . ' na máquina ' . $resource['resource_name'];
                break;
        }

        Hacktivity::create([
            'headline' => $headline,
            //text of activity
            'type' => $resource['type'],
            //own machine, own challenge, own flag, first blood, release
            'subject_type' => $resource['subject_type'],
            //machine, challenge, flag
            'subject_id' => $resource['subject_id'],
            //id of subject
            'user_id' => $resource['user_id'],
        ]);

        return;
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
        $hacktivity = Hacktivity::findorfail($id);

        $hacktivity->load(['reactions.user', 'reactionsUserAuth', 'user.getPatent', 'user.scoreGeneral', 'comments.user.getPatent', 'comments.reactions.user', 'subject']);

        return new HacktivityResource($hacktivity);
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

    public function fix($id)
    {

        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $hacktivity = Hacktivity::findorfail($id);

        if ($hacktivity->is_fixed != true) {

            $hacktivity->is_fixed = true;

            $hacktivity->save();

            return response()->json([
                'message' => 'Comment fixed',
                'success' => true
            ]);

        } else {
            $hacktivity->is_fixed = false;

            $hacktivity->save();

            return response()->json([
                'message' => 'Comment unfixed',
                'success' => true
            ]);
        }
    }
}
