<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machine;
use App\Models\Flag;
use Illuminate\Support\Str;

class MachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // cria as máquinas da plataforma e registra as flags de cada uma
        Machine::factory(40)->create()->each(function ($machine) {
            $levels = ['easy', 'medium', 'hard', 'insane'];
            $points = [5, 10, 20, 50];
            for ($i = 0; $i <= 3; $i++) {
                $flag = "hackingclub{" . Str::random(20) . "}";
                Flag::create([
                    'flaggable_id' => $machine->id,
                    'flaggable_type' => get_class($machine),
                    'dificulty' => $levels[$i],
                    'points' => $points[$i] + $points[$i] * array_search($machine->dificulty, $levels),
                    'flag' => $flag
                ]);
            }
        });
    }
}
