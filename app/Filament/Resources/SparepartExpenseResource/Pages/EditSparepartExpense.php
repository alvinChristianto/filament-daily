<?php

namespace App\Filament\Resources\SparepartExpenseResource\Pages;

use App\Filament\Resources\SparepartExpenseResource;
use App\Models\DailySparepartTrxRevenueExpenses;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EditSparepartExpense extends EditRecord
{
    protected static string $resource = SparepartExpenseResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {

        return $data;
    }

    protected function afterSave(): void
    {

        /**
         * jika update data pembayaran maka :
         *  delete data record yang terafiliasi di DailySparepartTrxRevenueExpenses, lalu insert lagi
         *  DELETE &  INSERT PATTERN : karena belum ada cara update DailySparepartTrxRevenueExpenses
         */
        $now = Carbon::now();
        $res = $this->record;
        $makePaymentToDailyAB = $res->expense_payment_detail;
        // dd($makePaymentToDailyAB);
        try {
            DB::beginTransaction();

            // delete frist
            if (empty($makePaymentToDailyAB) || !is_array($makePaymentToDailyAB)) {
                // If repeater is empty, just delete all related reports and stop
                DailySparepartTrxRevenueExpenses::where('id_transaction', $res["id_transaction"])->delete();
            } else {

                // --- 1. DELETE ALL OLD RECORDS ---
                // Delete all records in reportTable associated with this expense ID
                DailySparepartTrxRevenueExpenses::where('id_transaction', $res["id_transaction"])->delete();

                foreach ($makePaymentToDailyAB as $key => $value1) {
                    $drCash = 0;
                    $drNonCash = 0;

                    $paymentId = Payment::firstWhere('name', $value1["id_payment_from"]);

                    if ($paymentId->id === 1) {
                        $crCash = $value1["expense_price"];
                        $crNonCash = 0;
                    } else {
                        $crCash = 0;
                        $crNonCash = $value1["expense_price"];
                    }

                    DailySparepartTrxRevenueExpenses::create([
                        'date_record' => $value1['transaction_date'],
                        'id_transaction' =>  $res["id_transaction"],
                        'category' => 'BIAYA_BELI_SPAREPART',

                        'revenue_sell_sparepart' => 0,
                        'revenue_other' =>  0,
                        'expense_buy_sparepart' => $value1["expense_price"],
                        'expense_other' => 0,

                        'payment_category' => $paymentId->id,
                        'dr_cash' => $drCash,
                        'dr_noncash' =>  $drNonCash,
                        'cr_cash' =>  $crCash,
                        'cr_noncash' =>  $crNonCash
                    ]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            Log::warning('fail query ' . $th);

            // If any exception occurs during the operations, roll back the transaction
            DB::rollBack();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
