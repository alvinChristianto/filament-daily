<?php

namespace App\Traits;

use App\Models\Sparepart;
use App\Models\SparepartStock;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

trait CalcPrice
{
    public function calculatePricePer($idSparepart, $amountPer)
    {

        $price = 0;
        $stockFromGudang = SparepartStock::all()
            // ->where('id_outlet', $idOutlet)
            ->where('id_sparepart', $idSparepart)
            ->where('status', 'STOCK_IN')
            ->sum('amount');

        $stockSoldMain = SparepartStock::all()
            ->where('id_sparepart', $idSparepart)
            ->where('status', 'STOCK_SOLD_MAINSTORE')
            ->sum('amount');

        $stockSoldACService = SparepartStock::all()
            ->where('id_sparepart', $idSparepart)
            ->where('status', 'STOCK_SOLD_AC')
            ->sum('amount');

        $stockReturned = SparepartStock::all()
            // ->where('id_outlet', $idOutlet)
            ->where('id_sparepart', $idSparepart)
            ->where('status', 'RETURNED')
            ->sum('amount');

        $totalStock = $stockFromGudang - ($stockSoldMain + $stockSoldACService + $stockReturned);
        $checkStockBakpia = $totalStock - $amountPer;

        Log::info($checkStockBakpia . ' | IN ' . $stockFromGudang . ' | SOLD Gudang' . $stockSoldMain . ' SOLD Gudang ' . $stockSoldACService . ' SOLD ServiceAC ' . ' | RETN ' . $stockReturned . " || " . $amountPer);

        if ($checkStockBakpia < 0) {
            Notification::make()
                ->title('Error') // Set the title of the notification
                ->body('No sparepart Stock tersisa | ' . $checkStockBakpia) // Set the body of the notification
                ->danger() // Set the type to danger (for error)
                ->send(); // Send the notification

            // throw new \Exception('Record creation failed due to no bakpia stock left');

            return [0, $totalStock, $checkStockBakpia];
        }
        $price = Sparepart::where('id', $idSparepart)->value('sell_price');

        $price = $price * $amountPer;

        return [$price, $totalStock, $checkStockBakpia];
    }
}
