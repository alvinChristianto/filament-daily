<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartExpenseResource\Pages;
use App\Filament\Resources\SparepartExpenseResource\RelationManagers;
use App\Models\SparepartExpense;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
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
                    ->helperText('Agar lebih mudah dalam pendataan, isikan dengan format <NAMA SUPLIYER | TANGGAL TRANSAKSI> ')
                    ->maxLength(100)
                    ->columnSpanFull(),

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
                                    ->label('Harga beli barang')
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
                            // ->columnSpanfull()
                            ->addActionLabel('Tambah Barang dibeli')
                            ->collapsed()
                            ->cloneable()

                    ])->columns(1),

                Fieldset::make('Data Pembayaran')
                    ->schema([

                        Forms\Components\Textarea::make('expense_notes')
                            ->label('Catatan pembayaran')
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('expense_payment_detail')
                            ->label('Detail Pembayaran')
                            ->helperText('Isikan Pembayaran apakah DP atau lunas, Pada nominal bayar, diisi dengan nominal rupiah tanpa (titik) atau (koma)')
                            ->addActionLabel('Tambah Pembayaran')
                            // ->addable(false)
                            ->deletable(false)
                            ->keyLabel('Label Bayar')
                            ->keyPlaceholder('DP1, DP2 atau LUNAS')
                            // ->editableKeys(false)
                            ->valueLabel('Nominal Bayar')
                            ->required(),
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

                        // Forms\Components\Select::make('id_payment')
                        // ->label('Metode pembayaran')
                        // ->helperText('Isikan Status DP jika barang masih belum lunas, Isikan LUNAS jika barang sudah lunas')
                        // ->options([
                        //     'CASH' => 'CASH',
                        //     'BCA' => 'BCA',
                        // ])
                        // ->required(),
                        Forms\Components\Select::make('id_payment')
                            ->label('metode pembayaran')
                            ->relationship('payment', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->helperText('Isikan Status DP jika barang masih belum lunas, Isikan LUNAS jika barang sudah lunas')
                            ->options([
                                'DP' => 'DP',
                                'LUNAS' => 'LUNAS',
                            ])
                            ->required(),

                    ]),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No'),
                Tables\Columns\TextColumn::make('supplier_name')
                    ->label('Nama suppliyer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expense_price_total')
                    ->label('Harga beli total')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),

                Tables\Columns\TextColumn::make('payment.name')
                    ->label('metode bayar'),
                Tables\Columns\TextColumn::make('expense_notes'),

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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSparepartExpenses::route('/'),
            'create' => Pages\CreateSparepartExpense::route('/create'),
            'edit' => Pages\EditSparepartExpense::route('/{record}/edit'),
        ];
    }
}
