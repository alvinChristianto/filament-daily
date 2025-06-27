<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcCustomerResource\Pages;
use App\Filament\Resources\AcCustomerResource\RelationManagers;
use App\Models\AcCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcCustomerResource extends Resource
{
    protected static ?string $model = AcCustomer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Data Klien/Customer AC';
    protected static ?string $navigationGroup = 'Service AC';

    protected static ?string $modelLabel = 'Data Klien/Customer AC';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('nama klien')
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
                ->label('kategori')
                    ->options([
                        'PERSON' => 'PERSON',
                        'PT' => 'PT',
                        'OTHER' => 'OTHER',
                    ]),
                Forms\Components\TextInput::make('phone_number')
                ->label('no. telepon')
                    ->required()
                    ->tel(),
                Forms\Components\TextInput::make('email')
                    ->label('email'),
                Forms\Components\Textarea::make('address')
                    ->required()
                    ->label('Alamat'),

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
            'index' => Pages\ListAcCustomers::route('/'),
            'create' => Pages\CreateAcCustomer::route('/create'),
            'edit' => Pages\EditAcCustomer::route('/{record}/edit'),
        ];
    }
}
