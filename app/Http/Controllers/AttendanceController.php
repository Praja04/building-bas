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

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $attendances = Attendance::query()
                ->orderByDesc('created_at');

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
                    if ($row->is_doorprize === 1) {
                        return '<span class="badge bg-success">Ya</span>';
                    } elseif ($row->is_doorprize === 0) {
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
            // --- 1. CARI BERDASARKAN CARDNODEVICE ---
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen')
                ->where('CARDNODEVICE', $input)
                ->first();

            // Jika ketemu lewat cardnodevice → langsung return
            if ($data) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ], 200);
            }

            // --- 2. JIKA CARD TIDAK DITEMUKAN → CARI BERDASARKAN NIK ---
            $dataByNik = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen')
                ->where('NIK', $input)
                ->first();

            if ($dataByNik) {
                return response()->json([
                    'status' => 'success',
                    'data' => $dataByNik
                ], 200);
            }

            // --- 3. KEDUA-DUANYA TIDAK DITEMUKAN ---
            return response()->json([
                'status' => 'error',
                'message' => 'Data karyawan tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {

            Log::error('[AttendanceController getUser]', [
                'error' => $e->getMessage(),
                'input' => $input
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server.'
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

            $existing = Attendance::whereDate('jam_masuk', now()->toDateString())
                ->where('nik', $request->nik)
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta sudah melakukan registrasi.'
                ], 403);
            }

            // cek kelayakan doorprize (jam masuk > 8 -> tidak dapat)
            $jamMasuk = now();
            $batasMasuk = Carbon::today()->setTime(8, 0, 0);
            $isLate = $jamMasuk->gt($batasMasuk);

            $eligibleForDoorprize = $isLate ? false : null;

            $peserta = Attendance::create([
                'cardnodevice' => $request->cardnodevice,
                'nik'          => $request->nik,
                'nama_pegawai' => $request->nama_pegawai,
                'departemen'   => $request->departemen,
                'jam_masuk'    => $jamMasuk,
                'is_doorprize' => $eligibleForDoorprize,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil registrasi!',
                'data' => [
                    'nik' => $peserta->nik,
                    'nama_pegawai' => $peserta->nama_pegawai,
                    'departemen' => $peserta->departemen,
                    'jam_masuk_formatted'    => Carbon::parse($peserta->jam_masuk)->format('d M Y H:i'),
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

            $peserta = Attendance::where('nik', $request->nik)
                ->whereDate('jam_masuk', now()->toDateString())
                ->first();

            if (!$peserta) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Peserta belum registrasi masuk.'
                ], 404);
            }

            if ($peserta->jam_keluar !== null) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Peserta sudah melakukan checkout sebelumnya.'
                ], 403);
            }

            $jamKeluar = now();
            $jamMasuk = Carbon::parse($peserta->jam_masuk);

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

            $peserta->update([
                'jam_keluar'   => $jamKeluar,
                'is_doorprize' => $statusDoorprize
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Checkout berhasil!',
                'data' => [
                    'nik' => $peserta->nik,
                    'nama_pegawai' => $peserta->nama_pegawai,
                    'departemen' => $peserta->departemen,
                    'jam_keluar_formatted'    => Carbon::parse($peserta->jam_keluar)->format('d M Y H:i'),
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
            $fileName = 'attendance_' . now()->format('Ymd_His') . '.xlsx';
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
