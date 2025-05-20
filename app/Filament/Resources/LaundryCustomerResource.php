<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaundryCustomerResource\Pages;
use App\Filament\Resources\LaundryCustomerResource\RelationManagers;
use App\Models\LaundryCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaundryCustomerResource extends Resource
{
    protected static ?string $model = LaundryCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Customer Laundry';
    protected static ?string $navigationGroup = 'Laundry';
    protected static ?string $modelLabel = 'Customer Laundry';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(100)
                    ->columnSpanFull(),
                Forms\Components\Select::make('gender')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        '-' => '-',
                    ]),
                Forms\Components\Select::make('category')
                    ->label('Kategory')
                    ->options([
                        'PERSON' => 'PERSON',
                        'PT' => 'PT',
                        'OTHER' => 'OTHER',
                    ]),
                Forms\Components\TextInput::make('phone_number')
                    ->label('no.Telp')
                    ->required()
                    ->tel(),
                Forms\Components\TextInput::make('email')
                    ->label('email'),
                Forms\Components\Textarea::make('address')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Klien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Jenis Klien'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No. telpon'),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
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
            'index' => Pages\ListLaundryCustomers::route('/'),
            'create' => Pages\CreateLaundryCustomer::route('/create'),
            'edit' => Pages\EditLaundryCustomer::route('/{record}/edit'),
        ];
    }
}
