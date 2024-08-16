<?php

use App\Http\Controllers\Api\Userland\CertificateController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\SsoController;
use App\Http\Controllers\Api\Backoffice\MachineController;
use App\Http\Controllers\Api\Backoffice\UsersController;
use App\Http\Controllers\Api\Userland\InstancesController;
use App\Http\Controllers\Api\Billing\AddressesController;
use App\Http\Controllers\Api\Billing\CheckoutController;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Api\Userland\AccountController;
use App\Http\Controllers\Api\Userland\AcademyController;
use App\Http\Controllers\Api\Userland\LabController;
use App\Http\Controllers\Api\Userland\ScoreboardController;
use App\Http\Controllers\Api\Billing\BillingProfileController;
use App\Http\Controllers\Api\Billing\InvoicesController;
use App\Http\Controllers\Api\Billing\WebHooksController;
use App\Http\Controllers\Api\Userland\NewsController;
use App\Http\Controllers\Api\Userland\VPNController;
use App\Http\Controllers\Api\Userland\EmailVerificationController;
use App\Http\Controllers\Api\Userland\NewPasswordController;
use App\Http\Controllers\Api\Userland\SubscriptionsController;
use App\Http\Controllers\Api\Billing\PaymentMethodsController;
use App\Http\Controllers\Api\Userland\HacktivityController;
use App\Http\Controllers\Api\Userland\InteractionController;
use App\Http\Controllers\Api\Billing\ChargeController;
use App\Http\Controllers\Api\Billing\PaymentController;
use App\Http\Controllers\Api\Billing\InvoiceController;
use App\Http\Controllers\Api\Billing\PayerController;
use App\Http\Controllers\Api\Billing\PlanController;
use App\Http\Controllers\Api\Billing\SubscriptionController;
use App\Http\Controllers\Api\Userland\CertificationController;
use App\Http\Controllers\Api\Userland\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes

Route::domain(config('app.domain.api'))->name('api.auth.')->group(function () {
    Route::post('/auth/login', [SsoController::class, 'doLogin'])->name('default.login');
    Route::post('/auth/register', [SsoController::class, 'doRegister'])->name('default.register');

    Route::post('/auth/{provider}', [SsoController::class, 'SsoLogin'])->name('login');
    Route::get('/auth/verify', [SsoController::class, 'verify'])->middleware('auth')->name('verify');
    Route::any('/auth/logout', [SsoController::class, 'SsoLogout'])->name('logout');

    // dummy login, used only for test purposes
    if (App::environment(['local', 'development'])) {
        Route::get('dummy', [SsoController::class, 'dummyLogin'])->name('dummy');
    }

    Route::post('email/verification', [EmailVerificationController::class, 'sendVerificationEmail'])->middleware('throttle:10,1');

    Route::post('password/forgot', [NewPasswordController::class, 'forgotPassword'])->name('password.request')->middleware('throttle:10,1');
    Route::post('password/reset', [NewPasswordController::class, 'resetPassword'])->name('password.reset');
});

// Email Verification route
Route::get('verify/email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

Route::post('certificate/verify', [CertificateController::class, 'verify'])->name('certificate.verify');

// Discord Routes
Route::domain(config('app.domain.api'))->name('services.')->middleware(['check_api_key'])->group(function () {

    // Route::get('service/get-users', [UsersController::class, 'list'])->name('users.list');
    Route::post('service/search-user', [UsersController::class, 'search'])->name('user.search');
});

// End user routes
Route::domain(config('app.domain.api'))->name('api.')->middleware(['auth'])->group(function () {

    // account routes
    Route::prefix('account')->group(function () {
        Route::put('profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/checknick', [AccountController::class, 'checkNick'])->name('profile.checknick');
        Route::post('profile/avatar', [AccountController::class, 'addProfileAvatar'])->name('profile.avatar.add');
        Route::get('profile/certificates', [AccountController::class, 'indexUserCertificates'])->name('profile.certificates.index');
        Route::get('profile/public/{id}', [AccountController::class, 'showPublicProfile'])->name('profile.public');

        /**
         * Return user subscriptions
         */
        // this route should be placed before the resource routes to avoid bug on "show" method
        Route::get('subscriptions/current', [SubscriptionsController::class, 'current'])->name('subscriptions.current');
        Route::delete('subscriptions/cancel', [SubscriptionsController::class, 'cancel'])->name('subscriptions.cancel');
        Route::apiResource('subscriptions', SubscriptionsController::class);

        /**
         * Return user invoices
         */
        Route::get('invoices', [InvoicesController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{id}', [InvoicesController::class, 'show'])->name('invoices.show');
        /**
         * Return user transactions
         */
        Route::get('transactions', [InvoicesController::class, 'transactions'])->name('transactions.index');
        Route::get('transactions/{id}', [InvoicesController::class, 'transaction'])->name('transactions.show');

    });

    // academy routes
    Route::apiResource('academy', AcademyController::class);
    Route::prefix('academy')->group(function () {
        Route::get('courses/list', [AcademyController::class, 'index'])->name('academy.courses.index');
        Route::get('courses/{course}', [AcademyController::class, 'show'])->name('academy.courses.show');
        Route::get('modules/list', [AcademyController::class, 'indexModules'])->name('academy.modules.index');
        Route::get('modules/{module}', [AcademyController::class, 'showModule'])->name('academy.modules.show');
        Route::get('lessons/list', [AcademyController::class, 'indexLessons'])->name('academy.lessons.index');
        Route::get('lessons/{lesson}', [AcademyController::class, 'showLesson'])->name('academy.lesson.show');
        Route::put('lessons/{questionId}/answer', [AcademyController::class, 'answerLessons'])->name('lesson.answer');
        Route::put('lessons/{lesson}/check', [AcademyController::class, 'lessonCheck'])->name('lesson.check');
        Route::get('lessons/{lesson}/recommended_machine', [AcademyController::class, 'getRecommendedMachine'])->name('lesson.recommended_machine');
        Route::get('challenges/list', [AcademyController::class, 'indexChallenges'])->name('academy.challenges.index');
        Route::get('challenges/{challenge}', [AcademyController::class, 'showChallenge'])->name('academy.challenges.show');
        Route::put('challenges/pown/{challenge}', [AcademyController::class, 'pownChallenge'])->name('pown.challenge');
        Route::put('challenges/{questionId}/answer', [AcademyController::class, 'answerChallenges'])->name('challenges.answer');
        Route::get('quizzes/{quizz}', [AcademyController::class, 'showQuizz'])->name('academy.quizz.show');
        Route::get('questions/{question}', [AcademyController::class, 'showQuestion'])->name('academy.question.show');
    });

    // lab routes
    Route::prefix('labs')->group(function () {
        Route::get('machines', [LabController::class, 'machines'])->name('machines.index');
        Route::get('machines/{id}', [LabController::class, 'show'])->name('machine.show');
        Route::get('machines/{id}/activities', [LabController::class, 'activities'])->name('machine.activities');
        Route::get('machines/{id}/certification', [LabController::class, 'showMachineCertification'])->name('machine.showMachineCertification');
        Route::put('machines/pown/{id}', [LabController::class, 'pownMachine'])->name('machine.pown');

        Route::get('machines/{id}/start', [InstancesController::class, 'deploy'])->name('instance.deploy')->middleware('throttle:10,1');
        Route::get('machines/{id}/stop', [InstancesController::class, 'terminate'])->name('instace.stop');
        Route::get('machines/{id}/addTime', [InstancesController::class, 'addTime'])->name('instace.addTime');



        Route::put('challenges/pown/{id}', [LabController::class, 'pownChallenge'])->name('challenge.pown');
        Route::get('challenges/{id}/start', [InstancesController::class, 'deployChallenge'])->name('instance.challenge.deploy')->middleware('throttle:10,1');
        Route::get('challenges/{id}/stop', [InstancesController::class, 'terminateChallenge'])->name('instace.challenge.stop');
        Route::get('challenges/{id}/addTime', [InstancesController::class, 'addTimeChallenge'])->name('instace.challenge.addTime');
    });

    //feed routes
    Route::prefix('social')->group(function () {
        Route::get('hacktivities', [HacktivityController::class, 'index'])->name('hacktivities.index');
        Route::post('comment/lesson/{id}', [HacktivityController::class, 'create'])->name('social.comment.lesson');
        Route::post('comment/{resource}/{id}', [InteractionController::class, 'comment'])->name('social.comment');
        Route::get('comments/{resource}/{id}', [InteractionController::class, 'showComment'])->name('social.comments');
        Route::delete('comments/{resource}/{id}', [InteractionController::class, 'deleteComment'])->name('social.comments.delete');
        Route::get('hacktivities/{id}', [HacktivityController::class, 'show'])->name('hacktivities.show');
        Route::put('hacktivities/{id}/fix', [HacktivityController::class, 'fix'])->name('hacktivities.fix');
        Route::post('react/{resource}/{id}', [InteractionController::class, 'react'])->name('challenge.react');
    });

    // vpn routes
    Route::prefix('vpn')->group(function () {
        Route::get('/', [VPNController::class, 'getProfile'])->name('vpn.index');
        Route::post('/', [VPNController::class, 'createProfile'])->name('vpn.create');
    });

    Route::prefix('club')->group(function () {
        Route::get('scoreboard/{id}', [ScoreboardController::class, 'show'])->name('scoreboard.show');
    });

    /**
     * News routes
     */
    Route::get('events', [NewsController::class, 'showEvents'])->name('events.show');

    Route::prefix('payer')->group(function () {
        Route::get('index', [PayerController::class, 'index'])->name('payer.index');
        Route::post('store', [PayerController::class, 'store'])->name('payer.store');
        Route::post('update/{id}', [PayerController::class, 'update'])->name('payer.update');
        Route::delete('destroy/{id}', [PayerController::class, 'destroy'])->name('payer.destroy');
    });

    Route::prefix('payment')->group(function () {
        Route::get('index', [PaymentController::class, 'index'])->name('payment.index');
        Route::post('store', [PaymentController::class, 'store'])->name('payment.store');
        Route::post('update/{id}', [PaymentController::class, 'update'])->name('payment.update');
        Route::delete('destroy/{id}', [PaymentController::class, 'destroy'])->name('payment.destroy');
    });

    Route::prefix('subscription')->group(function () {
        Route::get('show', [SubscriptionController::class, 'show'])->name('subscription.show');
        Route::post('store', [SubscriptionController::class, 'store'])->name('subscription.store');
        Route::post('activate', [SubscriptionController::class, 'activate'])->name('subscription.activate');
        Route::post('suspend', [SubscriptionController::class, 'suspend'])->name('subscription.suspend');
        Route::post('change-plan', [SubscriptionController::class, 'changePlan'])->name('subscription.changePlan');
    });

    Route::prefix('plan')->group(function () {
        Route::get('index', [PlanController::class, 'index'])->name('plan.index');
    });

    Route::prefix('charge')->group(function () {
        Route::post('store', [ChargeController::class, 'store'])->name('charge.store');
        Route::get('installments/{planId}', [ChargeController::class, 'installments'])->name('charge.installments');
    });

    Route::prefix('invoice')->group(function () {
        Route::get('index', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::post('iuguInvoiceStore', [InvoiceController::class, 'iuguInvoiceStore'])->name('invoice.iuguInvoiceStore');
        Route::post('refund', [InvoiceController::class, 'iuguRefund'])->name('invoice.refund');
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::put('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::put('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    });

    Route::get('recommendedMachine', [MachineController::class, 'showRecommendedMachine'])->name('recommendedMachine.show');

    Route::get('dashboardRecommendedMachines', [MachineController::class, 'dashboardRecommendedMachines'])->name('dashboardRecommendedMachines');

    Route::prefix('certification')->group(function () {
        Route::get('ask/{certification}', [CertificationController::class, 'askForCertification'])->name('certification.ask');
        Route::get('status/{certification}', [CertificationController::class, 'status'])->name('certification.status');
        Route::get('time-left/{certification}', [CertificationController::class, 'timeLeft'])->name('certification.timeLeft');
        Route::get('index/machines/{certification}', [CertificationController::class, 'indexMachines'])->name('certification.indexMachines');
        Route::get('download/{certification}', [CertificationController::class, 'download'])->name('certification.download');
        Route::put('machines/pown/{machine}', [CertificationController::class, 'pownMachine'])->name('certification.machinePown');
        Route::post('send-report/{certification}', [CertificationController::class, 'sendReport'])->name('certification.sendReport');
        Route::get('start/{certification}/{machine}', [CertificationController::class, 'startMachine'])->name('certification.deploy');
        Route::get('check/{certification}', [CertificationController::class, 'check'])->name('certification.check');
        Route::get('start-deadline/{certification}', [CertificationController::class, 'startDeadline'])->name('certification.startDeadline');

        Route::get('start-send-report/{certification}', [CertificationController::class, 'startSendReport'])->name('certification.startSendReport');
        Route::get('disapproved/{certification}', [CertificationController::class, 'certificationDisapproved'])->name('certification.certificationDisapproved');
    });
});

/**
 * Webhooks routes
 */
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('pagarme', [WebHooksController::class, 'pagarme'])->name('pagarme');
    Route::get('pagarme', function () {
        return response()->json(['message' => 'Webhooks are disabled'], 403);
    })->name('pagarme.show');
    Route::post('stripe', [WebhooksController::class, 'stripe'])->name('stripe');
});
