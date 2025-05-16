<?php

namespace App\Filament\Resources\LaundryWorkerResource\Pages;

use App\Filament\Resources\LaundryWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaundryWorker extends CreateRecord
{
    protected static string $resource = LaundryWorkerResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
