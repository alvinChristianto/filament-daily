<?php

namespace App\Filament\Resources\AcWorkingComplainResource\Pages;

use App\Filament\Resources\AcWorkingComplainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAcWorkingComplain extends CreateRecord
{
    protected static string $resource = AcWorkingComplainResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
