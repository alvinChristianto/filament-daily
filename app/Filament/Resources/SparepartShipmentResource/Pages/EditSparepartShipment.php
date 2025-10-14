<?php

namespace App\Filament\Resources\SparepartShipmentResource\Pages;

use App\Filament\Resources\SparepartShipmentResource;
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
        $idToUpdate = $updateData['id_transaction'] ;
        $trxDate = $updateData['transaction_date'] ;

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
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
