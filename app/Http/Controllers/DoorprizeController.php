<?php

namespace App\Http\Controllers;

use App\Exports\DoorprizeWinnerExport;
use App\Models\Attendance;
use App\Models\DoorprizeWinner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DoorprizeController extends Controller
{
    public function index()
    {
        return view('app.doorprize.index');
    }

    /**
     * Ambil daftar peserta yang layak doorprize (is_doorprize = 1)
     * dan belum pernah menang, beserta daftar pemenang saat ini.
     */
    public function getData(Request $request)
    {
        try {
            $allEligible = Attendance::select('id', 'nik', 'nama_pegawai', 'departemen', 'jam_masuk')
                ->where('is_doorprize', 1)
                ->orderBy('nama_pegawai', 'asc')
                ->get();

            $winners = DoorprizeWinner::orderByDesc('waktu_menang')->get();

            $winnerNormalizedNiks = $winners->pluck('nik')
                ->map(fn($nik) => ltrim(trim((string)$nik), '0'))
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $skippedNiks = collect($request->input('skipped_niks', []))
                ->map(fn($nik) => ltrim(trim((string)$nik), '0'))
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $excludedSet = array_flip(array_merge($winnerNormalizedNiks, $skippedNiks));

            $remaining = $allEligible->filter(function ($item) use ($excludedSet) {
                $norm = ltrim(trim((string)$item->nik), '0');
                return !isset($excludedSet[$norm]);
            })->values();

            return response()->json([
                'status' => 'success',
                'eligible' => $remaining,
                'winners' => $winners->map(function ($w) {
                    return [
                        'id' => $w->id,
                        'nik' => $w->nik,
                        'nama_pegawai' => $w->nama_pegawai,
                        'departemen' => $w->departemen ?? '-',
                        'hadiah' => $w->hadiah ?: '-',
                        'waktu_menang_formatted' => $w->waktu_menang
                            ? $w->waktu_menang->format('d M Y H:i:s')
                            : '-',
                    ];
                }),
                'stats' => [
                    'total_eligible' => $allEligible->count(),
                    'total_winners' => $winners->count(),
                    'remaining' => $remaining->count(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('[DoorprizeController getData]', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat data undian: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Acak 1 pemenang secara adil di server dari peserta yang masih layak & belum menang.
     */
    public function draw(Request $request)
    {
        try {
            $allEligible = Attendance::select('id', 'nik', 'nama_pegawai', 'departemen')
                ->where('is_doorprize', 1)
                ->get();

            $winners = DoorprizeWinner::pluck('nik')
                ->map(fn($nik) => ltrim(trim((string)$nik), '0'))
                ->toArray();

            $skipped = collect($request->input('skipped_niks', []))
                ->map(fn($nik) => ltrim(trim((string)$nik), '0'))
                ->toArray();

            $excludedSet = array_flip(array_merge($winners, $skipped));

            $pool = $allEligible->filter(function ($item) use ($excludedSet) {
                $norm = ltrim(trim((string)$item->nik), '0');
                return !isset($excludedSet[$norm]);
            })->values();

            if ($pool->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada peserta layak doorprize yang tersisa untuk diundi.',
                ], 404);
            }

            $winnerIndex = random_int(0, $pool->count() - 1);
            $winner = $pool[$winnerIndex];

            return response()->json([
                'status' => 'success',
                'winner' => [
                    'attendance_id' => $winner->id,
                    'nik' => $winner->nik,
                    'nama_pegawai' => $winner->nama_pegawai,
                    'departemen' => $winner->departemen ?? '-',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('[DoorprizeController draw]', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengundi pemenang: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan pemenang yang sah ke database.
     */
    public function storeWinner(Request $request)
    {
        try {
            $request->validate([
                'attendance_id' => 'nullable|integer',
                'nik' => 'required|string',
                'nama_pegawai' => 'required|string',
                'departemen' => 'nullable|string',
                'hadiah' => 'nullable|string|max:255',
            ]);

            $nikVariants = $this->normalizeNumericCandidates($request->nik);
            $alreadyWon = DoorprizeWinner::whereIn('nik', $nikVariants)->exists();

            if ($alreadyWon) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Peserta dengan NIK ini sudah tercatat sebagai pemenang sebelumnya.',
                ], 409);
            }

            $winner = DoorprizeWinner::create([
                'attendance_id' => $request->attendance_id,
                'nik' => $request->nik,
                'nama_pegawai' => $request->nama_pegawai,
                'departemen' => $request->departemen ?: '-',
                'hadiah' => $request->hadiah ? trim($request->hadiah) : null,
                'waktu_menang' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Pemenang berhasil disimpan!',
                'winner' => $winner,
            ]);
        } catch (\Exception $e) {
            Log::error('[DoorprizeController storeWinner]', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan pemenang: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus satu pemenang dari daftar (misal salah input / dibatalkan).
     */
    public function destroyWinner($id)
    {
        try {
            $winner = DoorprizeWinner::findOrFail($id);
            $winner->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pemenang berhasil dihapus. Peserta kini dapat diundi kembali.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus pemenang: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset seluruh data pemenang doorprize.
     */
    public function resetWinners()
    {
        try {
            DoorprizeWinner::truncate();

            return response()->json([
                'status' => 'success',
                'message' => 'Seluruh daftar pemenang doorprize berhasil direset.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mereset pemenang: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export daftar pemenang ke Excel.
     */
    public function export()
    {
        try {
            $fileName = 'pemenang_doorprize_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new DoorprizeWinnerExport, $fileName);
        } catch (\Exception $e) {
            Log::error('[DoorprizeController export]', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal export pemenang: ' . $e->getMessage());
        }
    }
}
