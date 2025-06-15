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

        /**PENGELUARAN */
        $RawDailyExpenses = Expenses::query()
            ->whereDate('created_at', $today)
            ->sum('price_total');
        $RawMonthlyExpenses = Expenses::query()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear) // Pastikan tahun juga sesuai
            ->sum('price_total');
        $RawYearlyExpenses = Expenses::query()
            ->whereYear('created_at', $currentYear)
            ->sum('price_total');

        $dailyRevenue = 'Rp ' . number_format($RawdailyRevenue, 2, ',', '.');
        $monthlyRevenue = 'Rp ' . number_format($RawmonthlyRevenue, 2, ',', '.');
        $yearlyRevenue = 'Rp ' . number_format($RawyearlyRevenue, 2, ',', '.');

        $dailyExpenses  = 'Rp ' . number_format($RawDailyExpenses, 2, ',', '.');
        $monthlyExpenses  = 'Rp ' . number_format($RawMonthlyExpenses, 2, ',', '.');
        $yearlyExpenses  = 'Rp ' . number_format($RawYearlyExpenses, 2, ',', '.');

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


            Stat::make('Pengeluaran HARIAN ', $dailyExpenses)
                ->description('Pengeluaran Per Hari ini ' .  Carbon::now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
            Stat::make('Pengeluaran BULANAN ', $monthlyExpenses)
                ->description('Pengeluaran pada Bulan ' . Carbon::now()->format('M'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
            Stat::make('Pengeluaran TAHUNAN ', $yearlyExpenses)
                ->description('Pengeluaran pada tahun ' . $currentYear)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
        ];
    }
}
