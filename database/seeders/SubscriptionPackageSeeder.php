<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subscription_packages')->insert([
            'subscription_type_id' => 1,
            'name' => '1G',
            'slug' => '1g',
            'price_tax_excl' => 90.0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('subscription_packages')->insert([
            'subscription_type_id' => 1,
            'name' => '2G',
            'slug' => '2g',
            'price_tax_excl' => 130.0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('subscription_packages')->insert([
            'subscription_type_id' => 1,
            'name' => '5G',
            'slug' => '5g',
            'price_tax_excl' => 220.0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('subscription_packages')->insert([
            'subscription_type_id' => 2,
            'name' => 'Domena .pl',
            'slug' => 'pl',
            'price_tax_excl' => 50.0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
