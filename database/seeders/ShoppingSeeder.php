<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;

class ShoppingSeeder extends Seeder
{
    const PLANO_PRO_MENSAL = 2;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $admin = User::where('email', '=', config('development.email'))->first();

        $admin->billingProfile->addresses()->create([
            'line_1' => '84, Rua dos Bobos, Centro',
            'line_2' => 'apt 101',
            'city' => 'São Paulo',
            'state' => 'SP',
            'zip_code' => '01310000',
            'country' => 'BR',
            'user_id' => $admin->id,
        ]);

        $cart = $admin->cart;

        $cart->save();
        $cart->items()->create(['product_id' => self::PLANO_PRO_MENSAL, 'quantity' => 1]);
    }
}
