<?php

namespace App\Console\Commands;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Console\Command;

class FixCoursesPercentages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uhclabs:fixcoursespercents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill courses percentages based on certificates percentages';

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

        $certificates = Certificate::all();

        $bar = $this->output->createProgressBar(count($certificates));

        $bar->start();

        foreach ($certificates as $certificate) {

            $course = Course::find($certificate->course_id);

            $course->percentage_course = $certificate->percentage_course;
            $course->percentage_challenges = $certificate->percentage_challenges;

            $course->save();

            $bar->advance();
        }

        $bar->finish();
        return 0;
    }
}
