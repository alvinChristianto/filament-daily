<?php

namespace App\Filament\Resources\LaundryWorkerResource\Pages;

use App\Filament\Resources\LaundryWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaundryWorker extends EditRecord
{
    protected static string $resource = LaundryWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
