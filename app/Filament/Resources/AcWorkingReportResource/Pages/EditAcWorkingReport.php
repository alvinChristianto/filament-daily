<?php

namespace App\Filament\Resources\AcWorkingReportResource\Pages;

use App\Filament\Resources\AcWorkingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcWorkingReport extends EditRecord
{
    protected static string $resource = AcWorkingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
