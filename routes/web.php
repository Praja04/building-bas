<?php

use Illuminate\Support\Facades\Route;

Route::prefix('penitipan-barang')->group(function () {
   Route::get('/', [App\Http\Controllers\PenitipanBarangController::class, 'index'])->name('penitipan-barang.index');
   Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PenitipanBarangController::class, 'getByCard'])->name('penitipan-barang.getByCard');
   Route::get('/detail/{id}', [App\Http\Controllers\PenitipanBarangController::class, 'detail'])->name('penitipan-barang.detail');
   Route::post('/ambil/{id}', [App\Http\Controllers\PenitipanBarangController::class, 'ambil'])->name('penitipan-barang.ambil');
   Route::post('/store', [App\Http\Controllers\PenitipanBarangController::class, 'store'])->name('penitipan-barang.store');
});

Route::prefix('pengambilan-makan')->group(function () {
   Route::get('/', [App\Http\Controllers\PengambilanMakanController::class, 'index'])->name('pengambilan-makan.index');
   Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PengambilanMakanController::class, 'getByCard'])->name('pengambilan-makan.getByCard');
   Route::post('/ambil/{id}', [App\Http\Controllers\PengambilanMakanController::class, 'ambil'])->name('pengambilan-makan.ambil');
});

Route::prefix('pengambilan-makan')->group(function () {
   Route::get('/', [App\Http\Controllers\PengambilanMakanController::class, 'index'])->name('pengambilan-makan.index');
   Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PengambilanMakanController::class, 'getByCard'])->name('pengambilan-makan.getByCard');
   Route::post('/ambil/{id}', [App\Http\Controllers\PengambilanMakanController::class, 'ambil'])->name('pengambilan-makan.ambil');
});

Route::prefix('attendance')->group(function () {
   Route::get('/', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
   Route::get('/user/{cardnodevice}', [App\Http\Controllers\AttendanceController::class, 'getUser'])->name('attendance.getByCard');
   Route::post('/store', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
   Route::post('/checkout', [App\Http\Controllers\AttendanceController::class, 'checkout'])->name('attendance.checkout');
   Route::get('/export', [App\Http\Controllers\AttendanceController::class, 'export'])->name('attendance.export');
});
