<?php

namespace App\Filament\Resources\SparepartExpenseResource\Pages;

use App\Filament\Resources\SparepartExpenseResource;
use App\Models\DailySparepartTrxRevenueExpenses;
use App\Models\Payment;
use App\Models\Sparepart;
use App\Models\SparepartShipment;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    /** 
     *  Runs after : 
     *   - button confirmation getCreateFormAction()
     *  Rund before : 
     *   - the form fields are saved to the database : before mutateFormDataBeforeCreate().
     */
    protected function afterValidate(): void
    {
        // $res = $this->data;
        // foreach ($res['expense_payment_detail'] as $key => &$value) {
        //     $value['id_payment'] = $key;
        // }
        // dd($this);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        $now = Carbon::now();
        $year = $now->format('y'); // Use 'y' for two-digit year representation
        $month = $now->format('m'); // Use 'm' for zero-padded month number
        $day = $now->format('d'); // Use 'm' for zero-padded month number

        // Generate three random digits
        $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

        $transformId = "EXPPRT_" . $year . $month . $day . $randomDigits;
        $data['id_transaction'] = $transformId;
        return $data;
    }
    protected function afterCreate(): void
    {

        /**
         * ada pembiayaan/beli barang baru
         * masukkan ke repeater sparepat resource (for)
         * masukkan ke daily sparep trx
         * 
         * jika updatet maka update ke 2 table ini
         *  updatte daily sparepart seperti harga
         *  update sparepart  ?? mungkin tidak perlu update sparepat
         */
        $now = Carbon::now();
        $res = $this->record;
        $makeSparepartFromArr = $res->expense_sparepart_detail;
        $makePaymentToDailyAB = $res->expense_payment_detail;
        // dd($makePaymentToDailyAB);
        try {
            DB::beginTransaction();

            foreach ($makeSparepartFromArr as $key => $value) {

                $year = $now->format('y'); // Use 'y' for two-digit year representation
                $month = $now->format('m'); // Use 'm' for zero-padded month number
                $day = $now->format('d'); // Use 'm' for zero-padded month number

                // Generate three random digits
                $randomDigits = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);

                $transformId = "PART_" . $year . $month . $day . $randomDigits;
                $id_transaction = $transformId;


                //make sparepart first
                $sparepartMake = Sparepart::create([
                    'name' =>    $value['sparepart_name'],
                    'price'  => $value['buy_price'],
                    'sell_price' => $value['sell_price'],
                    'unit'  => $value['unit'],
                    'initial_amount' =>  $value['initial_amount'],
                    'origin_from' =>  $res['supplier_name'],
                    'description' =>  $value['description'],
                    'status'  =>  $value['status'],
                ]);

                $transactionDetailData = [
                    'id_sparepart' => $sparepartMake->id,
                    'sent_stock' => 0,
                    'price_per' => $value['sell_price'],
                    'stock_latest' => $value['initial_amount'],
                    'remaining_stock' => $value['initial_amount']
                ];

                SparepartShipment::create([
                    'id_transaction' => $id_transaction,
                    'id_payment' => '1',
                    'id_warehouse' => '1',
                    'transaction_detail' => [$transactionDetailData],
                    'status' => 'INITIAL',
                    'total_price' => $value["sell_price"],
                    'discount' => 0,
                    'description' => $value["description"],
                    'transaction_date' => $now,
                ]);

                SparepartStock::create([
                    'id_warehouse' => '1', //$res['id_outlet'], //lol
                    'id_sparepart' => $sparepartMake->id,
                    'id_transaction' => '-',
                    'status' => 'STOCK_IN',
                    'amount' =>  $value["initial_amount"],
                    'description' => $value["description"],
                    'stock_record_date' => $now,
                ]);
            }
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
            //add at 28112025

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
}
