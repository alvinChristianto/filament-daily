<?php

namespace App\Filament\Resources\SparepartResource\Pages;

use App\Filament\Resources\SparepartResource;
use App\Models\SparepartShipment;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSparepart extends CreateRecord
{
    protected static string $resource = SparepartResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        return $data;
    }

    protected function afterCreate(): void
    {

        $res = $this->record;
        // dd($res);

        $now = Carbon::now();
        SparepartShipment::create([
            'id_warehouse' => '1', //$res['id_outlet'], //lol
            'id_sparepart' => $res["id"],
            'status' => 'INITIAL',
            'remaining_stock' =>  $res["initial_amount"],
            'sent_stock' =>  0,
            'description' => $res["description"],
            'shipment_date' => $now,
        ]);

        SparepartStock::create([
            'id_warehouse' => '1', //$res['id_outlet'], //lol
            'id_sparepart' => $res["id"],
            'id_transaction' => '-',
            'status' => 'STOCK_IN',
            'amount' =>  $res["initial_amount"],
            'description' => $res["description"],
            'stock_record_date' => $now,
        ]);
        // Runs after the form fields are saved to the database.
    }
}
