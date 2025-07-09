<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpensesResource\Pages;
use App\Filament\Resources\ExpensesResource\RelationManagers;
use App\Models\Expenses;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ExpensesResource extends Resource
{
    protected static ?string $model = Expenses::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Pembiayaan';
    protected static ?string $navigationGroup = 'Pembiayaan';

    protected static ?string $modelLabel = 'Pembiayaan';
    public static function form(Form $form): Form
    {
        $typeOfExpenses = [
            'GAJI_LAUNDRY' => 'GAJI_LAUNDRY',
            'GAJI_SERVICE_AC' => 'GAJI_SERVICE_AC',
            'BIAYA_LAUNDRY' => 'BIAYA_LAUNDRY',
            'BIAYA_SPAREPART' => 'BIAYA_SPAREPART',
            'BIAYA_SERVICE_MOTOR' => 'BIAYA_SERVICE_MOTOR',
            'MAKAN_MINUM_SERVICE_AC' => 'MAKAN_MINUM_SERVICE_AC',
            'BENSIN' => 'BENSIN',
            'LAIN-LAIN' => 'LAIN-LAIN',
        ];

        function calculatePricePer($price_per, $amount)
        {
            // dd($amount);
            return $price_per * $amount;
        }

        return $form
            ->schema([
                Fieldset::make('Data Pengeluaran')
                    ->schema([
                        Forms\Components\DatePicker::make('record_date')
                            ->label('Tanggal pembiayaan')
                            ->required()
                            ->displayFormat('d/m/Y'),
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Pembiayaan')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(100),
                        Forms\Components\Select::make('category')
                            ->label('kategori biaya')
                            ->options($typeOfExpenses),
                        // Forms\Components\TextInput::make('amount')
                        //     ->label('jumlah satuan')
                        //     ->integer(),
                        // Forms\Components\Select::make('unit')
                        //     ->label('satuan')
                        //     ->options([
                        //         'pieces' => 'pieces',
                        //         'unit' => 'unit',
                        //         'buah' => 'buah',
                        //         'set' => 'set',
                        //         'potong' => 'potong',
                        //         'meter' => 'meter',
                        //         'kg' => 'kg',
                        //         'gram' => 'gram',
                        //         'roll' => 'roll',
                        //         'liter' => 'liter',
                        //         'galon' => 'galon',
                        //         'ekor' => 'ekor',
                        //         'kubik' => 'kubik',
                        //     ])
                        //     ->required(),
                        // Forms\Components\TextInput::make('price_per')
                        //     ->label('harga per satuan')
                        //     ->numeric()
                        //     ->prefix('Rp')
                        //     ->dehydrated(true)
                        //     ->reactive()
                        //     ->suffixAction(
                        //         Action::make('copyCostToPrice')
                        //             ->icon('heroicon-m-calculator')
                        //             ->action(function (Set $set, Get $get, $state) {
                        //                 $amountPer = $get('amount');

                        //                 $res =  calculatePricePer($state, $amountPer);

                        //                 $set('price_total', $res);
                        //             })
                        //     ),
                        Forms\Components\TextInput::make('price_total')
                            ->label('harga total')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters(',')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('origin_from')
                            ->label('Dibelanjakan oleh')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Catatan Pembiayaan'),
                        Forms\Components\Select::make('id_payment')
                            ->label('metode pembayaran')
                            ->relationship('payment', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\FileUpload::make('image_expenses')
                            ->label('Foto nota/biaya')
                            ->multiple()
                            ->image(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id_expenses')
                //     ->label('Id')
                //     ->sortable()
                //     ->searchable(),

                Tables\Columns\TextColumn::make('record_date')
                    ->label('Tgl Pembiayaan')
                    // ->date('d m Y H:i:s')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Pembiayaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment.name')
                    ->label('metode bayar'),
                Tables\Columns\TextColumn::make('category')
                    ->label('kategori'),
                // Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('price_total')
                    ->label('Nominal Total')
                    ->money('idr')
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('id_expenses')
                    ->label('Id transaksi')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['dibuat sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename('pembiayaan')
                            // ->askForFilename()
                            // ->withFilename(fn ($filename) => 'prefix-' . $filename)
                            ->withColumns([
                                Column::make('id_expenses'),
                                Column::make('category'),
                                Column::make('title'),
                                Column::make('payment.name'),
                                Column::make('origin_from'),
                                Column::make('description'),
                                Column::make('created_at'),

                            ]),
                    ]),
                ]),
            ])
            // ->defaultGroup('category')

            // ->groupingSettingsHidden(false)
            // ->groupsOnly()

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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpenses::route('/create'),
            'edit' => Pages\EditExpenses::route('/{record}/edit'),
        ];
    }
}
