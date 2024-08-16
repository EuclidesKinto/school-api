<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'message' => $this->faker->paragraph(3),
            'user_id' => $this->faker->numberBetween(1, 10),
            'commentable_id' => $this->faker->numberBetween(1, 10),
            'commentable_type' => $this->faker->randomElement(['App\Models\Hacktivity']),
        ];
    }
}
