<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            WarehouseSeeder::class,
            AcCustomerSeeder::class,
            PaymentSeeder::class,
            AcWorkingReportSeeder::class,
            AcWorkerSeeder::class,
            SparepartSeeder::class,
            SparepartStockSeeder::class,
            LaundryPacketSeeder::class,
            LaundryWorkerSeeder::class,
            LaundryCustomerSeeder::class

        ]);
    }
}
