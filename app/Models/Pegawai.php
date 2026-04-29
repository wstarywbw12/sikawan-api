<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pegawai extends Model
{
    protected $fillable = [
        'GelarDepan',
        'nama',
        'GelarBelakang',
        'unit_id',
        'nip',
        'nik',
        'WhatsApp',
        'kondisi'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

      public function pangkat(): BelongsTo
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_id', 'id');
    }
}
