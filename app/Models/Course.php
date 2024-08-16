<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'active',
        'metadata',
        'percentage_course',
        'percentage_challenges'
    ];

    /**
     * Tags relationship
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->where('active', true);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function certification(): HasOne
    {
        return $this->hasOne(Certification::class);
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function getChallenges()
    {

        $challenges = [];

        $lessons = $this->lessons->load('challenges');

        foreach ($lessons as $lesson) {

            if (!$lesson->challenges->isEmpty()) {

                foreach ($lesson->challenges as $challenge) {
                    $challenges[] = $challenge;
                }
            }
        }

        return $challenges;
    }

    public function checkCourseUserStatus($courseId)
    {
        $quizzStatus = $this->checkquizzStatus($courseId);

        $challengesFlagsStatus = $this->checkChallengesFlagsStatus($courseId);

        $lessonStatus = $this->checkLessonsStatus($this);

        if ($challengesFlagsStatus == 'completed' && $quizzStatus == 'completed' && $lessonStatus == 'completed') {

            return 'completed';
        } elseif ($challengesFlagsStatus == 'not-started' && $quizzStatus == 'not-started' && $lessonStatus == 'not-started') {

            return 'not-started';
        }

        $numberOfChallenges = $this->numberOfChallenges($courseId);

        $numberOfQuizzes = $this->numberOfQuizzes($courseId);

        $numberOfLessons = $this->numberOfLessons();

        if ($numberOfChallenges) {
            $hasChallenges = true;
        } else {
            $hasChallenges = false;
        }

        if ($numberOfQuizzes) {
            $hasQuizz = true;
        } else {
            $hasQuizz = false;
        }

        if ($numberOfLessons) {
            $hasLessons = true;
        } else {
            $hasLessons = false;
        }

        if ($hasQuizz && $hasChallenges && $hasLessons) {

            if ($challengesFlagsStatus == 'completed' && $quizzStatus == 'completed' && $lessonStatus == 'completed') {

                return 'completed';
            } elseif ($challengesFlagsStatus == 'not-started' && $quizzStatus == 'not-started' && $lessonStatus == 'not-started') {

                return 'not-started';
            } else {

                return 'started';
            }
        } elseif ($hasQuizz && $hasChallenges) {

            if ($challengesFlagsStatus == 'completed' && $quizzStatus == 'completed') {

                return 'completed';
            } elseif ($challengesFlagsStatus == 'not-started' && $quizzStatus == 'not-started') {

                return 'not-started';
            } else {

                return 'started';
            }
        } elseif ($hasQuizz && $hasLessons) {

            if ($quizzStatus == 'completed' && $lessonStatus == 'completed') {

                return 'completed';
            } elseif ($quizzStatus == 'not-started' && $lessonStatus == 'not-started') {

                return 'not-started';
            } else {

                return 'started';
            }
        } elseif ($hasChallenges && $hasLessons) {

            if ($challengesFlagsStatus == 'completed' && $lessonStatus == 'completed') {

                return 'completed';
            } elseif ($challengesFlagsStatus == 'not-started' && $lessonStatus == 'not-started') {

                return 'not-started';
            } else {

                return 'started';
            }

        } elseif ($hasQuizz) {

            return $quizzStatus;
        } elseif ($hasChallenges) {

            return $challengesFlagsStatus;
        } elseif ($hasLessons) {

            return $lessonStatus;
        } else {

            return 'not-started';
        }
    }

    public function checkLessonsStatus($courseId)
    {
        $user = Auth::user();

        $this->load(['lessons']);

        $userCheckedLessons = 0;

        foreach ($this->lessons as $lesson) {

            if ($user->lessons->contains($lesson->id)) {
                $userCheckedLessons += 1;
            }
        }

        $allCourseLessons = $this->lessons->count();

        if ($userCheckedLessons == $allCourseLessons) {
            return 'completed';
        } elseif ($userCheckedLessons != $allCourseLessons && $userCheckedLessons > 0) {
            return 'started';
        } else {
            return 'not-started';
        }
    }

    public function checkChallengesFlagsStatus($courseId)
    {

        $user = Auth::user();

        $course = Course::find($courseId);

        $ownedChallengesFlags = DB::table("challenge_owns")->where([["user_id", "=", $user->id], ["course_id", "=", $courseId]])->get()->count();

        if ($ownedChallengesFlags == 0) {

            $challengesFlagsStatus = 'not-started';
        } else {

            $modules = $course->modules;
            $allChallengesFlags = 0;
            foreach ($modules as $module) {

                $listOfLessonsWithChallenges = Lesson::where('module_id', $module->id)->with('challenges.questions')->get();

                $allChallengesFlags += $listOfLessonsWithChallenges->map(function ($lesson) {
                    $lesson->load('challenges.flags');
                    return $lesson->challenges->map(function ($challenge) {
                        return $challenge->flags->count();
                    })->sum();
                })->sum();
            }

            if ($allChallengesFlags == $ownedChallengesFlags) {

                $challengesFlagsStatus = 'completed';
            } else {

                $challengesFlagsStatus = 'started';
            }
        }

        return $challengesFlagsStatus;
    }

    public function checkquizzStatus($courseId)
    {

        $user = Auth::user();

        $course = Course::find($courseId);

        $answeredCourseQuestions = DB::table("answered_questions")->where([["user_id", "=", $user->id], ["course_id", "=", $courseId]])->get()->count();

        if ($answeredCourseQuestions == 0) {

            $quizzStatus = 'not-started';
        } else {

            $modules = $course->modules;

            $allCoursesQuestions = 0;

            foreach ($modules as $module) {

                $listOfLessonsWithChallenges = Lesson::where('module_id', $module->id)->with('challenges.questions')->get();

                $allChallengesQuestions = $listOfLessonsWithChallenges->map(function ($lesson) {
                    return $lesson->challenges->map(function ($challenge) {
                        return $challenge->questions->count();
                    })->sum();
                })->sum();

                $allLessonsQuestions = Lesson::where('module_id', $module->id)->withCount('questions')->get()->sum('questions_count');

                $allCoursesQuestions += $allChallengesQuestions + $allLessonsQuestions;
            }

            if ($allCoursesQuestions == $answeredCourseQuestions) {

                $quizzStatus = 'completed';
            } else {

                $quizzStatus = 'started';
            }
        }

        return $quizzStatus;
    }

    public function numberOfChallenges($courseId)
    {

        $course = Course::find($courseId);

        $courses = $course->with('modules.lessons.challenges')->where('id', $courseId)->get();
        $challenges = 0;
        foreach ($courses as $course) {
            foreach ($course->modules as $module) {
                foreach ($module->lessons as $lesson) {
                    $challenges += $lesson->challenges->count();
                }
            }
        }

        return $challenges;
    }

    public function numberOfLessons()
    {
        $lessons = $this->lessons;

        return $lessons->count();
    }

    public function numberOfQuizzes($courseId)
    {

        $course = Course::find($courseId);

        $courses = $course->with('modules.lessons.quizzes')->where('id', $courseId)->get();

        $quizzes = 0;

        foreach ($courses as $course) {
            foreach ($course->modules as $module) {
                foreach ($module->lessons as $lesson) {
                    $quizzes += $lesson->quizzes->count();
                }
            }
        }

        $courses = $course->with('modules.lessons.challenges.quizzes')->where('id', $courseId)->get();

        foreach ($courses as $course) {
            foreach ($course->modules as $module) {
                foreach ($module->lessons as $lesson) {
                    foreach ($lesson->challenges as $challenge) {
                        $quizzes += $challenge->quizzes->count();
                    }
                }
            }
        }

        return $quizzes;
    }

    public function getChallengeCompletionPercentage($courseId)
    {

        $user = Auth::user();

        $totalChallengesCompleted = 0;

        $challenges = $this->getChallenges();

        foreach ($challenges as $challenge) {

            $challenge->load('flags');

            if ($challenge->isChallengeCompleted($user->id)) {
                $totalChallengesCompleted += 1;
            }
        }

        $totalChallenges = $this->numberOfChallenges($courseId);

        try {
            $percentageCompletedChallenges = ($totalChallengesCompleted * 100) / $totalChallenges;
        } catch (\Throwable $th) {
            $percentageCompletedChallenges = 0;
        }

        return $percentageCompletedChallenges;
    }

    public function getCourseCompletionPercentage($courseId)
    {
        $user = Auth::user();

        $totalLessons = $this->numberOfLessons();

        $totalLessonsChecked = DB::table('users')
            ->join('lesson_user', 'lesson_user.user_id', '=', 'users.id')
            ->join('lessons', 'lessons.id', '=', 'lesson_user.lesson_id')
            ->join('modules', 'modules.id', '=', 'lessons.module_id')
            ->join('courses', 'courses.id', '=', 'modules.course_id')
            ->where('courses.id', $courseId)->where('users.id', $user->id)
            ->count() + 1;

        try {
            $percentageCompletedLessons = ($totalLessonsChecked * 100) / $totalLessons;
        } catch (\Throwable $th) {
            $percentageCompletedLessons = 0;
        }

        return $percentageCompletedLessons;
    }
}
