<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        $subscriptionId = DB::table('subscriptions')->insertGetId([
            'contractor_id' => 1,
            'subscription_type_id' => 1,
            'subscription_package_id' => 1,
            'reference' => 'XOYSKRS',
            'comments' => 'mechanika',
            'date_start' => $now->format('Y-m-d'),
            'custom_price_tax_excl' => null,
            'cycle' => 'annualy',
            'canceled_at' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('subscription_histories')->insert([
            'subscription_id' => $subscriptionId,
            'payment_date' => $now->addWeeks(2)->format('Y-m-d'),
            'status' => 'waiting_for_payment',
            'price_tax_excl' => 90,
            'invoice_pro_form_id' => null,
            'invoice_id' => null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
