<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaundryWorkReportResource\Pages;
use App\Filament\Resources\LaundryWorkReportResource\RelationManagers;
use App\Models\LaundryTransaction;
use App\Models\LaundryWorker;
use App\Models\LaundryWorkReport;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
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

class LaundryWorkReportResource extends Resource
{
    protected static ?string $model = LaundryWorkReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Penggajian Karyawan';
    protected static ?string $navigationGroup = 'Laundry';

    protected static ?string $modelLabel = 'Penggajian Karyawan';

    public static function form(Form $form): Form
    {
        function calculateWorkerFee($id_transc)
        {
            $transctType = LaundryTransaction::where('id_transaction', $id_transc)->first();
            $idPacket = $transctType->id_packet;
            $priceTransact = $transctType->total_price;

            Log::info('trx ' . $id_transc . ' | ' . $idPacket . ' | ' . $priceTransact);
            if ($idPacket == 1) {
                return [1, 1, 1, $priceTransact];
            } elseif ($idPacket == 2) {
                return [1, 0, 0, $priceTransact];
            } elseif ($idPacket == 3) {
                return [1, 1, 0, $priceTransact];
            } elseif ($idPacket == 4) {
                return [0, 0, 1, $priceTransact];
            }
            return true;
        }

        function calculateWorkingprice($a, $b, $c)
        {
            Log::info('Working Fee ' . $a . ' | ' . $b . ' | ' . $c);
            return $a + $b + $c;
        }

        return $form
            ->schema([
                Hidden::make('cuci_hidden'),
                Hidden::make('lipat_hidden'),
                Hidden::make('setrika_hidden'),
                Select::make('id_transaction')
                    ->label('Id transaksi laundry')
                    ->relationship(
                        'laundryTransaction',
                        'id_transaction',
                        fn ($query) => $query->where('status', 'ONPROGRESS')
                            ->orderBy('created_at', 'desc')
                    )
                    ->searchable()
                    ->preload()
                    ->required()

                    ->suffixAction(
                        Action::make('copyCostToPrice')
                            ->icon('heroicon-m-calculator')
                            ->action(function (Set $set, Get $get, $state) {
                                $res =  calculateWorkerFee($state);
                                $set('cuci_hidden', $res[0]);
                                $set('lipat_hidden', $res[1]);
                                $set('setrika_hidden', $res[2]);
                                $set('transaction_price', $res[3]);
                            })
                    ),
                Fieldset::make('Cuci')
                    ->schema([
                        Forms\Components\Select::make('cuci_worker')
                            ->label('petugas cuci')
                            ->options(function (Get $get) {

                                return LaundryWorker::all()->pluck('name', 'name');
                            }),
                        Forms\Components\TextInput::make('cuci_kg_amount')
                            ->numeric()
                            ->suffix('Kg'),
                        Forms\Components\TextInput::make('cuci_fee')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->dehydrated(true)
                    ->reactive()
                    ->hidden(fn (Get $get) => !$get('cuci_hidden') ?? true), // Use a closure

                Fieldset::make('Lipat')
                    ->schema([
                        Forms\Components\Select::make('lipat_worker')
                            ->label('petugas Lipat')
                            ->options(function (Get $get) {

                                return LaundryWorker::all()->pluck('name', 'name');
                            }),
                        Forms\Components\TextInput::make('lipat_kg_amount')
                            ->numeric()
                            ->suffix('Kg'),
                        Forms\Components\TextInput::make('lipat_fee')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->dehydrated(true)
                    ->reactive()
                    ->hidden(fn (Get $get) => !$get('lipat_hidden') ?? true),

                Fieldset::make('Setrika')
                    ->schema([
                        Forms\Components\Select::make('setrika_worker')
                            ->label('petugas Setrika')
                            ->options(function (Get $get) {

                                return LaundryWorker::all()->pluck('name', 'name');
                            }),
                        Forms\Components\TextInput::make('setrika_kg_amount')
                            ->numeric()
                            ->suffix('Kg'),
                        Forms\Components\TextInput::make('setrika_fee')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->dehydrated(true)
                    ->reactive()
                    ->hidden(fn (Get $get) => !$get('setrika_hidden') ?? true),

                Forms\Components\TextInput::make('transaction_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('working_price')
                    ->required()
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
                                $feeCuci = $get('cuci_fee');
                                $feeLipat = $get('lipat_fee');
                                $feeSetrk = $get('setrika_fee');

                                $priceTotl =  calculateWorkingprice($feeCuci, $feeLipat, $feeSetrk);

                                $set('working_price', $priceTotl);
                            })
                    ),
                Forms\Components\Textarea::make('report_description')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_report')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_transaction')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PAID' => 'success',
                        'ONGOING' => 'info',
                        'CANCEL' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('transaction_price')
                    ->label('Harga Klien')
                    ->numeric()
                    ->money('idr')
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('working_price')
                    ->label('Harga pengerjaan')
                    ->numeric()
                    ->money('idr')
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('cuci_worker')
                    ->label('petugas cuci')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuci_kg_amount')
                    ->label('Kg cuci')
                    ->numeric()
                    ->suffix(' kg')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuci_fee')
                    ->label('fee cuci')
                    ->numeric()
                    ->money('idr')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('lipat_worker')
                    ->label('petugas lipat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lipat_kg_amount')
                    ->label('kg lipat')
                    ->numeric()
                    ->suffix(' kg')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('lipat_fee')
                    ->label('fee lipat')
                    ->numeric()
                    ->money('idr')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('setrika_worker')
                    ->label('petugas setrika')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('setrika_kg_amount')
                    ->label('kg setrika')
                    ->numeric()
                    ->suffix(' kg')
                    ->default(0)
                    ->sortable(),
                Tables\Columns\TextColumn::make('setrika_fee')
                    ->label('fee setrika')
                    ->numeric()
                    ->money('idr')
                    ->default(0)
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
                Tables\Filters\SelectFilter::make('cuci_worker')
                    ->label('Pekerja Cuci')
                    ->options(function (Get $get) {

                        return LaundryWorker::all()->pluck('name', 'name');
                    }),
                Tables\Filters\SelectFilter::make('lipat_worker')
                    ->label('Pekerja Lipat')
                    ->options(function (Get $get) {

                        return LaundryWorker::all()->pluck('name', 'name');
                    }),
                Tables\Filters\SelectFilter::make('setrika_worker')
                    ->label('Pekerja Setrika')
                    ->options(function (Get $get) {

                        return LaundryWorker::all()->pluck('name', 'name');
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
            'index' => Pages\ListLaundryWorkReports::route('/'),
            'create' => Pages\CreateLaundryWorkReport::route('/create'),
            'edit' => Pages\EditLaundryWorkReport::route('/{record}/edit'),
        ];
    }
}
