<?php

namespace App\Exports;

use App\Models\DoorprizeWinner;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DoorprizeWinnerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    private int $no = 0;

    public function collection()
    {
        return DoorprizeWinner::orderByDesc('waktu_menang')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Pegawai',
            'Departemen',
            'Hadiah',
            'Waktu Menang',
        ];
    }

    public function map($winner): array
    {
        $this->no++;

        return [
            $this->no,
            $winner->nik,
            $winner->nama_pegawai,
            $winner->departemen ?? '-',
            $winner->hadiah ?: '-',
            $winner->waktu_menang
                ? Carbon::parse($winner->waktu_menang)->format('d-m-Y H:i:s')
                : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
