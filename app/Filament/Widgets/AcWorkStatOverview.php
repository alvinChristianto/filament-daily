<?php

namespace App\Filament\Widgets;

use App\Models\AcWorkingReport;
use App\Models\Expenses;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AcWorkStatOverview extends BaseWidget
{

    protected function getStats(): array
    {
        $now = Carbon::now()->format('Y-m-d');
        // Mendapatkan tanggal saat ini
        $today = Carbon::now()->format('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        /**PENDAPATAN */
        // Menghitung pendapatan HARIAN
        $RawdailyRevenue = AcWorkingReport::query()
            ->whereDate('created_at', $today)
            ->sum('total_price');

        // Menghitung pendapatan BULANAN
        $RawmonthlyRevenue = AcWorkingReport::query()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear) // Pastikan tahun juga sesuai
            ->sum('total_price');

        // Menghitung pendapatan TAHUNAN
        $RawyearlyRevenue = AcWorkingReport::query()
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        $arrServiceACExpense =
            [
                'GAJI_SERVICE_AC', 'BIAYA_SPAREPART',
                'BIAYA_SERVICE_MOTOR', 'MAKAN_MINUM_SERVICE_AC',
                'BENSIN', 'LAIN-LAIN'
            ];
        /**PENGELUARAN UNTUK KESELURUHAN*/
        $RawDailyExpenses = Expenses::query()
            ->wherein('category', $arrServiceACExpense)
            ->whereDate('created_at', $today)
            ->sum('price_total');
        $RawMonthlyExpenses = Expenses::query()
            ->wherein('category', $arrServiceACExpense)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear) // Pastikan tahun juga sesuai
            ->sum('price_total');
        $RawYearlyExpenses = Expenses::query()
            ->wherein('category', $arrServiceACExpense)
            ->whereYear('created_at', $currentYear)
            ->sum('price_total');

        $dailyRevenue = 'Rp ' . number_format($RawdailyRevenue, 2, ',', '.');
        $monthlyRevenue = 'Rp ' . number_format($RawmonthlyRevenue, 2, ',', '.');
        $yearlyRevenue = 'Rp ' . number_format($RawyearlyRevenue, 2, ',', '.');

        $dailyExpenses  = 'Rp ' . number_format($RawDailyExpenses, 2, ',', '.');
        $monthlyExpenses  = 'Rp ' . number_format($RawMonthlyExpenses, 2, ',', '.');
        $yearlyExpenses  = 'Rp ' . number_format($RawYearlyExpenses, 2, ',', '.');

        //keuntungan =  pendapatan - pengeluaran (biaya)
        $dailyProfit  = 'Rp ' . number_format($RawdailyRevenue - $RawDailyExpenses, 2, ',', '.');
        $monthlyProfit  = 'Rp ' . number_format($RawmonthlyRevenue - $RawMonthlyExpenses, 2, ',', '.');
        $yearlyProfit  = 'Rp ' . number_format($RawyearlyRevenue - $RawYearlyExpenses, 2, ',', '.');


        return [
            Stat::make('Pend. SERVICE AC HARIAN', $dailyRevenue)
                ->description('Pendapatan Service AC Per Hari ini ' . Carbon::now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('info'),
            Stat::make('Pend. SERVICE AC BULANAN', $monthlyRevenue)
                ->description('Pendapatan Service AC Per Bulan ' . Carbon::now()->format('M'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('info'),
            Stat::make('Pend. SERVICE AC TAHUNAN', $yearlyRevenue)
                ->description('Pendapatan Service AC pada tahun ' . $currentYear)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('info'),


            Stat::make('Pengeluaran SERVICE AC HARIAN ', $dailyExpenses)
                ->description('Pengeluaran SERVICE AC Per Hari ini ' .  Carbon::now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
            Stat::make('Pengeluaran SERVICE AC BULANAN ', $monthlyExpenses)
                ->description('Pengeluaran SERVICE AC pada Bulan ' . Carbon::now()->format('M'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
            Stat::make('Pengeluaran SERVICE AC TAHUNAN ', $yearlyExpenses)
                ->description('Pengeluaran SERVICE AC pada tahun ' . $currentYear)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),


            //KEUNTUNGAN BERSIH
            Stat::make('Untung HARIAN ', $dailyProfit)
                ->description('Keuntungan Per Hari ini ' .  Carbon::now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('success'),
            Stat::make('Untung BULANAN ', $monthlyProfit)
                ->description('Keuntungan pada Bulan ' . Carbon::now()->format('M'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('success'),
            Stat::make('Untung TAHUNAN ', $yearlyProfit)
                ->description('Keuntungan pada tahun ' . $currentYear)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('success'),
        ];
    }
}
