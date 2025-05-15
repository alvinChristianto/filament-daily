<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SparepartStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sparepart_stocks')->insert([
            [
                'id_warehouse' => '1',
                'id_sparepart' => '1',
                'id_transaction' => '-',
                'status' => 'STOCK_IN',
                'amount' => '10',
                'description' => '',
                'stock_record_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'id_warehouse' => '1',
                'id_sparepart' => '2',
                'id_transaction' => '-',
                'status' => 'STOCK_IN',
                'amount' => '10',
                'description' => '',
                'stock_record_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],


            [
                'id_warehouse' => '1',
                'id_sparepart' => '3',
                'id_transaction' => '-',
                'status' => 'STOCK_IN',
                'amount' => '3',
                'description' => '',
                'stock_record_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

            [
                'id_warehouse' => '1',
                'id_sparepart' => '4',
                'id_transaction' => '-',
                'status' => 'STOCK_IN',
                'amount' => '100',
                'description' => '',
                'stock_record_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
