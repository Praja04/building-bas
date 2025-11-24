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
        $nik        = trim($row['nik'] ?? '');
        $nama       = trim($row['nama'] ?? '');
        $departemen = trim($row['departemen'] ?? '');

        // skip kalau rownya kosong
        if ($nik === '' && $nama === '' && $departemen === '') {
            return null;
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
        if (Participant::where('nik', $nik)->exists()) {
            throw new \Exception(json_encode([
                'error' => 'DuplicateData',
                'message' => 'NIK sudah terdaftar',
                'data' => [
                    'nik' => $nik,
                    'nama' => $nama,
                    'departemen' => $departemen,
                ],
            ]));
        }

        return new Participant([
            'nik'        => $nik,
            'nama'       => $nama,
            'departemen' => $departemen,
        ]);
    }
}
