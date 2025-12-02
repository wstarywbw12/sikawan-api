<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private function normalizeAllFormats($phone)
    {
        // Hapus non-digit
        $digits = preg_replace('/\D/', '', $phone);

        // Jika nomor diawali 62 → ubah ke 0xxxxxxxxxx
        if (substr($digits, 0, 2) === '62') {
            $local = '0'.substr($digits, 2);
        }
        // Jika sudah 0xxxxxxxxxx
        elseif (substr($digits, 0, 1) === '0') {
            $local = $digits;
        }
        // Jika tidak sesuai (misal tidak mulai 0 dan tidak 62 → paksa jadi lokal)
        else {
            $local = '0'.$digits;
        }

        return [
            'local' => $local,
            '62' => '62'.substr($local, 1),
            'plus' => '+62'.substr($local, 1),
        ];
    }

    public function attemptLogin($input, $password)
    {
        $norm = $this->normalizeAllFormats($input);

        // Cek apakah input HANYA angka dan simbol telepon
        $isPhoneInput = preg_match('/^[0-9+()\-\s]+$/', $input);

        $user = User::with('pegawai.unit.atasanPegawai.jabatan')
            ->where(function ($q) use ($input, $norm, $isPhoneInput) {

                // 1. EMAIL
                $q->where('email', $input)

                // 2. USERNAME BIASA (admin, superadmin, timsim)
                    ->orWhere('username', $input);

                // 3. JIKA INPUT DIDUGA NOMOR TELP, cek semua format nomor
                if ($isPhoneInput) {

                    $local = preg_replace('/\D/', '', $norm['local']);
                    $f62 = preg_replace('/\D/', '', $norm['62']);
                    $fPlus = preg_replace('/\D/', '', $norm['plus']);

                    // username yang disimpan sebagai nomor
                    $q->orWhereRaw("
                    REGEXP_REPLACE(username, '[^0-9]', '') IN (?, ?, ?)
                ", [$local, $f62, $fPlus]);

                    // pegawai->whatsapp
                    $q->orWhereHas('pegawai', function ($p) use ($local, $f62, $fPlus) {
                        $p->whereRaw("
                        REGEXP_REPLACE(whatsapp, '[^0-9]', '') IN (?, ?, ?)
                    ", [$local, $f62, $fPlus]);
                    });
                }

                // 4. NIP / NIK (tetap tanpa normalisasi)
                $q->orWhereHas('pegawai', function ($p) use ($input) {
                    $p->where('nip', $input)
                        ->orWhere('nik', $input);
                });
            })
            ->first();

        // PASSWORD CHECK
        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
