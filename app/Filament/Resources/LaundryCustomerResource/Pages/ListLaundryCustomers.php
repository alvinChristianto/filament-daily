<?php

namespace App\Filament\Resources\LaundryCustomerResource\Pages;

use App\Filament\Resources\LaundryCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaundryCustomers extends ListRecords
{
    protected static string $resource = LaundryCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
