<?php

namespace App\Filament\Resources\SparepartShipmentResource\Pages;

use App\Filament\Resources\SparepartShipmentResource;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSparepartShipment extends CreateRecord
{
    protected static string $resource = SparepartShipmentResource::class;

    
    protected function afterCreate(): void
    {

        $res = $this->record;
        $now = Carbon::now();
        
        SparepartStock::create([
            'id_warehouse' => $res["id_warehouse"], //$res['id_outlet'], //lol
            'id_sparepart' => $res["id_sparepart"],
            'id_transaction' => $res["id"],
            'status' => 'STOCK_IN',
            'amount' =>  $res["sent_stock"],
            'description' => $res["description"],
            'stock_record_date' => $res['shipment_date'],
        ]);
    }
}
