<?php

namespace App\Filament\Resources\SparepartResource\Pages;

use App\Filament\Resources\SparepartResource;
use App\Filament\Widgets\StockSparepartStatsOverview;
use App\Models\DailySparepartTrxRevenueExpenses;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepart extends EditRecord
{
    protected static string $resource = SparepartResource::class;



    protected function getHeaderWidgets(): array
    {
        return [
            // Return an array of widgets to display
            StockSparepartStatsOverview::class,
        ];
    }

    protected function afterSave(): void
    {
        
        /**
         * 20251028
         * perubahan pada sparepart hanya akan mengubah data di widget dailyspareparttrxrevexp
         * tidak merubah data pada sparepartTransaction dengan alasan data pembelian sparepart 
         * sudah baku di awal input
         * 
         */



        //ALL SPAREPART TRX IS ONLY USING CASH
        $updateData = $this->data;
        // dd($updateData);
        $drCash = 0;
        $drNonCash = 0;
        $crCash = 0;
        $crNonCash = 0;

        // if ($updateData["id_payment"] == 1) {
        //     $drCash = $updateData["price"];
        // } else {
        //     $drNonCash = $updateData["price"];
        // }

        DailySparepartTrxRevenueExpenses::where('id_transaction', (string)$updateData['id'])
            ->update([
                
                //need superadmin to change this
                // 'id_transaction' => $updateData['id_transaction'],
                // 'category' => $updateData['category'],
                // 'revenue_sell_sparepart' => $updateData['revenue_sell_sparepart'],
                // 'revenue_other' => $updateData['revenue_other'],
                // 'expense_buy_sparepart' => $updateData['expense_buy_sparepart'],
                // 'expense_other' => $updateData['expense_other'],

                // 'revenue_other' => $updateData['revenue_other'],
                // 'payment_category' => $updateData["id_payment"],
                // 'dr_cash' => $drCash,
                // 'dr_noncash' =>  $drNonCash,
                'cr_cash' => str_replace(',', '', $updateData["price"]), //20251028 selalu cash
                //ALL SPAREPART TRX IS ONLY USING CASH
                // 'cr_noncash' =>  str_replace(',', '', $crNonCash),
                'expense_buy_sparepart' =>  str_replace(',', '', $updateData["price"])
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
