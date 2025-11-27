<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MsidController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select(
                    'NIK as nik',
                    'EMPNM as nama',
                    'CARDNODEVICE as card_id',
                    'DEPTID as departemen',
                    'EMPCARDID as id'
                )
                ->where('STATUS', 'X')
                ->orderBy('EMPNM', 'ASC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('app.master.msid.index');
    }

    public function edit($id)
    {
        try {
            $data = DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->select(
                    'NIK as nik',
                    'EMPNM as nama',
                    'CARDNODEVICE as card_id',
                    'DEPTID as departemen',
                    'EMPCARDID as id'
                )
                ->where('EMPCARDID', $id)
                ->where('STATUS', 'X')
                ->first();

            if (!$data) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nik' => 'required',
                'nama' => 'required',
                'card_id' => 'required',
                'departemen' => 'required',
            ], [
                'nik.required' => 'NIK wajib diisi.',
                'nama.required' => 'Nama wajib diisi.',
                'card_id.required' => 'Card ID wajib diisi.',
                'departemen.required' => 'Departemen wajib diisi.',
            ]);

            DB::connection('192.168.178.44-admin')
                ->table('MSIDCARD')
                ->where('EMPCARDID', $id)
                ->update([
                    'NIK' => $request->nik,
                    'EMPNM' => $request->nama,
                    'CARDNODEVICE' => $request->card_id,
                    'DEPTID' => $request->departemen,
                ]);

            return response()->json(['message' => 'Data berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
