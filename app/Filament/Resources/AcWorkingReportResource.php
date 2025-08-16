<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcWorkingReportResource\Pages;
use App\Filament\Resources\AcWorkingReportResource\RelationManagers;
use App\Models\AcWorkingReport;
use App\Models\Sparepart;
use App\Models\SparepartStock;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AcWorkingReportResource extends Resource
{
    protected static ?string $model = AcWorkingReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Laporan Pekerjaan Service AC';
    protected static ?string $navigationGroup = 'Service AC';

    protected static ?string $modelLabel = 'Laporan Pekerjaan Service AC';
    public static function form(Form $form): Form
    {

        function calculatePricePer_AC($idSparepart, $amountPer)
        {

            $price = 0;
            $stockFromGudang = SparepartStock::all()
                // ->where('id_outlet', $idOutlet)
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'STOCK_IN')
                ->sum('amount');

            $stockSoldMain = SparepartStock::all()
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'STOCK_SOLD_MAINSTORE')
                ->sum('amount');

            $stockSoldACService = SparepartStock::all()
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'STOCK_SOLD_AC')
                ->sum('amount');

            $stockReturned = SparepartStock::all()
                // ->where('id_outlet', $idOutlet)
                ->where('id_sparepart', $idSparepart)
                ->where('status', 'RETURNED')
                ->sum('amount');


            $totalStock = $stockFromGudang - ($stockSoldMain + $stockSoldACService + $stockReturned);
            $checkStockBakpia = $totalStock - $amountPer;

            Log::info($checkStockBakpia . ' | IN ' . $stockFromGudang . ' | SOLD Gudang' . $stockSoldMain . ' SOLD Gudang ' . $stockSoldACService . ' SOLD ServiceAC ' . ' | RETN ' . $stockReturned . " || " . $amountPer);

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

        function calculatePrice($transactDetail, $discount)
        {
            $tempSumAll = 0;
            foreach ($transactDetail as $key => $trxDetail) {
                $idBakpia = $trxDetail['id_sparepart'];
                $amountBakpia = $trxDetail['amount'];
                $pricePer = $trxDetail['price_per'];

                $price = $pricePer;
                Log::info($price);

                $tempSumAll = $tempSumAll + $price;
            }

            return $tempSumAll - $discount;
        }

        return $form
            ->schema([

                Fieldset::make('Data Pekerjaan')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Pekerjaan')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('address')
                            ->label('Alamat Service'),
                        Forms\Components\DateTimePicker::make('in_time')
                            ->label('Jam mulai')
                            ->seconds(false)
                            ->timezone('Asia/Jakarta')
                            ->required(),
                        Forms\Components\DateTimePicker::make('out_time')
                            ->label('Jam selesai')
                            ->after('in_time')
                            ->seconds(false)
                            ->timezone('Asia/Jakarta')
                            ->required(),
                        Forms\Components\Textarea::make('working_description')
                            ->label('Catatan Pekerjaan'),
                        Forms\Components\FileUpload::make('image_working')
                            ->label('Foto Pekerjaan')
                            ->multiple()
                            ->image(),
                    ]),
                Fieldset::make('Detail sparepart')
                    ->schema([
                        Repeater::make('transaction_detail')
                            ->label('detail sparepart')
                            ->schema([
                                Forms\Components\Select::make('id_sparepart')
                                    ->label('Nama Sparepart')
                                    ->options(function (Get $get) {
                                        return Sparepart::pluck('name', 'id');
                                    })
                                    ->live()
                                    ->afterStateUpdated(
                                        function (Set $set, Get $get, $state) {
                                            // $state here is the currently selected ID (e.g., '1', '2', etc.)
                                            $selectedSparepartId = $state;

                                            // You can now use $selectedSparepartId to fetch related data or update other fields.
                                            if ($selectedSparepartId) {
                                                $sparepart = Sparepart::find($selectedSparepartId);
                                                if ($sparepart) {
                                                    // Example: Set another TextInput named 'sparepart_price' with the selected sparepart's price
                                                    $set('name_sparepart', $sparepart->name);
                                                    $set('price_sell_sparepart', $sparepart->sell_price);
                                                }
                                            } else {
                                                $set('name_sparepart', null);
                                                $set('price_sell_sparepart', null);
                                            }
                                            Log::info($selectedSparepartId);
                                        }
                                    )
                                    ->searchable(),
                                    // ->required(),

                                Hidden::make('name_sparepart'),
                                Hidden::make('price_sell_sparepart'),
                                Forms\Components\TextInput::make('amount')
                                    ->label('jumlah satuan')
                                    ->integer(),
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
                                                $amountPer = $get('amount');
                                                $idSpareaprt = $get('id_sparepart');

                                                $res =  calculatePricePer_AC($idSpareaprt, $amountPer);

                                                $set('price_per', $res[0]);

                                                $set('stock_latest', $res[1]);

                                                $set('stock_after_sold', $res[2]);
                                            })
                                    ),

                                Forms\Components\TextInput::make('stock_latest')
                                    ->label('stock terakhir')
                                    ->integer()
                                    ->disabled(),

                                Forms\Components\TextInput::make('stock_after_sold')
                                    ->label('stock setelah dijual')
                                    ->integer()
                                    ->disabled(),

                            ])
                            ->columnSpan('full')
                            ->columns(3)
                    ]),
                Fieldset::make('Data Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('discount')
                            ->label('nominal diskon')
                            ->default(0)
                            ->numeric(),

                        Forms\Components\TextInput::make('total_price')
                            ->label('total harga yang harus dibayar')
                            ->numeric()
                            ->prefix('Rp')
                            // ->disabled()
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
                        Forms\Components\Select::make('id_payment')
                            ->label('metode pembayaran')
                            ->relationship('payment', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(3),


                Select::make('id_customer')
                    ->label('data pelanggan')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->createOptionForm([
                        Fieldset::make('Label')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone_number')
                                    ->label('Phone number')
                                    ->tel()
                                    ->required(),

                                Forms\Components\Select::make('category')
                                    ->options([
                                        'PERSON' => 'INDIVIDU',
                                        'PT' => 'BADAN USAHA',
                                        'OTHER' => 'Lainnya'
                                    ])
                                    ->required(),
                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                        '-' => 'Lainnya'
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('address')
                                    ->rows(2)
                                    ->cols(10)
                                    ->columnSpan('full'),


                            ])
                    ])
                    ->required(),
                Select::make('id_worker')
                    ->label('data pekerja')
                    ->relationship('worker', 'name')
                    ->searchable()
                    ->createOptionForm([
                        Fieldset::make('Label')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone_number')
                                    ->label('Phone number')
                                    ->tel()
                                    ->required(),


                                Forms\Components\Select::make('gender')
                                    ->options([
                                        'L' => 'Laki-laki',
                                        'P' => 'Perempuan',
                                        '-' => 'Lainnya'
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('address')
                                    ->rows(2)
                                    ->cols(10)
                                    ->columnSpan('full'),


                            ])
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('next_service_date')
                    ->label('Tanggal Service Berikutnya')
                    ->required()
                    ->displayFormat('d/m/Y')
                    ->minDate(now()),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_report')
                    ->label('Id'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Klien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment.name')
                    ->label('metode bayar'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Pekerjaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('in_time')
                    ->label('waktu mulai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('out_time')
                    ->label('waktu selesai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('total biaya')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SUCCESS' => 'success',
                        'OTHER' => 'info',
                        'FAILED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label('tgl service selanjutnya')
                    ->date()
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
                Tables\Filters\Filter::make('Tanggal Service terdekat')
                    ->form([
                        DatePicker::make('Pengingat Service dari'),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['Pengingat Service dari']) {
                            return null;
                        }
                        $indicatorFrom = 'reminder service dari ' . Carbon::parse($data['Pengingat Service dari'])->toFormattedDateString();
                        return $indicatorFrom;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['Pengingat Service dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('next_service_date', '>=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('id_payment')
                    ->label('Payment')
                    ->relationship('payment', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Pdf')
                    ->icon('heroicon-m-clipboard')
                    ->url(fn (AcWorkingReport $record) => route('acWorkReport.report', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename('report_service_ac')
                            // ->askForFilename()
                            // ->withFilename(fn ($filename) => 'prefix-' . $filename)
                            ->withColumns([
                                Column::make('id_report'),
                                Column::make('status'),
                                Column::make('customer.name'),
                                Column::make('payment.name'),
                                Column::make('worker.name'),
                                Column::make('title'),
                                Column::make('address'),
                                Column::make('working_description'),
                                Column::make('in_time'),
                                Column::make('out_time'),
                                Column::make('transaction_detail'),
                                Column::make('total_price'),
                                Column::make('discount'),
                                Column::make('created_at'),
                                Column::make('next_service_date'),

                            ]),
                    ]),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListAcWorkingReports::route('/'),
            'create' => Pages\CreateAcWorkingReport::route('/create'),
            'edit' => Pages\EditAcWorkingReport::route('/{record}/edit'),
        ];
    }
}
