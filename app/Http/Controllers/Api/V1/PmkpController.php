<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Nasional;

class PmkpController extends Controller
{
    public function index()
    {

        $data = Nasional::get();

        return response()->json([
            'success' => true,
            'message' => 'Data PMKP berhasil diambil',
            'data' => $data,
        ], 200);
    }
}
