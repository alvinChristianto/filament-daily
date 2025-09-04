<?php

namespace App\Filament\Resources\OtherRevenueResource\Pages;

use App\Filament\Resources\OtherRevenueResource;
use App\Models\DailyRevenueExpenses;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOtherRevenue extends EditRecord
{
    protected static string $resource = OtherRevenueResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
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
            $drCash = $updateData["total_revenue"];
        } else {
            $drNonCash = $updateData["total_revenue"];
        }

        DailyRevenueExpenses::where('id_transaction', $updateData['id_transaction'])
            ->update([
                'date_record' => $updateData["transaction_date"],
                'title' => $updateData['title'],
                'payment_category' => $updateData["id_payment"],
                'dr_cash' => $drCash,            //no need to update since it is expenses
                'dr_noncash' =>  $drNonCash,
                // 'cr_cash' => str_replace(',', '', $crCash),
                // 'cr_noncash' =>  str_replace(',', '', $crNonCash),
                'revenue_other' =>  str_replace(',', '', $updateData["total_revenue"])
            ]);
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
