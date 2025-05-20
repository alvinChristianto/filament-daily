<?php

namespace App\Filament\Resources\LaundryWorkReportResource\Pages;

use App\Filament\Resources\LaundryWorkReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaundryWorkReport extends EditRecord
{
    protected static string $resource = LaundryWorkReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
