<?php

use App\Livewire\Portal\CekTagihan;
use App\Livewire\Portal\UploadBukti;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Portal Pengguna — Fase 17
| Middleware: auth (harus login) — guard role pengguna dilakukan di mount()
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/tagihan', CekTagihan::class)
        ->name('tagihan');

    Route::get('/tagihan/{tagihan}/upload', UploadBukti::class)
        ->name('upload-bukti');
});

/*
|--------------------------------------------------------------------------
| Laporan Ekspor Excel — Fase 19
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/unit/{id}/export', function (string $id) {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\UnitTransaksiExport($id),
            "laporan-unit-{$id}.xlsx"
        );
    })->name('reports.unit.export');
});
