<?php

use App\Http\Controllers\Api\Backoffice\AnnouncementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Backoffice\MachineController;
use App\Http\Controllers\Api\Backoffice\PlansController;
use App\Http\Controllers\Api\Backoffice\UsersController;
use App\Http\Controllers\Api\Backoffice\SubscriptionsController;
use App\Http\Controllers\Api\Backoffice\InstancesController;
use App\Http\Controllers\Api\Backoffice\TournamentController;
use App\Http\Controllers\Api\Backoffice\LessonController;
use App\Http\Controllers\Api\Backoffice\ChallengeController;
use App\Http\Controllers\Api\Backoffice\CouponsController;
use App\Http\Controllers\Api\Backoffice\CoursesController;

/*
|--------------------------------------------------------------------------
| Backoffice Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Backoffice routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Backoffice routes
Route::domain(config('app.domain.backoffice'))->middleware(['auth', 'role:admin'])->name('backoffice.')->group(function () {

    // Users 
    Route::get('users/blocked', [UsersController::class, 'indexBlocked'])->name('blocked.users.index');
    Route::get('users/blocked/{id}', [UsersController::class, 'showBlocked'])->name('blocked.users.show');
    Route::get('unblock/blocked/{id}', [UsersController::class, 'unblockUser'])->name('blocked.users.unblock');
    Route::apiResource('users', UsersController::class);

    // Machines
    Route::get('machines/removed', [MachineController::class, 'indexRemoved'])->name('removed.machines.index');
    Route::get('machines/removed/{id}', [MachineController::class, 'showRemoved'])->name('removed.machines.show');
    Route::get('machines/restore/removed/{id}', [MachineController::class, 'restoreMachines'])->name('removed.machines.restore');
    Route::apiResource('machines', MachineController::class);
    Route::post('machines/{machine}/flags', [MachineController::class, 'storeFlags'])->name('machine.flags.store');
    Route::delete('machines/{machine}/flags/{flag}', [MachineController::class, 'removeFlag'])->name('machine.flags.remove');
    Route::post('machines/{flag}/tags', [MachineController::class, 'addTag'])->name('machine.tags.add');
    Route::delete('machines/{flag}/tags/{tag}', [MachineController::class, 'removeTag'])->name('machine.tags.remove');
    Route::post('machines/{machine}/avatar', [MachineController::class, 'addMachineAvatar'])->name('machine.avatar.add');
    Route::post('machines/{machine}/attachments', [MachineController::class, 'addMachineAttachments'])->name('machine.attachments.add');
    Route::delete('machines/{machine}/attachments/{attachment}', [MachineController::class, 'removeMachineAttachment'])->name('machine.attachment.remove');

    // Plans
    Route::apiResource('plans', PlansController::class);

    // Coupons
    Route::apiResource('coupons', CouponsController::class);

    // Subscriptions 
    Route::apiResource('subscriptions', SubscriptionsController::class);

    // Instances
    // Route::apiResource('instances', InstancesController::class);

    // Tournaments
    Route::apiResource('tournaments', TournamentController::class);

    // Annoucement
    Route::get('annoucement/removed', [AnnouncementController::class, 'indexRemoved'])->name('removed.annoucements.index');
    Route::get('annoucement/removed/{id}', [AnnouncementController::class, 'showRemoved'])->name('removed.annoucements.show');
    Route::apiResource('annoucements', AnnouncementController::class);

    // Challenge
    Route::apiResource('challenges', ChallengeController::class);
    Route::get('challenges/removed', [ChallengeController::class, 'indexRemoved'])->name('removed.challenges.index');
    Route::get('challenges/removed/{id}', [ChallengeController::class, 'showRemoved'])->name('removed.challenges.show');
    Route::get('challenges/restore/removed/{id}', [ChallengeController::class, 'restoreChallenges'])->name('removed.challenges.restore');
    Route::post('challenges/{challenge}/flags', [ChallengeController::class, 'storeFlag'])->name('challenges.flags.store');
    Route::delete('challenges/{challenge}/flags/{flag}', [ChallengeController::class, 'removeFlag'])->name('challenges.flags.remove');
    Route::post('challenges/{challenge}/quizzes', [ChallengeController::class, 'storeQuiz'])->name('challenges.quizzes.store');
    Route::delete('challenges/{challenge}/quizzes/{quiz}', [ChallengeController::class, 'removeQuiz'])->name('challenges.quizzes.remove');

    // Lesson
    Route::apiResource('lessons', LessonController::class);
    Route::get('lessons/removed', [LessonController::class, 'indexRemoved'])->name('removed.lessons.index');
    Route::get('lessons/removed/{id}', [LessonController::class, 'showRemoved'])->name('removed.lessons.show');
    Route::get('lessons/restore/removed/{id}', [LessonController::class, 'restoreLessons'])->name('removed.lessons.restore');
    Route::post('lessons/{lesson}/quizzes', [LessonController::class, 'storeQuiz'])->name('lessons.quizzes.store');
    Route::delete('lessons/{lesson}/quizzes/{quiz}', [LessonController::class, 'removeQuiz'])->name('lessons.quizzes.remove');

    // Courses
    Route::apiResource('courses', CoursesController::class);
    Route::post('courses/{course}/avatar', [CoursesController::class, 'addCourseAvatar'])->name('course.avatar.add');
    Route::get('courses/sign/video', [CoursesController::class, 'signVideoUrl'])->name('course.video.sign');

    // Question
    // Route::apiResource('questions', QuestionController::class);

    // Answer
    // Route::apiResource('answers', AnswerController::class);
});
