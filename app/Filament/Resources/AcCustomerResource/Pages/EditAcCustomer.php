<?php

namespace App\Filament\Resources\AcCustomerResource\Pages;

use App\Filament\Resources\AcCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcCustomer extends EditRecord
{
    protected static string $resource = AcCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
