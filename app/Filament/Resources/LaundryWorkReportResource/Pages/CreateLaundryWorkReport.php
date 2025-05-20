<?php

namespace App\Filament\Resources\LaundryWorkReportResource\Pages;

use App\Filament\Resources\LaundryWorkReportResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaundryWorkReport extends CreateRecord
{
    protected static string $resource = LaundryWorkReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        unset($data["cuci_hidden"]);
        unset($data["lipat_hidden"]);
        unset($data["setrika_hidden"]);


        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "FEE_" . $year . $month . $day . $randomDigits;
        $data['id_report'] = $transformId;
        // dd($data);
        return ($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
