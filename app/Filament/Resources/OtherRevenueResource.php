<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtherRevenueResource\Pages;
use App\Filament\Resources\OtherRevenueResource\RelationManagers;
use App\Models\OtherRevenue;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OtherRevenueResource extends Resource
{
    protected static ?string $model = OtherRevenue::class;


    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $navigationLabel = 'Pendapatan universal';
    protected static ?string $navigationGroup = 'Pendapatan Lain-Lain';

    protected static ?string $modelLabel = 'Pendapatan universal';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Data Pendapatan lain')
                    ->schema([
                        
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label('Tanggal Pendapatan')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('title')
                            ->label('Judul Pendapatan lain')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Textarea::make('description')
                            ->label('Catatan Pendapatan'),
                        Forms\Components\TextInput::make('total_revenue')
                            ->label('total pendapatan')
                            ->numeric()
                            ->prefix('Rp')
                            // ->disabled()
                            ->dehydrated(true)
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('id_payment')
                            ->label('metode pembayaran')
                            ->relationship('payment', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status Pendapatan ')
                            ->options([
                                'LUNAS' => 'LUNAS',
                                'DP' => 'DP',
                            ])
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id_transaction')
                //     ->label('Id'),

                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tgl Pembiayaan')
                    // ->date('d m Y H:i:s')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Pendapatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment.name')
                    ->label('metode bayar'),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('total pendapatan')
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'LUNAS' => 'success',
                        'DP' => 'danger',
                    }),


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
                    ->options([
                        'LUNAS' => 'Lunas/Success',
                        'DP' => 'DP',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        DatePicker::make('dibuat dari'),
                        DatePicker::make('dibuat sampai'),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['dibuat dari'] && !$data['dibuat sampai']) {
                            return null;
                        }
                        $indicatorFrom = 'dibuat dari ' . Carbon::parse($data['dibuat dari'])->toFormattedDateString();
                        $indicatorUntil = ' to ' . Carbon::parse($data['dibuat sampai'])->toFormattedDateString();
                        return $indicatorFrom . " " . $indicatorUntil;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dibuat dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['dibuat sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('id_payment')
                    ->label('Payment')
                    ->relationship('payment', 'name'),
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
            'index' => Pages\ListOtherRevenues::route('/'),
            'create' => Pages\CreateOtherRevenue::route('/create'),
            'edit' => Pages\EditOtherRevenue::route('/{record}/edit'),
        ];
    }
}
