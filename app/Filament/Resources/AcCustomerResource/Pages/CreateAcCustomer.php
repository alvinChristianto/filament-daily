<?php

namespace App\Filament\Resources\AcCustomerResource\Pages;

use App\Filament\Resources\AcCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAcCustomer extends CreateRecord
{
    protected static string $resource = AcCustomerResource::class;
    
    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('index');
}
}
