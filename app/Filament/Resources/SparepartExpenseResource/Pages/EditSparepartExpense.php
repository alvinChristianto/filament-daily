<?php

namespace App\Filament\Resources\SparepartExpenseResource\Pages;

use App\Filament\Resources\SparepartExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartExpense extends EditRecord
{
    protected static string $resource = SparepartExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
