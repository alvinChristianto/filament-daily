<?php

namespace App\Filament\Resources\SparepartShipmentResource\Pages;

use App\Filament\Resources\SparepartShipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ListSparepartShipments extends ListRecords
{
    protected static string $resource = SparepartShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // ExportAction::make()->exports([
            //     ExcelExport::make('table')->fromTable(),
            //     ExcelExport::make('form')->fromForm(),
            // ]),
        ];
    }
}
