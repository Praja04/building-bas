<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

abstract class Controller
{
    /**
     * Look up an active record in msidcard by the given indexed column.
     * Fetches metadata first so sorting never touches off-page FOTOBLOB data,
     * then retrieves FOTOBLOB by primary key only for the winning row.
     */
    protected function findActiveMsidcard(string $column, string $value, bool $withFoto = false): ?object
    {
        $values = [$value];
        if ($column === 'NIK') {
            $trimmed = ltrim($value, '0');
            $withZero = '0' . $trimmed;
            if ($trimmed !== '') {
                $values[] = $trimmed;
            }
            $values[] = $withZero;
            $values = array_values(array_unique($values));
        }

        $row = DB::table('msidcard')
            ->select(
                'EMPCARDID',
                'BARCODE',
                'NIK as nik',
                'EMPNM as nama',
                'DEPTID as departemen',
                'CARDNODEVICE as cardnodevice'
            )
            ->whereIn($column, $values)
            ->where('STATUS', 'X')
            ->orderByDesc('CHANGEDON')
            ->first();

        if (!$row) {
            return null;
        }

        if ($withFoto) {
            $row->foto = DB::table('msidcard')
                ->where('EMPCARDID', $row->EMPCARDID)
                ->where('BARCODE', $row->BARCODE)
                ->value('FOTOBLOB');
        }

        unset($row->EMPCARDID, $row->BARCODE);

        return $row;
    }

    /**
     * Cari data peserta berdasarkan NIK (mencocokkan nik & nik2, baik dengan atau tanpa leading zero 0).
     */
    protected function findParticipantByNik(?string $nik): ?\App\Models\Participant
    {
        if (!$nik) {
            return null;
        }

        $raw = trim($nik);
        $trimmed = ltrim($raw, '0');
        if ($trimmed === '') {
            $trimmed = '0';
        }

        $variants = array_values(array_filter(array_unique([$raw, $trimmed, '0' . $trimmed]), fn($v) => $v !== ''));

        // 1. Coba exact match / standard variants terlebih dahulu (cepat & menggunakan index B-Tree)
        $peserta = \App\Models\Participant::where(function ($query) use ($variants) {
            $query->whereIn('nik', $variants)
                ->orWhereIn('nik2', $variants);
        })->first();

        if ($peserta) {
            return $peserta;
        }

        // 2. Jika belum ditemukan (misal format leading zero ganda seperti '00...'), gunakan TRIM SQL
        return \App\Models\Participant::where(function ($query) use ($trimmed) {
            $query->whereRaw("TRIM(LEADING '0' FROM nik) = ?", [$trimmed])
                ->orWhereRaw("TRIM(LEADING '0' FROM nik2) = ?", [$trimmed]);
        })->first();
    }

    /**
     * Cari data peserta berdasarkan cardnodevice atau NIK dengan fleksibilitas leading zero.
     */
    protected function findParticipantByCardOrNik(string $input): ?\App\Models\Participant
    {
        // 1. Cek cardnodevice1 / cardnodevice2
        $peserta = \App\Models\Participant::where('cardnodevice1', $input)
            ->orWhere('cardnodevice2', $input)
            ->first();

        if ($peserta) {
            return $peserta;
        }

        // 2. Cek NIK / NIK2 dengan fleksibilitas leading zero
        return $this->findParticipantByNik($input);
    }

    /**
     * Dapatkan semua varian NIK yang sah untuk peserta (nik, nik2, trimmed, dengan leading zero 0)
     * untuk keperluan query riwayat transaksi/kehadiran.
     */
    protected function getParticipantNikVariants(?\App\Models\Participant $peserta, ?string $fallbackNik = null): array
    {
        $list = [];

        if ($peserta) {
            if ($peserta->nik) {
                $raw = trim($peserta->nik);
                $trimmed = ltrim($raw, '0');
                $list[] = $raw;
                if ($trimmed !== '') {
                    $list[] = $trimmed;
                    $list[] = '0' . $trimmed;
                }
            }

            if ($peserta->nik2) {
                $raw2 = trim($peserta->nik2);
                $trimmed2 = ltrim($raw2, '0');
                $list[] = $raw2;
                if ($trimmed2 !== '') {
                    $list[] = $trimmed2;
                    $list[] = '0' . $trimmed2;
                }
            }
        }

        if ($fallbackNik) {
            $rawF = trim($fallbackNik);
            $trimmedF = ltrim($rawF, '0');
            $list[] = $rawF;
            if ($trimmedF !== '') {
                $list[] = $trimmedF;
                $list[] = '0' . $trimmedF;
            }
        }

        return array_values(array_filter(array_unique($list), fn($v) => !empty($v)));
    }

    /**
     * Convert a binary image BLOB or stream resource into a JPEG data URI.
     */
    protected function convertBlobToBase64($blob): ?string
    {
        if (!$blob) {
            return null;
        }

        $content = is_resource($blob) ? stream_get_contents($blob) : $blob;

        return 'data:image/jpeg;base64,' . base64_encode($content);
    }
}
