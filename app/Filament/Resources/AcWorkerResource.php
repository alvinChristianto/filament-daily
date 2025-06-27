<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcWorkerResource\Pages;
use App\Filament\Resources\AcWorkerResource\RelationManagers;
use App\Models\AcWorker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcWorkerResource extends Resource
{
    protected static ?string $model = AcWorker::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pekerja AC';
    protected static ?string $navigationGroup = 'Service AC';

    protected static ?string $modelLabel = 'Pekerja AC';
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
                    ->label('Alamat'),
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
            'index' => Pages\ListAcWorkers::route('/'),
            'create' => Pages\CreateAcWorker::route('/create'),
            'edit' => Pages\EditAcWorker::route('/{record}/edit'),
        ];
    }
}
