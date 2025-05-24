<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcWorkingReportResource\Pages\EditAcWorkingReport;
use App\Filament\Resources\SparepartStockResource\Pages;
use App\Filament\Resources\SparepartStockResource\RelationManagers;
use App\Models\SparepartStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SparepartStockResource extends Resource
{
    protected static ?string $model = SparepartStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Stok Sparepart';
    protected static ?string $navigationGroup = 'Master Sparepart';

    protected static ?string $modelLabel = 'Stok Sparepart';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_warehouse')
                    ->label('Gudang Asal')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('id_sparepart')
                    ->label('nama Sparepart')
                    ->relationship('sparepart', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('id_transaction')
                    ->label('Id Transaksi')
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'STOCK_IN' => 'STOCK_IN',
                        'STOCK_SOLD_MAINSTORE' => 'STOCK_SOLD_MAINSTORE',
                        'STOCK_SOLD_AC' => 'STOCK_SOLD_AC',
                        'RETURNED' => 'RETURNED',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah sparepart')
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('stock_record_date')
                    ->label('Tanggal Perubahan Stock')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->recordUrl(
            //     fn (Model $record): string => EditAcWorkingReport::getUrl([$record->id_transaction]),
            // )
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Data Gudang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sparepart.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_transaction')
                    ->label('Id Transaksi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'STOCK_IN' => 'info',
                        'STOCK_SOLD_MAINSTORE' => 'success',
                        'STOCK_SOLD_AC' => 'success',
                        'RETURNED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah sparepart')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_record_date')
                    ->label('Tanggal Perubahan Stock')
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('status stock')
                    ->options([
                       'STOCK_IN' => 'STOCK_IN',
                        'STOCK_SOLD_MAINSTORE' => 'STOCK_SOLD_MAINSTORE',
                        'STOCK_SOLD_AC' => 'STOCK_SOLD_AC',
                        'RETURNED' => 'RETURNED',
                    ]),
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
            'index' => Pages\ListSparepartStocks::route('/'),
            'create' => Pages\CreateSparepartStock::route('/create'),
            'edit' => Pages\EditSparepartStock::route('/{record}/edit'),
        ];
    }
}
