<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PmkpController extends Controller
{
    public function index()
    {
        $tahun = 2026;

        $data = DB::connection('pmkp')
            ->table('view_indikatormutunasional as i')
            ->leftJoin('vdatamutunasional as d', 'd.kodemutu', '=', 'i.idnasional')
            ->select(
                'i.namaindikator as indikator',
                'i.target',

                DB::raw("
                    COALESCE(
                        ROUND(
                            SUM(CASE WHEN MONTH(d.tglmutu)=1 THEN d.numverifikasi ELSE 0 END)
                            /
                            NULLIF(SUM(CASE WHEN MONTH(d.tglmutu)=1 THEN d.denumverifikasi ELSE 0 END),0)
                            * 100, 2
                        ),0
                    ) as jan
                "),

                DB::raw("
                    COALESCE(
                        ROUND(
                            SUM(CASE WHEN MONTH(d.tglmutu)=2 THEN d.numverifikasi ELSE 0 END)
                            /
                            NULLIF(SUM(CASE WHEN MONTH(d.tglmutu)=2 THEN d.denumverifikasi ELSE 0 END),0)
                            * 100, 2
                        ),0
                    ) as feb
                "),

                DB::raw("
                    COALESCE(
                        ROUND(
                            SUM(CASE WHEN MONTH(d.tglmutu)=3 THEN d.numverifikasi ELSE 0 END)
                            /
                            NULLIF(SUM(CASE WHEN MONTH(d.tglmutu)=3 THEN d.denumverifikasi ELSE 0 END),0)
                            * 100, 2
                        ),0
                    ) as mar
                "),

                DB::raw("
                    COALESCE(
                        ROUND(
                            SUM(CASE WHEN MONTH(d.tglmutu) IN (1,2,3) THEN d.numverifikasi ELSE 0 END)
                            /
                            NULLIF(SUM(CASE WHEN MONTH(d.tglmutu) IN (1,2,3) THEN d.denumverifikasi ELSE 0 END),0)
                            * 100, 2
                        ),0
                    ) as triwulan
                ")
            )
            ->where(function ($q) use ($tahun) {
                $q->whereYear('d.tglmutu', $tahun)
                  ->orWhereNull('d.tglmutu');
            })
            ->groupBy('i.idnasional', 'i.namaindikator', 'i.target')
            ->orderBy('i.idnasional')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data PMKP berhasil diambil',
            'data' => $data
        ]);
    }
}