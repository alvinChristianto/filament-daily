<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AADailyRevExpenses;
use App\Filament\Widgets\ABDailySparepartTrxRevExpenses;
use App\Filament\Widgets\AcWorkStatOverview;
use App\Filament\Widgets\SparepartStatOverview;
use Filament\Pages\Page;

class AcWorkingDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.sparepart-dashboard';
    protected ?string $heading = ' Dashboard Pengerjaan AC';
    protected ?string $subheading = 'Halaman dashboard untuk melihat ringkasan transaksi (beli dan jual) Pengerjaan AC';

    protected static ?string $navigationLabel = 'Dashboard Pengerjaan AC';
    // public function getTitle(): string | Htmlable
    // {
    //     return __('Custom Page Title');
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            AADailyRevExpenses::class,
            AcWorkStatOverview::class
        ];
    }

    public static function canAccess(): bool
    {
        // return auth()->user()->isSuperAdmin();
        return true;
    }

    public static function canView(): bool
    {
        return true;
    }
}
