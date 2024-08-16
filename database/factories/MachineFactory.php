<?php

namespace Database\Factories;

use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MachineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Machine::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $os_names = ['windows', 'linux', 'osx', 'other'];
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
            'os_name' => $os_names[array_rand($os_names, 1)],
            'ami_id' => config('lab.aws_default_ami'),
            'dificulty' => $level,
            'tournament_id' => 1,
            'type' => $type,
            'blooder_id' => rand(1, 10),
            'creator_id' => rand(1, 10),
            'is_freemium' => rand(0, 1),
            'photo_path' => 'https://robohash.org/' . str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"),
            'description' => $this->faker->text(50),
            'release_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'retire_at' => $this->faker->dateTimeBetween('now', '+2 week'),
        ];
    }
}
