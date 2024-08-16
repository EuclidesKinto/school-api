<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    public function checkCertificate($courseId)
    {

        $course = Course::find($courseId);

        $challengePercentage = $course->getChallengeCompletionPercentage($course->id);

        $coursePercentage = $course->getCourseCompletionPercentage($course->id);

        if ($challengePercentage >= $course->percentage_challenges && $coursePercentage >= $course->percentage_course) {

            $user = Auth::user();

            $certificateId = $course->certificate->id;

            if (!$user->certificates->contains($course->certificate)) {

                $uuid = Str::uuid();

                $data = [
                    'student_name' => $user->name,
                    'security_code' => $uuid,
                    'issue_date' => Carbon::now()->format('d/m/y'),
                    'cert_name' => $course->certificate->name,
                    'cert_description' => $course->certificate->description
                ];

                $pdf = \App::make('snappy.pdf.wrapper');
                $pdf->loadView('pdf.certificate', $data)
                    ->setPaper('a4')
                    ->setOrientation('landscape')
                    ->setOption('margin-bottom', 0)
                    ->setOption('margin-left', 0)
                    ->setOption('margin-right', 0)
                    ->setOption('margin-top', 0);

                Storage::disk('s3')->put('user/certificate/' . $uuid . ".pdf", $pdf->output(), 'public');

                $path = Storage::disk('s3')->url('user/certificate/' . $uuid . ".pdf");

                DB::table('certificate_user')->insert([
                    'user_id' => $user->id,
                    'certificate_id' => $certificateId,
                    'validation_id' => $uuid,
                    'url' => $path,
                    'created_at' =>Carbon::now()
                ]);

                return $pdf->download($uuid . ".pdf");
            }
        }
        return;
    }
}
