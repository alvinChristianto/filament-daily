<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaundryCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('laundry_customers')->insert([
            [
                'name' => 'Alvin Ch',
                'gender' => 'L',
                'category' => 'PERSON',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'indah',
                'gender' => 'P',
                'category' => 'PERSON',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
