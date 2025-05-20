<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaundryPacketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('laundry_packets')->insert([
            [
                'name' => 'CUCI KERING SETRIKA',
                'alias' => 'KOMPLIT',
                'base_price' => '2000',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CUCI KERING',
                'alias' => 'CK',
                'base_price' => '1000',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'CUCI LIPAT',
                'alias' => 'CL',
                'base_price' => '900',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'SETRIKA',
                'alias' => 'S',
                'base_price' => '500',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
           
        ]);
    }
}
