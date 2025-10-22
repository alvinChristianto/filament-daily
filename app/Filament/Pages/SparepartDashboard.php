<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ABDailySparepartTrxRevExpenses;
use App\Filament\Widgets\SparepartStatOverview;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class SparepartDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.sparepart-dashboard';
    protected ?string $heading = ' Dashboard Sparepart';
    protected ?string $subheading = 'Halaman dashboard untuk melihat ringkasan transaksi (beli dan jual) sparepart';

    protected static ?string $navigationLabel = 'Dashboard Sparepart';
    // public function getTitle(): string | Htmlable
    // {
    //     return __('Custom Page Title');
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            ABDailySparepartTrxRevExpenses::class,
            SparepartStatOverview::class
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
