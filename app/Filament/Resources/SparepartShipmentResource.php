<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartShipmentResource\Pages;
use App\Filament\Resources\SparepartShipmentResource\RelationManagers;
use App\Models\SparepartShipment;
use App\Models\SparepartStock;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SparepartShipmentResource extends Resource
{
    protected static ?string $model = SparepartShipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pengiriman Sparepart';
    protected static ?string $navigationGroup = 'Master Sparepart';

    protected static ?string $modelLabel = 'Pengiriman Sparepart';
    public static function form(Form $form): Form
    {

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
        return $form
            ->schema([
                Forms\Components\Select::make('id_sparepart')
                    ->label('nama barang')
                    ->relationship('sparepart', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('id_warehouse')
                    ->label('Outlet Tujuan')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'SENT' => 'SENT',
                        'RETURNED' => 'RETURNED',
                    ]),
                Forms\Components\TextInput::make('remaining_stock')
                    ->label('jumlah tersisa')
                    ->required()
                    ->disabled()
                    ->dehydrated(true)
                    ->reactive()
                    ->suffixAction(
                        Action::make('copyCostToPrice')
                            ->icon('heroicon-m-calculator')
                            ->action(function (Set $set, Get $get, $state) {
                                $idSparepart = $get('id_sparepart');

                                $remainingStk =  getRemainingStock($idSparepart);
                                $set('remaining_stock', $remainingStk);
                            })
                    ),
                Forms\Components\TextInput::make('sent_stock')
                    ->label('jumlah pengiriman')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description'),
                Forms\Components\DateTimePicker::make('shipment_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sparepart.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipment_date')
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
                //
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
            'index' => Pages\ListSparepartShipments::route('/'),
            'create' => Pages\CreateSparepartShipment::route('/create'),
            'edit' => Pages\EditSparepartShipment::route('/{record}/edit'),
        ];
    }
}
