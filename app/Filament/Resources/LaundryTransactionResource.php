<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaundryTransactionResource\Pages;
use App\Filament\Resources\LaundryTransactionResource\RelationManagers;
use App\Models\LaundryPacket;
use App\Models\LaundryTransaction;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LaundryTransactionResource extends Resource
{
    protected static ?string $model = LaundryTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Transaksi Laundry';
    protected static ?string $navigationGroup = 'Laundry';

    protected static ?string $modelLabel = 'Transaksi Laundry';


    public static function form(Form $form): Form
    {

        function calculatePricePer($id_packet, $kg_amount)
        {
            $totPrice = 0;
            $priceForPacket = LaundryPacket::where('id', $id_packet)->value('base_price');

            if (!$id_packet) {
                Notification::make()
                    ->title('Error') // Set the title of the notification
                    ->body('Nilai tidak valid -> $id_packet ') // Set the body of the notification
                    ->danger() // Set the type to danger (for error)
                    ->send(); // Send the notification
                return 0;
            }

            if ($kg_amount === "") {
                Notification::make()
                    ->title('Error') // Set the title of the notification
                    ->body('Nilai tidak valid -> $kg_amount ') // Set the body of the notification
                    ->danger() // Set the type to danger (for error)
                    ->send(); // Send the notification
                return 0;
            }
            $totPrice = ($priceForPacket * $kg_amount);

            Log::info($totPrice . ' | packet price ' . $priceForPacket . ' | KG ' . $kg_amount . ' | disc ' . 0);

            return $totPrice;
        }

        function calculatePrice($transactDetail, $discount)
        {
            $tempSumAll = 0;
            foreach ($transactDetail as $key => $trxDetail) {
                // $idBakpia = $trxDetail['id_packet'];
                // $amountBakpia = $trxDetail['amount'];
                $pricePer = $trxDetail['price_per'];

                $price = $pricePer;
                Log::info($price);

                $tempSumAll = $tempSumAll + $price;
            }

            return $tempSumAll - $discount;
        }

        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->label('Status Laundry')
                    // ->hidden(!auth()->user()->hasRole('super_admin'))
                    ->disabled(),
                Fieldset::make('Detail paket laundry')
                    ->schema([
                        Repeater::make('transaction_detail')
                            ->label('paket laundry')
                            ->schema([
                                Select::make('id_packet')
                                    ->label('Paket yang dipilih')
                                    ->options(function (Get $get) {
                                        return LaundryPacket::pluck('name', 'id');
                                    })
                                    ->live()
                                    ->afterStateUpdated(
                                        function (Set $set, Get $get, $state) {
                                            // $state here is the currently selected ID (e.g., '1', '2', etc.)
                                            $selectedPacketId = $state;

                                            // You can now use $selectedPacketId to fetch related data or update other fields.
                                            if ($selectedPacketId) {
                                                $sparepart = LaundryPacket::find($selectedPacketId);
                                                if ($sparepart) {
                                                    // Example: Set another TextInput named 'sparepart_price' with the selected sparepart's price
                                                    $set('name_packet', $sparepart->name);
                                                    $set('price_packet', $sparepart->base_price);
                                                }
                                            } else {
                                                $set('name_sparepart', null);
                                                $set('price_sell_sparepart', null);
                                            }
                                            Log::info($selectedPacketId);
                                        }
                                    )
                                    ->searchable()
                                    ->required(),

                                Hidden::make('name_packet'),
                                Hidden::make('price_packet'),
                                Forms\Components\TextInput::make('kg_amount')
                                    ->label('Berat (Kg)')
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
                                                $amountPer = $get('kg_amount');
                                                $idPacket = $get('id_packet');

                                                $res =  calculatePricePer($idPacket, $amountPer);

                                                $set('price_per', $res);
                                            })
                                    ),

                            ])
                            ->columnSpan('full')
                            ->columns(3),
                    ]),
                Fieldset::make('Data Pelanggan dan Pembayaran')
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
                        Select::make('id_customer')
                            ->label('data pelanggan')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->createOptionForm([
                                Fieldset::make('Buat data pelanggan laundry')
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
                        Select::make('id_payment')
                            ->label('metode pembayaran')
                            ->relationship('payment', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                    ])
                    ->columns(2),
                Forms\Components\DateTimePicker::make('finish_date')
                    ->label('Tanggal Selesai ')
                    ->seconds(false)
                    ->timezone('Asia/Jakarta'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_transaction')
                    ->label('Id'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PAID' => 'success',
                        'ONPROGRESS' => 'info',
                        'CANCEL' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Klien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment.name')
                    ->label('metode bayar'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('total biaya')
                    ->numeric()
                    ->money('idr')
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('finish_date')
                    ->label('Tgl Selesai')
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
                Tables\Filters\Filter::make('Tanggal Harus Selesai')
                    ->form([
                        DatePicker::make('Harus selesai sebelum'),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['Harus selesai sebelum']) {
                            return null;
                        }
                        $indicatorFrom = 'Harus selesai sebelum ' . Carbon::parse($data['Harus selesai sebelum'])->toFormattedDateString();
                        return $indicatorFrom;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['Harus selesai sebelum'],
                                fn (Builder $query, $date): Builder => $query->whereDate('finish_date', '<=', $date)->where('status', 'ONPROGRESS'),
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
                    ->url(fn (LaundryTransaction $record) => route('laundryTransaction.report', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename('report_laundry')
                            // ->askForFilename()
                            // ->withFilename(fn ($filename) => 'prefix-' . $filename)
                            ->withColumns([
                                Column::make('id_transaction'),
                                Column::make('status'),
                                Column::make('customer.name'),
                                Column::make('payment.name'),
                                Column::make('transaction_detail'),
                                Column::make('total_price'),
                                Column::make('discount'),
                                Column::make('finish_date'),

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
            'index' => Pages\ListLaundryTransactions::route('/'),
            'create' => Pages\CreateLaundryTransaction::route('/create'),
            'edit' => Pages\EditLaundryTransaction::route('/{record}/edit'),
        ];
    }
}
