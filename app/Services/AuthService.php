<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Normalisasi nomor telepon Indonesia
     */
    private function normalizePhone($phone)
    {
        // Hapus semua karakter non digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika mulai dengan "62" ubah ke "0"
        if (substr($phone, 0, 2) === '62') {
            $phone = '0' . substr($phone, 2);
        }

        return $phone;
    }

    /**
     * Attempt login
     */
    public function attemptLogin($input, $password)
    {
        $normalized   = $this->normalizePhone($input);
        $format62     = '62' . substr($normalized, 1);
        $formatPlus62 = '+62' . substr($normalized, 1);

        $user = User::with('pegawai.unit.atasanPegawai')
            ->where(function ($q) use ($input, $normalized, $format62, $formatPlus62) {

                // 1. login via email
                $q->where('email', $input)

                // 2. login via username (biasa)
                  ->orWhere('username', $input)

                // 3. login via nomor whatsapp (dalam berbagai format)
                  ->orWhere('username', $normalized)
                  ->orWhere('username', $format62)
                  ->orWhere('username', $formatPlus62)

                // 4. login via NIP (relasi pegawai)
                  ->orWhereHas('pegawai', function ($p) use ($input) {
                      $p->where('nip', $input);
                  })

                // 5. login via NIK (relasi pegawai)
                  ->orWhereHas('pegawai', function ($p) use ($input) {
                      $p->where('nik', $input);
                  });
            })
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
