<?php

namespace App\Http\Controllers;

use App\Models\PenitipanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class PenitipanBarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PenitipanBarang::query()->orderBy('created_at', 'desc');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    if ($row->status == 'belum_diambil') {
                        return '<span class="badge bg-warning text-dark">Belum Diambil</span>';
                    } else {
                        return '<span class="badge bg-success">Sudah Diambil</span>';
                    }
                })
                ->addColumn('waktu_titip_formatted', function ($row) {
                    return $row->waktu_titip ? date('d M Y H:i', strtotime($row->waktu_titip)) : '-';
                })
                ->addColumn('waktu_ambil_formatted', function ($row) {
                    return $row->waktu_ambil ? date('d M Y H:i', strtotime($row->waktu_ambil)) : '-';
                })
                ->addColumn('action', function ($row) {
                    $detailBtn = '<button class="btn btn-sm btn-info text-white btn-detail" data-id="' . $row->id . '"><i class="mdi mdi-eye"></i> Detail</button> ';

                    $ambilBtn = '';
                    if ($row->status == 'belum_diambil') {
                        $ambilBtn = '<button class="btn btn-sm btn-success btn-ambil" data-id="' . $row->id . '"><i class="mdi mdi-check"></i> Ambil</button>';
                    }

                    return $detailBtn . $ambilBtn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('app.penitipan-barang.index');
    }

    public function getByCard($cardnodevice)
    {
        try {
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select('NIK as nik', 'EMPNM as nama', 'DEPTID as departemen')
                ->where('CARDNODEVICE', $cardnodevice)
                ->first();

            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Kartu tidak ditemukan.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // Simpan foto penitip
            $fotoPenitipPath = null;
            if ($request->foto_penitip) {
                $fotoPenitipPath = $this->saveBase64Image($request->foto_penitip, 'penitipan/penitip');
            }

            // Simpan foto barang
            $fotoBarangPath = null;
            if ($request->foto_barang) {
                $fotoBarangPath = $this->saveBase64Image($request->foto_barang, 'penitipan/barang');
            }

            // Simpan data ke database
            $penitipan = PenitipanBarang::create([
                'cardnodevice' => $request->cardnodevice,
                'nik' => $request->nik,
                'nama_penitip' => $request->nama_penitip,
                'departemen' => $request->departemen,
                'telepon' => $request->telepon,
                'no_penitipan' => $request->no_penitipan,
                'jenis_barang' => $request->jenis_barang,
                'foto_penitip' => $fotoPenitipPath,
                'foto_barang' => $fotoBarangPath,
                'waktu_titip' => $request->waktu_titip,
                'keterangan' => $request->keterangan,
                'status' => 'belum_diambil',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data penitipan berhasil disimpan!',
                'data' => $penitipan
            ]);
            if ($data) {
                return response()->json(['status' => 'success', 'data' => $data]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Kartu tidak ditemukan.']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function detail($id)
    {
        try {
            $data = PenitipanBarang::findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $data->id,
                    'nik' => $data->nik,
                    'nama_penitip' => $data->nama_penitip,
                    'departemen' => $data->departemen,
                    'telepon' => $data->telepon,
                    'no_penitipan' => $data->no_penitipan,
                    'jenis_barang' => ucfirst($data->jenis_barang),
                    'foto_penitip' => $data->foto_penitip ? asset('storage/' . $data->foto_penitip) : null,
                    'foto_barang' => $data->foto_barang ? asset('storage/' . $data->foto_barang) : null,
                    'waktu_titip' => date('d M Y H:i', strtotime($data->waktu_titip)),
                    'waktu_ambil' => $data->waktu_ambil ? date('d M Y H:i', strtotime($data->waktu_ambil)) : '-',
                    'keterangan' => $data->keterangan ?? '-',
                    'status' => $data->status,
                    'status_text' => $data->status == 'belum_diambil' ? 'Belum Diambil' : 'Sudah Diambil'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function ambil($id)
    {
        try {
            $data = PenitipanBarang::findOrFail($id);

            if ($data->status == 'sudah_diambil') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang sudah diambil sebelumnya!'
                ], 400);
            }

            $data->update([
                'status' => 'sudah_diambil',
                'waktu_ambil' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Barang berhasil diambil!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveBase64Image($base64String, $directory)
    {
        try {
            $image = str_replace('data:image/png;base64,', '', $base64String);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            // Generate unique filename
            $filename = $directory . '/' . Str::uuid() . '.png';

            // Save to storage/app/public
            Storage::disk('public')->put($filename, $imageData);

            return $filename;
        } catch (\Exception $e) {
            throw new \Exception('Gagal menyimpan foto: ' . $e->getMessage());
        }
    }

    /**
     * Delete image from storage
     */
    private function deleteImage($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
