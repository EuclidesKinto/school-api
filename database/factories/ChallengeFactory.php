<?php

namespace Database\Factories;

use App\Models\Challenge;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChallengeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Challenge::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $levels = ['easy', 'medium', 'hard', 'insane'];
        $types = ['default', 'training'];
        // escolhe um tipo de máquina a ser criada ('default' = máquina para ownar, 'training' = máquina para treinamento tipo THM)
        // isso aqui vai definir se as flags da máquina serão flags normais de desafio ou quiz de perguntas e respostas.
        $type = $types[array_rand($types, 1)];
        // se o tipo for training, o nível da máquina é definido para "fácil", se for default, o nível da máquina é escolhido
        // aleatóriamente a partir dos itens dentro do array $levels
        $level = $type == 'training' ? 'easy' : $levels[array_rand($levels, 1)];

        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text(50),
            'type' => $type,
            'container_image' => 'https://robohash.org/' . str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"),
            'blooder_id' => rand(1, 10),
            'tournament_id' => 1,
            'difficulty' => $level,
            'release_at' => Carbon::today()->subDays(rand(0, 179))->addSeconds(rand(0, 86400)),
            'creator_id' => rand(1, 10),
        ];
    }
}
