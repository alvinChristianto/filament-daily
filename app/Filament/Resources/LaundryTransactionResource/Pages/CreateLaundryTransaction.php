<?php

namespace App\Filament\Resources\LaundryTransactionResource\Pages;

use App\Filament\Resources\LaundryTransactionResource;
use App\Models\DailyRevenueExpenses;
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

        $transformId = "LDR_" . $year . $month . $day . $randomDigits . "_" . $str;
        $data['id_transaction'] = $transformId;

        return ($data);
    }

    protected function afterCreate(): void
    {

        $res = $this->record;
        $drCash = 0;
        $drNonCash = 0;
        $crCash = 0;
        $crNonCash = 0;

        if ($res["id_payment"] == 1) {
            $drCash = $res["total_price"];
        } else {
            $drNonCash = $res["total_price"];
        }
        $now = Carbon::now();
        if ($res) {
            //probably shound move after ubah status to paid
            DailyRevenueExpenses::create([
                'date_record' => $now,
                'title' => $res["id_transaction"],
                'category' => 'PEND_LAUNDRY',
                'id_transaction' => $res["id_transaction"],
                'revenue_laundry' =>  $res["total_price"],
                'revenue_serviceac' => 0,
                'revenue_sparepart' =>  0,
                'expense_buy_sparepart' => 0,
                'expense_other' => 0,


                'payment_category' =>  $res["id_payment"],
                'dr_cash' => $drCash,
                'dr_noncash' =>  $drNonCash,
                'cr_cash' => $crCash,
                'cr_noncash' =>  $crNonCash
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
