<?php

namespace App\Filament\Widgets;

use App\Models\DailyRevenueExpenses;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder as BuilderFilter;
use Illuminate\Database\Query\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AADailyRevExpenses extends BaseWidget
{
    protected static ?string $heading = 'Daily Financial Summary';
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(function (Builder $query) {


                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();

                // Return an Eloquent query for BakpiaTransaction model
                // Filter transactions created within the current month
                return DailyRevenueExpenses::query()
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->orderBy('created_at', 'desc'); // Order by creation date, newest first
                // tidak ada filter dan data tidak muncul di widget ac reminder
            })
            ->columns([
                Tables\Columns\TextColumn::make('date_record')
                    ->label('Tgl')
                    ->date('d F Y'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Deskripsi'),
                Tables\Columns\TextColumn::make('revenue_laundry')
                    ->label('Pendapatan Laundry')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('revenue_serviceac')
                    ->label('Pendapatan Service AC')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('revenue_sparepart')
                    ->label('Pendapatan Jual Sparepart')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('expense_buy_sparepart')
                    ->label('Biaya Beli Sparepart')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('expense_other')
                    ->label('Biaya Pembiayaan')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),

                Tables\Columns\TextColumn::make('net_profit') // This column name doesn't have to exist in DB
                    ->label('Net Profit')
                    ->numeric() // For display formatting if it were a regular column
                    ->money('idr') // For display formatting if it were a regular column
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Net Profit') // Label for the summary row
                            ->using(function (Builder $query): string {
                                // Calculate total revenue
                                $totalRevenue = $query->sum('revenue_laundry') +
                                    $query->sum('revenue_serviceac') +
                                    $query->sum('revenue_sparepart');

                                // Calculate total expenses
                                $totalExpenses = $query->sum('expense_buy_sparepart') +
                                    $query->sum('expense_other');

                                // Calculate net profit
                                $netProfit = $totalRevenue - $totalExpenses;

                                // Format the result as IDR
                                return 'Rp ' . number_format($netProfit, 0, ',', '.');
                            })
                    ),
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
                    ->query(function (BuilderFilter $query, array $data): BuilderFilter {
                        return $query
                            ->when(
                                $data['dibuat dari'],
                                fn (BuilderFilter $query, $date): BuilderFilter => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['dibuat sampai'],
                                fn (BuilderFilter $query, $date): BuilderFilter => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()->exports([
                        ExcelExport::make()
                            ->withFilename('Summary Financial')
                            // ->askForFilename()
                            // ->withFilename(fn ($filename) => 'prefix-' . $filename)
                            ->withColumns([
                                Column::make('id'),
                                Column::make('date_record'),
                                Column::make('title'),
                                Column::make('revenue_laundry'),
                                Column::make('revenue_serviceac'),
                                Column::make('revenue_sparepart'),

                                Column::make('expense_buy_sparepart'),
                                Column::make('expense_other'),
                                Column::make('expense_other'),

                                Column::make('net_profit'),

                            ]),
                    ]),
                ]),
            ])->defaultSort('created_at', 'desc');
    }
}
