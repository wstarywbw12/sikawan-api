<?php
namespace App\Http\Controllers\Api\V2\Auth;

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
                'metaData' => [
                    'code'    => '401',
                    'message' => 'Username atau password salah'
                ],
                'response' => null
            ], 401);
        }

        // Generate Sanctum Token
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'metaData' => [
                'code'    => '200',
                'message' => 'OK'
            ],
            'response' => [
                'data' => [
                    'jabatan' => [
                        'id'   => (string) $user->pegawai->jabatan->id ?? null,
                        'nama' => $user->pegawai->jabatan->jabatan ?? null
                    ],
                    'karyawan' => [
                        'id'       => (string) $user->pegawai_id,
                        'nama'     => trim(
                            ($user->pegawai->GelarDepan ? $user->pegawai->GelarDepan . ' ' : '') .
                            $user->pegawai->nama .
                            ($user->pegawai->GelarBelakang ? '' . $user->pegawai->GelarBelakang : '')
                        ),
                        'nip'      => $user->pegawai->nip,
                        'password' => $user->password // hashed password
                    ],
                    'ruang' => [
                        'id'   => (string) $user->pegawai->unit_id,
                        'nama' => $user->pegawai->unit->unit
                    ]
                ]
            ],
            'token' => $token
        ]);
    }
}
