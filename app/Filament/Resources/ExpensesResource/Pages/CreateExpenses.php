<?php

namespace App\Filament\Resources\ExpensesResource\Pages;

use App\Filament\Resources\ExpensesResource;
use App\Filament\Widgets\DailyRevExpenses;
use App\Models\DailyRevenueExpenses;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenses extends CreateRecord
{
    protected static string $resource = ExpensesResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "EXP_" . $year . $month . $day . $randomDigits;
        $data['id_expenses'] = $transformId;
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
            $crCash = $res["price_total"];
        } else {
            $crNonCash = $res["price_total"];
        }

        $now = Carbon::now();
        if ($res) {

            DailyRevenueExpenses::create([
                'date_record' => $res["record_date"],
                'title' => $res["title"],
                'category' => 'BIAYA_PEMBIAYAAN',
                'id_transaction' => $res["id_expenses"],
                'revenue_laundry' => 0,
                'revenue_serviceac' => 0,
                'revenue_sparepart' =>  0,
                'expense_buy_sparepart' => 0,
                'expense_other' => $res["price_total"],

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
