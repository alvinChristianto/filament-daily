<?php

namespace App\Filament\Resources\AcWorkerResource\Pages;

use App\Filament\Resources\AcWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcWorkers extends ListRecords
{
    protected static string $resource = AcWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
