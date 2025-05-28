<?php

namespace App\Filament\Resources\LaundryTransactionResource\Pages;

use App\Filament\Resources\LaundryTransactionResource;
use App\Models\LaundryTransaction;
use Filament\Forms;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditLaundryTransaction extends EditRecord
{
    protected static string $resource = LaundryTransactionResource::class;
    protected $allowedUserIds = [1];

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            Actions\Action::make('Ubah Status')
                ->hidden(fn (LaundryTransaction $record) => $record->status === 'PAID' || !in_array(auth()->id(), $this->allowedUserIds)) // Disable if status is "SELESAI"
                ->form([
                    Forms\Components\Select::make('status')
                        ->options([
                            'PAID' => 'PAID',
                            'CANCEL' => 'CANCEL',
                        ]),
                    // ...
                ])
                ->action(function (array $data, LaundryTransaction $record): void {

                    $record->status = $data['status'];

                    $record->save();
                    Notification::make()
                        ->success()
                        ->title('status laundry updated')
                        ->body('transaksi diubah menjadi PAID/TERBAYAR')
                        ->send();
                }),

        ];
    }
}
