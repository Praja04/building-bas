<?php

namespace App\Http\Controllers;

use App\Exports\PengambilanKaosExport;
use App\Models\Participant;
use App\Models\PengambilanKaos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class PengambilanKaosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PengambilanKaos::query()->orderBy('created_at', 'desc');
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('participant_id', function ($row) {
                    return $row->participant_id
                        ? '<span class="fw-semibold">' . e($row->participant_id) . '</span>'
                        : '<span class="text-muted">-</span>';
                })
                ->editColumn('ukuran_baju', function ($row) {
                    return $row->ukuran_baju
                        ? '<span class="badge bg-primary fs-6 px-2 py-1">' . e($row->ukuran_baju) . '</span>'
                        : '<span class="text-muted">-</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->status == 'belum_diambil') {
                        return '<span class="badge bg-warning text-dark">Belum Diambil</span>';
                    } else {
                        return '<span class="badge bg-success">Sudah Diambil</span>';
                    }
                })
                ->addColumn('waktu_ambil_formatted', function ($row) {
                    return $row->waktu_ambil ? date('d M Y H:i', strtotime($row->waktu_ambil)) : '-';
                })
                ->rawColumns(['participant_id', 'ukuran_baju', 'status_badge'])
                ->make(true);
        }

        return view('app.pengambilan-kaos.index');
    }

    public function getByCard($input)
    {
        try {
            $pesertaFromFallback = null;

            // 1️. CARI BERDASARKAN CARDNODEVICE
            $data = $this->findActiveMsidcard('CARDNODEVICE', $input, true);

            // 2️. JIKA TIDAK DITEMUKAN, CARI BERDASARKAN NIK 
            if (!$data) {
                $data = $this->findActiveMsidcard('NIK', $input, true);
            }

            // last option: cari di table participants
            // note: untuk PT PAS akan cari data di sini
            if (!$data) {
                $pesertaFromFallback = $this->findParticipantByCardOrNik($input);

                if ($pesertaFromFallback) {
                    $data = (object)[
                        'nik'        => $pesertaFromFallback->nik,
                        'nama'       => $pesertaFromFallback->nama,
                        'departemen' => $pesertaFromFallback->departemen,
                        'foto'       => null
                    ];
                }
            }

            // JIKA MASIH TIDAK ADA → RETURN TIDAK TERDAFTAR
            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'error_code' => 'TIDAK_TERDAFTAR_IDCARD',
                    'message' => 'Data tidak ditemukan, kartu atau NIK tidak terdaftar!'
                ], 404);
            }

            // Cek apakah peserta terdaftar di tabel participants (cek nik & nik2 beserta leading zero)
            $peserta = $pesertaFromFallback ?? $this->findParticipantByNik($data->nik);

            if (!$peserta) {
                return response()->json([
                    'status' => 'rejected',
                    'error_code' => 'TIDAK_TERDAFTAR_PESERTA',
                    'message' => 'Anda tidak berhak mengambil kaos. Anda tidak terdaftar sebagai peserta!',
                    'data' => [
                        'nik' => $data->nik,
                        'nama' => $data->nama,
                        'departemen' => $data->departemen,
                        'foto' => $this->convertBlobToBase64($data->foto)
                    ]
                ], 403);
            }

            // Kumpulkan semua varian NIK peserta yang sah (nik, nik2, dan varian leading-0) untuk validasi riwayat
            $nikList = $this->getParticipantNikVariants($peserta, $data->nik);

            // VALIDASI PENGAMBILAN HANYA 1 KALI
            $pengambilanSebelumnya = PengambilanKaos::whereIn('nik', $nikList)->first()
                ?? PengambilanKaos::where('cardnodevice', $input)->first();

            // Jika sudah pernah ambil, TOLAK
            if ($pengambilanSebelumnya) {
                $waktuAmbil = Carbon::parse($pengambilanSebelumnya->waktu_ambil)->format('d M Y H:i:s');

                return response()->json([
                    'status' => 'rejected',
                    'error_code' => 'SUDAH_AMBIL',
                    'message' => 'Anda telah mengambil kaos sebelumnya.',
                    'data' => [
                        'participant_id' => $pengambilanSebelumnya->participant_id ?? $peserta->participant_id ?? '-',
                        'nik' => $data->nik,
                        'nama' => $data->nama,
                        'departemen' => $data->departemen,
                        'ukuran_baju' => $pengambilanSebelumnya->ukuran_baju ?? $peserta->ukuran_baju ?? '-',
                        'waktu_ambil_sebelumnya' => $waktuAmbil,
                        'jumlah_pengambilan' => 1,
                        'foto' => $this->convertBlobToBase64($data->foto)
                    ]
                ], 403);
            }

            // SIMPAN DATA PENGAMBILAN KAOS (First time today)
            // Gunakan $peserta->nik yang valid di tabel participants agar foreign key constraint tidak gagal
            $pengambilan = PengambilanKaos::create([
                'participant_id' => $peserta->participant_id ?? null,
                'cardnodevice' => $data->cardnodevice ?? '0',
                'nik' => $peserta->nik,
                'nama_pegawai' => $data->nama,
                'departemen' => $data->departemen,
                'ukuran_baju' => $peserta->ukuran_baju ?? null,
                'waktu_ambil' => now(),
                'status' => 'sudah_diambil',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengambilan kaos berhasil dicatat!',
                'data' => [
                    'participant_id' => $peserta->participant_id ?? '-',
                    'nik' => $peserta->nik,
                    'nama' => $data->nama,
                    'departemen' => $data->departemen,
                    'ukuran_baju' => $peserta->ukuran_baju ?? '-',
                    'waktu_ambil' => $pengambilan->waktu_ambil->format('H:i:s'),
                    'tanggal' => $pengambilan->waktu_ambil->format('d/m/Y'),
                    'foto' => $this->convertBlobToBase64($data->foto)
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        try {
            $fileName = 'pengambilan_kaos_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new PengambilanKaosExport, $fileName);
        } catch (\Exception $e) {
            Log::error('[PengambilanKaosController export]', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }
    }
}
