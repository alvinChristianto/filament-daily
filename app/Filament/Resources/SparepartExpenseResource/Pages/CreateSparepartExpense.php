<?php

namespace App\Filament\Resources\SparepartExpenseResource\Pages;

use App\Filament\Resources\SparepartExpenseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Colors\Color;

class CreateSparepartExpense extends CreateRecord
{
    protected static string $resource = SparepartExpenseResource::class;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(form: null)
            ->requiresConfirmation()
            ->modalDescription('Pastikan anda melengkapi semua data termasuk klik Icon Calculator pada data HARGA BELI KESELURUHAN ')
            ->modalIconColor(Color::Green)
            ->action(function () {
                $this->closeActionModal();
                $this->create();
            });
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        dd($data);
        return $data;
    }
    protected function afterCreate(): void
    {
        $res = $this->record;
    }
}
