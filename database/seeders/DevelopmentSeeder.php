<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\App;
use App\Models\Machine;
use App\Models\Instance;
use App\Models\Own;
class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // create fake data to use on development environment
        if (App::environment('local') || App::environment('staging')) {
            // cria os usuários e inscreve eles por padrão no ranking geral
            User::factory(10)->create()->each(function ($user) {
                // gives the default role to the users
                $user->assignRole('user');
            });


            // seeds administrator users to the database
            $this->call(AdministratorSeeder::class);

        }
    }
}
