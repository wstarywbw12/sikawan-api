<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Jadwal;
use Carbon\Carbon;

class SikawanController extends Controller
{
    public function index()
    {
        // Base query pegawai aktif
        $base = Pegawai::where('kondisi', 'Aktif');

        // ======================
        // SHIFT HARI INI
        // ======================
        $today = Carbon::today();

        // Pagi (termasuk "Pagi Siang")
        $total_shift_pagi = Jadwal::whereDate('tanggal', $today)
            ->whereHas('shift', function ($q) {
                $q->where('shift', 'like', '%pagi%');
            })
            ->count();

        // Siang (tidak termasuk "Pagi Siang")
        $total_shift_siang = Jadwal::whereDate('tanggal', $today)
            ->whereHas('shift', function ($q) {
                $q->where('shift', 'like', '%siang%')
                  ->where('shift', 'not like', '%pagi%');
            })
            ->count();

        // Malam
        $total_shift_malam = Jadwal::whereDate('tanggal', $today)
            ->whereHas('shift', function ($q) {
                $q->where('shift', 'like', '%malam%');
            })
            ->count();

        // ======================
        // DATA STATISTIK
        // ======================
        $data = [
            // Pegawai
            'total_pegawai' => (clone $base)->count(),
            'total_pns' => (clone $base)->where('statusx', 'PNS')->count(),
            'total_p3k' => (clone $base)->where('statusx', 'P3K')->count(),
            'total_p3k_pw' => (clone $base)->where('statusx', 'P3K Paruh Waktu')->count(),
            'total_cpns' => (clone $base)->where('statusx', 'CPNS')->count(),
            'total_kontrak' => (clone $base)->where('statusx', 'KONTRAK')->count(),
            'total_tetap' => (clone $base)->where('statusx', 'TETAP')->count(),
            'total_orientasi' => (clone $base)->where('statusx', 'Orientasi')->count(),

            // Jabatan
            'total_dokter_umum' => (clone $base)->whereHas('jabatan', function ($q) {
                $q->where('jabatan', 'like', '%dokter umum%');
            })->count(),

            'total_dokter_spesialis' => (clone $base)->whereHas('jabatan', function ($q) {
                $q->where('jabatan', 'like', '%spesialis%');
            })->count(),

            'total_perawat' => (clone $base)->whereHas('jabatan', function ($q) {
                $q->where('jabatan', 'like', '%perawat%');
            })->count(),

            'total_bidan' => (clone $base)->whereHas('jabatan', function ($q) {
                $q->where('jabatan', 'like', '%bidan%');
            })->count(),

            'total_medis' => (clone $base)->whereHas('jabatan', function ($q) {
                $q->where(function ($query) {
                    $query->where('jabatan', 'like', '%dokter%')
                        ->orWhere('jabatan', 'like', '%perawat%')
                        ->orWhere('jabatan', 'like', '%bidan%');
                });
            })->count(),

            'total_non_medis' => (clone $base)->whereHas('jabatan', function ($q) {
                $q->where(function ($query) {
                    $query->where('jabatan', 'not like', '%dokter%')
                        ->where('jabatan', 'not like', '%perawat%')
                        ->where('jabatan', 'not like', '%bidan%');
                });
            })->count(),

            // Shift Hari Ini
            'total_shift_pagi' => $total_shift_pagi,
            'total_shift_siang' => $total_shift_siang,
            'total_shift_malam' => $total_shift_malam,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Data statistik pegawai & shift hari ini berhasil diambil',
            'data' => $data,
        ], 200);
    }
}