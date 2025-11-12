<?php

namespace App\Http\Controllers;

use App\Models\PengambilanMakan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function getByCard($cardnodevice)
    {
        try {
            // Ambil data dari database eksternal
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen', 'FOTOBLOB as foto')
                ->where('CARDNODEVICE', $cardnodevice)
                ->first();

            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kartu tidak ditemukan di sistem!'
                ], 404);
            }

            // VALIDASI PENGAMBILAN HARI INI (hanya boleh 1 kali)
            $today = Carbon::today();
            $pengambilanHariIni = PengambilanMakan::where('cardnodevice', $cardnodevice)
                ->whereDate('waktu_ambil', $today)
                ->orderBy('waktu_ambil', 'desc')
                ->first();

            // Jika sudah pernah ambil hari ini, TOLAK
            if ($pengambilanHariIni) {
                $waktuAmbil = Carbon::parse($pengambilanHariIni->waktu_ambil)->format('H:i:s');

                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Pengambilan makan sudah dilakukan hari ini!',
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

            // Hitung total pengambilan hari ini (untuk memastikan tidak lebih dari 1)
            $totalPengambilanHariIni = PengambilanMakan::where('cardnodevice', $cardnodevice)
                ->whereDate('waktu_ambil', $today)
                ->count();

            // Double check: jika ada data (meskipun query pertama null)
            if ($totalPengambilanHariIni > 0) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Anda sudah mencapai batas pengambilan makan hari ini!',
                    'data' => [
                        'nik' => $data->nik,
                        'nama' => $data->nama,
                        'total_pengambilan' => $totalPengambilanHariIni,
                        'foto' => $this->convertBlobToBase64($data->foto)
                    ]
                ], 403);
            }

            // SIMPAN DATA PENGAMBILAN MAKAN (First time today)
            $pengambilan = PengambilanMakan::create([
                'cardnodevice' => $cardnodevice,
                'nik' => $data->nik,
                'nama_pegawai' => $data->nama,
                'departemen' => $data->departemen,
                'waktu_ambil' => now(),
                'status' => 'sudah_diambil',
            ]);

            // Log untuk audit (optional)
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
                'cardnodevice' => $cardnodevice
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
}
