<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Services\AuthService;

class LoginController extends Controller
{
    public function login(LoginRequest $request, AuthService $authService)
    {
        $user = $authService->attemptLogin(
            $request->username,
            $request->password
        );

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 401);
        }

        // generate token Sanctum
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'token'  => $token,
            'user'   => [
                'username' => $user->username,
                'email'    => $user->email,
                'pegawai'  => [
                    'nama_lengkap' => trim($user->pegawai->GelarDepan.' '.$user->pegawai->nama.' '.$user->pegawai->GelarBelakang),
                    'nip'         => $user->pegawai->nip,
                    'nik'         => $user->pegawai->nik,
                    'whatsapp'    => $user->pegawai->whatsapp,
                    'unit' => [
                        'nama_unit' => $user->pegawai->unit->unit,
                        'atasan'    => $user->pegawai->unit->atasanPegawai
                            ? trim($user->pegawai->unit->atasanPegawai->GelarDepan.' '.$user->pegawai->unit->atasanPegawai->nama.' '.$user->pegawai->unit->atasanPegawai->GelarBelakang)
                            : null
                    ]
                ]
            ]
        ]);
    }
}
