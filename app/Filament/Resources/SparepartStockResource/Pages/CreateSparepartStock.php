<?php

namespace App\Filament\Resources\SparepartStockResource\Pages;

use App\Filament\Resources\SparepartStockResource;
use App\Models\Sparepart;
use App\Models\SparepartStock;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Colors\Color;

use App\Traits\CalcSparepartStock;

class CreateSparepartStock extends CreateRecord
{
    use CalcSparepartStock;
    public $finalLatestStock = 0;

    protected static string $resource = SparepartStockResource::class;

    protected function getModalDescription($res)
    {
        $idSparepart = $res['id_sparepart'];
        $typeStock = $res['status'];
        $amtToAddOrminus = (int) $res['amount'];

        $modalDescAction = '';

        $sparepartLatestDt = $this->calcLatestStock($idSparepart);

        if ($typeStock === 'ADJUST_PLUS') {
            $this->finalLatestStock = $sparepartLatestDt['current_stock'] + $amtToAddOrminus;
            $modalDescAction = "Penyesuaian Stock untuk " . $sparepartLatestDt['sparepart_name'] . ", Stock terkini adalah " . $sparepartLatestDt['current_stock'] . ", akan (+) " . $amtToAddOrminus . " menjadi " . $this->finalLatestStock;
        } elseif ($typeStock === 'ADJUST_MINUS') {
            $this->finalLatestStock = $sparepartLatestDt['current_stock'] - $amtToAddOrminus;
            $modalDescAction = "Penyesuaian Stock untuk " . $sparepartLatestDt['sparepart_name'] . ", Stock terkini adalah " . $sparepartLatestDt['current_stock'] . ", akan (-) " . $amtToAddOrminus . " menjadi " . $this->finalLatestStock;
        } elseif ($typeStock === 'STOCK_IN') {
            $this->finalLatestStock = $sparepartLatestDt['current_stock'] + $amtToAddOrminus;
            $modalDescAction = "Stock Baru untuk " . $sparepartLatestDt['sparepart_name'] . ", Stock terkini adalah " . $sparepartLatestDt['current_stock'] . ", akan (+) " . $amtToAddOrminus . " menjadi " . $this->finalLatestStock;
        } elseif ($typeStock === 'STOCK_SOLD_MAINSTORE' || $typeStock === 'STOCK_SOLD_AC') {
            $this->finalLatestStock = $sparepartLatestDt['current_stock'] - $amtToAddOrminus;
            $modalDescAction = "Pembelian Sparepart untuk " . $sparepartLatestDt['sparepart_name'] . ", Stock terkini adalah " . $sparepartLatestDt['current_stock'] . ", akan (-) " . $amtToAddOrminus . " menjadi " . $this->finalLatestStock;
        }
        return $modalDescAction;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(form: null)
            ->requiresConfirmation()
            ->modalDescription(function () {
                $res = $this->data;
                $modalDesc = $this->getModalDescription($res);
                return $modalDesc;
            })
            ->modalIconColor(Color::Yellow)
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
    }

    protected function beforeValidate(): void
    {

        $res = $this->data;
        $idSparepart = $res['id_sparepart'];
        $amtToAddOrminus = (int) $res['amount'];

        $sparepartLatestDt = $this->calcLatestStock($idSparepart);
        if ($this->finalLatestStock < 0) {
            $modalDescAction = "update data gagal, stock Akhir tidak boleh minus: ({$sparepartLatestDt['current_stock']}|{$amtToAddOrminus}|{$this->finalLatestStock})";
            Notification::make()
                ->title($modalDescAction)
                ->warning()
                ->duration(5000)
                ->send();
            // 2. Hentikan proses agar tidak lanjut ke database
            $this->halt();
        }

        // dd($res);
        // DONE 31012026 : selanjutnya buat mekanisme input ke database dengan penambahan stock ata pengurangan stock
    }
    // buat mekanisme check apakah jika adjust minus, stock harus  > 0 atau adjus plus,

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
