<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartExpenseResource\Pages;
use App\Filament\Resources\SparepartExpenseResource\RelationManagers;
use App\Models\Payment;
use App\Models\SparepartExpense;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class SparepartExpenseResource extends Resource
{
    protected static ?string $model = SparepartExpense::class;

    protected static ?string $navigationIcon = 'heroicon-c-arrow-down-tray';

    protected static ?string $navigationLabel = 'Pembelian Sparepart';
    protected static ?string $navigationGroup = 'Master Sparepart';

    protected static ?string $modelLabel = 'Pembelian Sparepart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('supplier_name')
                    ->required()
                    ->label('Nama Suppliyer/tempat pembelian')
                    ->helperText('Agar lebih mudah dalam pendataan, isikan dengan format <NAMA SUPLIYER | KOTA> ')
                    ->maxLength(100),
                // Forms\Components\TextInput::make('supplier_phone')
                //     ->label('Nomor Telp Supliyer')
                //     ->tel(),
                //notelp

                Fieldset::make('Data semua barang dibeli dari suppliyer ini')
                    ->schema([
                        Repeater::make('expense_sparepart_detail')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('sparepart_name')
                                    ->label('Nama Sparepart')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('buy_price')
                                    ->label('Harga beli barang keseluruhan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->required()
                                    ->maxValue(42949672.95),
                                Forms\Components\Select::make('status')
                                    ->label('Status Barang ketika dibeli')
                                    ->helperText('Kondisi barang ketika dibeli')
                                    ->options([
                                        'NEW' => 'BARU',
                                        'SECOND' => 'SECOND',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('initial_amount')
                                    ->label('Jumlah Pembelian')
                                    ->helperText('Jumlah Pembelian barang')
                                    ->required(),
                                Forms\Components\Select::make('unit')
                                    ->label('Satuan')
                                    ->options([
                                        'pieces' => 'pieces',
                                        'unit' => 'unit',
                                        'buah' => 'buah',
                                        'set' => 'set',
                                        'potong' => 'potong',
                                        'meter' => 'meter',
                                        'kg' => 'kg',
                                        'gram' => 'gram',
                                        'roll' => 'roll',
                                        'liter' => 'liter',
                                        'galon' => 'galon',
                                        'ekor' => 'ekor',
                                        'kubik' => 'kubik',
                                    ])
                                    ->required(),


                                Forms\Components\TextInput::make('sell_price')
                                    ->label('Harga Jual kembali per satuan ')
                                    ->helperText('harga barang ini jika dijual kembali per satuannya')
                                    ->required()
                                    ->numeric()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->prefix('Rp')
                                    ->maxValue(42949672.95),
                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi sparepart')
                                    ->rows(3)
                                    ->maxLength(255),

                            ])
                            ->columns(2)
                            ->grid(2)
                            ->itemLabel(fn(array $state): ?string => $state['sparepart_name'] ?? null)
                            ->addActionLabel('Tambah Barang dibeli')
                            ->collapsed()
                            ->cloneable()
                            ->disabledOn('edit')


                    ])->columns(1),


                Fieldset::make('Data Pembayaran')
                    ->schema([
                        Forms\Components\Textarea::make('expense_notes')
                            ->label('Catatan pembayaran')
                            ->rows(4),
                        Forms\Components\TextInput::make('expense_price_total')
                            ->label('Harga Beli Keseluruhan')
                            ->helperText(new HtmlString('Masukkan data pembayaran pada kolom <b>DETAIL PEMBAYARAN</b>, lalu klik icon calculator'))
                            ->required()
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
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
                                        $payment_detail = $get('expense_sparepart_detail');
                                        $numeric_value_string = str_replace(',', '', array_column($payment_detail, 'buy_price'));
                                        $sumValue = array_sum($numeric_value_string);

                                        $set('expense_price_total', $sumValue);
                                    })
                            ),
                        Repeater::make('expense_payment_detail')
                            ->label('')
                            ->schema([
                                Forms\Components\TextInput::make('payment_category')
                                    ->label('kategori pembayaran')
                                    ->helperText('Isikan Pembayaran apakah DP1, DP2 atau LUNAS dst')
                                    ->required(),
                                Forms\Components\Select::make('id_payment_from')
                                    ->label('metode pembayaran')
                                    ->options(function (Get $get) {
                                        return Payment::all()->pluck('name', 'name');
                                    })
                                    ->required(),

                                Forms\Components\TextInput::make('expense_price')
                                    ->label('Nominal Pembayaran')
                                    ->helperText(new HtmlString('Masukkan nominal pembayaran untuk kategori pembayaran ini'))
                                    ->required()
                                    ->mask(RawJs::make('$money($input)'))
                                    ->stripCharacters(',')
                                    ->numeric()
                                    ->prefix('Rp'),
                                Forms\Components\TextInput::make('id_payment_to')
                                    ->label('Bank Tujuan Transfer / Cash')
                                    ->helperText('Isikan nama bank tujuan transfer, atau isikan CASH jika pembayaran lewat cash')
                                    ->required(),
                                Forms\Components\TextInput::make('account_number')
                                    ->label('Nomor Bank Tujuan')
                                    ->helperText('Isikan nomor rekening, atau isikan CASH jika pembayaran lewat cash ')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('transaction_date')
                                    ->label('Tgl Pembayaran')
                                    ->required(),

                            ])->columns(3)
                            ->columnSpanfull()
                            ->addActionLabel('Tambah Pembayaran')
                            ->itemLabel(fn(array $state): ?string => $state['payment_category'] ?? null)
                            ->cloneable()
                            // ->disabled(fn(string $context): bool => $context === 'edit')
                            ->collapsible(),


                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->helperText('Isikan Status DP jika barang masih belum lunas, Isikan LUNAS jika barang sudah lunas')
                            ->options([
                                'DP' => 'DP',
                                'LUNAS' => 'LUNAS',
                                'BON' => 'BON',
                            ])
                            ->required(),

                    ])->columns(2),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_transaction')
                    ->label('ID transaksi')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('supplier_name')
                    ->label('Nama suppliyer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expense_price_total')
                    ->label('Harga beli total')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn(string $state): string => match ($state) {
                            'DP' => 'warning',
                            'LUNAS' => 'success',
                        }
                    ),

                Tables\Columns\TextColumn::make('expense_notes')
                    ->label('Catatan Pembelian'),

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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'LUNAS' => 'Lunas/Success',
                        'DP' => 'DP',
                    ]),
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['dibuat sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSparepartExpenses::route('/'),
            'create' => Pages\CreateSparepartExpense::route('/create'),
            'edit' => Pages\EditSparepartExpense::route('/{record}/edit'),
        ];
    }
}
