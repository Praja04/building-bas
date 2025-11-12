<?php

use Illuminate\Support\Facades\Route;

Route::prefix('penitipan-barang')->group(function () {
   Route::get('/', [App\Http\Controllers\PenitipanBarangController::class, 'index'])->name('penitipan-barang.index');
   Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PenitipanBarangController::class, 'getByCard'])->name('penitipan-barang.getByCard');
   Route::get('/detail/{id}', [App\Http\Controllers\PenitipanBarangController::class, 'detail'])->name('penitipan-barang.detail');
   Route::post('/ambil/{id}', [App\Http\Controllers\PenitipanBarangController::class, 'ambil'])->name('penitipan-barang.ambil');
   Route::post('/store', [App\Http\Controllers\PenitipanBarangController::class, 'store'])->name('penitipan-barang.store');
});
