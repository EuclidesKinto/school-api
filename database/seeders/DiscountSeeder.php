<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // cria os cupons de desconto
        $coupon_uhclabs = Coupon::create(['code' => Coupon::COUPON_UHCLABS, 'type' => 'flat', 'limit' => 0, 'description' => 'Desconto para usuários vindos do UHCLabs', 'is_active' => true, 'value' => 20.00]);
        $coupon_tropa = Coupon::create(['code' => Coupon::COUPON_TROPA, 'type' => 'percentage', 'limit' => 0, 'description' => 'Desconto para usuários da Tropa do WebHacking', 'is_active' => true, 'value' => 20]);

        // associa os cupons de desconto aos planos de assinatura
        $plano_uhclabs = Plan::where('identifier', 'premium')->first();
        $plano_uhclabs->product->coupons()->attach($coupon_uhclabs);

        $planos_da_tropa = Plan::where('price', '>', 0)->get();
        $planos_da_tropa->each(function ($plan) use ($coupon_tropa) {
            $plan->product->coupons()->attach($coupon_tropa);
        });
    }
}
