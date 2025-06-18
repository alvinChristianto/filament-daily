<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceReportResource\Pages;
use App\Filament\Resources\AttendanceReportResource\RelationManagers;
use App\Models\AcWorker;
use App\Models\AttendanceReport;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class AttendanceReportResource extends Resource
{
    protected static ?string $model = AttendanceReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Laporan Absensi';
    protected static ?string $navigationGroup = 'Absensi';

    protected static ?string $modelLabel = 'Laporan Absensi';

    public static function form(Form $form): Form
    {
        $typeOfAbsence = [
            // 'MASUK' => 'MASUK',
            'CUTI' => 'CUTI',
            'IJIN' => 'IJIN',
            'SAKIT' => 'SAKIT',
            'KEPERLUAN_KELUARGA' => 'KEPERLUAN_KELUARGA',
            'LAIN-LAIN' => 'LAIN-LAIN',
        ];

        return $form
            ->schema([
                Fieldset::make('Data Absensi inti')
                    ->schema([
                        Forms\Components\DatePicker::make('record_date')
                            ->label('Tanggal Absensi')
                            ->default(Carbon::now()->format('d-M-Y'))
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->timezone('Asia/Jakarta')
                            ->readOnly()
                            ->required(),
                        Forms\Components\Select::make('worker_name')
                            ->label('Pegawai')
                            ->options(function (Get $get) {
                                return AcWorker::all()->pluck('name', 'name');
                            })
                            ->required(),
                        Forms\Components\Toggle::make('is_present')
                            ->label('Apakah Pegawai Masuk')
                            ->helperText(new HtmlString('geser ke <strong>KIRI</strong> jika tidak masuk. Lalu isikan data pada <strong>Keterangan Tambahan</strong>'))

                            // ->hint('Geser')
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-x-circle')
                            ->onColor('success')
                            ->offColor('warning')
                            ->default(true)
                            ->inline(false)
                            ->reactive()
                            ->requiredWith('type_of_absence', 'in_time')
                            ->required()
                    ])
                    ->columns(3),
                Fieldset::make('Keterangan Tambahan')
                    ->schema([
                        Forms\Components\Select::make('type_absence')
                            ->label('tipe absen')
                            ->options($typeOfAbsence)
                            ->disabled(
                                fn ($get): bool => $get('is_present') == true
                            )
                            ->required(
                                fn ($get): bool => $get('is_present') == false
                            ),
                        Forms\Components\DateTimePicker::make('in_time')
                            ->label('Waktu Masuk')
                            ->seconds(false)
                            ->timezone('Asia/Jakarta')
                            ->disabled(
                                fn ($get): bool => $get('is_present') == true
                            ),
                        Forms\Components\DateTimePicker::make('out_time')
                            ->label('Waktu Pulang')
                            ->seconds(false)
                            ->after('in_time')
                            ->timezone('Asia/Jakarta')
                            ->disabled(
                                fn ($get): bool => $get('is_present') == true
                            ),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Absensi')
                            ->columnSpan(2),
                    ])
                    ->columns(3)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->label('Id'),
                Tables\Columns\TextColumn::make('record_date')
                    ->label('waktu absensi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('worker_name')
                    ->label('Pegawai')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_present')
                    ->label('Status absensi')
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('info')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('type_absence')
                    ->label('Alasan'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Keterangan')
                    ->words(6),

            ])
            ->filters([
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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['dibuat sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListAttendanceReports::route('/'),
            'create' => Pages\CreateAttendanceReport::route('/create'),
            'edit' => Pages\EditAttendanceReport::route('/{record}/edit'),
        ];
    }
}
