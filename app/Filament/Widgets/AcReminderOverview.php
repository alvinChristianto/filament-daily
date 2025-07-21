<?php

namespace App\Filament\Widgets;

use App\Models\AcWorkingReport;
use App\Models\LaundryTransaction;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder; // <--- ADD THIS LINE

class AcReminderOverview extends BaseWidget
{

    public function table(Table $table): Table
    {
        return $table
            ->query(function (Builder $query) {


                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();

                // Return an Eloquent query for BakpiaTransaction model
                // Filter transactions created within the current month
                return AcWorkingReport::query()
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->orderBy('created_at', 'desc'); // Order by creation date, newest first
                // tidak ada filter dan data tidak muncul di widget ac reminder
            })
            ->columns([
                Tables\Columns\TextColumn::make('id_report')
                    ->label('Id'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Klien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.phone_number')
                    ->label('No.telp'),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label('tgl service selanjutnya')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // Only apply the month filter if a month is selected
                        return $query->when(
                            $data['value'], // The selected month number (1-12)
                            fn(Builder $query, $month) => $query->whereMonth('next_service_date', $month)
                        );
                    })
                    ->label('Filter by Month')
                    ->default(Carbon::now()->month),
            ]);
    }
    public static function canView(): bool
    {
        return false;
    }
}
