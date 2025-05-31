<?php

namespace App\Filament\Resources\LaundryPacketResource\Pages;

use App\Filament\Resources\LaundryPacketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaundryPacket extends EditRecord
{
    protected static string $resource = LaundryPacketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
