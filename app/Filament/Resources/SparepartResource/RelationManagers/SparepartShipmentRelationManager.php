<?php

namespace App\Filament\Resources\SparepartResource\RelationManagers;

use App\Models\SparepartStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;

class SparepartShipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'sparepartShipment';


    public function form(Form $form): Form
    {

        return $form
            ->schema([
                //pengirisan shipment tidak dilakukan disini karena kendala di edit database sparepart stock, tidak ada mutatebeforesave


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
                    ->disabled(),

                Forms\Components\TextInput::make('sent_stock')
                    ->label('jumlah pengiriman')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description'),
                Forms\Components\DateTimePicker::make('shipment_date')
                    ->required(),

            ]);
    }


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('shipment')
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('outlet')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sparepart.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('remaining_stock')
                    ->label('stock awal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_stock')
                    ->label('stock dikirim')
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
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
