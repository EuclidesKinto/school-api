<?php

namespace App\Http\Controllers\Api\Userland;

use App\Http\Controllers\Controller;
use App\Http\Resources\Collections\ChallengeCollection;
use App\Http\Resources\Userland\ChallengeResource;
use App\Http\Resources\Userland\MachineResource;
use App\Models\Hacktivity;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Http\Resources\Userland\CourseResource;
use App\Http\Resources\Userland\LessonResource;
use App\Http\Resources\Userland\ModuleResource;
use App\Http\Resources\Userland\QuestionResource;
use App\Http\Resources\Userland\QuizzResource;
use App\Models\Challenge;
use App\Models\Module;
use App\Models\Quizz;
use Illuminate\Support\Facades\Auth;

class AcademyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CourseResource::collection(Course::where('active', true)->orderBy('position')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return CourseResource
     */
    public function show($id)
    {
        return new CourseResource(Course::with([
            'modules' => function ($query) {
                $query->orderBy('position');
            },
            'modules.lessons'  => function ($query) {
                $query->orderBy('position');
            },
            'modules.course',
            'modules.lessons.hacktivities',
            'modules.lessons.hacktivities.subject',
            'modules.lessons.hacktivities.reactions',
            'modules.lessons.hacktivities.reactions.user'
        ])->findOrFail($id));
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

    public function showModule($moduleId)
    {
        $module = Module::findOrFail($moduleId);

        $module->load([
            'lessons' => function ($query) {
                $query->orderBy('position');
            },
            'lessons.tags',
            'lessons.challenges.instanceActive' => function ($query) {
                $query->where('user_id', auth()->user()->id);
            },
            'lessons.challenges.blood',
            'lessons.challenges.blood.scoreGeneral',
            'lessons.challenges.flags',
            'lessons.challenges.flags.tags',
            'lessons.challenges.quizzes',
            'lessons.challenges.quizzes.questions',
            'lessons.challenges.quizzes.questions.answers',
            'lessons.hacktivities.reactions.user',
            'lessons.hacktivities.comments.user.getPatent',
            'lessons.hacktivities.comments.reactions.user',
            'lessons.hacktivities.user',
            'lessons.hacktivities.user.getPatent',
            'lessons.attachments',
            'lessons.quizzes',
            'lessons.quizzes.questions',
            'lessons.quizzes.questions.answers'
        ]);

        return new ModuleResource($module);
    }

    public function indexModules()
    {
        $modules = Module::paginate(10);

        $modules->load([
            'course',
            'lessons',
            'lessons.tags',
            'lessons.challenges',
            'lessons.challenges.blood',
            'lessons.challenges.blood.scoreGeneral',
            'lessons.challenges.flags',
            'lessons.challenges.flags.tags',
            'lessons.challenges.quizzes',
            'lessons.challenges.quizzes.questions',
            'lessons.challenges.quizzes.questions.answers',
            'lessons.hacktivities',
            'lessons.hacktivities.subject',
            'lessons.hacktivities.reactions',
            'lessons.hacktivities.reactions.user',
            'lessons.attachments',
            'lessons.quizzes',
            'lessons.quizzes.questions',
            'lessons.quizzes.questions.answers'
        ]);

        return ModuleResource::collection($modules->sortBy('position'));
    }

    public function showLesson($lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);

        $lesson->load([
            'tags',
            'challenges.instanceActive' => function ($query) {
                $query->where('user_id', auth()->user()->id);
            },
            'challenges.blood',
            'challenges.blood.scoreGeneral',
            'challenges.flags',
            'challenges.flags.tags',
            'challenges.quizzes',
            'challenges.quizzes.questions',
            'challenges.quizzes.questions.answers',
            'attachments',
            'quizzes',
            'quizzes.questions',
            'quizzes.questions.answers'
        ]);

        $hacktivity = Hacktivity::where('subject_id', $lesson->id)->where('subject_type', 'App\Models\Lesson')->orderBy('is_fixed', 'desc')->get();

        $hacktivity->load([
            'reactions.user',
            'comments.user.getPatent',
            'comments.reactions.user',
            'user',
            'user.getPatent',
            'subject'
        ]);

        $lesson->hacktivities = $hacktivity;

        return new LessonResource($lesson);
    }

    public function indexLessons()
    {
        $lessons = Lesson::paginate(10);

        $lessons->load([
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
            'hacktivities.subject',
            'hacktivities.reactions',
            'hacktivities.reactions.user',
            'attachments',
        ]);

        return LessonResource::collection($lessons->sortBy('position'));
    }

    public function showChallenge($challengeId)
    {
        $challenge = Challenge::findOrFail($challengeId);

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

    public function showQuizz($quizzId)
    {
        $quizz = Quizz::findOrFail($quizzId);

        $quizz->load(['questions', 'questions.answers']);

        return new QuizzResource($quizz);
    }

    public function showQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);

        $question->load('answers');

        return new QuestionResource($question);
    }

    public function indexChallenges(Request $request)
    {

        $type = $request->type;
        $dificulty = $request->dificulty;
        $progress = $request->progress;

        $query = Challenge::query();

        if ($dificulty) {
            $query->where('dificulty', $dificulty);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($progress) {
        }

        return new ChallengeCollection($query->with(['blood:id,name,nick,profile_photo_path', 'flags.tags'])->paginate(12));
    }

    public function pownChallenge(Request $request, $id)
    {

        $user = auth()->user();


        if (!$user->is_premium()) {
            return response()->json(['success' => 'false', 'message' => 'Somente assinantes podem enviar flags!'], 403);
        }


        $flag = trim($request->input('flag'));

        $challenge = Challenge::findOrFail($id);

        $flagController = new FlagController;

        return $flagController->own($flag, $challenge);
    }

    public function answerLessons(Request $request, $questionId)
    {

        $user = auth()->user();

        if (!$user->is_premium()) {
            return back()->with(['type' => 'error', 'message' => 'Somente assinantes podem responder aos quizzes!']);
        }

        $answer = trim($request->input('answer'));

        $questionController = new QuestionController;

        try {

            $course = Question::find($questionId)->quizz->quizzable->lesson->module->course;

            return $questionController->answerQuestion($questionId, $answer, $course->id);
        } catch (\Throwable $th) {

            return response()->json([
                "success" => true,
                "message" => 'Submitted answer is incorrect',
            ]);
        }
    }

    public function answerChallenges(Request $request, $questionId)
    {

        $user = auth()->user();

        if (!$user->is_premium()) {
            return back()->with(['type' => 'error', 'message' => 'Somente assinantes podem responder aos quizzes!']);
        }

        $answer = trim($request->input('answer'));

        $questionController = new QuestionController;

        try {
            $course = Question::find($questionId)->quizz->quizzable->lesson->module->course;

            return $questionController->answerQuestion($questionId, $answer, $course->id);
        } catch (\Throwable $th) {

            return response()->json([
                "success" => true,
                "message" => 'Submitted answer is incorrect',
            ]);
        }
    }

    public function lessonCheck(Request $request, $lessonId)
    {
        $user = Auth::user();

        $lesson = Lesson::find($lessonId);

        if ($user->lessons->contains($lessonId)) {

            $lesson->users()->detach($user->id);

            return response()->json([
                "success" => true,
                "message" => 'lesson unchecked',
            ]);
        } else {

            $lesson->users()->attach($user->id);

            $certService = new CertificateService;

            $certService->checkCertificate($lesson->module->course->id);

            return response()->json([
                "success" => true,
                "message" => 'lesson checked',
            ]);
        }
    }

    public function getRecommendedMachine(Lesson $lesson)
    {
        $machines = $lesson->getRecommendedMachine($lesson);

        $machines->load([
            'creator.subscriptionPremium',
            'creator.scoreGeneral',
            'creator.getPatent',
            'blood.subscriptionPremium',
            'blood.scoreGeneral',
            'blood.owns.flag.tags',
            'flags.tags',
            'attachments',
        ]);

        return MachineResource::collection($machines);
    }
}
