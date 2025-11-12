<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penitipan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('cardnodevice');
            $table->string('nik');
            $table->string('nama_penitip');
            $table->string('departemen');
            $table->string('telepon', 20)->nullable();
            $table->string('no_penitipan')->nullable();
            $table->enum('jenis_barang', ['Tas', 'Elektronik', 'Dokumen', 'Lainnya']);
            $table->string('foto_penitip')->nullable();
            $table->string('foto_barang')->nullable();
            $table->dateTime('waktu_titip');
            $table->dateTime('waktu_ambil')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['belum_diambil', 'sudah_diambil'])->default('belum_diambil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penitipan_barang');
    }
};
