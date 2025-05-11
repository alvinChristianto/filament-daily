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
                'type' => 'GUDANG',
                'phone_number' => '08999999',
                'address' => 'address test',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'GUDANG LAUNDRY',
                'type' => 'LAUNDRY',
                'phone_number' => '08999999',
                'address' => 'address test',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Toko Samidi',
                'type' => 'OTHER',
                'phone_number' => '08999999',
                'address' => 'address test',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);
    }
}
