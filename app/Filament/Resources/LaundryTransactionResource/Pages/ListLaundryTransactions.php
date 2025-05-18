<?php

namespace App\Filament\Resources\LaundryTransactionResource\Pages;

use App\Filament\Resources\LaundryTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaundryTransactions extends ListRecords
{
    protected static string $resource = LaundryTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
