<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaundryWorkerResource\Pages;
use App\Filament\Resources\LaundryWorkerResource\RelationManagers;
use App\Models\LaundryWorker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaundryWorkerResource extends Resource
{
    protected static ?string $model = LaundryWorker::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Pekerja Laundry';
    protected static ?string $navigationGroup = 'Laundry';
    protected static ?string $modelLabel = 'Pekerja Laundry';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('nama Pekerja')
                    ->maxLength(100)
                    ->columnSpanFull(),
                Forms\Components\Select::make('gender')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        '-' => '-',
                    ]),
                Forms\Components\TextInput::make('phone_number')
                    ->label('No Telepon')
                    ->required()
                    ->tel(),
                Forms\Components\TextInput::make('email')
                    ->label('email'),
                Forms\Components\Textarea::make('address')
                    ->label('Alamat')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama pekerja')
                    ->searchable(),
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
            'index' => Pages\ListLaundryWorkers::route('/'),
            'create' => Pages\CreateLaundryWorker::route('/create'),
            'edit' => Pages\EditLaundryWorker::route('/{record}/edit'),
        ];
    }
}
