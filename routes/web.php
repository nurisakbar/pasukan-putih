<?php

use Illuminate\Support\Facades\Route;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/bridging-oph', [App\Http\Controllers\OphLogController::class, 'index']);
Route::post('/bridging-oph', [App\Http\Controllers\OphLogController::class, 'store']);

// Semua rute yang butuh autentikasi
Route::middleware('auth')->group(function () {
    Route::resource('/pasiens', App\Http\Controllers\PasienController::class);
    Route::get('/pasien/nik', [App\Http\Controllers\PasienController::class, 'getPasienByNik'])->name('pasiens.nik');
    Route::get('/pasiens/search', [App\Http\Controllers\PasienController::class, 'autofill'])->name('pasiens.search');
    Route::get('/pasiens/{id}/asuhan-keluarga', [App\Http\Controllers\PasienController::class, 'createAsuhanKeluarga'])->name('pasiens.asuhanKeluarga');

    // Save Form Asuhan Keluarga
    Route::post('/Kondisi-rumah', [App\Http\Controllers\AsuhanKeluargaController::class, 'saveKondisiRumah'])->name('form.saveKondisiRumah');
    Route::put('/Kondisi-rumah/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updateKondisiRumah'])->name('form.updateKondisiRumah');
    Route::post('/Phbs-rumah-tangga', [App\Http\Controllers\AsuhanKeluargaController::class, 'savePhbsRumahTangga'])->name('form.savePhbsRumahTangga');
    Route::put('/Phbs-rumah-tangga/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updatePhbsRumahTangga'])->name('form.updatePhbsRumahTangga');
    Route::post('/pemeliaharaan-kesehatan-keluarga', [App\Http\Controllers\AsuhanKeluargaController::class, 'savePemeliharaanKesehatanKeluarga'])->name('form.savePemeliharaanKesehatanKeluarga');
    Route::put('/pemeliaharaan-kesehatan-keluarga/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updatePemeliharaanKesehatanKeluarga'])->name('form.updatePemeliharaanKesehatanKeluarga');
    Route::post('/pengkajian-individu', [App\Http\Controllers\AsuhanKeluargaController::class, 'savePengkajianIndividu'])->name('form.savePengkajianIndividu');
    Route::put('/pengkajian-individu/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updatePengkajianIndividu'])->name('form.updatePengkajianIndividu');
    Route::post('/sirkulasi-cairan', [App\Http\Controllers\AsuhanKeluargaController::class, 'saveSirkulasiCairan'])->name('form.saveSirkulasiCairan');
    Route::put('/sirkulasi-cairan/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updateSirkulasiCairan'])->name('form.updateSirkulasiCairan');
    Route::post('/perkemihan', [App\Http\Controllers\AsuhanKeluargaController::class, 'savePerkemihan'])->name('form.savePerkemihan');
    Route::put('/perkemihan/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updatePerkemihan'])->name('form.updatePerkemihan');
    Route::post('/pencernaan', [App\Http\Controllers\AsuhanKeluargaController::class, 'savePencernaan'])->name('form.savePencernaan');
    Route::put('/pencernaan/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updatePencernaan'])->name('form.updatePencernaan');
    Route::post('/muskuloskeletal', [App\Http\Controllers\AsuhanKeluargaController::class, 'saveMuskuloskeletal'])->name('form.saveMuskuloskeletal');
    Route::put('/muskuloskeletal/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updateMuskuloskeletal'])->name('form.updateMuskuloskeletal');
    Route::post('/neurosensori', [App\Http\Controllers\AsuhanKeluargaController::class, 'saveNeurosensori'])->name('form.saveNeurosensori');
    Route::put('/neurosensori/{id}', [App\Http\Controllers\AsuhanKeluargaController::class, 'updateNeurosensori'])->name('form.updateNeurosensori');




    Route::resource('/users', App\Http\Controllers\UserController::class);

    // Login by email routes
    Route::get('/login-by-email', [App\Http\Controllers\Auth\LoginController::class, 'showLoginByEmailForm'])->name('login.email.form');
    Route::post('/login-by-email', [App\Http\Controllers\Auth\LoginController::class, 'loginByEmail'])->name('login.email');

    Route::resource('/kunjungans', App\Http\Controllers\KunjunganController::class);
    Route::get('/kunjungan/rencana-kunjungan-awal', [App\Http\Controllers\KunjunganController::class, 'rencanaKunjunganAwal'])->name('kunjungan.rencanaKunjunganAwal');
    Route::get('/kunjungan/{id}/skrining-adl', [App\Http\Controllers\KunjunganController::class, 'skriningAdl'])->name('kunjungan.skriningAdl');
    Route::post('/kunjungan/{id}/skrining-adl', [App\Http\Controllers\KunjunganController::class, 'storeSkriningAdl'])->name('kunjungan.storeSkriningAdl');
    Route::put('/kunjungan/{id}/skrining-adl', [App\Http\Controllers\KunjunganController::class, 'updateSkriningAdl'])->name('kunjungan.updateSkriningAdl');
    
    Route::get('kunjungan/export', [\App\Http\Controllers\KunjunganController::class, 'exportKunjungan'])->name('kunjungan.export');
    Route::get('sasaran-bulanan/export', [\App\Http\Controllers\ExportController::class, 'exportSasaranBulanan'])->name('export.sasaran-bulanan');
    Route::get('jumlah-sasaran/export', [\App\Http\Controllers\ExportController::class, 'exportJumlahSasaran'])->name('export.jumlah-sasaran');

});

Route::get('/get-regencies/{province_id}', function ($province_id) {
    return Regency::where('province_id', $province_id)->get();
});

Route::get('/get-districts/{regency_id}', function ($regency_id) {
    return District::where('regency_id', $regency_id)->get();
});

Route::get('/get-villages/{district_id}', function ($district_id) {
    return Village::where('district_id', $district_id)->get();
});

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::get('/test-view', function () {
    
    return view('kunjungans.form-pencatatan');
});

