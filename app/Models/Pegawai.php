<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = [
        'GelarDepan',
        'nama',
        'GelarBelakang',
        'unit_id',
        'nip',
        'nik',
        'WhatsApp'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
