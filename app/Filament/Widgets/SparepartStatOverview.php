<?php

namespace App\Filament\Widgets;

use App\Models\Sparepart;
use App\Models\SparepartShipment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SparepartStatOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $now = Carbon::now()->format('Y-m-d');
        // Mendapatkan tanggal saat ini
        $today = Carbon::now()->format('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Menghitung pendapatan HARIAN
        $RawdailyRevenue = SparepartShipment::query()
            ->whereDate('transaction_date', $today)
            ->whereNot('status', 'INITIAL')
            ->sum('total_price');
        // ->get();
        // dd($RawdailyRevenue);

        // Menghitung pendapatan BULANAN
        $RawmonthlyRevenue = SparepartShipment::query()
            ->whereMonth('transaction_date', $currentMonth)
            ->whereNot('status', 'INITIAL')
            ->whereYear('transaction_date', $currentYear) // Pastikan tahun juga sesuai
            ->sum('total_price');

        // Menghitung pendapatan TAHUNAN
        $RawyearlyRevenue = SparepartShipment::query()
            ->whereNot('status', 'INITIAL')
            ->whereYear('transaction_date', $currentYear)
            ->sum('total_price');



        /**PENGELUARAN UNTUK KESELURUHAN*/
        $RawDailyExpenses = Sparepart::query()
            ->whereDate('created_at', $today)
            ->sum('price');
        $RawMonthlyExpenses = Sparepart::query()
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear) // Pastikan tahun juga sesuai
            ->sum('price');
        $RawYearlyExpenses = Sparepart::query()
            ->whereYear('created_at', $currentYear)
            ->sum('price');

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
            //20251028 : pendapatan dari PENJUALAN sparepart yang dibuat di fitur Transaksi Sparepart 
            Stat::make('Pend. JUAL SPAREPART', $dailyRevenue)
                ->description('Pendapatan SPAREPART Per Hari ini ' . Carbon::now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('info'),
            Stat::make('Pend. JUAL SPAREPART', $monthlyRevenue)
                ->description('Pendapatan SPAREPART Per Bulan ' . Carbon::now()->format('M'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('info'),
            Stat::make('Pend. JUAL SPAREPART', $yearlyRevenue)
                ->description('Pendapatan SPAREPART pada tahun ' . $currentYear)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('info'),

            //20251028 : pengeluaran  dari PEMBELIAN sparepart yang dibuat di fitur Sparepart 
            Stat::make('Pengeluaran BELI SPAREPART', $dailyExpenses)
                ->description('Pendapatan BELI SPAREPART Per Hari ini ' . Carbon::now()->format('d-m-Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
            Stat::make('Pengeluaran BELI SPAREPART', $monthlyExpenses)
                ->description('Pengeluaran BELI SPAREPART Per Bulan ' . Carbon::now()->format('M'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),
            Stat::make('Pengeluaran BELI SPAREPART', $yearlyExpenses)
                ->description('Pengeluaran BELI SPAREPART pada tahun ' . $currentYear)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                // ->url(route('filament.admin.resources.reservations.index'))
                ->color('warning'),

            //20251028 : KEUNTUNGAN BERSIH
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
