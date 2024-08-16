<?php

namespace App\Http\Controllers\Api\Backoffice;

use App\Http\Controllers\Controller;
use App\Http\Resources\Userland\LessonResource;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lessons = Lesson::paginate(10);

        return $lessons;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $lesson = $request->only([
            'course_id',
            'title',
            'description',
            'metadata',
        ]);

        $lesson['active'] = 1;

        return Lesson::create($lesson);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);

        $lesson->load([
            'tags',
            'quizzes',
            'quizzes.questions',
            'quizzes.questions.answers',
            'challenges',
            'challenges.blood',
            'challenges.blood.scoreGeneral',
            'challenges.flags',
            'challenges.flags.tags',
            'challenges.quizzes',
            'challenges.quizzes.questions',
            'challenges.quizzes.questions.answers',
            'hacktivities',
            'attachments',
        ]);

        return new LessonResource($lesson);
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
        $lesson = Lesson::findOrFail($id);

        $lesson->fill($request->only([
            'course_id',
            'title',
            'description',
            'metadata',
            'active',
            'video_unique_id'
        ]));

        $lesson->save();

        $lesson->load([
            'tags',
            'quizzes',
            'quizzes.questions',
            'quizzes.questions.answers',
            'challenges',
            'challenges.blood',
            'challenges.blood.scoreGeneral',
            'challenges.flags',
            'challenges.flags.tags',
            'challenges.quizzes',
            'challenges.quizzes.questions',
            'challenges.quizzes.questions.answers',
            'hacktivities',
            'attachments',
        ]);

        return new LessonResource($lesson);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        try {
            $lesson->delete();

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
     * Display a listing of soft deleted lessons.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRemoved()
    {
        try {

            return Lesson::onlyTrashed()->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Display the specified soft deleted lessons.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRemoved($id)
    {
        try {

            return Lesson::onlyTrashed()->where("id", "=", $id)->get();
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    /**
     * Unblock specified soft deleted lessons.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restoreLessons($id)
    {

        $lesson = Lesson::onlyTrashed()->find($id);

        try {

            $lesson->restore();

            return response()->json([
                "success" => true,
            ]);
        } catch (\Exception $th) {

            return response()->json([
                "success" => false
            ]);
        }
    }

    public function storeQuiz(Request $request, Lesson $lesson)
    {

        $data = array(
            "title" => $request->title,
        );

        $auxQuiz = new Quiz();

        $auxQuiz->fill($data);

        $lesson->quizzes()->save($auxQuiz);

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