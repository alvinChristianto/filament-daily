<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaundryWorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('laundry_workers')->insert([
            [
                'name' => 'Bu Lastri',
                'gender' => 'P',
                'email' => '',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bu Yuli',
                'gender' => 'P',
                'email' => '',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bu Nur',
                'gender' => 'P',
                'email' => '',
                'address' => 'address test',
                'phone_number' => '08999999',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
