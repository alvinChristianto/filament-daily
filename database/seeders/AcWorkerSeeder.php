<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcWorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ac_workers')->insert([
            [
                'name' => 'Aji',
                'gender' => 'L',
                'email' => '',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Endra',
                'gender' => 'L',
                'email' => '',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kasim',
                'gender' => 'L',
                'email' => '',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
