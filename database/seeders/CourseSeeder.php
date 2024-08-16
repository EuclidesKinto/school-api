<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Challenge;
use App\Models\Flag;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Question;
use App\Models\Quizz;
use Database\Factories\CourseFactory;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i = 0; $i < 3; $i++) {

            CourseFactory::new()->count(1)
                ->has(
                    Module::factory()
                        ->has(
                            Lesson::factory()
                                ->has(
                                    Challenge::factory()->has(
                                        Quizz::factory()
                                            ->has(
                                                Question::factory()
                                                    ->has(
                                                        Answer::factory()->count(rand(1, 3)),
                                                        'answers'
                                                    )->count(rand(1, 4)),
                                                'questions'
                                            )->count(rand(1, 2)),
                                        'quizzes'
                                    )->has(
                                        Flag::factory()->count(rand(1, 3)),
                                        'flags'
                                    )->count(rand(1, 3)),
                                    'challenges'
                                )->has(
                                    Quizz::factory()
                                        ->has(
                                            Question::factory()
                                                ->has(
                                                    Answer::factory()->count(rand(1, 3)),
                                                    'answers'
                                                )->count(rand(1, 4)),
                                            'questions'
                                        )->count(rand(1, 2)),
                                    'quizzes'
                                )->count(rand(1, 3)),
                            'lessons'
                        )->count(rand(1, 4)),
                    'modules'
                )->create();
        }
    }
}
