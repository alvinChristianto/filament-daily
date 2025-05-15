<?php

namespace App\Filament\Resources\AcWorkingReportResource\Pages;

use App\Filament\Resources\AcWorkingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcWorkingReports extends ListRecords
{
    protected static string $resource = AcWorkingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
