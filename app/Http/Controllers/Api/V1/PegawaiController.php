<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PegawaiController extends Controller
{
    /**
     * Display a listing of pegawai with their relations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'nama' => 'nullable|string|max:255'
            ]);

            // Query dasar dengan relasi
            $query = Pegawai::with([
                'unit:id,unit',
                'pangkat:id,gol,golongan',
                'jabatan:id,jabatan'
            ]);

            // Filter berdasarkan nama jika parameter nama tersedia
            if ($request->has('nama') && !empty($request->nama)) {
                $nama = $request->nama;
                $query->where(function($q) use ($nama) {
                    $q->where('nama', 'LIKE', "%{$nama}%")
                      ->orWhere('GelarDepan', 'LIKE', "%{$nama}%")
                      ->orWhere('GelarBelakang', 'LIKE', "%{$nama}%");
                });
            }

            // Eksekusi query
            $pegawais = $query->get();

            // Format response
            $formattedPegawais = $pegawais->map(function($pegawai) {
                return [
                    'id' => $pegawai->id,
                    'nama_lengkap' => trim(
                        ($pegawai->GelarDepan ? $pegawai->GelarDepan . ' ' : '') .
                        $pegawai->nama .
                        ($pegawai->GelarBelakang ? ', ' . $pegawai->GelarBelakang : '')
                    ),
                    'nama' => $pegawai->nama,
                    'gelar_depan' => $pegawai->GelarDepan,
                    'gelar_belakang' => $pegawai->GelarBelakang,
                    'unit' => $pegawai->unit ? [
                        'id' => $pegawai->unit->id,
                        'unit' => $pegawai->unit->unit
                    ] : null,
                    'pangkat' => $pegawai->pangkat ? [
                        'id' => $pegawai->pangkat->id,
                        'gol' => $pegawai->pangkat->gol,
                        'golongan' => $pegawai->pangkat->golongan
                    ] : null,
                    'jabatan' => $pegawai->jabatan ? [
                        'id' => $pegawai->jabatan->id,
                        'jabatan' => $pegawai->jabatan->jabatan
                    ] : null,
                    'created_at' => $pegawai->created_at,
                    'updated_at' => $pegawai->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data pegawai berhasil diambil',
                'data' => $formattedPegawais,
                'total' => $formattedPegawais->count()
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pegawai',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified pegawai with relations.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $pegawai = Pegawai::with([
                'unit:id,unit',
                'pangkat:id,gol,golongan',
                'jabatan:id,jabatan'
            ])->find($id);

            if (!$pegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pegawai tidak ditemukan'
                ], Response::HTTP_NOT_FOUND);
            }

            $formattedPegawai = [
                'id' => $pegawai->id,
                'nama_lengkap' => trim(
                    ($pegawai->GelarDepan ? $pegawai->GelarDepan . ' ' : '') .
                    $pegawai->nama .
                    ($pegawai->GelarBelakang ? ', ' . $pegawai->GelarBelakang : '')
                ),
                'nama' => $pegawai->nama,
                'gelar_depan' => $pegawai->GelarDepan,
                'gelar_belakang' => $pegawai->GelarBelakang,
                'unit' => $pegawai->unit ? [
                    'id' => $pegawai->unit->id,
                    'unit' => $pegawai->unit->unit
                ] : null,
                'pangkat' => $pegawai->pangkat ? [
                    'id' => $pegawai->pangkat->id,
                    'gol' => $pegawai->pangkat->gol,
                    'golongan' => $pegawai->pangkat->golongan
                ] : null,
                'jabatan' => $pegawai->jabatan ? [
                    'id' => $pegawai->jabatan->id,
                    'jabatan' => $pegawai->jabatan->jabatan
                ] : null,
                'created_at' => $pegawai->created_at,
                'updated_at' => $pegawai->updated_at
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data pegawai berhasil diambil',
                'data' => $formattedPegawai
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pegawai',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}