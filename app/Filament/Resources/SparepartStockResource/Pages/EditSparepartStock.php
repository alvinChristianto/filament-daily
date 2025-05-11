<?php

namespace App\Filament\Resources\SparepartStockResource\Pages;

use App\Filament\Resources\SparepartStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartStock extends EditRecord
{
    protected static string $resource = SparepartStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
