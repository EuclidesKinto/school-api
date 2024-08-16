<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\AnsweredQuestion;
use Illuminate\Http\Request;

class QuestionController extends Controller
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

    public function answerQuestion($questionId, $answer, $courseId)
    {

        $user = auth()->user();

        if (!$user->is_premium()) {
            return back()->with(['type' => 'error', 'message' => 'Somente assinantes podem responder aos quizzes!']);
        }

        $answer = Answer::where([['text', $answer], ['question_id', $questionId]])->first();

        if ($answer) {

            $exists = AnsweredQuestion::where([
                ['user_id', $user->id],
                ['course_id', $courseId],
                ['question_id', $questionId],
            ])->first();

            if (!$exists) {

                AnsweredQuestion::firstOrCreate([
                    'user_id' => $user->id,
                    'course_id' => $courseId,
                    'question_id' => $questionId,
                    'answer_id' => $answer->id,
                ]);

                return response()->json([
                    "success" => true,
                    "message" => 'You answered right',
                ]);
                return;
            } else {

                return response()->json([
                    "success" => true,
                    "message" => 'You already answered this question before',
                ]);
            }
        } else {

            return response()->json([
                "success" => true,
                "message" => 'Submitted answer is incorrect',
            ]);
        }
    }
}