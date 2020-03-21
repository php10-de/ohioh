<?php

use Illuminate\Database\Seeder;

class CountryCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('country_codes')->insert([
            'name' => 'Germany',
            'code' => '+49',
        ]);
    }
}
