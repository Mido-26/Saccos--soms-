<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('settings')->insert([
            'organization_name' => 'Techqourum Solutions',
            'organization_email' => 'info@techqourum.com',
            'organization_phone' => '+255700000000',
            'organization_address' => 'Dar es Salaam, Tanzania',
            'organization_logo' => 'storage/logos/default.png',
            'min_savings' => 5000.00,
            'interest_rate' => 12.50,
            'loan_duration' => 24,
            'loan_type' => 'fixed',
            'loan_max_amount' => 10000000.00,
            'currency' => 'TZS',
            'allow_guarantor' => true,
            'min_guarantor' => 2,
            'max_guarantor' => 5,
            'min_savings_guarantor' => 2000.00,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
