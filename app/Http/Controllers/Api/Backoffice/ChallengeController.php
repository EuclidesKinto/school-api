<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\ChallengeResource;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Models\Challenge;
use App\Models\Flag;
use App\Models\Question;
use App\Models\Tag;
use App\Models\Quiz;
use Illuminate\Support\Str;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Challenge::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $challenge = $request->only([
            'lesson_id',
            'tournment_id',
            'name',
            'description',
            'type',
            'container_image',
        ]);

        $challenge = Challenge::create($challenge);

        $challenge->load([
            'blood',
            'blood.scoreGeneral',
            'flags',
            'flags.tags',
            'quizzes',
            'quizzes.questions',
            'quizzes.questions.answers',
        ]);

        return new ChallengeResource($challenge);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $challenge = Challenge::findOrFail($id);

        $challenge->load([
            'blood',
            'blood.scoreGeneral',
            'flags',
            'flags.tags',
            'quizzes',
            'quizzes.questions',
            'quizzes.questions.answers',
        ]);

        return new ChallengeResource($challenge);
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
        $challenge = Challenge::findOrFail($id);

        $challenge->fill($request->only([
            'lesson_id',
            'name',
            'description',
            'type',
            'container_image'
        ]));

        $challenge->save();

        $challenge->load([
            'blood',
            'blood.scoreGeneral',
            'flags',
            'flags.tags',
            'quizzes',
            'quizzes.questions',
            'quizzes.questions.answers',
        ]);

        return new ChallengeResource($challenge);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $challenge = Challenge::find($id);

        try {
            $challenge->delete();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Display a listing of soft deleted challenges.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRemoved()
    {
        try {

            return Challenge::onlyTrashed()->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Display the specified soft deleted challenge.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRemoved($id)
    {
        try {

            return Challenge::onlyTrashed()->where("id", "=", $id)->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Unblock specified soft deleted challenge.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreChallenges($id)
    {

        $challenge = Challenge::onlyTrashed()->find($id);

        try {

            $challenge->restore();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    public function storeFlag(Request $request, Challenge $challenge)
    {

        $data = array(
            "flag" => $request->flag,
            "points" => $request->points,
            "dificulty" => $request->dificulty,
        );

        $auxFlag = new Flag();

        $auxFlag->fill($data);

        $challenge->flags()->save($auxFlag);

        $auxFlag->id;

        foreach ($request->tags as $tag) {

            if (Tag::where('name', '=', $tag)->exists()) {

                $auxTag = Tag::where('name', '=', $tag)->get();

                $auxFlag->tags()->attach($auxTag);
            } else {

                $data = array(
                    "name" => $tag,
                );

                $auxTag = new Tag();

                $auxTag->fill($data);

                $auxTag['slug'] = Str::slug($tag, '-');

                $auxFlag->tags()->save($auxTag);
            }
        }

        return response(['success'], 201);
    }

    public function removeFlag(Request $request, Flag $flag)
    {
        $flag->delete();
        return response([], 204);
    }

    public function storeQuiz(Request $request, Challenge $challenge)
    {

        $data = array(
            "title" => $request->title,
        );

        $auxQuiz = new Quiz();

        $auxQuiz->fill($data);

        $challenge->quizzes()->save($auxQuiz);

        $auxQuiz->id;

        foreach ($request->questions as $question => $answers) {

            $questionData = array(
                "quizz_id" => $auxQuiz->id,
                "text" => $question,
            );

            $auxQuestion = new Question();

            $auxQuestion->fill($questionData);

            $auxQuiz->questions()->save($auxQuestion);

            foreach ($answers as $answer) {

                $answerData = array(
                    "question_id" => $auxQuestion->id,
                    "text" => $answer,
                );

                $auxAnswer = new Answer();

                $auxAnswer->fill($answerData);

                $auxQuestion->answers()->save($auxAnswer);
            }
        }

        return response(['success'], 201);
    }

    public function removeQuiz(Request $request, Quiz $quiz)
    {
        $quiz->delete();
        return response([], 204);
    }
}