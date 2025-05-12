<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ac_customers')->insert([
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
                'name' => 'Lisa',
                'gender' => 'P',
                'category' => 'PERSON',
                'address' => 'address test',
                'phone_number' => '08999988',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'The Cabin Hotel',
                'gender' => '-',
                'category' => 'PT',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
