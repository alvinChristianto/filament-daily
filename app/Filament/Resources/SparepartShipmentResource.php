<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartShipmentResource\Pages;
use App\Filament\Resources\SparepartShipmentResource\RelationManagers;
use App\Models\Sparepart;
use App\Models\SparepartShipment;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class SparepartShipmentResource extends Resource
{
    protected static ?string $model = SparepartShipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Transaksi Sparepart';
    protected static ?string $navigationGroup = 'Master Sparepart';

    protected static ?string $modelLabel = 'Transaksi Sparepart';
    public static function form(Form $form): Form
    {

        function calculatePrice($transactDetail, $discount)
        {
            $tempSumAll = 0;
            foreach ($transactDetail as $key => $trxDetail) {
                $idPart = $trxDetail['id_sparepart'];
                $amountPart = $trxDetail['sent_stock'];
                $pricePer = $trxDetail['price_per'];

                $price = $pricePer;
                Log::info($price);

                $tempSumAll = $tempSumAll + $price;
            }

            return $tempSumAll - $discount;
        }
        function getRemainingStock($idSparepart)
        {

            $StockIn = SparepartStock::all()
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'STOCK_IN')
                ->sum('amount');

            $StockSold = SparepartStock::all()
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'STOCK_SOLD')
                ->sum('amount');

            return $StockIn - $StockSold;
        }
        function calculatePricePer($idSparepart, $amountPer)
        {

            $price = 0;
            $stockFromGudang = SparepartStock::all()
                // ->where('id_outlet', $idOutlet)
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'STOCK_IN')
                ->sum('amount');

            $stockSold = SparepartStock::all()
                // ->where('id_outlet', $idOutlet)
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'STOCK_SOLD')
                ->sum('amount');

            $stockReturned = SparepartStock::all()
                // ->where('id_outlet', $idOutlet)
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'RETURNED')
                ->sum('amount');

            $totalStock = $stockFromGudang - $stockSold - $stockReturned;
            $checkStockBakpia = $totalStock - $amountPer;

            Log::info($checkStockBakpia . ' | IN ' . $stockFromGudang . ' | SOLD ' . $stockSold . ' | RETN ' . $stockReturned . " || " . $amountPer);

            if ($checkStockBakpia < 0) {
                Notification::make()
                    ->title('Error') // Set the title of the notification
                    ->body('No sparepart Stock tersisa | ' . $checkStockBakpia) // Set the body of the notification
                    ->danger() // Set the type to danger (for error)
                    ->send(); // Send the notification

                // throw new \Exception('Record creation failed due to no bakpia stock left');

                return [0, $totalStock, $checkStockBakpia];
            }
            $price = Sparepart::where('id', $idSparepart)->value('sell_price');

            $price = $price * $amountPer;

            return [$price, $totalStock, $checkStockBakpia];
        }
        return $form
            ->schema([
                Repeater::make('transaction_detail')
                    ->label('detail transaksi sparepart')
                    ->schema([
                        Forms\Components\Select::make('id_sparepart')
                            ->label('nama sparepart')
                            ->options(function (Get $get) {
                                return Sparepart::pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),

                        Forms\Components\TextInput::make('sent_stock')
                            ->label('jumlah unit dijual')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('price_per')
                            ->label('harga per satuan')
                            // ->numeric()
                            // ->disabled()
                            ->dehydrated(true)
                            ->reactive()
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-calculator')
                                    ->action(function (Set $set, Get $get, $state) {
                                        $amountPer = $get('sent_stock');
                                        $idSpareaprt = $get('id_sparepart');

                                        $res =  calculatePricePer($idSpareaprt, $amountPer);

                                        $set('price_per', $res[0]);

                                        $set('stock_latest', $res[1]);

                                        $set('remaining_stock', $res[2]);
                                    })
                            ),
                        Forms\Components\TextInput::make('stock_latest')
                            ->label('stock terakhir')
                            ->readOnly(),

                        Forms\Components\TextInput::make('remaining_stock')
                            ->label('stock setelah dijual')
                            ->readOnly(),

                    ])
                    ->columnSpan('full')
                    ->columns(3),
                Fieldset::make('Data Pembelian')
                    ->schema([
                        Forms\Components\TextInput::make('discount')
                            ->label('nominal diskon')
                            ->default(0)
                            ->numeric(),

                        Forms\Components\TextInput::make('total_price')
                            ->label('total harga yang harus dibayar')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(true)
                            ->reactive()
                            ->required()
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-calculator')
                                    ->action(function (Set $set, Get $get, $state) {
                                        $transaction_detail = $get('transaction_detail');
                                        $discount = $get('discount');

                                        $priceTotl =  calculatePrice($transaction_detail, $discount);
                                        Log::info($priceTotl);
                                        $set('total_price', $priceTotl);
                                    })
                            ),
                        Forms\Components\Select::make('id_warehouse')
                            ->label('Pembeli/Toko Tujuan')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('id_payment')
                            ->label('metode pembayaran')
                            ->relationship('payment', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\FileUpload::make('payment_image')
                            ->label('Foto Pembayaran')
                            ->multiple()
                            ->image(),
                        Forms\Components\DateTimePicker::make('transaction_date')
                            ->label('Tgl Transaksi')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Catatan Penjualan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_transaction')
                    ->label('Id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label(' Pembeli')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'), initial
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Nominal Total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment.name')
                    ->label('metode bayar'),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tgl Transaksi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('dibuat dari'),
                        DatePicker::make('dibuat sampai'),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['dibuat dari'] && !$data['dibuat sampai']) {
                            return null;
                        }
                        $indicatorFrom = 'dibuat dari ' . Carbon::parse($data['dibuat dari'])->toFormattedDateString();
                        $indicatorUntil = ' to ' . Carbon::parse($data['dibuat sampai'])->toFormattedDateString();
                        return $indicatorFrom . " " . $indicatorUntil;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dibuat dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['dibuat sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('status pengiriman')
                    ->options([
                        'SENT' => 'SENT',
                        'RETURNED' => 'RETURNED',
                    ]),
                Tables\Filters\SelectFilter::make('id_payment')
                    ->label('Payment')
                    ->relationship('payment', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename('Penjualan_sparepart')
                            // ->askForFilename()
                            // ->withFilename(fn ($filename) => 'prefix-' . $filename)
                            ->withColumns([
                                Column::make('id_transaction'),
                                Column::make('transaction_date'),
                                Column::make('status'),
                                Column::make('warehouse.name'),
                                Column::make('payment.name'),
                                Column::make('transaction_detail'),
                                Column::make('total_price'),
                                Column::make('discount'),

                            ]),
                    ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSparepartShipments::route('/'),
            'create' => Pages\CreateSparepartShipment::route('/create'),
            'edit' => Pages\EditSparepartShipment::route('/{record}/edit'),
        ];
    }
}
