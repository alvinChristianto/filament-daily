<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('warehouses')->insert([
            [
                'name' => 'GUDANG UTAMA',
                'type' => 'OTHER',
                'phone_number' => '08999999',
                'address' => 'address test',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Andri AC',
                'type' => 'PERSON',
                'phone_number' => '08999999',
                'address' => 'address test',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Toko Samidi',
                'type' => 'PT',
                'phone_number' => '08999999',
                'address' => 'address test',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
    }
}
