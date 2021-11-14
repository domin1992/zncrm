<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class ContractorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contractorId = DB::table('contractors')->insertGetId([
            'name' => 'Mechanika Pojazdowa Andrzej Nowak',
            'identity' => 'MECHANIKA POJAZD',
            'vat_number' => '7261830224',
            'supplier' => true,
            'receiver' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('contractor_addresses')->insertGetId([
            'contractor_id' => $contractorId,
            'type' => 'basic',
            'street' => 'Centralna 21A',
            'postcode' => '91-502',
            'country' => 'Polska',
            'city' => 'Łódź',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $contractorId = DB::table('contractors')->insertGetId([
            'name' => 'Marta Wójcik',
            'identity' => 'M_WOJCIK',
            'vat_number' => null,
            'supplier' => false,
            'receiver' => true,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        DB::table('contractor_addresses')->insertGetId([
            'contractor_id' => $contractorId,
            'type' => 'basic',
            'street' => 'Łupkowa 9',
            'postcode' => '91-500',
            'country' => 'Polska',
            'city' => 'Łódź',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
