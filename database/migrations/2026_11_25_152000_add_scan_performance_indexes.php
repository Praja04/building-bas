<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $targetTable = Schema::hasTable('MSIDCARD') ? 'MSIDCARD' : (Schema::hasTable('msidcard') ? 'msidcard' : null);

        if ($targetTable) {
            $existing = DB::select("SHOW INDEX FROM `{$targetTable}` WHERE Key_name = 'IDXCARDNODEVICE'");
            if (empty($existing)) {
                DB::statement("ALTER TABLE `{$targetTable}` ADD INDEX `IDXCARDNODEVICE` (`CARDNODEVICE`, `STATUS`, `CHANGEDON`) USING BTREE");
            }

            $existingNikStatus = DB::select("SHOW INDEX FROM `{$targetTable}` WHERE Key_name = 'IDXNIK_STATUS_CHANGEDON'");
            if (empty($existingNikStatus)) {
                DB::statement("ALTER TABLE `{$targetTable}` ADD INDEX `IDXNIK_STATUS_CHANGEDON` (`NIK`, `STATUS`, `CHANGEDON`) USING BTREE");
            }
        }

        Schema::table('participants', function (Blueprint $table) {
            $table->index('nik2', 'participants_nik2_index');
            $table->index('cardnodevice1', 'participants_cardnodevice1_index');
            $table->index('cardnodevice2', 'participants_cardnodevice2_index');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['nik', 'jam_masuk'], 'attendances_nik_jam_masuk_index');
        });

        Schema::table('pengambilan_makan', function (Blueprint $table) {
            $table->index(['nik', 'waktu_ambil'], 'pengambilan_makan_nik_waktu_ambil_index');
            $table->index(['cardnodevice', 'waktu_ambil'], 'pengambilan_makan_card_waktu_ambil_index');
        });

        Schema::table('pengambilan_kaos', function (Blueprint $table) {
            $table->index('cardnodevice', 'pengambilan_kaos_cardnodevice_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $targetTable = Schema::hasTable('MSIDCARD') ? 'MSIDCARD' : (Schema::hasTable('msidcard') ? 'msidcard' : null);

        if ($targetTable) {
            $existing = DB::select("SHOW INDEX FROM `{$targetTable}` WHERE Key_name = 'IDXCARDNODEVICE'");
            if (!empty($existing)) {
                DB::statement("ALTER TABLE `{$targetTable}` DROP INDEX `IDXCARDNODEVICE`");
            }

            $existingNikStatus = DB::select("SHOW INDEX FROM `{$targetTable}` WHERE Key_name = 'IDXNIK_STATUS_CHANGEDON'");
            if (!empty($existingNikStatus)) {
                DB::statement("ALTER TABLE `{$targetTable}` DROP INDEX `IDXNIK_STATUS_CHANGEDON`");
            }
        }

        Schema::table('participants', function (Blueprint $table) {
            $table->dropIndex('participants_nik2_index');
            $table->dropIndex('participants_cardnodevice1_index');
            $table->dropIndex('participants_cardnodevice2_index');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_nik_jam_masuk_index');
        });

        Schema::table('pengambilan_makan', function (Blueprint $table) {
            $table->dropIndex('pengambilan_makan_nik_waktu_ambil_index');
            $table->dropIndex('pengambilan_makan_card_waktu_ambil_index');
        });

        Schema::table('pengambilan_kaos', function (Blueprint $table) {
            $table->dropIndex('pengambilan_kaos_cardnodevice_index');
        });
    }
};
