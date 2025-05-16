<?php

namespace App\Filament\Resources\LaundryPacketResource\Pages;

use App\Filament\Resources\LaundryPacketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaundryPacket extends CreateRecord
{
    protected static string $resource = LaundryPacketResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
