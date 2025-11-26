<?php

namespace App\Http\Controllers;

use App\Exports\KaryawanExport;
use App\Exports\ParticipantExport;
use App\Imports\ParticipantsImport;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

/**
 * Controller untuk CRUD data peserta acara.
 * Tidak berhubungan dengan kehadiran (attendance).
 */
class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $participants = Participant::orderBy('nama', 'asc');

            return DataTables::of($participants)
                ->addIndexColumn()
                ->make(true);
        }

        return view('app.master.participants.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:participants,nik',
            'nama' => 'required',
            'departemen' => 'required',
        ], [
            'nik.unique' => 'NIK sudah terdaftar, gunakan NIK lain.'
        ]);

        try {
            Participant::create([
                'nik'   => $request->nik,
                'nama'      => $request->nama,
                'departemen' => $request->departemen,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Peserta berhasil ditambahkan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Peserta gagal ditambahkan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $participant = Participant::find($id);

        if (!$participant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Peserta tidak ditemukan'
            ], 404);
        }

        return response()->json($participant);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nik' => 'required|unique:participants,nik,' . $id . ',id',
            'nama' => 'required',
            'departemen' => 'required',
        ], [
            'nik.unique' => 'NIK sudah terdaftar, gunakan NIK lain.'
        ]);

        try {
            $participant = Participant::find($id);

            if (!$participant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta tidak ditemukan'
                ], 404);
            }

            $participant->update([
                'nik'   => $request->nik,
                'nama'      => $request->nama,
                'departemen' => $request->departemen,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Peserta berhasil diubah'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Peserta gagal diubah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $participant = Participant::find($id);

            if (!$participant) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta tidak ditemukan atau sudah dihapus'
                ], 404);
            }

            $participant->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Peserta berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta tidak dapat dihapus karena sudah memiliki riwayat penggunaan data.'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Peserta gagal dihapus: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import participant data from an uploaded Excel file.
     *
     * This method validates the uploaded file, checks whether the
     * required column headers exist, and then processes the file
     * using the ParticipantsImport class. It returns a JSON response
     * indicating success or detailed error information if validation
     * or import fails.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file.mimes' => 'Format file harus .xlsx, .xls, atau .csv'
        ]);

        try {
            // cek header
            $firstSheet =  Excel::toArray([], $request->file('file'));
            $headings = array_map('strtolower', $firstSheet[0][0]);
            $required = ['nik', 'nama', 'departemen'];

            foreach ($required as $col) {
                if (!in_array($col, $headings)) {
                    return response()->json([
                        'status' => 'error',
                        'error'   => 'InvalidTemplate',
                        'message' => "Format file tidak sesuai. Mohon gunakan template import yang sudah disediakan.",
                    ], 422);
                }
            }

            Excel::import(new ParticipantsImport, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'message' => 'Import data peserta berhasil!'
            ]);
        } catch (\Exception $e) {
            // parse JSON error 
            $decoded = json_decode($e->getMessage(), true);

            if ($decoded) {
                return response()->json([
                    'status'  => 'error',
                    'error'   => $decoded['error'] ?? 'ImportError',
                    'message' => $decoded['message'] ?? 'Terjadi kesalahan saat import',
                    'data'    => $decoded['data'] ?? null
                ], 422);
            }

            // fallback
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete multiple participants based on the provided array of IDs
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->ids;

            if (!is_array($ids) || count($ids) === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada data untuk dihapus'
                ], 400);
            }

            $found = Participant::whereIn('id', $ids)->count();

            if ($found === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data yang dipilih tidak ditemukan.'
                ], 404);
            }

            Participant::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Peserta terpilih berhasil dihapus'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sebagian atau seluruh peserta tidak dapat dihapus karena sudah memiliki data penggunaan.'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus peserta: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all participants from the database.
     */
    public function destroyAll()
    {
        try {
            Participant::query()->delete();
            DB::statement("ALTER TABLE participants AUTO_INCREMENT = 1");

            return response()->json([
                'status' => 'success',
                'message' => 'Semua peserta berhasil dihapus'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak dapat menghapus semua peserta karena beberapa peserta sudah memiliki data penggunaan.'
                ], 409);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus semua peserta: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    // TESTING ONLY
    public function downloadExcel(Request $request)
    {
        $nikList = preg_split('/\r\n|\r|\n/', trim($request->nik_list));
        $nikList = array_filter(array_unique($nikList));

        $finalData = collect();

        foreach ($nikList as $nik) {

            // 1. Cari NIK normal
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select(
                    'NIK as nik',
                    'EMPNM as nama',
                    'DEPTID as departemen',
                    'CARDNODEVICE as card'
                )
                ->where('NIK', $nik)
                ->first();

            // 2. Jika tidak ada dan nik TIDAK punya leading zero → coba tambah 0
            if (!$data && !str_starts_with($nik, '0')) {
                $data = DB::connection('192.168.178.44-admin')
                    ->table('MSIDCARD')
                    ->select(
                        'NIK as nik',
                        'EMPNM as nama',
                        'DEPTID as departemen',
                        'CARDNODEVICE as card'
                    )
                    ->where('NIK', '0' . $nik)
                    ->first();
            }

            // 3. Jika ada setelah fallback → gunakan data MSIDCARD
            if ($data) {
                $finalData->push([
                    'nik'        => $data->nik,
                    'nama'       => $data->nama,
                    'departemen' => $data->departemen,
                    'card'       => $data->card ?? null,
                ]);
            } else {
                // 4. Jika tetap tidak ditemukan → output kosong
                $finalData->push([
                    'nik'        => $nik,
                    'nama'       => '(tidak ditemukan)',
                    'departemen' => '(tidak ditemukan)',
                    'card'       => null,
                ]);
            }
        }

        return Excel::download(new KaryawanExport($finalData), 'data_karyawan.xlsx');
    }

    public function export()
    {
        try {
            $fileName = 'peserta_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new ParticipantExport, $fileName);
        } catch (\Exception $e) {
            Log::error('[ParticipantController export]', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal export data: ' . $e->getMessage()
            ], 500);
        }
    }
}
