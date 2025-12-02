<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}
