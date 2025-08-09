<?php

namespace App\Filament\Widgets;

use App\Models\Sparepart;
use App\Models\SparepartStock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockSparepartStatsOverview extends BaseWidget
{
    public ?Sparepart $record = null;

    protected function getStats(): array
    {
        // Check if the record exists to prevent errors
        if (!$this->record) {
            return [];
        }
        $SpId = $this->record->id;

        $latestSTOCK_IN = SparepartStock::where('id_sparepart', $SpId)
            ->where('status', 'stock_IN')
            ->select('amount')
            ->sum('amount');

        $latestSTOCK_SOLD_MAINSTORE = SparepartStock::where('id_sparepart', $SpId)
            ->where('status', 'STOCK_SOLD_MAINSTORE')
            ->select('amount')
            ->sum('amount');

        $latestSTOCK_SOLD_AC = SparepartStock::where('id_sparepart', $SpId)
            ->where('status', 'STOCK_SOLD_AC')
            ->select('amount')
            ->sum('amount');

        $currentStock = $latestSTOCK_IN - ($latestSTOCK_SOLD_MAINSTORE + $latestSTOCK_SOLD_AC);


        return [
            Stat::make('Stock Terkini', $currentStock)
                ->description('Stok barang terkini yang ada di gudang/toko')
                ->color('primary'),

        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
