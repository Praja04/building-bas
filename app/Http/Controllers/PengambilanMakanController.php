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
            $data = null;

            // ========================================
            // LANGKAH 1: Cek di tabel MSIDCARD berdasarkan CARDNODEVICE
            // ========================================
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen', 'CARDNODEVICE as cardnodevice', 'FOTOBLOB as foto')
                ->where('CARDNODEVICE', $input)
                ->where('STATUS', 'X') // active
                ->orderByRaw('CAST(CHANGEDON AS SIGNED) DESC')
                ->first();

            // ========================================
            // LANGKAH 2: Jika tidak ditemukan, cek di tabel participants berdasarkan CARDNODEVICE
            // ========================================
            if (!$data) {
                // Cek cardnodevice1 terlebih dahulu
                $peserta = Participant::where('cardnodevice1', $input)->first();

                // Jika tidak ada, cek cardnodevice2
                if (!$peserta) {
                    $peserta = Participant::where('cardnodevice2', $input)->first();
                }

                // Jika ditemukan, format data
                if ($peserta) {
                    $data = (object)[
                        'nik'          => $peserta->nik,
                        'nama'         => $peserta->nama,
                        'departemen'   => $peserta->departemen,
                        'cardnodevice' => $input,
                        'foto'         => null
                    ];
                }
            }

            // ========================================
            // LANGKAH 3: Jika masih tidak ditemukan, cek berdasarkan NIK
            // ========================================
            if (!$data) {
                // 3a. Cek NIK di tabel MSIDCARD
                $data = DB::connection('192.168.178.44-admin')
                    ->table('MSIDCARD')
                    ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen', 'CARDNODEVICE as cardnodevice', 'FOTOBLOB as foto')
                    ->where('NIK', $input)
                    ->where('STATUS', 'X') // active
                    ->orderByRaw('CAST(CHANGEDON AS SIGNED) DESC')
                    ->first();

                // 3b. Jika tidak ada, cek NIK dan NIK2 di tabel participants
                if (!$data) {
                    // Cek NIK terlebih dahulu
                    $peserta = Participant::where('nik', $input)->first();

                    // Jika tidak ada, cek NIK2
                    if (!$peserta) {
                        $peserta = Participant::where('nik2', $input)->first();
                    }

                    // Jika ditemukan, format data
                    if ($peserta) {
                        $data = (object)[
                            'nik'          => $peserta->nik,
                            'nama'         => $peserta->nama,
                            'departemen'   => $peserta->departemen,
                            'cardnodevice' => $peserta->cardnodevice1 ?? $peserta->cardnodevice2,
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
            $peserta = Participant::where('nik', $data->nik)
                ->orWhere('nik2', $data->nik)
                ->first();

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

            // ========================================
            // VALIDASI KEHADIRAN (HARUS SUDAH ADA DI TABEL ATTENDANCE)
            // ========================================
            $isRegist = Attendance::where('nik', $data->nik)
                ->whereDate('jam_masuk', Carbon::today())
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
            $today = Carbon::today();
            $pengambilanHariIni = PengambilanMakan::where(function ($query) use ($input, $data) {
                $query->where('cardnodevice', $input)
                    ->orWhere('nik', $data->nik);
            })
                ->whereDate('waktu_ambil', $today)
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

            // Hitung total pengambilan (untuk memastikan tidak lebih dari 1)
            $totalPengambilanHariIni = PengambilanMakan::where(function ($query) use ($input, $data) {
                $query->where('cardnodevice', $input)
                    ->orWhere('nik', $data->nik);
            })
                ->whereDate('waktu_ambil', $today)
                ->count();

            // Double check: jika ada data (meskipun query pertama null)
            if ($totalPengambilanHariIni > 0) {
                return response()->json([
                    'status' => 'rejected',
                    'error_code' => 'SUDAH_AMBIL',
                    'message' => 'Anda sudah mencapai batas pengambilan makan hari ini!',
                    'data' => [
                        'nik' => $data->nik,
                        'nama' => $data->nama,
                        'total_pengambilan' => $totalPengambilanHariIni,
                        'foto' => $this->convertBlobToBase64($data->foto)
                    ]
                ], 403);
            }

            // ========================================
            // SIMPAN DATA PENGAMBILAN MAKAN (First time today)
            // ========================================
            $pengambilan = PengambilanMakan::create([
                'cardnodevice' => $data->cardnodevice ?? "0",
                'nik' => $data->nik,
                'nama_pegawai' => $data->nama,
                'departemen' => $data->departemen,
                'waktu_ambil' => now(),
                'status' => 'sudah_diambil',
            ]);

            // Log untuk audit
            Log::info('Pengambilan Makan Berhasil', [
                'nik' => $data->nik,
                'nama' => $data->nama,
                'waktu' => now()->toDateTimeString()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengambilan makan berhasil dicatat!',
                'data' => [
                    'nik' => $data->nik,
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

    private function convertBlobToBase64($blob)
    {
        if (!$blob) {
            return null;
        }

        if (is_resource($blob)) {
            return 'data:image/jpeg;base64,' . base64_encode(stream_get_contents($blob));
        } else {
            return 'data:image/jpeg;base64,' . base64_encode($blob);
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
