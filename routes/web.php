<?php

use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   return view('home');
});

Route::get('/test', function () {
   return view('test');
});

Route::post('/download-excel', [ParticipantController::class, 'downloadExcel']);

// Route::prefix('penitipan-barang')->group(function () {
//    Route::get('/', [App\Http\Controllers\PenitipanBarangController::class, 'index'])->name('penitipan-barang.index');
//    Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PenitipanBarangController::class, 'getByCard'])->name('penitipan-barang.getByCard');
//    Route::get('/detail/{id}', [App\Http\Controllers\PenitipanBarangController::class, 'detail'])->name('penitipan-barang.detail');
//    Route::post('/ambil/{id}', [App\Http\Controllers\PenitipanBarangController::class, 'ambil'])->name('penitipan-barang.ambil');
//    Route::post('/store', [App\Http\Controllers\PenitipanBarangController::class, 'store'])->name('penitipan-barang.store');
// });

Route::prefix('pengambilan-makan')->group(function () {
   Route::get('/', [App\Http\Controllers\PengambilanMakanController::class, 'index'])->name('pengambilan-makan.index');
   Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PengambilanMakanController::class, 'getByCard'])->name('pengambilan-makan.getByCard');
   Route::post('/ambil/{id}', [App\Http\Controllers\PengambilanMakanController::class, 'ambil'])->name('pengambilan-makan.ambil');
   Route::get('/export', [App\Http\Controllers\PengambilanMakanController::class, 'export'])->name('pengambilan-makan.export');
});

Route::prefix('attendance')->group(function () {
   Route::get('/', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
   Route::get('/user/{cardnodevice}', [App\Http\Controllers\AttendanceController::class, 'getUser'])->name('attendance.getByCard');
   Route::post('/store', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');
   Route::post('/checkout', [App\Http\Controllers\AttendanceController::class, 'checkout'])->name('attendance.checkout');
   Route::get('/export', [App\Http\Controllers\AttendanceController::class, 'export'])->name('attendance.export');
});

Route::prefix('pengambilan-kaos')->group(function () {
   Route::get('/', [App\Http\Controllers\PengambilanKaosController::class, 'index'])->name('pengambilan-kaos.index');
   Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PengambilanKaosController::class, 'getByCard'])->name('pengambilan-kaos.getByCard');
   Route::post('/ambil/{id}', [App\Http\Controllers\PengambilanKaosController::class, 'ambil'])->name('pengambilan-kaos.ambil');
   Route::get('/export', [App\Http\Controllers\PengambilanKaosController::class, 'export'])->name('pengambilan-kaos.export');
});

Route::prefix('pengambilan-eskrim')->group(function () {
   Route::get('/', [App\Http\Controllers\PengambilanEskrimController::class, 'index'])->name('pengambilan-eskrim.index');
   Route::get('/get-by-card/{cardnodevice}', [App\Http\Controllers\PengambilanEskrimController::class, 'getByCard'])->name('pengambilan-eskrim.getByCard');
   Route::get('/export', [App\Http\Controllers\PengambilanEskrimController::class, 'export'])->name('pengambilan-eskrim.export');
});

Route::prefix('doorprize')->group(function () {
   Route::get('/', [App\Http\Controllers\DoorprizeController::class, 'index'])->name('doorprize.index');
   Route::get('/data', [App\Http\Controllers\DoorprizeController::class, 'getData'])->name('doorprize.data');
   Route::post('/draw', [App\Http\Controllers\DoorprizeController::class, 'draw'])->name('doorprize.draw');
   Route::post('/winners', [App\Http\Controllers\DoorprizeController::class, 'storeWinner'])->name('doorprize.storeWinner');
   Route::delete('/winners/{id}', [App\Http\Controllers\DoorprizeController::class, 'destroyWinner'])->name('doorprize.destroyWinner');
   Route::post('/reset', [App\Http\Controllers\DoorprizeController::class, 'resetWinners'])->name('doorprize.reset');
   Route::get('/export', [App\Http\Controllers\DoorprizeController::class, 'export'])->name('doorprize.export');
});

Route::prefix('master')->group(function () {

   Route::prefix('participants')->group(function () {
      Route::delete('/destroy-all', [App\Http\Controllers\ParticipantController::class, 'destroyAll'])
         ->name('participants.destroyAll'); // jangan dipindahin urutannya
      Route::post('/import', [App\Http\Controllers\ParticipantController::class, 'import'])
         ->name('participants.import');
      Route::post('/bulk-delete', [App\Http\Controllers\ParticipantController::class, 'bulkDelete'])
         ->name('participants.bulkDelete');

      Route::get('/export', [App\Http\Controllers\ParticipantController::class, 'export'])
         ->name('participants.export');
      Route::resource('/', App\Http\Controllers\ParticipantController::class)
         ->parameters(['' => 'participant'])
         ->names('participants');
   });

   Route::prefix('msid')->group(function () {
      Route::get('/', [App\Http\Controllers\MsidController::class, 'index'])->name('msid.index');
      Route::get('/{id}', [App\Http\Controllers\MsidController::class, 'edit'])->name('msid.edit');
      Route::post('/{id}', [App\Http\Controllers\MsidController::class, 'update'])->name('msid.update');
   });
});
