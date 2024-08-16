<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AnsweredQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\AnsweredQuestion::create([
            'user_id' => '11',
            'course_id' => '1',
            'question_id' => '1',
            'answer_id' => '1',
        ]);
    }
}
