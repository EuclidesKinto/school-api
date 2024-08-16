<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailingListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mailing_lists')->insert([
            'provider' => 'active_campaign',
            'name' => 'hacking-club',
            'description' => 'registered users list',
            'list_id' => '32'
        ]);
        
        DB::table('mailing_lists')->insert([
            'provider' => 'active_campaign',
            'name' => 'assinantes-hc',
            'description' => 'paid users list',
            'list_id' => '37'
        ]);
    }
}
