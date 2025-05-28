<?php

namespace App\Filament\Resources\LaundryTransactionResource\Pages;

use App\Filament\Resources\LaundryTransactionResource;
use App\Models\LaundryCustomer;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaundryTransaction extends CreateRecord
{
    protected static string $resource = LaundryTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $custName = LaundryCustomer::where('id', $data['id_customer'])->value('name');
        $mergedString = preg_replace('/[^a-zA-Z0-9]/', '', $custName);

        // 2. Set all to uppercase
        $str = substr(strtoupper($mergedString), 0, 4);

        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "LDR_" . $year . $month . $day . $randomDigits ."_". $str;
        $data['id_transaction'] = $transformId;

        return ($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
