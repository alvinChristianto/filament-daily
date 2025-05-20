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
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class LaundryTransactionResource extends Resource
{
    protected static ?string $model = LaundryTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Transaksi Laundry';
    protected static ?string $navigationGroup = 'Laundry';

    protected static ?string $modelLabel = 'Transaksi Laundry';


    public static function form(Form $form): Form
    {

        function calculatePrice($id_packet, $kg_amount, $discount)
        {
            $totPrice = 0;
            $priceForPacket = LaundryPacket::where('id', $id_packet)->value('base_price');


            $totPrice = ($priceForPacket * $kg_amount) - $discount;

            Log::info($totPrice . ' | packet price ' . $priceForPacket . ' | KG ' . $kg_amount . ' | disc ' . $discount);

            return $totPrice;
        }

        return $form
            ->schema([
                Select::make('id_packet')
                    ->label('Paket yang dipilih')
                    ->relationship('packet', 'name')
                    ->preload()
                    ->required(),

                Fieldset::make('Data Pelanggan dan Pembayaran')

                    ->schema([

                        Forms\Components\TextInput::make('kg_amount')
                            ->label('Berat (Kg)')
                            ->numeric(),
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
                                        $id_packet = $get('id_packet');
                                        $kg_amount = $get('kg_amount');
                                        $discount = $get('discount');

                                        $priceTotl =  calculatePrice($id_packet, $kg_amount, $discount);
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
                    ->columns(3),
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
                Tables\Columns\TextColumn::make('packet.alias')
                    ->label('Paket'),
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
