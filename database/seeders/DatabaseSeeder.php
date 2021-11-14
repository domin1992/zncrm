<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if(env('APP_ENV') == 'local'){
            $this->call([
                UserSeeder::class,
                ContractorSeeder::class,
                SubscriptionSeeder::class,
            ]);
        }

        $this->call([
            SubscriptionTypeSeeder::class,
            SubscriptionPackageSeeder::class,
        ]);
    }
}
