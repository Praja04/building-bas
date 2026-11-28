<?php

namespace App\Http\Controllers;

use App\Exports\PengambilanMakanExport;
use App\Models\Attendance;
use App\Models\Participant;
use App\Models\PengambilanMakan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class PengambilanMakanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PengambilanMakan::query()->orderBy('created_at', 'desc');
            return DataTables::of($query)
                ->addIndexColumn()
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
                ->rawColumns(['status_badge'])
                ->make(true);
        }

        return view('app.pengambilan-makan.index');
    }

    public function getByCard($input)
    {
        try {
            $pesertaFromFallback = null;

            // ========================================
            // LANGKAH 1: Cek di tabel MSIDCARD berdasarkan CARDNODEVICE
            // ========================================
            $data = $this->findActiveMsidcard('CARDNODEVICE', $input, true);

            // ========================================
            // LANGKAH 2: Jika tidak ditemukan, cek di tabel participants berdasarkan CARDNODEVICE / NIK
            // ========================================
            if (!$data) {
                $pesertaFromFallback = $this->findParticipantByCardOrNik($input);

                if ($pesertaFromFallback) {
                    $data = (object)[
                        'nik'          => $pesertaFromFallback->nik,
                        'nama'         => $pesertaFromFallback->nama,
                        'departemen'   => $pesertaFromFallback->departemen,
                        'cardnodevice' => $input,
                        'foto'         => null
                    ];
                }
            }

            // ========================================
            // LANGKAH 3: Jika masih tidak ditemukan, cek berdasarkan NIK
            // ========================================
            if (!$data) {
                $data = $this->findActiveMsidcard('NIK', $input, true);

                if (!$data && !$pesertaFromFallback) {
                    $pesertaFromFallback = $this->findParticipantByCardOrNik($input);

                    if ($pesertaFromFallback) {
                        $data = (object)[
                            'nik'          => $pesertaFromFallback->nik,
                            'nama'         => $pesertaFromFallback->nama,
                            'departemen'   => $pesertaFromFallback->departemen,
                            'cardnodevice' => $pesertaFromFallback->cardnodevice1 ?? $pesertaFromFallback->cardnodevice2,
                            'foto'         => null
                        ];
                    }
                }
            }

            // ========================================
            // JIKA MASIH TIDAK ADA → RETURN TIDAK TERDAFTAR
            // ========================================
            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'error_code' => 'TIDAK_TERDAFTAR_IDCARD',
                    'message' => 'Data tidak ditemukan, kartu atau NIK tidak terdaftar!'
                ], 404);
            }

            // ========================================
            // CEK APAKAH TERDAFTAR SEBAGAI PESERTA
            // ========================================
            $peserta = $pesertaFromFallback ?? $this->findParticipantByNik($data->nik);

            if (!$peserta) {
                return response()->json([
                    'status' => 'rejected',
                    'error_code' => 'TIDAK_TERDAFTAR_PESERTA',
                    'message' => 'Anda tidak berhak mengambil makan. Anda tidak terdaftar sebagai peserta!',
                    'data' => [
                        'nik' => $data->nik,
                        'nama' => $data->nama,
                        'departemen' => $data->departemen,
                        'foto' => $this->convertBlobToBase64($data->foto)
                    ]
                ], 403);
            }

            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();

            // Kumpulkan semua varian NIK peserta yang sah
            $nikList = $this->getParticipantNikVariants($peserta, $data->nik);

            // ========================================
            // VALIDASI KEHADIRAN (HARUS SUDAH ADA DI TABEL ATTENDANCE)
            // ========================================
            $isRegist = Attendance::whereIn('nik', $nikList)
                ->whereBetween('jam_masuk', [$todayStart, $todayEnd])
                ->first();

            if (!$isRegist) {
                return response()->json([
                    'status' => 'rejected',
                    'error_code' => 'BELUM_MASUK',
                    'message' => 'Anda belum melakukan registrasi, tidak dapat mengambil makan!',
                    'data' => [
                        'nik' => $data->nik,
                        'nama' => $data->nama,
                        'departemen' => $data->departemen,
                        'foto' => $this->convertBlobToBase64($data->foto)
                    ]
                ], 403);
            }

            // ========================================
            // VALIDASI PENGAMBILAN (hanya boleh 1 kali)
            // ========================================
            $cardCandidates = $this->normalizeNumericCandidates($input);
            $pengambilanHariIni = PengambilanMakan::whereIn('nik', $nikList)
                ->whereBetween('waktu_ambil', [$todayStart, $todayEnd])
                ->orderBy('waktu_ambil', 'desc')
                ->first()
                ?? PengambilanMakan::whereIn('cardnodevice', $cardCandidates)
                ->whereBetween('waktu_ambil', [$todayStart, $todayEnd])
                ->orderBy('waktu_ambil', 'desc')
                ->first();

            // Jika sudah pernah ambil, TOLAK
            if ($pengambilanHariIni) {
                $waktuAmbil = Carbon::parse($pengambilanHariIni->waktu_ambil)->format('H:i:s');

                return response()->json([
                    'status' => 'rejected',
                    'error_code' => 'SUDAH_AMBIL',
                    'message' => 'Pengambilan makan sudah dilakukan!',
                    'data' => [
                        'nik' => $data->nik,
                        'nama' => $data->nama,
                        'departemen' => $data->departemen,
                        'waktu_ambil_sebelumnya' => $waktuAmbil,
                        'jumlah_pengambilan' => 1,
                        'foto' => $this->convertBlobToBase64($data->foto)
                    ]
                ], 403);
            }

            // ========================================
            // SIMPAN DATA PENGAMBILAN MAKAN (First time today)
            // ========================================
            // Gunakan $peserta->nik yang valid di tabel participants agar foreign key constraint tidak gagal
            $pengambilan = PengambilanMakan::create([
                'cardnodevice' => $data->cardnodevice ?? "0",
                'nik' => $peserta->nik,
                'nama_pegawai' => $data->nama,
                'departemen' => $data->departemen,
                'waktu_ambil' => now(),
                'status' => 'sudah_diambil',
            ]);

            // Log untuk audit
            Log::info('Pengambilan Makan Berhasil', [
                'nik' => $peserta->nik,
                'nama' => $data->nama,
                'waktu' => now()->toDateTimeString()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengambilan makan berhasil dicatat!',
                'data' => [
                    'nik' => $peserta->nik,
                    'nama' => $data->nama,
                    'departemen' => $data->departemen,
                    'waktu_ambil' => $pengambilan->waktu_ambil->format('H:i:s'),
                    'tanggal' => $pengambilan->waktu_ambil->format('d/m/Y'),
                    'foto' => $this->convertBlobToBase64($data->foto)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error Pengambilan Makan', [
                'error' => $e->getMessage(),
                'cardnodevice' => $input
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        try {
            $fileName = 'pengambilan_makan_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new PengambilanMakanExport, $fileName);
        } catch (\Exception $e) {
            Log::error('[PengambilanMakanController export]', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }
    }
}
