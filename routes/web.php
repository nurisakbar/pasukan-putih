<?php

use Illuminate\Support\Facades\Route;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;

Auth::routes();

Route::get('/lbe', function () {
    return view('auth.email.form');
});

Route::middleware('guest')->group(function () {
    Route::post('/lbe', [App\Http\Controllers\Auth\LoginController::class, 'loginByEmail'])->name('login.lbe');
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
});


Route::get('/bridging-oph', [App\Http\Controllers\OphLogController::class, 'index']);
Route::post('/bridging-oph', [App\Http\Controllers\OphLogController::class, 'store']);

// Semua rute yang butuh autentikasi
Route::middleware(['auth', 'dashboard.only'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/pasiens', [App\Http\Controllers\PasienController::class, 'index'])->name('pasiens.index');
    Route::post('/pasiens/data', [App\Http\Controllers\PasienController::class, 'getData'])->name('pasiens.data');
    Route::get('/pasien/search', [App\Http\Controllers\PasienController::class, 'autofill'])->name('pasiens.search');
    Route::get('/pasien/nik', [App\Http\Controllers\PasienController::class, 'getPasienByNik'])->name('pasiens.nik');
    Route::get('/pasien/carik/nik', [App\Http\Controllers\PasienController::class, 'getDataPasienCarik'])->name('pasiens.carik');
    Route::get('/users/operators', [App\Http\Controllers\UserController::class, 'getOperators'])->name('users.operators');
    Route::resource('/pasiens', App\Http\Controllers\PasienController::class);
    Route::get('/pasiens/{id}/asuhan-keluarga', [App\Http\Controllers\PasienController::class, 'createAsuhanKeluarga'])->name('pasiens.asuhanKeluarga');
    // Route::get('/pasiens/search-village', [App\Http\Controllers\PasienController::class, 'searchVillage'])->name('pasiens.searchVillage');
    Route::get('/search-village', [App\Http\Controllers\PasienController::class, 'searchVillage'])->name('search.village');
    Route::post('/syncronisasi-carik', [App\Http\Controllers\PasienController::class, 'startSyncCarik'])->name('syncronisasi.carik');
    Route::get('/sync-progress/{syncId}', [App\Http\Controllers\PasienController::class, 'checkSyncProgress'])->name('syncronisasi.progress');
    
    // Export routes
    Route::post('/pasiens/export', [App\Http\Controllers\PasienController::class, 'exportPasien'])->name('pasiens.export');
    Route::get('/export-progress/{exportId}', [App\Http\Controllers\PasienController::class, 'checkExportProgress'])->name('export.progress');
    Route::get('/pasiens/download/{filename}', [App\Http\Controllers\PasienController::class, 'downloadFile'])->name('pasiens.download');

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
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'editProfile'])->name('users.editProfile');
    Route::put('/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('users.updateProfile');

    Route::post('/import-users', [App\Http\Controllers\UserController::class, 'importUsers'])->name('import.users');

    // Login by email routes
    Route::get('/login-by-email', [App\Http\Controllers\Auth\LoginController::class, 'showLoginByEmailForm'])->name('login.email.form');
    Route::post('/login-by-email', [App\Http\Controllers\Auth\LoginController::class, 'loginByEmail'])->name('login.email');

    Route::resource('/kunjungans', App\Http\Controllers\KunjunganController::class);
    Route::get('/kunjungan/rencana-kunjungan-awal', [App\Http\Controllers\KunjunganController::class, 'rencanaKunjunganAwal'])->name('kunjungan.rencanaKunjunganAwal');
    Route::get('/kunjungan/{id}/skrining-adl', [App\Http\Controllers\KunjunganController::class, 'skriningAdl'])->name('kunjungan.skriningAdl');
    Route::get('/kunjungan/{id}/edit-from-pasien', [App\Http\Controllers\KunjunganController::class, 'editKunjunganFromPasiens'])->name('kunjungan.editKunjunganFromPasiens');
    Route::put('/kunjungan/{id}/update-from-pasien', [App\Http\Controllers\KunjunganController::class, 'updateKunjunganFromPasiens'])->name('kunjungan.updateKunjunganFromPasiens');
    Route::post('/kunjungan/{id}/skrining-adl', [App\Http\Controllers\KunjunganController::class, 'storeSkriningAdl'])->name('kunjungan.storeSkriningAdl');
    Route::put('/kunjungan/{id}/skrining-adl', [App\Http\Controllers\KunjunganController::class, 'updateSkriningAdl'])->name('kunjungan.updateSkriningAdl');

    Route::resource('ttv', App\Http\Controllers\TtvController::class);
    Route::get('kunjungan/{kunjungan}/ttv/create', [\App\Http\Controllers\TtvController::class, 'create'])->name('kunjungan.ttv.create');
    Route::post('calculate-bmi', [\App\Http\Controllers\TtvController::class, 'calculateBmi'])->name('calculate.bmi');

    //import
    Route::get('/pasiens/download-template', [\App\Http\Controllers\PasienController::class, 'downloadTemplate'])->name('pasiens.downloadTemplate');
    Route::post('pasien/import', [\App\Http\Controllers\PasienController::class, 'importPasien'])->name('pasiens.import');


    //export
    Route::get('kunjungan/export', [\App\Http\Controllers\KunjunganController::class, 'exportKunjungan'])->name('kunjungan.export');
    Route::get('sasaran-bulanan/export', [\App\Http\Controllers\ExportController::class, 'exportSasaranBulanan'])->name('export.sasaran-bulanan');
    Route::get('jumlah-sasaran/export', [\App\Http\Controllers\ExportController::class, 'exportJumlahSasaran'])->name('export.jumlah-sasaran');
    Route::get('kunjugan-awal/export', [\App\Http\Controllers\ExportController::class, 'exportKunjuganAwal'])->name('export.kunjungan-awal');
    Route::get('summary-kunjungan-awal/export', [\App\Http\Controllers\ExportController::class, 'exportSummaryKunjunganAwal'])->name('export.summary-kunjungan-awal');
    Route::get('kunjungan-lanjutan/export', [\App\Http\Controllers\ExportController::class, 'exportKunjunganLanjutan'])->name('export.kunjungan-lanjutan');
    Route::get('summary-kunjungan-lanjutan/export', [\App\Http\Controllers\ExportController::class, 'exportSummaryKunjunganLanjutan'])->name('export.summary-kunjungan-lanjutan');
    Route::get('henti-layanan/export', [\App\Http\Controllers\ExportController::class, 'exportHentiLayanan'])->name('export.henti-layanan');
    Route::get('summary-henti-layanan/export', [\App\Http\Controllers\ExportController::class, 'exportSummaryHentiLayanan'])->name('export.summary-henti-layanan');
    Route::get('kohort-hs/export', [\App\Http\Controllers\ExportController::class, 'exportKohortHs'])->name('export.kohort-hs');

    //visiting
    Route::resource('visitings', \App\Http\Controllers\VisitingController::class);
    Route::get('/visitings/{id}/edit-form-pasien', [\App\Http\Controllers\VisitingController::class, 'editKunjunganFromPasiens'])->name('visitings.editKunjunganFromPasiens');
    Route::get('/visitings/{id}/dashboard', [\App\Http\Controllers\VisitingController::class, 'dashboard'])->name('visitings.dashboard');
    Route::post('/visitings/{id}/ttv', [\App\Http\Controllers\VisitingController::class, 'storeTtv'])->name('visitings.storeTtv');
    Route::post('/visitings/{id}/health-form', [\App\Http\Controllers\VisitingController::class, 'storeHealthForm'])->name('visitings.storeHealthForm');
    Route::get('/visitings/{id}/skrining-adl', [\App\Http\Controllers\VisitingController::class, 'skriningAdl'])->name('visitings.skriningAdl');
    Route::post('/visitings/{id}/skrining-adl', [\App\Http\Controllers\VisitingController::class, 'storeSkriningAdl'])->name('visitings.storeSkriningAdl');
    Route::put('/visitings/{id}/skrining-adl', [\App\Http\Controllers\VisitingController::class, 'updateSkriningAdl'])->name('visitings.updateSkriningAdl');
    Route::post('/visitings/{id}/skrining-adl-ajax', [\App\Http\Controllers\VisitingController::class, 'storeSkriningAdlAjax'])->name('visitings.storeSkriningAdlAjax');

    //health form
    Route::resource('health-form', \App\Http\Controllers\HealthFormController::class);
    Route::get('/health-form/create/{visiting}', [\App\Http\Controllers\HealthFormController::class, 'create'])->name('health-form.create');

    // API: get scheduled follow-up dates for a pasien from health forms
    Route::get('/pasiens/{pasien}/scheduled-dates', [\App\Http\Controllers\VisitingController::class, 'getScheduledDates'])
        ->name('pasiens.scheduledDates');

    //pustu
    Route::resource('pustu', \App\Http\Controllers\PustuController::class);

    
});

Route::get('/test', [App\Http\Controllers\TestController::class, 'getDetailKunjungan']);

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

Route::get('/export/test', [App\Http\Controllers\ExportController::class, 'test']);

Route::get('/test-carik', function () {
    $response = Http::withOptions([
        'proxy' => 'http://10.15.3.20:80', 
        'verify' => true, 
    ])
    ->withHeaders([
        'carik-api-key' => 'WydtKanwCc0dhbaclOLy2uUBl7WVICQA',
        'Cookie' => 'TS01f239ec=01b53461a6e068c46f652602c6a09733f49a58e0f31899b767a13a3358d6cac47368fe86ad7fb78a2034b98e8cb19c758b6dc2c1bf',
    ])
    ->get('https://carik.jakarta.go.id/api/v1/dilan/activity-daily-living');

    // Cek status response
    if ($response->successful()) {
        return $response->json();
    } else {
        return response()->json([
            'status' => $response->status(),
            'error' => $response->body(),
        ]);
    }
});