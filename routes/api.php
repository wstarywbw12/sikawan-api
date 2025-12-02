<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\LoginController as LoginControllerV1;
use App\Http\Controllers\Api\V2\Auth\LoginController as LoginControllerV2;
Route::prefix('v1')->group(function () {
    Route::post('/login', [LoginControllerV1::class, 'login']);
});


Route::prefix('v2')->group(function () {
    Route::post('/login', [LoginControllerV2::class, 'login']);
});