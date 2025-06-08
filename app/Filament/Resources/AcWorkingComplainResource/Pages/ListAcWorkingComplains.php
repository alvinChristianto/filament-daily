<?php

namespace App\Filament\Resources\AcWorkingComplainResource\Pages;

use App\Filament\Resources\AcWorkingComplainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcWorkingComplains extends ListRecords
{
    protected static string $resource = AcWorkingComplainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
