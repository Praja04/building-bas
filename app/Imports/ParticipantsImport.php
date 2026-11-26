<?php

namespace App\Imports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $participant_id = trim((string)($row['participant_id'] ?? $row['id_participant'] ?? $row['id_peserta'] ?? ''));
        $nik            = trim((string)($row['nik'] ?? ''));
        $nama           = trim((string)($row['nama_karyawan'] ?? $row['nama_pegawai'] ?? $row['nama_peserta'] ?? $row['nama'] ?? ''));
        $departemen     = trim((string)($row['departemen'] ?? $row['department'] ?? ''));
        $ukuran_baju    = trim((string)($row['ukuran_baju'] ?? $row['ukuran'] ?? ''));

        // skip kalau rownya kosong
        if ($nik === '' && $nama === '' && $departemen === '') {
            return null;
        }

        // jika NIK kosong (misal data cadangan/spare), buat NIK placeholder agar tetap bisa masuk
        if ($nik === '' && $nama !== '') {
            $nik = 'SPARE-' . strtoupper(substr(md5($nama . microtime(true)), 0, 8));
        }

        // ada data required yang kosong
        if ($nik === '' || $nama === '' || $departemen === '') {
            throw new \Exception(json_encode([
                'error' => 'MissingData',
                'message' => 'Data belum terisi lengkap',
                'data' => [
                    'nik' => $nik,
                    'nama' => $nama,
                    'departemen' => $departemen,
                ],
            ]));
        }

        // NIK already exists 
        // if (Participant::where('nik', $nik)->exists()) {
        //     throw new \Exception(json_encode([
        //         'error' => 'DuplicateData',
        //         'message' => 'NIK sudah terdaftar',
        //         'data' => [
        //             'nik' => $nik,
        //             'nama' => $nama,
        //             'departemen' => $departemen,
        //         ],
        //     ]));
        // }

        // skip if NIK already exists
        if (Participant::where('nik', $nik)->exists()) {
            return null;
        }

        return new Participant([
            'participant_id' => $participant_id !== '' ? $participant_id : null,
            'nik'            => $nik,
            'nama'           => $nama,
            'departemen'     => $departemen,
            'ukuran_baju'    => $ukuran_baju !== '' ? $ukuran_baju : null,
        ]);
    }
}
