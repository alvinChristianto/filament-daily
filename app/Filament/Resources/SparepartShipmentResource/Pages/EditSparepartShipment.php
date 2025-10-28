<?php

namespace App\Filament\Resources\SparepartShipmentResource\Pages;

use App\Filament\Resources\SparepartShipmentResource;
use App\Models\DailySparepartTrxRevenueExpenses;
use App\Models\SparepartStock;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSparepartShipment extends EditRecord
{
    protected static string $resource = SparepartShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $this->data = $data;
    //     dd($data);
    //     return $data;   
    // }

    protected function afterSave(): void
    {
        //20251014 update sparepart stock if there is any changes in the sparepart transaction details
        $updateData = $this->data;
        $sparepartStockToUpdate = $updateData['transaction_detail'];
        $idToUpdate = $updateData['id_transaction'];
        $trxDate = $updateData['transaction_date'];

        foreach ($sparepartStockToUpdate as $key => $value) {
            SparepartStock::where('id_transaction', $updateData['id_transaction'])
                ->update([
                    'id_warehouse' => '1', //$res["id_warehouse"], //$res['id_outlet'], //lol
                    'id_sparepart' => $value["id_sparepart"],
                    'id_transaction' => $idToUpdate,
                    'status' => 'STOCK_SOLD_MAINSTORE',     //stock sold from mainstore 
                    'amount' =>  $value["sent_stock"],
                    'description' => "",
                    'stock_record_date' => $trxDate,
                ]);

            # code...
        }

        /**
         * 20251028
         * perubahan pada sparepart shipment akan  mengubah data di widget dailyspareparttrxrevexp
         * 
         */

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

        DailySparepartTrxRevenueExpenses::where('id_transaction', (string)$idToUpdate)
            ->update([

                //need superadmin to change this
                // 'id_transaction' => $updateData['id_transaction'],
                // 'category' => $updateData['category'],
                'revenue_sell_sparepart' =>  str_replace(',', '', $updateData["total_price"]),
                // 'revenue_other' => $updateData['revenue_other'],
                // 'expense_buy_sparepart' => $updateData['expense_buy_sparepart'],
                // 'expense_other' => $updateData['expense_other'],

                // 'revenue_other' => $updateData['revenue_other'],
                'payment_category' => $updateData["id_payment"],
                'dr_cash' => $drCash,
                'dr_noncash' =>  $drNonCash,
                //ALL SPAREPART TRX IS ONLY USING CASH
                // 'cr_cash' =>  str_replace(',', '', $crNonCash),
                // 'cr_noncash' =>  str_replace(',', '', $crNonCash),
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
