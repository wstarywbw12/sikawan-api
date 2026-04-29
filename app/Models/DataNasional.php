<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataNasional extends Model
{
     protected $connection = 'pmkp'; 
    protected $table = 'vdatamutunasional';

    public $timestamps = false; 
    protected $guarded = []; 
}
