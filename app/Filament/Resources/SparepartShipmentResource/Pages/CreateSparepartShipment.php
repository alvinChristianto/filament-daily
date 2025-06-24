<?php

namespace App\Filament\Resources\SparepartShipmentResource\Pages;

use App\Filament\Resources\SparepartShipmentResource;
use App\Models\DailyRevenueExpenses;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateSparepartShipment extends CreateRecord
{
    protected static string $resource = SparepartShipmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "PART_" . $year . $month . $day . $randomDigits;
        $data['id_transaction'] = $transformId;
        return $data;
    }
    protected function afterCreate(): void
    {

        $res = $this->record;
        // dd($res);
        $now = Carbon::now();

        foreach ($res['transaction_detail'] as $item) {

            $stockFromGudang = SparepartStock::all()
                ->where('id_sparepart', $item["id_sparepart"])
                ->where('status', 'STOCK_IN')
                ->sum('amount');

            $stockSoldMain = SparepartStock::all()
                ->where('id_sparepart', $item["id_sparepart"])
                ->where('status', 'STOCK_SOLD_MAINSTORE')
                ->sum('amount');

            $stockSoldACService = SparepartStock::all()
                ->where('id_sparepart', $item["id_sparepart"])
                ->where('status', 'STOCK_SOLD_AC')
                ->sum('amount');

            $stockReturned = SparepartStock::all()
                ->where('id_sparepart', $item["id_sparepart"])
                ->where('status', 'RETURNED')
                ->sum('amount');

            $totalStock = $stockFromGudang - ($stockSoldMain + $stockSoldACService + $stockReturned);
            $checkStockBakpia = $totalStock - $item["sent_stock"];

            Log::info($checkStockBakpia . ' | IN ' . $stockFromGudang . ' | SOLD Gudang' . $stockSoldMain . ' SOLD Gudang ' . $stockSoldACService . ' SOLD ServiceAC ' . ' | RETN ' . $stockReturned . " || " . $item["sent_stock"]);

            if ($checkStockBakpia > 0) {

                SparepartStock::create([
                    'id_warehouse' => '1', //$res["id_warehouse"], //$res['id_outlet'], //lol
                    'id_sparepart' => $item["id_sparepart"],
                    'id_transaction' => $res['id_transaction'],
                    'status' => 'STOCK_SOLD_MAINSTORE',
                    'amount' =>  $item["sent_stock"],
                    'description' => $res["description"],
                    'stock_record_date' => $now,
                ]);
            } else {
                //failed
            }
        }

        DailyRevenueExpenses::create([
            'date_record' => $now,
            'title' => $res["id_transaction"],
            'category' => 'PEND_SPAREPART',
            'id_transaction' => $res["id_transaction"],
            'revenue_laundry' => 0,
            'revenue_serviceac' => 0,
            'revenue_sparepart' =>  $res["total_price"],
            'expense_buy_sparepart' => 0,
            'expense_other' => 0,
        ]);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
