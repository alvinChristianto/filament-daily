<?php

namespace App\Filament\Resources\LaundryPacketResource\Pages;

use App\Filament\Resources\LaundryPacketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaundryPackets extends ListRecords
{
    protected static string $resource = LaundryPacketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
