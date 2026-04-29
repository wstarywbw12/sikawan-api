<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nasional extends Model
{
    protected $connection = 'pmkp'; 
    // protected $table = 'vdatamutunasional';
    protected $table = 'view_indikatormutunasional';

    public $timestamps = false; 
    protected $guarded = []; 
}
