<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Flag;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = collect([
            ['name' => 'Buffer Overflow'],
            ['name' => 'Business logic vulnerability'],
            ['name' => 'CRLF Injection'],
            ['name' => 'CSV Injection'],
            ['name' => 'Catch NullPointerException'],
            ['name' => 'Covert storage channel'],
            ['name' => 'Deserialization of untrusted data'],
            ['name' => 'Directory Restriction Error'],
        ])->map(function ($tag) {
            return Tag::create($tag);
        });

        Flag::all()->each(function ($flag) use ($tags) {
            $flag->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });

        Lesson::all()->each(function ($lesson) use ($tags) {
            $lesson->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
    }
}
