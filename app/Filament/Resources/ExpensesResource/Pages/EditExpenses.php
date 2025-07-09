<?php

namespace App\Filament\Resources\ExpensesResource\Pages;

use App\Filament\Resources\ExpensesResource;
use App\Models\DailyRevenueExpenses;
use App\Models\Expenses;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpenses extends EditRecord
{
    protected static string $resource = ExpensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function (Expenses $record) {
                    $idToDelete = $record->id_expenses;
                    DailyRevenueExpenses::where('id_transaction', $idToDelete )->delete();
                }),
        ];
    }
}
