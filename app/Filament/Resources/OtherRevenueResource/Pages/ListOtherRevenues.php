<?php

namespace App\Filament\Resources\OtherRevenueResource\Pages;

use App\Filament\Resources\OtherRevenueResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOtherRevenues extends ListRecords
{
    protected static string $resource = OtherRevenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
