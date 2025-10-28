<?php

namespace App\Filament\Resources\SparepartResource\Pages;

use App\Filament\Resources\SparepartResource;
use App\Models\DailyRevenueExpenses;
use App\Models\DailySparepartTrxRevenueExpenses;
use App\Models\SparepartShipment;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSparepart extends CreateRecord
{
    protected static string $resource = SparepartResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        return $data;
    }

    protected function afterCreate(): void
    {

        $res = $this->record;
        $drCash = 0;
        $drNonCash = 0;
        $crCash = 0;
        $crNonCash = 0;
        
        // no id_payment from res 
        // if ($res["id_payment"] == 1) {
        //     $drCash = $res["total_price"];
        // } else {
        //     $drNonCash = $res["total_price"];
        // }


        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "PART_" . $year . $month . $day . $randomDigits;
        $id_transaction = $transformId;
        // dd($res);

        $now = Carbon::now();

        $transactionDetailData = [
            'id_sparepart' => $res["id"],
            'sent_stock' => 0,
            'price_per' => $res["price"],
            'stock_latest' => $res["initial_amount"],
            'remaining_stock' => $res["initial_amount"]
        ];
        SparepartShipment::create([
            'id_transaction' => $id_transaction,
            'id_payment' => '1',
            'id_warehouse' => '1',
            'transaction_detail' => [$transactionDetailData],
            'status' => 'INITIAL',
            'total_price' => $res["sell_price"],
            'discount' => 0,
            'description' => $res["description"],
            'transaction_date' => $now,
        ]);

        SparepartStock::create([
            'id_warehouse' => '1', //$res['id_outlet'], //lol
            'id_sparepart' => $res["id"],
            'id_transaction' => '-',
            'status' => 'STOCK_IN',
            'amount' =>  $res["initial_amount"],
            'description' => $res["description"],
            'stock_record_date' => $now,
        ]);

        // 25102025 not used
        DailyRevenueExpenses::create([
            'date_record' => $now,
            'title' => $res["name"],
            'id_transaction' =>  $res["id"],
            'category' => 'BIAYA_BELI_SPAREPART',
            'revenue_laundry' => 0,
            'revenue_serviceac' => 0,
            'revenue_sparepart' =>  0,
            'expense_buy_sparepart' => $res["price"],
            'expense_other' => 0,

            'payment_category' => 1,
            'dr_cash' => $drCash,
            'dr_noncash' =>  $drNonCash,
            'cr_cash' =>  $res["price"],    //buy sparepart only use cash
            'cr_noncash' =>  $crNonCash
        ]);

        //add at 25102025
        DailySparepartTrxRevenueExpenses::create([
            'date_record' => $now,
            'id_transaction' =>  $res["id"],
            'category' => 'BIAYA_BELI_SPAREPART',
            
            'revenue_sell_sparepart' => 0,
            'revenue_other' =>  0,
            'expense_buy_sparepart' => $res["price"],
            'expense_other' => 0,

            'payment_category' => 1,
            'dr_cash' => $drCash,
            'dr_noncash' =>  $drNonCash,
            'cr_cash' =>  $res["price"],    //buy sparepart only use cash
            'cr_noncash' =>  $crNonCash
        ]);
        // Runs after the form fields are saved to the database.
    }
}
