<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SparepartResource\Pages;
use App\Filament\Resources\SparepartResource\RelationManagers;
use App\Models\Sparepart;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class SparepartResource extends Resource
{
    protected static ?string $model = Sparepart::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Sparepart';
    protected static ?string $navigationGroup = 'Master Sparepart';

    protected static ?string $modelLabel = 'Sparepart';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('nama sparepart')
                    ->maxLength(100),
                Forms\Components\TextInput::make('price')
                    ->label('harga beli')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('sell_price')
                    ->label('harga jual')
                    ->required()
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('origin_from')
                    ->label('dibeli dari')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('unit')
                    ->label('satuan')
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
                Forms\Components\TextInput::make('initial_amount')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'NEW' => 'BARU',
                        'SECOND' => 'SECOND',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Beli')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sell_price')
                    ->label('Harga Jual')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('origin_from')
                    ->label('Tempat Pembelian')
                    ->searchable(),

                Tables\Columns\TextColumn::make('initial_amount'),
                Tables\Columns\TextColumn::make('status'),
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
                    // Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename('data_sparepart')
                            // ->askForFilename()
                            // ->withFilename(fn ($filename) => 'prefix-' . $filename)
                            ->withColumns([
                                Column::make('id'),
                                Column::make('status'),
                                Column::make('name'),
                                Column::make('price'),
                                Column::make('sell_price'),
                                Column::make('initial_amount'),
                                Column::make('origin_from'),

                            ]),
                    ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\SparepartShipmentRelationManager::class,
            RelationManagers\SparepartStocksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpareparts::route('/'),
            'create' => Pages\CreateSparepart::route('/create'),
            'edit' => Pages\EditSparepart::route('/{record}/edit'),
        ];
    }
}
