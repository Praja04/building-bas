<?php

namespace App\Http\Controllers;

use App\Models\PengambilanKaos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class PengambilanKaosController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PengambilanKaos::query()->orderBy('created_at', 'desc');
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

        return view('app.pengambilan-kaos.index');
    }

    public function getByCard($input)
    {
        try {
            // 1️. CARI BERDASARKAN CARDNODEVICE
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen', 'FOTOBLOB as foto')
                ->where('CARDNODEVICE', $input)
                ->first();

            // 2️. JIKA TIDAK DITEMUKAN, CARI BERDASARKAN NIK
            if (!$data) {
                $data = DB::connection('192.168.178.44-admin')
                    ->table('MSIDCARD')
                    ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen', 'FOTOBLOB as foto')
                    ->where('NIK', $input)
                    ->first();
            }

            // JIKA MASIH TIDAK ADA → RETURN TIDAK TERDAFTAR
            if (!$data) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan, kartu atau NIK tidak terdaftar!'
                ], 404);
            }

            // VALIDASI PENGAMBILAN HANYA 1 KALI
            $pengambilanSebelumnya = PengambilanKaos::where('cardnodevice', $input)->first();

            // Jika sudah pernah ambil, TOLAK
            if ($pengambilanSebelumnya) {
                $waktuAmbil = Carbon::parse($pengambilanSebelumnya->waktu_ambil)->format('d M Y H:i:s');

                return response()->json([
                    'status' => 'rejected',
                    'error_code' => 'SUDAH_AMBIL',
                    'message' => 'Anda telah mengambil kaos sebelumnya.',
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

            // SIMPAN DATA PENGAMBILAN KAOS (First time today)
            $pengambilan = PengambilanKaos::create([
                'cardnodevice' => $input,
                'nik' => $data->nik,
                'nama_pegawai' => $data->nama,
                'departemen' => $data->departemen,
                'waktu_ambil' => now(),
                'status' => 'sudah_diambil',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengambilan kaos berhasil dicatat!',
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
