<?php

namespace App\Filament\Resources\OtherRevenueResource\Pages;

use App\Filament\Resources\OtherRevenueResource;
use App\Models\DailyRevenueExpenses;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOtherRevenue extends CreateRecord
{
    protected static string $resource = OtherRevenueResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);

        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "ORV_" . $year . $month . $day . $randomDigits;
        $data['id_transaction'] = $transformId;
        
        
        return $data;
    }

    protected function afterCreate(): void
    {

        $res = $this->record;
        $drCash = 0;
        $drNonCash = 0;
        $crCash = 0;
        $crNonCash = 0;

        if ($res["id_payment"] == 1) {
            $drCash = $res["total_revenue"];
        } else {
            $drNonCash = $res["total_revenue"];
        }

        $now = Carbon::now();
        if ($res) {
            //probably shound move after ubah status to paid
            DailyRevenueExpenses::create([
                'date_record' => $res["transaction_date"],
                'title' => $res["title"],
                'category' => 'PEND_LAINLAIN',
                'id_transaction' => $res["id_transaction"],
                'revenue_laundry' => 0,
                'revenue_serviceac' => 0,
                'revenue_other' => $res["total_revenue"],
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
