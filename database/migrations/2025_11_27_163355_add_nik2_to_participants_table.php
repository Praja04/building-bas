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
        Schema::table('participants', function (Blueprint $table) {
            $table->string('nik2')->nullable()->after('nik');
            $table->string('cardnodevice1')->nullable()->after('nik2');
            $table->string('cardnodevice2')->nullable()->after('cardnodevice1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('nik2');
            $table->dropColumn('cardnodevice1');
            $table->dropColumn('cardnodevice2');
        });
    }
};
