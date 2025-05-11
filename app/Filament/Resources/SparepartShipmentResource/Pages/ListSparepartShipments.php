<?php

namespace App\Filament\Resources\SparepartShipmentResource\Pages;

use App\Filament\Resources\SparepartShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSparepartShipments extends ListRecords
{
    protected static string $resource = SparepartShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
