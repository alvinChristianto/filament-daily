<?php

namespace App\Filament\Resources\LaundryCustomerResource\Pages;

use App\Filament\Resources\LaundryCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaundryCustomer extends CreateRecord
{
    protected static string $resource = LaundryCustomerResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
