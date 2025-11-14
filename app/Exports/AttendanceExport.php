<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $rowNumber = 0;

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Attendance::select('nik', 'nama_pegawai', 'departemen', 'jam_masuk', 'jam_keluar', 'is_doorprize')
            // ->where('is_doorprize', 1)
            ->orderBy('is_doorprize', 'asc') // 0 dulu, lalu 1
            ->get();
    }

    /**
     * Headings untuk kolom
     */
    public function headings(): array
    {
        return ["No", "NIK", "Nama Pegawai", "Departemen", "Jam Masuk", "Jam Keluar", "Layak Doorprize"];
    }

    /**
     * Mapping data untuk setiap row dengan nomor urut
     */
    public function map($attendance): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $attendance->nik,
            $attendance->nama_pegawai,
            $attendance->departemen,
            $attendance->jam_masuk,
            $attendance->jam_keluar ?? "Belum Keluar",
            $attendance->is_doorprize === 1
                ? "Ya"
                : ($attendance->is_doorprize === 0
                    ? "Tidak"
                    : "Belum Ditentukan"),
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
            'E' => 25,  // Jam Masuk 
            'F' => 25,  // Jam Keluar
            'G' => 25,  // Status Layak
        ];
    }

    /**
     * Styling untuk Excel dengan Border
     */
    public function styles(Worksheet $sheet)
    {
        $rowCount = $sheet->getHighestRow();

        // Style untuk header (baris 1)
        $sheet->getStyle('A1:G1')->applyFromArray([
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
        $sheet->getStyle('A1:G' . $rowCount)->applyFromArray([
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
        $sheet->getStyle('G2:G' . $rowCount)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
