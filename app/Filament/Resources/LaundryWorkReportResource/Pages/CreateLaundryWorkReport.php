<?php

namespace App\Filament\Resources\LaundryWorkReportResource\Pages;

use App\Filament\Resources\LaundryWorkReportResource;
use App\Models\LaundryWorker;
use App\Models\LaundryWorkReport;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLaundryWorkReport extends CreateRecord
{
    protected static string $resource = LaundryWorkReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $transactionDetails = $data['transaction_detail'];
        $firstNMinusOneItems = array_slice($transactionDetails, 0, count($transactionDetails) - 1);
        $lastWorkerData = end($transactionDetails);


        // Check if the array has more than one element
        if (count($transactionDetails) > 1) {
            // Slice the array from the beginning (offset 0) up to (count - 1) elements
            $firstNMinusOneItems = array_slice($transactionDetails, 0, count($transactionDetails) - 1);
            foreach ($firstNMinusOneItems as $key => $value) {

                LaundryWorkReport::create([
                    'id_transaction' => $data['id_transaction'],
                    'transaction_price' => $data['transaction_price'],
                    'working_price' => $data['working_price'],
                    'report_description' => $data['report_description'],
                    'worker' => $value['worker'],
                    'fee_pekerja' => $value['fee'],
                    'transaction_detail' => [$value]

                ]);
            }
        } else {
            // If there's 1 or 0 elements, return an empty array or handle as needed
            $firstNMinusOneItems = [];
        }


        $data['transaction_detail'] = [$lastWorkerData];
        $data['worker'] = $lastWorkerData['worker'];
        $data['fee_pekerja'] = $lastWorkerData['fee'];
        return ($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
