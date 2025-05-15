<?php

namespace App\Filament\Resources\AcWorkingReportResource\Pages;

use App\Filament\Resources\AcWorkingReportResource;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateAcWorkingReport extends CreateRecord
{
    protected static string $resource = AcWorkingReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);

        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "INV_" . $year . $month . $day . $randomDigits;
        $data['id_report'] = $transformId;
        foreach ($data['transaction_detail'] as $item) {

            $stockFromGudang = SparepartStock::all()
                ->where('id_sparepart', $item["id_sparepart"])
                ->where('status', 'STOCK_IN')
                ->sum('amount');

            $stockSold = SparepartStock::all()
                ->where('id_sparepart', $item["id_sparepart"])
                ->where('status', 'STOCK_SOLD')
                ->sum('amount');

            $stockReturned = SparepartStock::all()
                ->where('id_sparepart', $item["id_sparepart"])
                ->where('status', 'RETURNED')
                ->sum('amount');

            $totalStock = $stockFromGudang - $stockSold + $stockReturned;
            $checkStockBakpia = $totalStock - $item["amount"];

            Log::info($checkStockBakpia . ' | IN ' . $stockFromGudang . ' | SOLD ' . $stockSold . ' | RETN ' . $stockReturned . " || " . $item["amount"]);

            if ($checkStockBakpia > 0) {

                SparepartStock::create([
                    'id_warehouse' => '1', //$res["id_warehouse"], //$res['id_outlet'], //lol
                    'id_sparepart' => $item["id_sparepart"],
                    'id_transaction' => $data['id_report'],
                    'status' => 'STOCK_SOLD',
                    'amount' =>  $item["amount"],
                    'description' => $data["working_description"],
                    'stock_record_date' => $now,
                ]);
            } else {
                //failed
            }
        }
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
