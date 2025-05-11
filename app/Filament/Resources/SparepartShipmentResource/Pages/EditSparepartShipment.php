<?php

namespace App\Filament\Resources\SparepartShipmentResource\Pages;

use App\Filament\Resources\SparepartShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartShipment extends EditRecord
{
    protected static string $resource = SparepartShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
