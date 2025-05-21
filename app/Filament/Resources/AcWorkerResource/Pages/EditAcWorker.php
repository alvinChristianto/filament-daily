<?php

namespace App\Filament\Resources\AcWorkerResource\Pages;

use App\Filament\Resources\AcWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcWorker extends EditRecord
{
    protected static string $resource = AcWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
