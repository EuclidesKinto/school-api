<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tournament;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // cria o ranking geral da plataforma
        Tournament::create([
            'name' => 'Geral',
            'description' => 'Ranking Geral do UHC Labs',
        ]);

        /**
         * Outros torneios que forem eventualmente criados, deverão ser registrados aqui
         */
    }
}
