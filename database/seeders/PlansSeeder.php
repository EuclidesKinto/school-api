<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Plan::create([
            'name' => 'Free',
            'identifier' => 'freemium',
            'interval_months' => '1',
            'value_cents' => '0',
        ]);

        \App\Models\Plan::create([
            'name' => 'Premium',
            'identifier' => 'premium',
            'interval_months' => '1',
            'value_cents' => '10000',
        ]);

        \App\Models\Plan::create([
            'name' => 'Plan2',
            'identifier' => 'plan_2',
            'interval_months' => '1',
            'value_cents' => '20000',
        ]);
    }
}
