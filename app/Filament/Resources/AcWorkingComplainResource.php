<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcWorkingComplainResource\Pages;
use App\Filament\Resources\AcWorkingComplainResource\RelationManagers;
use App\Models\AcWorkingComplain;
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

class AcWorkingComplainResource extends Resource
{
    protected static ?string $model = AcWorkingComplain::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Komplain Service AC';
    protected static ?string $navigationGroup = 'Service AC';

    protected static ?string $modelLabel = 'Komplain Service AC';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_report')
                    ->label('Id transaksi yang komplain')
                    ->relationship(
                        'acworkingReport',
                        'id_report',
                        fn ($query) => $query->where('status', 'SUCCESS')
                            ->select('id_report', 'title')
                            ->orderBy('created_at', 'desc')
                    )
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->required(),

                // ->suffixAction(
                //     Action::make('copyCostToPrice')
                //         ->icon('heroicon-m-calculator')
                //         ->action(function (Set $set, Get $get, $state) {
                //             // $res =  calculatePriceTransac($state);
                //             // $set('transaction_price', $res);
                //         })
                // ),

                Forms\Components\Split::make([
                    Forms\Components\Section::make([
                        Forms\Components\TextInput::make('description_complain')
                            ->label('deskripsi komplain')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image_complain')
                            ->label('Foto komplain')
                            ->multiple()
                            ->image(),
                    ]),
                    Forms\Components\Section::make([
                        Forms\Components\TextInput::make('description_solving')
                            ->label('Penanganan')
                            ->maxLength(100)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image_solving')
                            ->label('Foto penanganan')
                            ->multiple()
                            ->image(),
                    ])
                ])->columnSpanFull()
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_report')
                    ->label('id_report')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description_complain')
                    ->label('Komplain'),
                Tables\Columns\TextColumn::make('status')
                    ->label('status')
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
            'index' => Pages\ListAcWorkingComplains::route('/'),
            'create' => Pages\CreateAcWorkingComplain::route('/create'),
            'edit' => Pages\EditAcWorkingComplain::route('/{record}/edit'),
        ];
    }
}
