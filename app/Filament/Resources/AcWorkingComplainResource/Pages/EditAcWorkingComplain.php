<?php

namespace App\Filament\Resources\AcWorkingComplainResource\Pages;

use App\Filament\Resources\AcWorkingComplainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcWorkingComplain extends EditRecord
{
    protected static string $resource = AcWorkingComplainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
