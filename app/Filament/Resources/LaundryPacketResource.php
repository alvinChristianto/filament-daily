<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaundryPacketResource\Pages;
use App\Filament\Resources\LaundryPacketResource\RelationManagers;
use App\Models\LaundryPacket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaundryPacketResource extends Resource
{
    protected static ?string $model = LaundryPacket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Paket Laundry';
    protected static ?string $navigationGroup = 'Laundry';
    protected static ?string $modelLabel = 'Paket Laundry';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('nama paket')
                    ->maxLength(100),
                Forms\Components\TextInput::make('alias')
                    ->required()
                    ->label('alias')
                    ->maxLength(100),
                Forms\Components\TextInput::make('base_price')
                    ->label('harga paket per Kg')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Textarea::make('description')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('alias')
                    ->label('Nama alias')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama paket')
                    ->searchable(),
                //
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
            'index' => Pages\ListLaundryPackets::route('/'),
            'create' => Pages\CreateLaundryPacket::route('/create'),
            'edit' => Pages\EditLaundryPacket::route('/{record}/edit'),
        ];
    }
}
