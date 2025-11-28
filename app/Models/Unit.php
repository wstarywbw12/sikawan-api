<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'unit',
        'atasan'
    ];

    public function atasanPegawai()
    {
        return $this->belongsTo(Pegawai::class, 'atasan');
    }

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}
