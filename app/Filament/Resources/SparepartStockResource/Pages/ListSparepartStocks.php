<?php

namespace App\Filament\Resources\SparepartStockResource\Pages;

use App\Filament\Resources\SparepartStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSparepartStocks extends ListRecords
{
    protected static string $resource = SparepartStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
