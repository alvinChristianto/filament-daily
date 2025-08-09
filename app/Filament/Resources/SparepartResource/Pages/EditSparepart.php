<?php

namespace App\Filament\Resources\SparepartResource\Pages;

use App\Filament\Resources\SparepartResource;
use App\Filament\Widgets\StockSparepartStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepart extends EditRecord
{
    protected static string $resource = SparepartResource::class;



    protected function getHeaderWidgets(): array
    {
        return [
            // Return an array of widgets to display
            StockSparepartStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
