<?php

namespace App\Filament\Resources\AcWorkingReportResource\Pages;

use App\Filament\Resources\AcWorkingReportResource;
use App\Models\DailyRevenueExpenses;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcWorkingReport extends EditRecord
{
    protected static string $resource = AcWorkingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
            /**not yet use */
            // Actions\DeleteAction::make()
            //     ->after(function (Expenses $record) {
            //         $idToDelete = $record->id_expenses;
            //         DailyRevenueExpenses::where('id_transaction', $idToDelete)->delete();
            //     }),

        ];
    }

    protected function afterSave(): void
    {
        $updateData = $this->data;
        // dd($updateData);
        $drCash = 0;
        $drNonCash = 0;
        $crCash = 0;
        $crNonCash = 0;

        if ($updateData["id_payment"] == 1) {
            $drCash = $updateData["total_price"];
        } else {
            $drNonCash = $updateData["total_price"];
        }

        DailyRevenueExpenses::where('id_transaction', $updateData['id_report'])
            ->update([
                'title' => $updateData['title'],
                'payment_category' => $updateData["id_payment"],
                'dr_cash' => $drCash,            
                'dr_noncash' =>  $drNonCash,
                // 'cr_cash' => str_replace(',', '', $crCash), //no need to update since the case is pendapatan
                // 'cr_noncash' =>  str_replace(',', '', $crNonCash),
                'revenue_serviceac' =>  str_replace(',', '', $updateData["total_price"])
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
