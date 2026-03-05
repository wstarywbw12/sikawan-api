<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pangkat extends Model
{
      protected $fillable = [
        'gol',
        'golongan'
    ];

      public function pegawais(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'pangkat_id', 'id');
    }
}
