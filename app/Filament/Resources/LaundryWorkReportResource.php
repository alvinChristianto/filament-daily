<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaundryWorkReportResource\Pages;
use App\Filament\Resources\LaundryWorkReportResource\RelationManagers;
use App\Models\LaundryPacket;
use App\Models\LaundryTransaction;
use App\Models\LaundryWorker;
use App\Models\LaundryWorkReport;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
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
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LaundryWorkReportResource extends Resource
{
    protected static ?string $model = LaundryWorkReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Penggajian Karyawan';
    protected static ?string $navigationGroup = 'Laundry';

    protected static ?string $modelLabel = 'Penggajian Karyawan';

    public static function form(Form $form): Form
    {
        function calculatePriceTransac($id_transc)
        {
            if (!$id_transc) {
                return 0;
            }
            $transctType = LaundryTransaction::where('id_transaction', $id_transc)->first();

            $priceTransact = $transctType->total_price;

            return $priceTransact;
        }

        function calculateWorkingprice($transactionDetail)
        {
            $tempSumAll = 0;
            foreach ($transactionDetail as $key => $trxDetail) {
                // $idBakpia = $trxDetail['id_packet'];
                // $amountBakpia = $trxDetail['amount'];
                $fee = $trxDetail['fee'];
                if (!$fee || !$fee === "") {
                    Notification::make()
                        ->title('Error') // Set the title of the notification
                        ->body('Nilai tidak valid -> $fee ') // Set the body of the notification
                        ->danger() // Set the type to danger (for error)
                        ->send(); // Send the notification
                    return 0;
                }

                $price = $fee;
                Log::info($price);

                $tempSumAll = $tempSumAll + $price;
            }

            return $tempSumAll;
        }

        return $form
            ->schema([
                Select::make('id_transaction')
                    ->label('Id transaksi laundry')
                    ->relationship(
                        'laundryTransaction',
                        'id_transaction',
                        fn ($query) => $query->where('status', 'PAID')
                            ->orderBy('created_at', 'desc')
                    )
                    ->searchable()
                    ->preload()
                    ->required()

                    ->suffixAction(
                        Action::make('copyCostToPrice')
                            ->icon('heroicon-m-calculator')
                            ->action(function (Set $set, Get $get, $state) {
                                $res =  calculatePriceTransac($state);
                                $set('transaction_price', $res);
                            })
                    ),

                Fieldset::make('Detail Pekerjaan')
                    ->schema([
                        Repeater::make('transaction_detail')
                            ->label('Pekerjaan ')
                            ->schema([
                                Select::make('id_packet')
                                    ->label('Paket yang dikerjakan')
                                    ->options(function (Get $get) {
                                        return LaundryPacket::pluck('name', 'id');
                                    })
                                    ->required(),

                                Forms\Components\Select::make('worker')
                                    ->label('petugas')
                                    ->options(function (Get $get) {

                                        return LaundryWorker::all()->pluck('name', 'name');
                                    }),
                                Forms\Components\TextInput::make('kg_amount')
                                    ->numeric()
                                    ->suffix('Kg'),
                                Forms\Components\TextInput::make('fee')
                                    ->numeric()
                                    ->prefix('Rp'),

                            ])
                            ->columnSpan('full')
                            ->columns(3)
                    ]),

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
                                $transaction_detail = $get('transaction_detail');

                                $priceTotl =  calculateWorkingprice($transaction_detail);

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
                Tables\Columns\TextColumn::make('id_transaction')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'SUCCESS' => 'SUCCESS',
                        'CANCEL' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('worker')
                    ->label('Nama pekerja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee_pekerja')
                    ->label('fee pekerja')
                    ->numeric()
                    ->money('idr'),

                Tables\Columns\TextColumn::make('transaction_price')
                    ->label('Harga Klien')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                // ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('working_price')
                    ->label('Harga pengerjaan total')
                    ->numeric()
                    ->money('idr')
                    ->sortable(),
                // ->summarize(Average::make()),

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
                Tables\Filters\SelectFilter::make('worker')
                    ->label('Pekerja')
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
                    // Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename('fee_pekerja_laundry')
                            // ->askForFilename()
                            // ->withFilename(fn ($filename) => 'prefix-' . $filename)
                            ->withColumns([
                                Column::make('id'),
                                Column::make('id_transaction'),
                                Column::make('status'),
                                Column::make('worker'),
                                Column::make('fee_pekerja'),
                                Column::make('transaction_price'),
                                Column::make('working_price'),
                                Column::make('created_at'),

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
            'index' => Pages\ListLaundryWorkReports::route('/'),
            'create' => Pages\CreateLaundryWorkReport::route('/create'),
            'edit' => Pages\EditLaundryWorkReport::route('/{record}/edit'),
        ];
    }
}
