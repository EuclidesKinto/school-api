<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Services\CertificateService;

class checkCertificate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:checkcertificate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user if user already has a certificate, if not, create a one for it';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $courses = Course::with('certificate')->get();

        foreach ($courses as $course) {

            $this->info("\n checking users for certificate in course " . $course->name);

            if ($course->certificate()->exists()) {

                $users = DB::table('users as u')
                    ->join('lesson_user as lu', 'lu.user_id', '=', 'u.id')
                    ->join('lessons as l', 'l.id', '=', 'lu.lesson_id')
                    ->join('modules as m', 'm.id', '=', 'l.module_id')
                    ->select('u.*')
                    ->where('m.course_id', $course->id)
                    ->distinct()
                    ->get();

                $bar = $this->output->createProgressBar(count($users));

                $bar->start();

                foreach ($users as $user) {

                    Auth::loginUsingId($user->id);

                    $certService = new CertificateService;

                    $certService->checkCertificate($course->id);


                    $bar->advance();
                }

                $bar->finish();
            }
        }

        return 0;
    }
}
