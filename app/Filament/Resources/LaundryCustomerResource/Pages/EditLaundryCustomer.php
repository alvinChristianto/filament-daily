<?php

namespace App\Filament\Resources\LaundryCustomerResource\Pages;

use App\Filament\Resources\LaundryCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaundryCustomer extends EditRecord
{
    protected static string $resource = LaundryCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
