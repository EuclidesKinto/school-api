<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use stdClass;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => config('development.name', 'Administrator'),
            'nick' => config('development.nick', 'Administrator'),
            'email' => config('development.email', 'admin@crowsec.com.br'),
            'password' => bcrypt(config('development.password', 'password')),
            'email_verified_at' => now()
        ]);

        $admin->assignRole('admin');
    }
}
