<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use App\Exports\AttendanceExport;
use App\Models\Participant;

/**
 * Controller untuk proses kehadiran (absensi).
 * Referensinya ke tabel participants tapi bukan data peserta.
 */
class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $attendances = Attendance::query()
                ->orderByDesc('created_at');

            if ($request->filled('is_doorprize')) {
                $attendances->where('is_doorprize', $request->is_doorprize);
            }

            return DataTables::of($attendances)
                ->addIndexColumn()
                ->editColumn('jam_masuk_formatted', function ($row) {
                    return $row->jam_masuk
                        ? date('d M Y H:i', strtotime($row->jam_masuk)) : '-';
                })
                ->editColumn('jam_keluar_formatted', function ($row) {
                    return $row->jam_keluar
                        ? date('d M Y H:i', strtotime($row->jam_keluar)) : '-';
                })
                ->addColumn('status_doorprize', function ($row) {
                    if ($row->is_doorprize == 1) {
                        return '<span class="badge bg-success">Ya</span>';
                    } elseif ($row->is_doorprize == 0) {
                        return '<span class="badge bg-danger">Tidak</span>';
                    } else {
                        return '<span class="badge bg-secondary">Belum Ditentukan</span>';
                    }
                })
                ->rawColumns(['status_doorprize'])
                ->make(true);
        }

        return view('app.attendance.index');
    }

    public function getUser($input)
    {
        try {
            // ========================================
            // LANGKAH 1: Cek di tabel MSIDCARD berdasarkan CARDNODEVICE
            // ========================================
            $data = $this->findActiveMsidcard('CARDNODEVICE', $input);

            // ========================================
            // LANGKAH 2: Jika tidak ditemukan, cek di tabel participants berdasarkan CARDNODEVICE / NIK
            // ========================================
            if (!$data) {
                $peserta = $this->findParticipantByCardOrNik($input);

                if ($peserta) {
                    $data = (object)[
                        'nik'          => $peserta->nik,
                        'nama'         => $peserta->nama,
                        'departemen'   => $peserta->departemen,
                        'cardnodevice' => $input,
                    ];
                }
            }

            // ========================================
            // LANGKAH 3: Jika masih tidak ditemukan, cek berdasarkan NIK di MSIDCARD
            // ========================================
            if (!$data) {
                $data = $this->findActiveMsidcard('NIK', $input);

                if (!$data) {
                    $peserta = $this->findParticipantByCardOrNik($input);

                    if ($peserta) {
                        $data = (object)[
                            'nik'          => $peserta->nik,
                            'nama'         => $peserta->nama,
                            'departemen'   => $peserta->departemen,
                            'cardnodevice' => $peserta->cardnodevice1 ?? $peserta->cardnodevice2,
                        ];
                    }
                }
            }

            // ========================================
            // HASIL AKHIR
            // ========================================
            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data karyawan tidak ditemukan.'
                ], 404);
            }

            // Sinkronkan data dengan tabel participants (agar NIK resmi peserta digunakan)
            $peserta = $this->findParticipantByNik($data->nik);
            if ($peserta) {
                $data->nik = $peserta->nik;
                $data->nama = $peserta->nama;
                $data->departemen = $peserta->departemen;
            }

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            Log::error('[AttendanceController getUser]', [
                'error' => $e->getMessage(),
                'input' => $input
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'cardnodevice' => 'nullable|string',
                'nik' => 'required|string',
                'nama_pegawai' => 'required|string',
                'departemen'   => 'required|string',
                'is_doorprize' => 'nullable|boolean',
            ]);

            $peserta = $this->findParticipantByNik($request->nik);

            if (!$peserta) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Registrasi ditolak. Anda tidak terdaftar sebagai peserta!',
                ], 404);
            }

            $todayStart = Carbon::today()->startOfDay();
            $todayEnd = Carbon::today()->endOfDay();

            $nikList = $this->getParticipantNikVariants($peserta, $request->nik);

            $existing = Attendance::whereIn('nik', $nikList)
                ->whereBetween('jam_masuk', [$todayStart, $todayEnd])
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta sudah melakukan registrasi.'
                ], 409);
            }

            // cek kelayakan doorprize (jam masuk > 8 -> tidak dapat)
            $jamMasuk = now();
            $batasMasuk = Carbon::today()->setTime(8, 0, 0);
            $isLate = $jamMasuk->gt($batasMasuk);

            $eligibleForDoorprize = $isLate ? false : true;

            $pesertaRecord = Attendance::create([
                'cardnodevice' => $request->cardnodevice,
                'nik'          => $peserta->nik,
                'nama_pegawai' => $request->nama_pegawai,
                'departemen'   => $request->departemen,
                'jam_masuk'    => $jamMasuk,
                'is_doorprize' => $eligibleForDoorprize,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil registrasi!',
                'data' => [
                    'nik' => $pesertaRecord->nik,
                    'nama_pegawai' => $pesertaRecord->nama_pegawai,
                    'departemen' => $pesertaRecord->departemen,
                    'jam_masuk_formatted'    => Carbon::parse($pesertaRecord->jam_masuk)->format('d M Y H:i'),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'cardnodevice' => 'nullable|string',
                'nik' => 'required|string',
            ]);

            $peserta = $this->findParticipantByNik($request->nik);

            $nikList = $this->getParticipantNikVariants($peserta, $request->nik);

            $attendance = Attendance::whereIn('nik', $nikList)
                ->whereBetween('jam_masuk', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
                ->first();

            if (!$attendance) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Peserta belum registrasi masuk'
                ], 404);
            }

            if ($attendance->jam_keluar !== null) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Peserta sudah melakukan checkout sebelumnya.'
                ], 403);
            }

            $jamKeluar = now();
            $jamMasuk = Carbon::parse($attendance->jam_masuk);

            $batasMasuk = Carbon::today()->setTime(8, 0, 0);
            $batasKeluar = Carbon::today()->setTime(14, 0, 0);

            $isLate = $jamMasuk->gt($batasMasuk);
            $keluarSebelumWaktu = $jamKeluar->lt($batasKeluar);

            if ($isLate) {
                $statusDoorprize = false;
            } elseif ($keluarSebelumWaktu) {
                $statusDoorprize = false;
            } else {
                $statusDoorprize = true;
            }

            $attendance->update([
                'jam_keluar'   => $jamKeluar,
                'is_doorprize' => $statusDoorprize
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Checkout berhasil!',
                'data' => [
                    'nik' => $attendance->nik,
                    'nama_pegawai' => $attendance->nama_pegawai,
                    'departemen' => $attendance->departemen,
                    'jam_keluar_formatted'    => Carbon::parse($attendance->jam_keluar)->format('d M Y H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        try {
            $fileName = 'kehadiran_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new AttendanceExport, $fileName);
        } catch (\Exception $e) {
            Log::error('[AttendanceController export]', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }
    }
}
