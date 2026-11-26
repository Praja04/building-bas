<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\PengambilanEskrim;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PengambilanEskrimExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $rowNumber = 0;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Participant::select(
            'participants.id as participant_id',
            'participants.nik',
            'participants.nama',
            'participants.departemen',
            'pengambilan_eskrim.waktu_ambil',
            'pengambilan_eskrim.status'
        )
            ->leftJoin('pengambilan_eskrim', 'pengambilan_eskrim.nik', '=', 'participants.nik')
            ->orderBy('participants.nama')
            ->get();
    }

    /**
     * Headings untuk kolom
     */
    public function headings(): array
    {
        return ["No", "NIK", "Nama Karyawan", "Departemen", "Waktu Ambil", "Status"];
    }

    /**
     * Mapping data untuk setiap row dengan nomor urut
     */
    public function map($data): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $data->nik,
            $data->nama_pegawai ?? $data->nama,
            $data->departemen,
            $data->waktu_ambil ?? "-",
            $data->status === "sudah_diambil"
                ? "Sudah Diambil"
                : "Belum Diambil"
        ];
    }

    /**
     * Set lebar kolom
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 15,  // NIK
            'C' => 30,  // Nama Pegawai
            'D' => 15,  // Departemen
            'E' => 25,  // Waktu Ambil
            'F' => 25,  // Status
        ];
    }

    /**
     * Styling untuk Excel dengan Border
     */
    public function styles(Worksheet $sheet)
    {
        $rowCount = $sheet->getHighestRow();

        // Style untuk header (baris 1)
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Style untuk semua data (termasuk border)
        $sheet->getStyle('A1:F' . $rowCount)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Alignment center
        $sheet->getStyle('A2:A' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:E' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
