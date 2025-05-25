<?php

use App\Http\Controllers\DownloadPDFController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/sparepartShipment-report/{record}', [DownloadPDFController::class, 'sparepartShipment'])->name('sparepartShipment.report');
Route::get('/acwork-report/{record}', [DownloadPDFController::class, 'acWorkReport'])->name('acWorkReport.report');
Route::get('/laundrytransaction-report/{record}', [DownloadPDFController::class, 'laundryTransaction'])->name('laundryTransaction.report');