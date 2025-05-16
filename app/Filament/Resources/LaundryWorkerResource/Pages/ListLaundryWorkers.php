<?php

namespace App\Filament\Resources\LaundryWorkerResource\Pages;

use App\Filament\Resources\LaundryWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaundryWorkers extends ListRecords
{
    protected static string $resource = LaundryWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
