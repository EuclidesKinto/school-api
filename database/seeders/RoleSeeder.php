<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            ['name' => 'admin'],
            ['name' => 'beta'],
            ['name' => 'user']
        ])->each(function ($role) {
            Role::create($role);
        });
    }
}
