<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/lbe', [App\Http\Controllers\Auth\LoginController::class, 'loginByEmail'])->name('login.lbe');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/bridging-oph', [App\Http\Controllers\OphLogController::class, 'index']);
Route::post('/bridging-oph', [App\Http\Controllers\OphLogController::class, 'store']);