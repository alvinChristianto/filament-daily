<?php

namespace App\Filament\Widgets;

use App\Models\DailyRevenueExpenses;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder as BuilderFilter;
use Illuminate\Database\Query\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AADailyRevExpenses extends BaseWidget
{
    protected static ?string $heading = 'Summary Service AC';
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(function (Builder $query) {


                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();

                // Return an Eloquent query for BakpiaTransaction model
                // Filter transactions created within the current month
                $res =  DailyRevenueExpenses::query()
                    // ->select([
                    //     'id', 'title', 'revenue_laundry', 'revenue_serviceac', 'revenue_sparepart', 'expense_buy_sparepart',
                    //     'expense_other', 'category'
                    // ])
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->whereIn('category', ['BIAYA_PEMBIAYAAN', 'PEND_SERVICE_AC', 'PEND_LAINLAIN'])
                    ->orderBy('created_at', 'desc'); // Order by creation date, newest first
                // tidak ada filter dan data tidak muncul di widget ac reminder
                // dd($res);
                return $res;
            })
            ->columns([
                Tables\Columns\TextColumn::make('date_record')
                    ->label('Tgl')
                    ->date('d F Y'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Deskripsi'),
                Tables\Columns\TextColumn::make('id_transaction')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment.name')
                    ->label('metode bayar')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('dr_cash')
                    ->label('Debet Cash')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('dr_noncash')
                    ->label('Debet non Cash')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('cr_cash')
                    ->label('Kredit Cash')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('cr_noncash')
                    ->label('Kredit non Cash')
                    ->numeric()
                    ->money('idr')
                    ->summarize(Sum::make()),

                Tables\Columns\TextColumn::make('revenue_laundry')
                    ->label('Pendapatan Laundry')
                    ->numeric()
                    ->money('idr')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('revenue_serviceac')
                    ->label('Pendapatan Service AC')
                    ->numeric()
                    ->money('idr')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('revenue_sparepart')
                    ->label('Pendapatan Jual Sparepart')
                    ->numeric()
                    ->money('idr')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('expense_buy_sparepart')
                    ->label('Biaya Beli Sparepart')
                    ->numeric()
                    ->money('idr')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Sum::make()),
                Tables\Columns\TextColumn::make('expense_other')
                    ->label('Biaya Pembiayaan')
                    ->numeric()
                    ->money('idr')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Sum::make()),

            ])
            ->filters([
                Tables\Filters\Filter::make('date_record')
                    ->form([
                        DatePicker::make('transaksi dari'),
                        DatePicker::make('transaksi sampai'),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['transaksi dari'] && !$data['transaksi sampai']) {
                            return null;
                        }
                        $indicatorFrom = 'transaksi dari ' . Carbon::parse($data['transaksi dari'])->toFormattedDateString();
                        $indicatorUntil = ' to ' . Carbon::parse($data['transaksi sampai'])->toFormattedDateString();
                        return $indicatorFrom . " " . $indicatorUntil;
                    })
                    ->query(function (BuilderFilter $query, array $data): BuilderFilter {
                        return $query
                            ->when(
                                $data['transaksi dari'],
                                fn(BuilderFilter $query, $date): BuilderFilter => $query->whereDate('date_record', '>=', $date),
                            )
                            ->when(
                                $data['transaksi sampai'],
                                fn(BuilderFilter $query, $date): BuilderFilter => $query->whereDate('date_record', '<=', $date),
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
            ])
            ->groups([
                'category',
            ])

            // ->defaultGroup('category')

            // ->groupingSettingsHidden(false)
            // ->groupsOnly()
            ->defaultSort('created_at', 'desc');
    }
}
