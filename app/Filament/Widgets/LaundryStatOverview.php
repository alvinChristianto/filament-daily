<?php

namespace App\Filament\Widgets;

use App\Models\LaundryTransaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaundryStatOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = Carbon::now()->format('Y-m-d');
        // Mendapatkan tanggal saat ini
        $today = Carbon::now()->format('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Menghitung pendapatan HARIAN
        $RawdailyRevenue = LaundryTransaction::query()
            ->whereDate('created_at', $today)
            ->sum('total_price');

        // Menghitung pendapatan BULANAN
        $RawmonthlyRevenue = LaundryTransaction::query()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear) // Pastikan tahun juga sesuai
            ->sum('total_price');

        // Menghitung pendapatan TAHUNAN
        $RawyearlyRevenue = LaundryTransaction::query()
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        $dailyRevenue = 'Rp ' . number_format($RawdailyRevenue, 2, '.', '');
        $monthlyRevenue = 'Rp ' . number_format($RawmonthlyRevenue, 2, '.', '');
        $yearlyRevenue = 'Rp ' . number_format($RawyearlyRevenue, 2, '.', '');

        return [
            Stat::make('Pend. LAUNDRY HARIAN', $dailyRevenue)
                ->description('Pendapatan LAUNDRY Per Hari ini ' . Carbon::now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('primary'),
            Stat::make('Pend. LAUNDRY BULANAN', $monthlyRevenue)
                ->description('Pendapatan LAUNDRY Per Bulan ' . Carbon::now()->format('M'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('info'),
            Stat::make('Pend. LAUNDRY TAHUNAN', $yearlyRevenue)
                ->description('Pendapatan LAUNDRY pada tahun ' . $currentYear)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
        ];
    }
}
