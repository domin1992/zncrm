<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class SubscriptionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subscription_types')->insert([
            'name' => 'Hosting',
            'slug' => 'hosting',
            'invoice_position_name' => 'Zlecenie odnowienia hostingu (1 rok)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('subscription_types')->insert([
            'name' => 'Domena',
            'slug' => 'domain',
            'invoice_position_name' => 'Zlecenie odnowienia domeny {domain} (1 rok)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
