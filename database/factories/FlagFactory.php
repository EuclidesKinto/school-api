<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FlagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'flag' => "hc_flag{" . Str::random(5) . "}",
            'points' => 10,
            'dificulty' => "easy",
        ];
    }
}
