<?php

namespace App\Traits;

use App\Models\Sparepart;
use App\Models\SparepartStock;
use Illuminate\Support\Facades\Log;

trait CalcSparepartStock
{
    public static function calcLatestStock($idSparepart)
    {
        $sparepartDt = Sparepart::find($idSparepart, ['name']);
        if (!$sparepartDt) {
            return null; // Atau throw exception jika id wajib ada
        }
        
        $latestSTOCK_IN = SparepartStock::where('id_sparepart', $idSparepart)
            ->where('status', 'stock_IN')
            ->select('amount')
            ->sum('amount');

        $latestSTOCK_SOLD_MAINSTORE = SparepartStock::where('id_sparepart', $idSparepart)
            ->where('status', 'STOCK_SOLD_MAINSTORE')
            ->select('amount')
            ->sum('amount');

        $latestSTOCK_SOLD_AC = SparepartStock::where('id_sparepart', $idSparepart)
            ->where('status', 'STOCK_SOLD_AC')
            ->select('amount')
            ->sum('amount');

        $latestSTOCK_ADJUST_PLUS = SparepartStock::where('id_sparepart', $idSparepart)
            ->where('status', 'ADJUST_PLUS')
            ->select('amount')
            ->sum('amount');

        $latestSTOCK_ADJUST_MINUS = SparepartStock::where('id_sparepart', $idSparepart)
            ->where('status', 'ADJUST_MINUS')
            ->select('amount')
            ->sum('amount');

        $currentStock = $latestSTOCK_IN - ($latestSTOCK_SOLD_MAINSTORE + $latestSTOCK_SOLD_AC) + $latestSTOCK_ADJUST_PLUS - $latestSTOCK_ADJUST_MINUS;

        return
            [
                'current_stock' => (int) $currentStock,
                'sparepart_name' => $sparepartDt->name,
            ];
    }
}
