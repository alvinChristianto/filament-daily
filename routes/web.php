<?php

use App\Http\Controllers\DownloadPDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/sparepartShipment-report/{record}', [DownloadPDFController::class, 'sparepartShipment'])->name('sparepartShipment.report');