<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AADailyRevExpenses;
use App\Filament\Widgets\AcReminderOverview;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class sparepartDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.sparepart-dashboard';
    protected ?string $heading = ' Dashboard Sparepart';
    protected ?string $subheading = 'Halaman dashboard untuk manajemen keuangan sparepart';

    // protected static ?string $navigationLabel = 'Custom Navigation Label';
    // public function getTitle(): string | Htmlable
    // {
    //     return __('Custom Page Title');
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            // AcReminderOverview::class
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isSuperAdmin();
    }
}
