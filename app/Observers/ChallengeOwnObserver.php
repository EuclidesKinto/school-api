<?php

namespace App\Observers;

use App\Models\ChallengeOwn;
use App\Services\CertificateService;

class ChallengeOwnObserver
{
    /**
     * Handle the ChallengeOwn "created" event.
     *
     * @param  \App\Models\ChallengeOwn  $challengeOwn
     * @return void
     */
    public function created(ChallengeOwn $challengeOwn)
    {
        $course = $challengeOwn->challenge->lesson->module->course;
        if($course->certificate()->exists()){
            $certService = new CertificateService;
            $certService->checkCertificate($course->id);
        }
        return;
    }

    /**
     * Handle the ChallengeOwn "updated" event.
     *
     * @param  \App\Models\ChallengeOwn  $challengeOwn
     * @return void
     */
    public function updated(ChallengeOwn $challengeOwn)
    {
        //
    }

    /**
     * Handle the ChallengeOwn "deleted" event.
     *
     * @param  \App\Models\ChallengeOwn  $challengeOwn
     * @return void
     */
    public function deleted(ChallengeOwn $challengeOwn)
    {
        //
    }

    /**
     * Handle the ChallengeOwn "restored" event.
     *
     * @param  \App\Models\ChallengeOwn  $challengeOwn
     * @return void
     */
    public function restored(ChallengeOwn $challengeOwn)
    {
        //
    }

    /**
     * Handle the ChallengeOwn "force deleted" event.
     *
     * @param  \App\Models\ChallengeOwn  $challengeOwn
     * @return void
     */
    public function forceDeleted(ChallengeOwn $challengeOwn)
    {
        //
    }
}
