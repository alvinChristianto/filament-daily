<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SparepartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('spareparts')->insert([
            [
                'name' => 'Evaporator',
                'price' => '800000',
                'sell_price' => '900000',
                'unit' => 'pcs',
                'initial_amount' => '10',
                'origin_from' => 'Pasar',
                'status' => 'NEW',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pipa AC Daikin',
                'price' => '100000',
                'sell_price' => '10000',
                'unit' => 'meter',
                'initial_amount' => '100',
                'origin_from' => 'Pasar',
                'status' => 'NEW',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Fan Blade',
                'price' => '300000',
                'sell_price' => '350000',
                'unit' => 'pcs',
                'initial_amount' => '3',
                'origin_from' => 'Pasar',
                'status' => 'NEW',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Baut',
                'price' => '20000',
                'sell_price' => '1000',
                'unit' => 'pcs',
                'initial_amount' => '100',
                'origin_from' => 'Pasar',
                'status' => 'NEW',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
