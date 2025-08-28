<?php

namespace App\Filament\Resources\ExpensesResource\Pages;

use App\Filament\Resources\ExpensesResource;
use App\Models\DailyRevenueExpenses;
use App\Models\Expenses;
use Filament\Actions;
use Filament\Actions\Action; // Import Action for custom actions if needed
use Filament\Actions\EditAction;
use Filament\Actions\SaveAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditExpenses extends EditRecord
{
    protected static string $resource = ExpensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function (Expenses $record) {
                    $idToDelete = $record->id_expenses;
                    DailyRevenueExpenses::where('id_transaction', $idToDelete)->delete();
                }),

        ];
    }

    protected function afterSave(): void
    {
        $updateData = $this->data;

        $drCash = 0;
        $drNonCash = 0;
        $crCash = 0;
        $crNonCash = 0;

        if ($updateData["id_payment"] == 1) {
            $crCash = $updateData["price_total"];
        } else {
            $crNonCash = $updateData["price_total"];
        }

        DailyRevenueExpenses::where('id_transaction', $updateData['id_expenses'])
            ->update([
                'date_record' => $updateData["record_date"],
                'title' => $updateData['title'],
                'payment_category' => $updateData["id_payment"],
                // 'dr_cash' => $drCash,            //no need to update since it is expenses
                // 'dr_noncash' =>  $drNonCash,
                'cr_cash' => str_replace(',', '', $crCash),
                'cr_noncash' =>  str_replace(',', '', $crNonCash),
                'expense_other' =>  str_replace(',', '', $updateData["price_total"])
            ]);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
