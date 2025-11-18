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
        Schema::create('pengambilan_kaos', function (Blueprint $table) {
            $table->id();
            $table->string('cardnodevice');
            $table->string('nik');
            $table->string('nama_pegawai');
            $table->string('departemen');
            $table->dateTime('waktu_ambil')->nullable();
            $table->enum('status', ['belum_diambil', 'sudah_diambil'])->default('belum_diambil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengambilan_kaos');
    }
};
