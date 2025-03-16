@extends('layouts.app')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Asuhan Keperawatan Keluarga</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Input Data</h5>
                        </div>
                        <div class="card-body">

                            <div class="accordion" id="formAccordion">
                                <!-- Section 1: Rumah dan Sanitasi -->
                                <div class="accordion-item mb-3 border">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button bg-primary bg-opacity-10 text-primary"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            @if (isset($kondisiRumah))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fas fa-home me-2"></i> Rumah dan Sanitasi Lingkungan
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                        data-bs-parent="#formAccordion">
                                        <div class="accordion-body bg-light">
                                            <h5 class="mb-4 pb-2 border-bottom text-secondary">Kondisi Rumah</h5>
                                            <form
                                                action="{{ isset($kondisiRumah) ? route('form.updateKondisiRumah', $kondisiRumah->id) : route('form.saveKondisiRumah') }}"
                                                method="post" id="kondisiRumahForm"
                                                data-id="{{ $kondisiRumah->id ?? '' }}">
                                                @csrf
                                                @if (isset($kondisiRumah))
                                                    @method('PUT')
                                                @endif

                                                <input type="hidden" name="pasien_id" value="{{ $pasienId }}">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="row g-3">
                                                    <div class="col-lg-6">
                                                        <div class="mb-3">
                                                            <label for="ventilasi" class="form-label">Ventilasi</label>
                                                            <select class="form-select form-select-sm" id="ventilasi"
                                                                name="ventilasi" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Baik"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->ventilasi == 'Baik' ? 'selected' : '' }}>
                                                                    Baik</option>
                                                                <option value="Cukup"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->ventilasi == 'Cukup' ? 'selected' : '' }}>
                                                                    Cukup</option>
                                                                <option value="Kurang"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->ventilasi == 'Kurang' ? 'selected' : '' }}>
                                                                    Kurang</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="pencahayaan" class="form-label">Pencahayaan
                                                                Rumah</label>
                                                            <select class="form-select form-select-sm" id="pencahayaan"
                                                                name="pencahayaan" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Baik"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->pencahayaan == 'Baik' ? 'selected' : '' }}>
                                                                    Baik</option>
                                                                <option value="Cukup"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->pencahayaan == 'Cukup' ? 'selected' : '' }}>
                                                                    Cukup</option>
                                                                <option value="Kurang"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->pencahayaan == 'Kurang' ? 'selected' : '' }}>
                                                                    Kurang</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="saluran_limbah" class="form-label">Saluran Buang
                                                                Limbah</label>
                                                            <select class="form-select form-select-sm" id="saluran_limbah"
                                                                name="saluran_limbah" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Baik"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->saluran_limbah == 'Baik' ? 'selected' : '' }}>
                                                                    Baik</option>
                                                                <option value="Cukup"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->saluran_limbah == 'Cukup' ? 'selected' : '' }}>
                                                                    Cukup</option>
                                                                <option value="Kurang"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->saluran_limbah == 'Kurang' ? 'selected' : '' }}>
                                                                    Kurang</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6">
                                                        <div class="card bg-light mb-3">
                                                            <div class="card-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Sumber Air Bersih</label>
                                                                    <div class="d-flex">
                                                                        <div class="form-check me-3">
                                                                            <input class="form-check-input" type="radio"
                                                                                id="sumber_air_ya" name="sumber_air"
                                                                                value="Ya"
                                                                                {{ isset($kondisiRumah) && $kondisiRumah->sumber_air == 'Ya' ? 'checked' : '' }}
                                                                                required>
                                                                            <label class="form-check-label"
                                                                                for="sumber_air_ya">Ya</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                id="sumber_air_tidak" name="sumber_air"
                                                                                value="Tidak"
                                                                                {{ isset($kondisiRumah) && $kondisiRumah->sumber_air == 'Tidak' ? 'checked' : '' }}
                                                                                required>
                                                                            <label class="form-check-label"
                                                                                for="sumber_air_tidak">Tidak</label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Jamban Memenuhi Syarat</label>
                                                                    <div class="d-flex">
                                                                        <div class="form-check me-3">
                                                                            <input class="form-check-input" type="radio"
                                                                                id="jamban_ya" name="jamban"
                                                                                value="Ya"
                                                                                {{ isset($kondisiRumah) && $kondisiRumah->jamban == 'Ya' ? 'checked' : '' }}
                                                                                required>
                                                                            <label class="form-check-label"
                                                                                for="jamban_ya">Ya</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                id="jamban_tidak" name="jamban"
                                                                                value="Tidak"
                                                                                {{ isset($kondisiRumah) && $kondisiRumah->jamban == 'Tidak' ? 'checked' : '' }}
                                                                                required>
                                                                            <label class="form-check-label"
                                                                                for="jamban_tidak">Tidak</label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">Ada Tempat Sampah</label>
                                                                    <div class="d-flex">
                                                                        <div class="form-check me-3">
                                                                            <input class="form-check-input" type="radio"
                                                                                id="tempat_sampah_ya" name="tempat_sampah"
                                                                                value="Ya"
                                                                                {{ isset($kondisiRumah) && $kondisiRumah->tempat_sampah == 'Ya' ? 'checked' : '' }}
                                                                                required>
                                                                            <label class="form-check-label"
                                                                                for="tempat_sampah_ya">Ya</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio"
                                                                                id="tempat_sampah_tidak"
                                                                                name="tempat_sampah" value="Tidak"
                                                                                {{ isset($kondisiRumah) && $kondisiRumah->tempat_sampah == 'Tidak' ? 'checked' : '' }}
                                                                                required>
                                                                            <label class="form-check-label"
                                                                                for="tempat_sampah_tidak">Tidak</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tombol Simpan -->
                                                <div class="text-end mt-3">
                                                    <button type="submit" class="btn btn-primary"><i
                                                            class="fas fa-save me-2"></i>{{ isset($kondisiRumah) ? 'Update' : 'Simpan' }}</button>
                                                </div>
                                            </form>

                                            <div id="notif" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- /* PHBS di Rumah Tangga */ --}}
                            <div class="accordion" id="formAccrordion2">
                                <div class="accordion-item mb-3 border">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button  bg-primary bg-opacity-10 text-primary"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                            aria-expanded="true" aria-controls="collapseTwo">
                                            @if (isset($PhbsRumahTangga))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fa-solid fa-leaf me-2"></i> PHBS di Rumah Tangga
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse"
                                        aria-labelledby="headingOne" data-bs-parent="#formAccrordion2">
                                        <div class="accordion-body bg-light">
                                            <form
                                                action="{{ isset($PhbsRumahTangga) ? route('form.updatePhbsRumahTangga', $PhbsRumahTangga->id) : route('form.savePhbsRumahTangga') }}"
                                                method="post" id="formPhbsRumahTangga"
                                                data-id="{{ $PhbsRumahTangga->id ?? '' }}">
                                                @csrf
                                                @if (isset($PhbsRumahTangga))
                                                    @method('PUT')
                                                @endif
                                                <input type="hidden" name="pasien_id" value="{{ $pasienId }}">
                                                <div class="row">
                                                    @php
                                                        $questions = [
                                                            'ibu_nifas' => 'Jika ada ibu nifas, persalinan ditolong oleh tenaga kesehatan',
                                                            'ada_bayi' => 'Jika ada bayi, memberi ASI Eksklusif',
                                                            'ada_balita' => 'Jika ada balita, menimbang balita tiap bulan',
                                                            'air_bersih' => 'Menggunakan air bersih untuk kebersihan diri',
                                                            'mencuci_tangan' => 'Mencuci tangan dengan air bersih & sabun',
                                                            'buang_sampah' => 'Melakukan pembuangan sampah pada tempatnya',
                                                            'menjaga_lingkungan_rumah' => 'Menjaga lingkungan rumah tampak bersih',
                                                            'konsumsi_lauk' => 'Mengkonsumsi lauk dan pauk tiap hari',
                                                            'gunakan_jamban' => 'Menggunakan jamban sehat',
                                                            'jentik_dirumah' => 'Memberantas jentik di rumah sekali seminggu',
                                                            'makan_buah_sayur' => 'Makan buah dan sayur setiap hari',
                                                            'aktivitas_fisik' => 'Melakukan aktivitas fisik setiap hari',
                                                            'merokok_dalam_rumah' => 'Tidak merokok di dalam rumah',
                                                        ];
                                                    @endphp

                                                    @foreach ($questions as $key => $label)
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <label class="form-label">{{ $label }}</label>
                                                            <div class="mb-3">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" id="{{ $key }}_ya" name="{{ $key }}" value="Ya" 
                                                                        {{ isset($PhbsRumahTangga) && $PhbsRumahTangga->$key == 'Ya' ? 'checked' : '' }} required>
                                                                    <label class="form-check-label" for="{{ $key }}_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" id="{{ $key }}_tidak" name="{{ $key }}" value="Tidak" 
                                                                        {{ isset($PhbsRumahTangga) && $PhbsRumahTangga->$key == 'Tidak' ? 'checked' : '' }} required>
                                                                    <label class="form-check-label" for="{{ $key }}_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary"><i
                                                                class="fas fa-save me-2"></i>{{ isset($PhbsRumahTangga) ? 'Update' : 'Simpan' }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Kemampuan Keluarga Melakukan Tugas Pemeliharaan Kesehatan Anggota Keluarga --}}
                            <div class="accordion" id="formAccordion3">
                                <div class="accordion-item mb-3 border">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button  bg-primary bg-opacity-10 text-primary"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                            aria-expanded="false" aria-controls="collapseThree">
                                            @if (isset($pemeliharaanKesehatanKeluarga))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fa fa-stethoscope me-2 "></i>Kemampuan Keluarga Melakukan Tugas
                                            Pemeliharaan Kesehatan
                                            Anggota Keluarga
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse"
                                        aria-labelledby="headingThree" data-bs-parent="#formAccordion3">
                                        <div class="accordion-body bg-light">

                                            <div class="row">
                                                <form
                                                    action="{{ isset($pemeliharaanKesehatanKeluarga) ? route('form.updatePemeliharaanKesehatanKeluarga', $pemeliharaanKesehatanKeluarga->id) : route('form.savePemeliharaanKesehatanKeluarga') }}"
                                                    method="post" id="pemeliharaanKesehatanKeluargaForm"
                                                    data-id="{{ $pemeliharaanKesehatanKeluarga->id ?? '' }}">
                                                    @csrf
                                                    @if (isset($pemeliharaanKesehatanKeluarga))
                                                        @method('PUT')
                                                    @endif

                                                    <input type="hidden" name="pasien_id" value="{{ $pasienId }}">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Adakah perhatian keluarga kepada
                                                            anggotanya yang menderita sakit?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="perhatian_keluarga_ya"
                                                                        name="perhatian_keluarga" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->perhatian_keluarga == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="perhatian_keluarga_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="perhatian_keluarga_tidak"
                                                                        name="perhatian_keluarga" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->perhatian_keluarga == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="perhatian_keluarga_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga mengetahui masalah
                                                            kesehatan yang dialami anggota dalam keluarganya?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="mengetahui_masalah_kesehatan_ya"
                                                                        name="mengetahui_masalah_kesehatan" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->mengetahui_masalah_kesehatan == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="mengetahui_masalah_kesehatan_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="mengetahui_masalah_kesehatan_tidak"
                                                                        name="mengetahui_masalah_kesehatan" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->mengetahui_masalah_kesehatan == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="mengetahui_masalah_kesehatan_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga mengetahui penyebab
                                                            masalah kesehatan yang dialami anggota dalam
                                                            keluarganya?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="penyebab_masalah_kesehatan_ya"
                                                                        name="penyebab_masalah_kesehatan" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->penyebab_masalah_kesehatan == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="penyebab_masalah_kesehatan_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="penyebab_masalah_kesehatan_tidak"
                                                                        name="penyebab_masalah_kesehatan" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->penyebab_masalah_kesehatan == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="penyebab_masalah_kesehatan_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga mengetahui akibat
                                                            masalah
                                                            kesehatan yang dialami anggota dalam keluarganya bila tidak
                                                            diobati/dirawat?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="akibat_masalah_kesehatan_ya"
                                                                        name="akibat_masalah_kesehatan" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->akibat_masalah_kesehatan == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="akibat_masalah_kesehatan_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="akibat_masalah_kesehatan_tidak"
                                                                        name="akibat_masalah_kesehatan" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->akibat_masalah_kesehatan == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="akibat_masalah_kesehatan_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Keyakinan keluarga tentang masalah
                                                            kesehatan yang dialami anggota keluarganya</label>
                                                        <div class="mb-3">
                                                            <select class="form-select form-select-sm"
                                                                id="keyakinan_keluarga" name="keyakinan_keluarga"
                                                                required>
                                                                <option
                                                                    value="Tidak perlu ditangani karena akan sembuh sendiri biasanya"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->ventilasi == 'Tidak perlu ditangani karena akan sembuh sendiri biasanya' ? 'selected' : '' }}>
                                                                    Tidak perlu ditangani karena akan sembuh sendiri
                                                                    biasanya</option>
                                                                <option value="Perlu berobat ke faskes"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->ventilasi == 'Perlu berobat ke faskes' ? 'selected' : '' }}>
                                                                    Perlu berobat ke faskes</option>
                                                                <option value="Tidak terpikir"
                                                                    {{ isset($kondisiRumah) && $kondisiRumah->ventilasi == 'Tidak terpikir' ? 'selected' : '' }}>
                                                                    Tidak terpikir</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga melakukan upaya
                                                            peningkatan kesehatan yang dialami anggota keluarganya secara
                                                            aktif?
                                                            (ada deskripsi jika pilih ya)</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="upaya_peningkatan_kesehatan_ya"
                                                                        name="upaya_peningkatan_kesehatan" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->upaya_peningkatan_kesehatan == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="upaya_peningkatan_kesehatan_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="upaya_peningkatan_kesehatan_tidak"
                                                                        name="upaya_peningkatan_kesehatan" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->upaya_peningkatan_kesehatan == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="upaya_peningkatan_kesehatan_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                            <div id="input-deskripsi" class="mt-2"
                                                                style="display: none;">
                                                                <input type="text" class="form-control"
                                                                    name="upaya_peningkatan_kesehatan_deskripsi"
                                                                    placeholder="Jelaskan"
                                                                    value="{{ isset($pemeliharaanKesehatanKeluarga) ? $pemeliharaanKesehatanKeluarga->upaya_peningkatan_kesehatan_deskripsi : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga mengetahui kebutuhan
                                                            pengobatan masalah kesehatan yang dialami anggota
                                                            keluarganya?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="kebutuhan_pengobatan_ya"
                                                                        name="kebutuhan_pengobatan" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->kebutuhan_pengobatan == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="kebutuhan_pengobatan_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="kebutuhan_pengobatan_tidak"
                                                                        name="kebutuhan_pengobatan" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->kebutuhan_pengobatan == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="kebutuhan_pengobatan_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga dapat melakukan cara
                                                            merawat anggota keluarga dengan masalah kesehatan yang
                                                            dialaminya?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="merawat_anggota_keluarga_ya"
                                                                        name="merawat_anggota_keluarga" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->merawat_anggota_keluarga == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="merawat_anggota_keluarga_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="merawat_anggota_keluarga_tidak"
                                                                        name="merawat_anggota_keluarga" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->merawat_anggota_keluarga == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="merawat_anggota_keluarga_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga dapat melakukan
                                                            pencegahan masalah kesehatan yang dialami anggota
                                                            keluarganya?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="melakukan_pencegahan_masalah_ya"
                                                                        name="melakukan_pencegahan_masalah" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->melakukan_pencegahan_masalah == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="melakukan_pencegahan_masalah_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="melakukan_pencegahan_masalah_tidak"
                                                                        name="melakukan_pencegahan_masalah" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->melakukan_pencegahan_masalah == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="melakukan_pencegahan_masalah_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga mampu memelihara atau
                                                            memodifikasi lingkungan yang mendukung kesehatan anggota
                                                            keluarga yang
                                                            mengalami masalah kesehatan?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="mendukung_kesehatan_ya"
                                                                        name="mendukung_kesehatan" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->mendukung_kesehatan == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="mendukung_kesehatan_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="mendukung_kesehatan_tidak"
                                                                        name="mendukung_kesehatan" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->mendukung_kesehatan == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="mendukung_kesehatan_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label ">Apakah keluarga mampu menggali dan
                                                            memanfaatkan sumber di masyarakat untuk mengatasi masalah
                                                            kesehatan
                                                            anggota keluarganya?</label>
                                                        <div class="mb-3">
                                                            <div class="d-flex">
                                                                <div class="form-check me-3">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="memanfaatkan_sumber_ya"
                                                                        name="memanfaatkan_sumber" value="Ya"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->memanfaatkan_sumber == 'Ya' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="memanfaatkan_sumber_ya">Ya</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="memanfaatkan_sumber_tidak"
                                                                        name="memanfaatkan_sumber" value="Tidak"
                                                                        {{ isset($pemeliharaanKesehatanKeluarga) && $pemeliharaanKesehatanKeluarga->memanfaatkan_sumber == 'Tidak' ? 'checked' : '' }}
                                                                        required>
                                                                    <label class="form-check-label"
                                                                        for="memanfaatkan_sumber_tidak">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary"><i
                                                                class="fas fa-save me-2"></i>{{ isset($pemeliharaanKesehatanKeluarga) ? 'Update' : 'Simpan' }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Data Pengkajian Individu yang Sakit --}}
                            <div class="accordion shadow-sm" id="individualAssessmentAccordion">
                                <div class="accordion-item border-0 rounded overflow-hidden mb-3">
                                    <h2 class="accordion-header" id="headingIndividualAssessment">
                                        <button class="accordion-button  bg-primary bg-opacity-10 text-primary"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseIndividualAssessment" aria-expanded="true"
                                            aria-controls="collapseIndividualAssessment">
                                            @if (isset($pengkajianIndividu))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fas fa-clipboard-check me-2"></i>Data Pengkajian Individu yang
                                            Sakit
                                        </button>
                                    </h2>
                                    <div id="collapseIndividualAssessment" class="accordion-collapse collapse"
                                        aria-labelledby="headingIndividualAssessment">
                                        <div class="accordion-body bg-light">
                                            <h5 class=" mb-4 pb-2 border-bottom text-secondary">Kesadaran Umum</h5>

                                            <form
                                                action="{{ isset($pengkajianIndividu) ? route('form.updatePengkajianIndividu', $pengkajianIndividu->id) : route('form.savePengkajianIndividu') }}"
                                                method="post" id="pengkajianIndividuForm"
                                                data-id="{{ $pengkajianIndividu->id ?? '' }}">
                                                @csrf
                                                @if (isset($pengkajianIndividu))
                                                    @method('PUT')
                                                @endif
                                                <input type="hidden" name="pasien_id" value="{{ $pasienId }}">
                                                <div class="row g-4">
                                                    <!-- First row -->
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="text" class="form-control" id="kesadaran"
                                                                name="kesadaran" placeholder="Kesadaran"
                                                                value="{{ old('kesadaran', $pengkajianIndividu->kesadaran ?? '') }}"
                                                                required>
                                                            <label for="kesadaran">Kesadaran <span
                                                                    class="text-danger">*</span></label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="number" class="form-control" id="gcs"
                                                                name="gcs" placeholder="GCS"
                                                                value="{{ old('gcs', $pengkajianIndividu->gcs ?? '') }}">
                                                            <label for="gcs">GCS</label>
                                                        </div>
                                                    </div>

                                                    <!-- Blood Pressure Row -->
                                                    <div class="col-lg-12 col-md-12">
                                                        <label class="form-label mb-2">Tekanan darah</label>
                                                        <div class="row g-2">
                                                            <div class="col-md-3 col-12">
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-white">Sistole</span>
                                                                    <input type="number" class="form-control"
                                                                        name="sistole"
                                                                        value="{{ old('sistole', $pengkajianIndividu->sistole ?? '') }}">
                                                                    <span class="input-group-text bg-light">mm/Hg</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 col-12">
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-white">Diastole</span>
                                                                    <input type="number" class="form-control"
                                                                        name="diastole"
                                                                        value="{{ old('diastole', $pengkajianIndividu->diastole ?? '') }}">
                                                                    <span class="input-group-text bg-light">mm/Hg</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Vital Signs Row -->
                                                    <div class="col-md-4 col-sm-6">
                                                        <label class="form-label mb-2">Pernapasan</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="pernapasan"
                                                                value="{{ old('pernapasan', $pengkajianIndividu->pernapasan ?? '') }}">
                                                            <span class="input-group-text bg-light">kali/Menit</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 col-sm-6">
                                                        <label class="form-label mb-2">Suhu</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="suhu"
                                                                step="0.1"
                                                                value="{{ old('suhu', $pengkajianIndividu->suhu ?? '') }}">
                                                            <span class="input-group-text bg-light">Celcius</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 col-sm-6">
                                                        <label class="form-label mb-2">Nadi</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="nadi"
                                                                id="nadi"
                                                                value="{{ old('nadi', $pengkajianIndividu->nadi ?? '') }}"
                                                                oninput="checkNadi()">
                                                            <span class="input-group-text bg-light">kali/Menit</span>
                                                        </div>
                                                    </div>

                                                    <!-- Additional Fields -->
                                                    <div class="col-lg-3 col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="number" class="form-control" id="takikardi"
                                                                name="takikardi" readonly
                                                                value="{{ old('takikardi', $pengkajianIndividu->takikardi ?? '') }}">
                                                            <label for="takikardi">Takikardi</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-6">
                                                        <div class="form-floating mb-3">
                                                            <input type="number" class="form-control" id="bradikardia"
                                                                name="bradikardia" readonly
                                                                value="{{ old('bradikardia', $pengkajianIndividu->bradikardia ?? '') }}">
                                                            <label for="bradikardia">Bradikardia</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-6">
                                                        <div class="card border-0 h-100">
                                                            <div class="card-body px-3 py-2 bg-light rounded">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        id="tubuhHangat" name="tubuhHangat"
                                                                        value="Ya"
                                                                        {{ old('tubuhHangat', $pengkajianIndividu->tubuhHangat ?? '') == 'Ya' ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="tubuhHangat">Tubuh terasa hangat</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-md-6">
                                                        <div class="card border-0 h-100">
                                                            <div class="card-body px-3 py-2 bg-light rounded">
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        id="menggigil" name="menggigil" value="Ya"
                                                                        {{ old('menggigil', $pengkajianIndividu->menggigil ?? '') == 'Ya' ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="menggigil">Menggigil</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i
                                                                class="fas fa-save me-2"></i>{{ isset($pengkajianIndividu) ? 'Update' : 'Simpan' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sirkulasi Cairan --}}
                            <div class="accordion shadow-sm" id="individualAssessmentAccordion">
                                <div class="accordion-item border-0 rounded overflow-hidden mb-3">
                                    <h2 class="accordion-header" id="headingIndividualAssessment">
                                        <button class="accordion-button  bg-primary bg-opacity-10 text-primary "
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseIndividualAssessmentTwo" aria-expanded="true"
                                            aria-controls="collapseIndividualAssessmentTwo">
                                            @if (isset($sirkulasiCairan))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fas fa-tint me-2"></i>Sirkulasi/Cairan
                                        </button>
                                    </h2>
                                    <div id="collapseIndividualAssessmentTwo" class="accordion-collapse collapse"
                                        aria-labelledby="headingIndividualAssessment">
                                        <div class="accordion-body bg-light">
                                            <form
                                                action="{{ isset($sirkulasiCairan) ? route('form.updateKondisiRumah', $sirkulasiCairan->id) : route('form.saveKondisiRumah') }}"
                                                method="post" id="sirkulasiCairanForm"
                                                data-id="{{ $sirkulasiCairan->id ?? '' }}">
                                                @csrf
                                                @if (isset($sirkulasiCairan))
                                                    @method('PUT')
                                                @endif
                                                <input type="hidden" name="pasien_id" value="{{ $pasienId }}">
                                                <div class="row g-4">
                                                    <!-- First row -->
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Edema</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="edema_ya" name="edema" value="Ya" required
                                                                    {{ isset($sirkulasiCairan->edema) && $sirkulasiCairan->edema == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="edema_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="edema_tidak" name="edema" value="Tidak"
                                                                    required
                                                                    {{ isset($sirkulasiCairan->edema) && $sirkulasiCairan->edema == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="edema_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Bunyi jantung</label>
                                                        <div class="mb-3">
                                                            <select class="form-select form-select-sm" id="bunyi_jantung"
                                                                name="bunyi_jantung" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Tidak"
                                                                    {{ isset($sirkulasiCairan->bunyi_jantung) && $sirkulasiCairan->bunyi_jantung == 'Tidak' ? 'selected' : '' }}>
                                                                    Tidak perlu ditangani karena
                                                                    akan sembuh sendiri biasanya</option>
                                                                <option value="Ya"
                                                                    {{ isset($sirkulasiCairan->bunyi_jantung) && $sirkulasiCairan->bunyi_jantung == 'Ya' ? 'selected' : '' }}>
                                                                    Perlu berobat ke faskes</option>
                                                                <option value="2"
                                                                    {{ isset($sirkulasiCairan->bunyi_jantung) && $sirkulasiCairan->bunyi_jantung == '2' ? 'selected' : '' }}>
                                                                    Tidak terpikir</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Asites</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="asites_ya" name="asites" value="Ya" required
                                                                    {{ isset($sirkulasiCairan->asites) && $sirkulasiCairan->asites == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="asites_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="asites_tidak" name="asites" value="Tidak"
                                                                    required
                                                                    {{ isset($sirkulasiCairan->asites) && $sirkulasiCairan->asites == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="asites_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Akral dingin</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="akral_dingin_ya" name="akral_dingin"
                                                                    value="Ya" required
                                                                    {{ isset($sirkulasiCairan->akral_dingin) && $sirkulasiCairan->akral_dingin == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="akral_dingin_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="akral_dingin_tidak" name="akral_dingin"
                                                                    value="Tidak" required
                                                                    {{ isset($sirkulasiCairan->akral_dingin) && $sirkulasiCairan->akral_dingin == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="akral_dingin_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Tanda perdarahan
                                                            (Purpura/Hematom/Petekie/Epistaksis)</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tanda_perdarahan_ya" name="tanda_perdarahan"
                                                                    value="Ya" required
                                                                    {{ isset($sirkulasiCairan->tanda_perdarahan) && $sirkulasiCairan->tanda_perdarahan == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tanda_perdarahan_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tanda_perdarahan_tidak" name="tanda_perdarahan"
                                                                    value="Tidak" required
                                                                    {{ isset($sirkulasiCairan->tanda_perdarahan) && $sirkulasiCairan->tanda_perdarahan == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tanda_perdarahan_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Tanda anemia (pucat/konjungtiva
                                                            pucat/lidah pucat/bibir pucat/akral pucat)</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tanda_anemia_ya" name="tanda_anemia"
                                                                    value="Ya" required
                                                                    {{ isset($sirkulasiCairan->tanda_anemia) && $sirkulasiCairan->tanda_anemia == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tanda_anemia_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tanda_anemia_tidak" name="tanda_anemia"
                                                                    value="Tidak" required
                                                                    {{ isset($sirkulasiCairan->tanda_anemia) && $sirkulasiCairan->tanda_anemia == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tanda_anemia_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Tanda dehidrasi (mata cekung/turgor
                                                            kulit berkurang/bibir kering)</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tanda_dehidrasi_ya" name="tanda_dehidrasi"
                                                                    value="Ya" required
                                                                    {{ isset($sirkulasiCairan->tanda_dehidrasi) && $sirkulasiCairan->tanda_dehidrasi == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tanda_dehidrasi_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tanda_dehidrasi_tidak" name="tanda_dehidrasi"
                                                                    value="Tidak" required
                                                                    {{ isset($sirkulasiCairan->tanda_dehidrasi) && $sirkulasiCairan->tanda_dehidrasi == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tanda_dehidrasi_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Pusing</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="pusing_ya" name="pusing" value="Ya" required
                                                                    {{ isset($sirkulasiCairan->pusing) && $sirkulasiCairan->pusing == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="pusing_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="pusing_tidak" name="pusing" value="Tidak"
                                                                    required
                                                                    {{ isset($sirkulasiCairan->pusing) && $sirkulasiCairan->pusing == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="pusing_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Kesemutan</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="kesemutan_ya" name="kesemutan" value="Ya"
                                                                    required
                                                                    {{ isset($sirkulasiCairan->kesemutan) && $sirkulasiCairan->kesemutan == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="kesemutan_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="kesemutan_tidak" name="kesemutan" value="Tidak"
                                                                    required
                                                                    {{ isset($sirkulasiCairan->kesemutan) && $sirkulasiCairan->kesemutan == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="kesemutan_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Berkeringat</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="berkeringat_ya" name="berkeringat" value="Ya"
                                                                    required
                                                                    {{ isset($sirkulasiCairan->berkeringat) && $sirkulasiCairan->berkeringat == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="berkeringat_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="berkeringat_tidak" name="berkeringat"
                                                                    value="Tidak" required
                                                                    {{ isset($sirkulasiCairan->berkeringat) && $sirkulasiCairan->berkeringat == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="berkeringat_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Rasa haus</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="rasa_haus_ya" name="rasa_haus" value="Ya"
                                                                    required
                                                                    {{ isset($sirkulasiCairan->rasa_haus) && $sirkulasiCairan->rasa_haus == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="rasa_haus_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="rasa_haus_tidak" name="rasa_haus"
                                                                    value="Tidak" required
                                                                    {{ isset($sirkulasiCairan->rasa_haus) && $sirkulasiCairan->rasa_haus == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="rasa_haus_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Pengisian kapiler >2 detik</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="pengisian_kapiler_ya" name="pengisian_kapiler"
                                                                    value="Ya" required
                                                                    {{ isset($sirkulasiCairan->pengisian_kapiler) && $sirkulasiCairan->pengisian_kapiler == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="pengisian_kapiler_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="pengisian_kapiler_tidak"
                                                                    name="pengisian_kapiler" value="Tidak" required
                                                                    {{ isset($sirkulasiCairan->pengisian_kapiler) && $sirkulasiCairan->pengisian_kapiler == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="pengisian_kapiler_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary"><i
                                                                class="fas fa-save me-2"></i>{{ isset($sirkulasiCairan) ? 'Update' : 'Simpan' }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Perkemihan --}}
                            <div class="accordion shadow-sm" id="individualAssessmentAccordion">
                                <div class="accordion-item border-0 rounded overflow-hidden mb-3">
                                    <h2 class="accordion-header" id="headingIndividualAssessment">
                                        <button class="accordion-button  bg-primary bg-opacity-10 text-primary"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseIndividualAssessmentTree" aria-expanded="true"
                                            aria-controls="collapseIndividualAssessmentTree">
                                            @if (isset($perkemihan))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fas fa-toilet me-2"></i>Perkemihan
                                        </button>
                                    </h2>
                                    <div id="collapseIndividualAssessmentTree" class="accordion-collapse collapse"
                                        aria-labelledby="headingIndividualAssessment">
                                        <div class="accordion-body bg-light">
                                            <form
                                                action="{{ isset($perkemihan) ? route('form.updatePerkemihan', $perkemihan->id) : route('form.savePerkemihan') }}"
                                                method="post" id="perkemihanForm"
                                                data-id="{{ $perkemihan->id ?? '' }}">
                                                @csrf
                                                @if (isset($perkemihan))
                                                    @method('PUT')
                                                @endif
                                                <div class="row g-4">
                                                    <!-- First row -->
                                                    <input type="hidden" name="pasien_id"
                                                        value="{{ $pasienId }}">
                                                    <div class="col-md-6 col-sm-12">
                                                        <label class="form-label mb-2">Pola BAK</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="pola_bak"
                                                                value="{{ isset($perkemihan->pola_bak) ? $perkemihan->pola_bak : '' }}">
                                                            <span class="input-group-text bg-light">kali/Hari</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 col-sm-12">
                                                        <label class="form-label mb-2">Volume</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="volume"
                                                                step="0.1"
                                                                value="{{ isset($perkemihan->volume) ? $perkemihan->volume : '' }}">
                                                            <span class="input-group-text bg-light">ml/hari</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Hematuri</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="hematuri_ya" name="hematuri" value="Ya"
                                                                    {{ isset($perkemihan->hematuri) && $perkemihan->hematuri == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="hematuri_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="hematuri_tidak" name="hematuri" value="Tidak"
                                                                    {{ isset($perkemihan->hematuri) && $perkemihan->hematuri == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="hematuri_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Poliuria</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="poliuria_ya" name="poliuria" value="Ya"
                                                                    {{ isset($perkemihan->poliuria) && $perkemihan->poliuria == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="poliuria_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="poliuria_tidak" name="poliuria" value="Tidak"
                                                                    {{ isset($perkemihan->poliuria) && $perkemihan->poliuria == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="poliuria_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Oliguria</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="oliguria_ya" name="oliguria" value="Ya"
                                                                    {{ isset($perkemihan->oliguria) && $perkemihan->oliguria == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="oliguria_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="oliguria_tidak" name="oliguria" value="Tidak"
                                                                    {{ isset($perkemihan->oliguria) && $perkemihan->oliguria == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="oliguria_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Disuria</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="disuria_ya" name="disuria" value="Ya"
                                                                    {{ isset($perkemihan->disuria) && $perkemihan->disuria == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="disuria_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="disuria_tidak" name="disuria" value="Tidak"
                                                                    {{ isset($perkemihan->disuria) && $perkemihan->disuria == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="disuria_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Inkontinensia</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="inkontinensia_ya" name="inkontinensia"
                                                                    value="Ya"
                                                                    {{ isset($perkemihan->inkontinensia) && $perkemihan->inkontinensia == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="inkontinensia_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="inkontinensia_tidak" name="inkontinensia"
                                                                    value="Tidak"
                                                                    {{ isset($perkemihan->inkontinensia) && $perkemihan->inkontinensia == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="inkontinensia_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Retensi</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="retensi_ya" name="retensi" value="Ya"
                                                                    {{ isset($perkemihan->retensi) && $perkemihan->retensi == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="retensi_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="retensi_tidak" name="retensi" value="Tidak"
                                                                    {{ isset($perkemihan->retensi) && $perkemihan->retensi == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="retensi_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Nyeri saat BAK</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="nyeri_bak_ya" name="nyeri_bak" value="Ya"
                                                                    {{ isset($perkemihan->nyeri_bak) && $perkemihan->nyeri_bak == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="nyeri_bak_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="nyeri_bak_tidak" name="nyeri_bak"
                                                                    value="Tidak"
                                                                    {{ isset($perkemihan->nyeri_bak) && $perkemihan->nyeri_bak == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="nyeri_bak_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Kemampuan BAK</label>
                                                        <div class="mb-3">
                                                            <select class="form-select form-select-sm"
                                                                id="kemampuan_bak" name="kemampuan_bak" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Mandiri"
                                                                    {{ isset($perkemihan->kemampuan_bak) && $perkemihan->kemampuan_bak == 'Mandiri' ? 'selected' : '' }}>
                                                                    Mandiri</option>
                                                                <option value="Bantu sebagian"
                                                                    {{ isset($perkemihan->kemampuan_bak) && $perkemihan->kemampuan_bak == 'Bantu sebagian' ? 'selected' : '' }}>
                                                                    Bantu sebagian</option>
                                                                <option value="Tergantung"
                                                                    {{ isset($perkemihan->kemampuan_bak) && $perkemihan->kemampuan_bak == 'Tergantung' ? 'selected' : '' }}>
                                                                    Tergantung</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Alat bantu BAK</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alat_bantu_bak_ya" name="alat_bantu_bak"
                                                                    value="Ya"
                                                                    {{ isset($perkemihan->alat_bantu_bak) && $perkemihan->alat_bantu_bak == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="alat_bantu_bak_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alat_bantu_bak_tidak" name="alat_bantu_bak"
                                                                    value="Tidak"
                                                                    {{ isset($perkemihan->alat_bantu_bak) && $perkemihan->alat_bantu_bak == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="alat_bantu_bak_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Gunakan obat BAK</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="obat_bak_ya" name="obat_bak" value="Ya"
                                                                    {{ isset($perkemihan->obat_bak) && $perkemihan->obat_bak == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="obat_bak_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="obat_bak_tidak" name="obat_bak" value="Tidak"
                                                                    {{ isset($perkemihan->obat_bak) && $perkemihan->obat_bak == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="obat_bak_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Kemampuan BAB</label>
                                                        <div class="mb-3">
                                                            <select class="form-select form-select-sm"
                                                                id="kemampuan_bab" name="kemampuan_bab" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Mandiri"
                                                                    {{ isset($perkemihan->kemampuan_bab) && $perkemihan->kemampuan_bab == 'Mandiri' ? 'selected' : '' }}>
                                                                    Mandiri</option>
                                                                <option value="Bantu sebagian"
                                                                    {{ isset($perkemihan->kemampuan_bab) && $perkemihan->kemampuan_bab == 'Bantu sebagian' ? 'selected' : '' }}>
                                                                    Bantu sebagian</option>
                                                                <option value="Tergantung"
                                                                    {{ isset($perkemihan->kemampuan_bab) && $perkemihan->kemampuan_bab == 'Tergantung' ? 'selected' : '' }}>
                                                                    Tergantung</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Alat bantu BAB</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alat_bantu_bab_ya" name="alat_bantu_bab"
                                                                    value="Ya"
                                                                    {{ isset($perkemihan->alat_bantu_bab) && $perkemihan->alat_bantu_bab == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="alat_bantu_bab_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alat_bantu_bab_tidak" name="alat_bantu_bab"
                                                                    value="Tidak"
                                                                    {{ isset($perkemihan->alat_bantu_bab) && $perkemihan->alat_bantu_bab == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="alat_bantu_bab_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Gunakan obat BAB</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="obat_bab_ya" name="obat_bab" value="Ya"
                                                                    {{ isset($perkemihan->obat_bab) && $perkemihan->obat_bab == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="obat_bab_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="obat_bab_tidak" name="obat_bab" value="Tidak"
                                                                    {{ isset($perkemihan->obat_bab) && $perkemihan->obat_bab == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="obat_bab_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary"><i
                                                                class="fas fa-save me-2"></i>{{ isset($perkemihan) ? 'Update' : 'Simpan' }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Pencernaan --}}
                            <div class="accordion shadow-sm" id="individualAssessmentAccordion">
                                <div class="accordion-item border-0 rounded overflow-hidden mb-3">
                                    <h2 class="accordion-header" id="headingIndividualAssessment">
                                        <button class="accordion-button  bg-primary bg-opacity-10 text-primary"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseIndividualAssessmentFive" aria-expanded="true"
                                            aria-controls="collapseIndividualAssessmentFive">
                                            @if (isset($pencernaan))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fas fa-seedling me-2"></i>Pencernaan

                                        </button>
                                    </h2>
                                    <div id="collapseIndividualAssessmentFive" class="accordion-collapse collapse"
                                        aria-labelledby="headingIndividualAssessment">
                                        <div class="accordion-body bg-light">
                                            <form
                                                action="{{ isset($pencernaan) ? route('form.updatePencernaan', $pencernaan->id) : route('form.savePencernaan') }}"
                                                method="post" id="pencernaanForm"
                                                data-id="{{ $pencernaan->id ?? '' }}">
                                                @csrf
                                                @if (isset($pencernaan))
                                                    @method('PUT')
                                                @endif
                                                <div class="row g-4">
                                                    <!-- First row -->
                                                    <input type="hidden" name="pasien_id"
                                                        value="{{ $pasienId }}">
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Mual</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="mual_ya" name="mual" value="Ya"
                                                                    {{ isset($pencernaan->mual) && $pencernaan->mual == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="mual_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="mual_tidak" name="mual" value="Tidak"
                                                                    {{ isset($pencernaan->mual) && $pencernaan->mual == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="mual_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Muntah</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="muntah_ya" name="muntah" value="Ya"
                                                                    {{ isset($pencernaan->muntah) && $pencernaan->muntah == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="muntah_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="muntah_tidak" name="muntah" value="Tidak"
                                                                    {{ isset($pencernaan->muntah) && $pencernaan->muntah == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="muntah_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Kembung</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="kembung_ya" name="kembung" value="Ya"
                                                                    {{ isset($pencernaan->kembung) && $pencernaan->kembung == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="kembung_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="kembung_tidak" name="kembung" value="Tidak"
                                                                    {{ isset($pencernaan->kembung) && $pencernaan->kembung == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="kembung_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Nafsu makan berkurang</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="nafsu_makan_ya" name="nafsu_makan"
                                                                    value="Ya"
                                                                    {{ isset($pencernaan->nafsu_makan) && $pencernaan->nafsu_makan == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="nafsu_makan_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="nafsu_makan_tidak" name="nafsu_makan"
                                                                    value="Tidak"
                                                                    {{ isset($pencernaan->nafsu_makan) && $pencernaan->nafsu_makan == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="nafsu_makan_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Sulit menelan</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="sulit_menelan_ya" name="sulit_menelan"
                                                                    value="Ya"
                                                                    {{ isset($pencernaan->sulit_menelan) && $pencernaan->sulit_menelan == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="sulit_menelan_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="sulit_menelan_tidak" name="sulit_menelan"
                                                                    value="Tidak"
                                                                    {{ isset($pencernaan->sulit_menelan) && $pencernaan->sulit_menelan == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="sulit_menelan_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Disfagia</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="disfagia_ya" name="disfagia" value="Ya"
                                                                    {{ isset($pencernaan->disfagia) && $pencernaan->disfagia == 'Ya' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="disfagia_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="disfagia_tidak" name="disfagia" value="Tidak"
                                                                    {{ isset($pencernaan->disfagia) && $pencernaan->disfagia == 'Tidak' ? 'checked' : '' }}
                                                                    required>
                                                                <label class="form-check-label"
                                                                    for="disfagia_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Bau napas</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="bau_napas_ya" name="bau_napas" value="Ya"
                                                                    required
                                                                    {{ isset($pencernaan->bau_napas) && $pencernaan->bau_napas == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="bau_napas_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="bau_napas_tidak" name="bau_napas"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->bau_napas) && $pencernaan->bau_napas == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="bau_napas_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Kerusakan
                                                            gigi/gusi/lidah/geraham/rahang/palatum</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="kerusakan_gigi_ya" name="kerusakan_gigi"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->kerusakan_gigi) && $pencernaan->kerusakan_gigi == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="kerusakan_gigi_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="kerusakan_gigi_tidak" name="kerusakan_gigi"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->kerusakan_gigi) && $pencernaan->kerusakan_gigi == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="kerusakan_gigi_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Distensi abdomen</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="distensi_abdomen_ya" name="distensi_abdomen"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->distensi_abdomen) && $pencernaan->distensi_abdomen == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="distensi_abdomen_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="distensi_abdomen_tidak" name="distensi_abdomen"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->distensi_abdomen) && $pencernaan->distensi_abdomen == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="distensi_abdomen_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Bising usus</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="bising_usus_ya" name="bising_usus"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->bising_usus) && $pencernaan->bising_usus == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="bising_usus_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="bising_usus_tidak" name="bising_usus"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->bising_usus) && $pencernaan->bising_usus == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="bising_usus_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Konstipasi</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="konstipasi_ya" name="konstipasi"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->konstipasi) && $pencernaan->konstipasi == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="konstipasi_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="konstipasi_tidak" name="konstipasi"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->konstipasi) && $pencernaan->konstipasi == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="konstipasi_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label mb-2">Diare</label>
                                                        <div class="input-group mb-3">
                                                            <input type="number" class="form-control" name="diare"
                                                                value="{{ isset($pencernaan->diare) ? $pencernaan->diare : '' }}">
                                                            <span class="input-group-text bg-light">kali/Hari</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Hemoroid, grade</label>
                                                        <div class="mb-3">
                                                            <select class="form-select form-select-sm" id="hemoroid"
                                                                name="hemoroid" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="1"
                                                                    {{ isset($pencernaan->hemoroid) && $pencernaan->hemoroid == '1' ? 'selected' : '' }}>
                                                                    1</option>
                                                                <option value="2"
                                                                    {{ isset($pencernaan->hemoroid) && $pencernaan->hemoroid == '2' ? 'selected' : '' }}>
                                                                    2</option>
                                                                <option value="3"
                                                                    {{ isset($pencernaan->hemoroid) && $pencernaan->hemoroid == '3' ? 'selected' : '' }}>
                                                                    3</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Teraba massa abdomen</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="massa_abdomen_ya" name="massa_abdomen"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->massa_abdomen) && $pencernaan->massa_abdomen == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="massa_abdomen_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="massa_abdomen_tidak" name="massa_abdomen"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->massa_abdomen) && $pencernaan->massa_abdomen == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="massa_abdomen_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Stomatitis, warna (jika "Ya" masukkan
                                                            keterangan warna)</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="stomatitis_ya" name="stomatitis"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->stomatitis) && $pencernaan->stomatitis == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="stomatitis_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="stomatitis_tidak" name="stomatitis"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->stomatitis) && $pencernaan->stomatitis == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="stomatitis_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Riwayat obat pencahar</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="obat_pencahar_ya" name="obat_pencahar"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->obat_pencahar) && $pencernaan->obat_pencahar == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="obat_pencahar_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="obat_pencahar_tidak" name="obat_pencahar"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->obat_pencahar) && $pencernaan->obat_pencahar == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="obat_pencahar_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Konsistensi</label>
                                                        <div class="mb-3">
                                                            <select class="form-select form-select-sm" id="konsistensi"
                                                                name="konsistensi" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="cair"
                                                                    {{ isset($pencernaan->konsistensi) && $pencernaan->konsistensi == 'cair' ? 'selected' : '' }}>
                                                                    Cair</option>
                                                                <option value="lembek"
                                                                    {{ isset($pencernaan->konsistensi) && $pencernaan->konsistensi == 'lembek' ? 'selected' : '' }}>
                                                                    Lembek</option>
                                                                <option value="keras"
                                                                    {{ isset($pencernaan->konsistensi) && $pencernaan->konsistensi == 'keras' ? 'selected' : '' }}>
                                                                    Keras</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Diet khusus</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="diet_khusus_ya" name="diet_khusus"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->diet_khusus) && $pencernaan->diet_khusus == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="diet_khusus_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="diet_khusus_tidak" name="diet_khusus"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->diet_khusus) && $pencernaan->diet_khusus == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="diet_khusus_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Kebiasaan makan/minum</label>
                                                        <div class="mb-3">
                                                            <select class="form-select form-select-sm"
                                                                id="kebiasaan_makan" name="kebiasaan_makan" required>
                                                                <option value="">-- Pilih --</option>
                                                                <option value="Mandiri"
                                                                    {{ isset($pencernaan->kebiasaan_makan) && $pencernaan->kebiasaan_makan == 'Mandiri' ? 'selected' : '' }}>
                                                                    Mandiri</option>
                                                                <option value="Bantu sebagian"
                                                                    {{ isset($pencernaan->kebiasaan_makan) && $pencernaan->kebiasaan_makan == 'Bantu sebagian' ? 'selected' : '' }}>
                                                                    Bantu sebagian</option>
                                                                <option value="Tergantung"
                                                                    {{ isset($pencernaan->kebiasaan_makan) && $pencernaan->kebiasaan_makan == 'Tergantung' ? 'selected' : '' }}>
                                                                    Tergantung</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Alergi makanan/minuman</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alergi_makanan_ya" name="alergi_makanan"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->alergi_makanan) && $pencernaan->alergi_makanan == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="alergi_makanan_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alergi_makanan_tidak" name="alergi_makanan"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->alergi_makanan) && $pencernaan->alergi_makanan == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="alergi_makanan_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Alat bantu</label>
                                                        <div class="mb-3">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alat_bantu_ya" name="alat_bantu"
                                                                    value="Ya" required
                                                                    {{ isset($pencernaan->alat_bantu) && $pencernaan->alat_bantu == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="alat_bantu_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="alat_bantu_tidak" name="alat_bantu"
                                                                    value="Tidak" required
                                                                    {{ isset($pencernaan->alat_bantu) && $pencernaan->alat_bantu == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="alat_bantu_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary"><i
                                                                class="fas fa-save me-2"></i>{{ isset($pencernaan) ? 'Update' : 'Simpan' }}</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Muskuloskeletal --}}
                            <div class="accordion shadow-sm" id="individualAssessmentAccordion">
                                <div class="accordion-item border-0 rounded overflow-hidden mb-3">
                                    <h2 class="accordion-header" id="headingIndividualAssessment">
                                        <button class="accordion-button  bg-primary bg-opacity-10 text-primary"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseIndividualAssessmentSix" aria-expanded="true"
                                            aria-controls="collapseIndividualAssessmentSix">
                                            @if (isset($muskuloskeletal))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                            <i class="fas fa-bone me-2"></i>Muskuloskeletal
                                        </button>
                                    </h2>
                                    <div id="collapseIndividualAssessmentSix" class="accordion-collapse collapse"
                                        aria-labelledby="headingIndividualAssessment">
                                        <div class="accordion-body bg-light">
                                            <form
                                                action="{{ isset($muskuloskeletal) ? route('form.updateMuskuloskeletal', $muskuloskeletal->id) : route('form.saveMuskuloskeletal') }}"
                                                method="post" id="muskuloskeletalForm"
                                                data-id="{{ $muskuloskeletal->id ?? '' }}">
                                                @csrf
                                                @if (isset($muskuloskeletal))
                                                    @method('PUT')
                                                @endif
                                                <div class="row g-4">
                                                    <!-- First row -->
                                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                                        <label class="form-label">Tonus Otot</label>
                                                        <div class="mb-3">
                                                            <input type="hidden" name="pasien_id"
                                                                value={{ $pasienId }}>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tonus_otot_ya" name="tonus_otot"
                                                                    value="Ya" required
                                                                    {{ isset($muskuloskeletal) && $muskuloskeletal->tonus_otot == 'Ya' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tonus_otot_ya">Iya</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio"
                                                                    id="tonus_otot_tidak" name="tonus_otot"
                                                                    value="Tidak" required
                                                                    {{ isset($muskuloskeletal) && $muskuloskeletal->tonus_otot == 'Tidak' ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="tonus_otot_tidak">Tidak</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @foreach (['kontraktur', 'fraktur', 'nyeri_otot_tulang', 'drop_foot_lokasi', 'tremor', 'malaise_fatigue', 'atrofi', 'kekuatan_otot', 'postur_tidak_normal', 'alat_bantu', 'nyeri'] as $field)
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <label
                                                                class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                            <div class="mb-3">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="{{ $field }}_ya"
                                                                        name="{{ $field }}" value="Ya"
                                                                        required
                                                                        {{ isset($muskuloskeletal) && $muskuloskeletal->$field == 'Ya' ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="{{ $field }}_ya">Iya</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        id="{{ $field }}_tidak"
                                                                        name="{{ $field }}" value="Tidak"
                                                                        required
                                                                            {{ isset($muskuloskeletal) && $muskuloskeletal->$field == 'Tidak' ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $field }}_tidak">Tidak</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                        @foreach (['ekstremitas_atas' => ['Bebas', 'Terbatas', 'Kelemahan', 'Kelumpuhan (kanan/kiri)'], 'berdiri' => ['Mandiri', 'Bantu sebagian', 'Tergantung'], 'berjalan' => ['Mandiri', 'Bantu sebagian', 'Tergantung']] as $field => $options)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label
                                                                    class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                                <div class="mb-3">
                                                                    <select class="form-select form-select-sm"
                                                                        id="{{ $field }}"
                                                                        name="{{ $field }}" required>
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach ($options as $option)
                                                                            <option value="{{ $option }}"
                                                                                {{ isset($muskuloskeletal) && $muskuloskeletal->$field == $option ? 'selected' : '' }}>
                                                                                {{ $option }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                        <div class="text-end mt-3">
                                                            <button type="submit" class="btn btn-primary"><i
                                                                    class="fas fa-save me-2"></i>{{ isset($muskuloskeletal) ? 'Update' : 'Simpan' }}</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Neurosensori --}}
                                <div class="accordion shadow-sm" id="individualAssessmentAccordion">
                                    <div class="accordion-item border-0 rounded overflow-hidden mb-3">
                                        <h2 class="accordion-header" id="headingIndividualAssessment">
                                            <button class="accordion-button  bg-primary bg-opacity-10 text-primary"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseIndividualAssessmentSeven" aria-expanded="true"
                                                aria-controls="collapseIndividualAssessmentSeven">
                                                @if (isset($neurosensori))
                                                <i class="fas fa-check-circle me-2 text-success"></i>
                                            @endif
                                                <i class="fas fa-brain me-2"></i>Neurosensori
                                            </button>
                                        </h2>
                                        <div id="collapseIndividualAssessmentSeven" class="accordion-collapse collapse"
                                            aria-labelledby="headingIndividualAssessment">
                                            <div class="accordion-body bg-light">
                                                <form
                                                    action="{{ isset($neurosensori) ? route('form.updateNeurosensori', $neurosensori->id) : route('form.saveNeurosensori') }}"
                                                    method="post" id="neurosensoriForm"
                                                    data-id="{{ $neurosensori->id ?? '' }}">
                                                    @csrf
                                                    @if (isset($neurosensori))
                                                        @method('PUT')
                                                    @endif
                                                    <input type="hidden" name="pasien_id" value="{{$pasienId}}">
                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Fungsi Penglihatan</h5>
                                                    <div class="row g-4">
                                                        @foreach([
                                                            'buram' => 'Buram',
                                                            'tidak_bisa_melihat' => 'Tidak bisa melihat',
                                                            'alat_bantu_penglihatan' => 'Alat Bantu'
                                                        ] as $name => $label)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $label }}</label>
                                                                <div class="mb-3">
                                                                    @foreach(['Ya' => 'Iya', 'Tidak' => 'Tidak'] as $value => $text)
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" id="{{ $name }}_{{ strtolower($value) }}" 
                                                                                name="{{ $name }}" value="{{ $value }}"
                                                                                @if(old($name, $neurosensori->$name ?? '') == $value) checked @endif required>
                                                                            <label class="form-check-label" for="{{ $name }}_{{ strtolower($value) }}">{{ $text }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach

                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <label class="form-label">Visus</label>
                                                            <div class="mb-3">
                                                                <input type="text" name="visus" class="form-control" value="{{ old('visus', $neurosensori->visus ?? '') }}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Fungsi Peraba</h5>
                                                    <div class="row g-4">
                                                        @foreach(['kesemutan' => 'Kesemutan pada', 'kebas' => 'Kebas pada'] as $name => $label)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $label }}</label>
                                                                <div class="mb-3">
                                                                    <input type="text" name="{{ $name }}" class="form-control" 
                                                                        value="{{ old($name, $neurosensori->$name ?? '') }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                      
                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Fungsi Pendengaran</h5>
                                                    <div class="row g-4">
                                                        @foreach(['kurang_jelas' => 'Kurang jelas', 'tuli' => 'Tuli', 'tinnitus' => 'Tinnitus'] as $name => $label)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $label }}</label>
                                                                <div class="mb-3">
                                                                    <input type="text" name="{{ $name }}" class="form-control" 
                                                                        value="{{ old($name, $neurosensori->$name ?? '') }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Fungsi Saraf</h5>
                                                    <div class="row g-4">
                                                        @foreach (['refleks_patologi', 'disorientasi', 'parese', 'alat_bantu_saraf', 'halusinasi', 'disatria', 'amnesia', 'kekuatan_otot', 'postur_tidak_normal', 'alat_bantu', 'nyeri'] as $field)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                                <div class="mb-3">
                                                                    @foreach (['Ya' => 'Iya', 'Tidak' => 'Tidak'] as $value => $label)
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" id="{{ $field }}_{{ strtolower($value) }}" 
                                                                                name="{{ $field }}" value="{{ $value }}" required
                                                                                {{ isset($neurosensori->$field) ? ($neurosensori->$field == $value ? 'checked' : '') : (old($field, $muskuloskeletal->$field ?? '') == $value ? 'checked' : '') }}>
                                                                            <label class="form-check-label" for="{{ $field }}_{{ strtolower($value) }}">{{ $label }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    
                                                        <div class="col-lg-12 col-md-6 col-sm-12">
                                                            <label class="form-label">Kejang</label>
                                                        </div>
                                                        @foreach (['Sifat' => 'sifat', 'Frekuensi' => 'frekuensi'] as $label => $name)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $label }}</label>
                                                                <div class="mb-3">
                                                                    <input type="text" name="{{ $name }}" class="form-control" 
                                                                        value="{{ isset($neurosensori->$name) ? $neurosensori->$name : old($name, $neurosensori->$name ?? '') }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label mb-2">Lama</label>
                                                            <div class="input-group mb-3">
                                                                <input type="number" class="form-control" name="lama" 
                                                                    value="{{ isset($neurosensori->lama) ? $neurosensori->lama : old('lama', $neurosensori->lama ?? '') }}">
                                                                <span class="input-group-text bg-light">Menit</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    

                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Fungsi Perasa</h5>
                                                    <div class="row g-4">
                                                        @foreach (['Mampu' => 'mampu', 'Terganggu' => 'terganggu'] as $label => $name)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $label }}</label>
                                                                <div class="mb-3">
                                                                    <input type="text" name="{{ $name }}" class="form-control" 
                                                                        value="{{ old($name, $neurosensori->$name ?? '') }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Kulit</h5>
                                                    <div class="row g-4">
                                                        @foreach (['Memar', 'Laserasi', 'Ulserasi', 'Pus', 'Bulae/lepuh', 'Perdarahan bawah', 'Krusta', 'Perubahan warna'] as $field)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $field }}</label>
                                                                <div class="mb-3">
                                                                    <input type="text" name="{{ strtolower(str_replace([' ', '/'], '_', $field)) }}" class="form-control"
                                                                        value="{{ isset($neurosensori->{strtolower(str_replace([' ', '/'], '_', $field))}) ? $neurosensori->{strtolower(str_replace([' ', '/'], '_', $field))} : old(strtolower(str_replace([' ', '/'], '_', $field)), '') }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label mb-2">Luka bakar kulit</label>
                                                            <div class="input-group mb-3">
                                                                <input type="number" class="form-control" name="luka_bakar_kulit"
                                                                    value="{{ isset($neurosensori->luka_bakar_kulit) ? $neurosensori->luka_bakar_kulit : old('luka_bakar_kulit', '') }}">
                                                                <span class="input-group-text bg-light">Derajat</span>
                                                            </div>
                                                        </div>
                                                    
                                                        <div class="col-md-6 col-sm-12">
                                                            <label class="form-label mb-2">Decubitus</label>
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="form-control" name="decubitus_grade"
                                                                    value="{{ isset($neurosensori->decubitus_grade) ? $neurosensori->decubitus_grade : old('decubitus_grade', '') }}">
                                                                <span class="input-group-text bg-light">Grade</span>
                                                            </div>
                                                            <div class="input-group mb-3">
                                                                <input type="text" class="form-control" name="decubitus_lokasi"
                                                                    value="{{ isset($neurosensori->decubitus_lokasi) ? $neurosensori->decubitus_lokasi : old('decubitus_lokasi', '') }}">
                                                                <span class="input-group-text bg-light">Lokasi</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Tidur dan Istirahat</h5>
                                                    <div class="row g-4">
                                                        @foreach ([
                                                            'susah_tidur' => [
                                                                'label' => 'Susah tidur',
                                                                'type' => 'radio',
                                                                'options' => ['Ya' => 'Iya', 'Tidak' => 'Tidak']
                                                            ],
                                                            'perubahan_warna' => [
                                                                'label' => 'Perubahan warna',
                                                                'type' => 'text'
                                                            ],
                                                            'waktu_tidur' => [
                                                                'label' => 'Waktu tidur',
                                                                'type' => 'text',
                                                                'suffix' => 'Jam'
                                                            ],
                                                            'bantuan_obat' => [
                                                                'label' => 'Bantuan obat',
                                                                'type' => 'text'
                                                            ]
                                                        ] as $name => $field)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $field['label'] }}</label>
                                                                <div class="mb-3">
                                                                    @if ($field['type'] === 'radio')
                                                                        @foreach ($field['options'] as $value => $optionLabel)
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" id="{{ $name }}_{{ $value }}" name="{{ $name }}" value="{{ $value }}" required
                                                                                    {{ isset($neurosensori->$name) && $neurosensori->$name == $value ? 'checked' : '' }}>
                                                                                <label class="form-check-label" for="{{ $name }}_{{ $value }}">{{ $optionLabel }}</label>
                                                                            </div>
                                                                        @endforeach
                                                                    @elseif ($field['type'] === 'text' && isset($field['suffix']))
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control" name="{{ $name }}"
                                                                                value="{{ $neurosensori->$name ?? old($name, '') }}">
                                                                            <span class="input-group-text bg-light">{{ $field['suffix'] }}</span>
                                                                        </div>
                                                                    @else
                                                                        <input type="text" class="form-control" name="{{ $name }}"
                                                                            value="{{ $neurosensori->$name ?? old($name, '') }}">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    
                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Mental</h5>
                                                    <div class="row g-4">
                                                        @foreach ([
                                                            'cemas', 'marah', 'denial', 'takut', 'putus_asa', 'depresi', 
                                                            'rendah_diri', 'menarik_diri', 'agresif', 'perilaku_kekerasan', 
                                                            'tidak_mau_melihat_bagian_tubuh_yang_rusak'
                                                        ] as $field)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                                <div class="mb-3">
                                                                    @foreach (['Ya' => 'Iya', 'Tidak' => 'Tidak'] as $value => $label)
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" id="{{ $field }}_{{ strtolower($value) }}"
                                                                                name="{{ $field }}" value="{{ $value }}" required
                                                                                {{ isset($neurosensori->$field) && $neurosensori->$field == $value ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="{{ $field }}_{{ strtolower($value) }}">{{ $label }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <label class="form-label">Respon pasca trauma</label>
                                                            <div class="mb-3">
                                                                <input type="text" name="respon_pasca_trauma" id="respon_pasca_trauma" class="form-control"
                                                                    value="{{ $neurosensori->respon_pasca_trauma ?? old('respon_pasca_trauma', '') }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Komunikasi dan Budaya</h5>
                                                    <div class="row g-4">
                                                        @foreach ([
                                                            'interaksi_keluarga' => ['label' => 'Interaksi dengan keluarga', 'options' => ['baik', 'terhambat']],
                                                            'berkomunikasi' => ['label' => 'Berkomunikasi', 'options' => ['lancar', 'terhambat']]
                                                        ] as $name => $field)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $field['label'] }}</label>
                                                                <div class="mb-3">
                                                                    <select class="form-select form-select-sm" name="{{ $name }}" required>
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach ($field['options'] as $option)
                                                                            <option value="{{ $option }}" {{ isset($neurosensori) && $neurosensori->$name == $option ? 'selected' : '' }}>
                                                                                {{ ucfirst($option) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        <div class="col-lg-6 col-md-6 col-sm-12">
                                                            <label class="form-label">Kegiatan sosial sehari-hari</label>
                                                            <div class="mb-3">
                                                                <input type="text" name="kegiatan_sosial" class="form-control" value="{{ isset($neurosensori) ? $neurosensori->kegiatan_sosial : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Kebersihan Diri</h5>
                                                    <div class="row g-4">
                                                        @foreach ([
                                                            'gigi_dan_mulut_kotor', 'kulit_kotor', 'hidung_kotor', 
                                                            'telinga_kotor', 'mata_kotor', 'perial_genial_kotor', 
                                                            'kuku_kotor', 'rambut_kepala_kotor'
                                                        ] as $field)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ ucwords(str_replace('_', ' ', $field)) }}</label>
                                                                <div class="mb-3">
                                                                    @foreach (['Ya' => 'Iya', 'Tidak' => 'Tidak'] as $value => $label)
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" id="{{ $field }}_{{ strtolower($value) }}"
                                                                                name="{{ $field }}" value="{{ $value }}" required
                                                                                {{ isset($neurosensori) && $neurosensori->$field == $value ? 'checked' : '' }}>
                                                                            <label class="form-check-label" for="{{ $field }}_{{ strtolower($value) }}">{{ $label }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    
                                                    <h5 class="mb-4 pb-2 border-bottom text-secondary">Perawatan Diri Sehari-Hari</h5>
                                                    <div class="row g-4">
                                                        @foreach (['Mandi', 'Berpakaian', 'Menyisir rambut'] as $field)
                                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                                <label class="form-label">{{ $field }}</label>
                                                                <div class="mb-3">
                                                                    <select class="form-select form-select-sm" name="{{ strtolower(str_replace(' ', '_', $field)) }}" required>
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach (['Mandiri', 'Dibantu sebagian', 'Tergantung'] as $option)
                                                                            <option value="{{ $option }}" {{ isset($neurosensori) && $neurosensori->{strtolower(str_replace(' ', '_', $field))} == $option ? 'selected' : '' }}>
                                                                                {{ $option }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    
                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-primary"><i
                                                                class="fas fa-save me-2"></i>{{ isset($neurosensori) ? 'Update' : 'Simpan' }}</button>
                                                    </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex mt-3">
                                <a href="{{ route('pasiens.index') }}"
                                    class="btn btn-outline-secondary px-4">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function checkNadi() {
            const nadiValue = parseInt(document.getElementById('nadi').value) || 0;
            const takikardiInput = document.getElementById('takikardi');
            const bradikardiaInput = document.getElementById('bradikardia');

            // Reset the values
            takikardiInput.value = '';
            bradikardiaInput.value = '';

            // Autofill logic
            if (nadiValue > 100) {
                takikardiInput.value = nadiValue;
            } else if (nadiValue < 60) {
                bradikardiaInput.value = nadiValue;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const radioTidak = document.getElementById("perhatian_keluarga_tidak");
            const radioIya = document.getElementById("perhatian_keluarga_ya");
            const inputPenjelasan = document.getElementById("input-penjelasan");

            function toggleInput() {
                if (radioTidak.checked) {
                    inputPenjelasan.style.display = "block";
                } else {
                    inputPenjelasan.style.display = "none";
                }
            }

            radioTidak.addEventListener("change", toggleInput);
            radioIya.addEventListener("change", toggleInput);
        });

        document.addEventListener("DOMContentLoaded", function() {
            let radioYa = document.getElementById("upaya_peningkatan_kesehatan_ya");
            let radioTidak = document.getElementById("upaya_peningkatan_kesehatan_tidak");
            let inputDeskripsi = document.getElementById("input-deskripsi");
            let deskripsiField = document.querySelector("[name='upaya_peningkatan_kesehatan_deskripsi']");

            // Fungsi untuk menampilkan atau menyembunyikan input deskripsi
            function toggleDeskripsi() {
                if (radioYa.checked || deskripsiField.value.trim() !== "") {
                    inputDeskripsi.style.display = "block";
                } else {
                    inputDeskripsi.style.display = "none";
                    deskripsiField.value = ""; // Reset nilai input saat disembunyikan
                }
            }

            radioYa.addEventListener("change", toggleDeskripsi);
            radioTidak.addEventListener("change", toggleDeskripsi);

            toggleDeskripsi();
        });


        $(document).ready(function() {
            $("#kondisiRumahForm").submit(function(event) {
                event.preventDefault(); // Mencegah reload halaman

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                // Mengecek apakah form dalam mode edit (punya data-id)
                let isEdit = form.attr("data-id") !== undefined;
                let id = form.attr("data-id"); // Ambil ID jika mode edit
                let url = isEdit ? `{{ url('Kondisi-rumah') }}/${id}` :
                    "{{ route('form.saveKondisiRumah') }}";
                let method = isEdit ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });


        $(document).ready(function() {
            $("#formPhbsRumahTangga").submit(function(event) {
                event.preventDefault(); // Mencegah reload halaman

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined;
                let id = form.attr("data-id");
                let url = isEdit ? `{{ url('Phbs-rumah-tangga') }}/${id}` :
                    "{{ route('form.savePhbsRumahTangga') }}";
                let method = isEdit ? "PUT" : "POST";

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $("#pemeliharaanKesehatanKeluargaForm").submit(function(event) {
                event.preventDefault(); // Mencegah reload halaman

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined && form.attr("data-id") !== "";
                let id = form.attr("data-id");
                let url = isEdit ? `/pemeliaharaan-kesehatan-keluarga/${id}` :
                    "{{ route('form.savePemeliharaanKesehatanKeluarga') }}";

                // Jika edit, tambahkan _method = PUT
                if (isEdit) {
                    formData += "&_method=PUT";
                }

                $.ajax({
                    url: url,
                    type: "POST", // Selalu gunakan POST, Laravel akan mengenali PUT melalui _method
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $("#pengkajianIndividuForm").submit(function(event) {
                event.preventDefault(); // Mencegah reload halaman

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined && form.attr("data-id") !== "";
                let id = form.attr("data-id");
                let url = isEdit ? `/pengkajian-individu/${id}` :
                    "{{ route('form.savePengkajianIndividu') }}";

                // Jika edit, tambahkan _method = PUT
                if (isEdit) {
                    formData += "&_method=PUT";
                }

                $.ajax({
                    url: url,
                    type: "POST", // Selalu gunakan POST, Laravel akan mengenali PUT melalui _method
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $("#sirkulasiCairanForm").submit(function(event) {
                event.preventDefault(); // Mencegah reload halaman

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined && form.attr("data-id") !== "";
                let id = form.attr("data-id");
                let url = isEdit ? `/sirkulasi-cairan/${id}` : "{{ route('form.saveSirkulasiCairan') }}";

                if (isEdit) {
                    formData += "&_method=PUT";
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $("#perkemihanForm").submit(function(event) {
                event.preventDefault();

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined && form.attr("data-id") !== "";
                let id = form.attr("data-id");
                let url = isEdit ? `/perkemihan/${id}` : "{{ route('form.savePerkemihan') }}";

                if (isEdit) {
                    formData += "&_method=PUT";
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $("#pencernaanForm").submit(function(event) {
                event.preventDefault();

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined && form.attr("data-id") !== "";
                let id = form.attr("data-id");
                let url = isEdit ? `/pencernaan/${id}` : "{{ route('form.savePencernaan') }}";

                if (isEdit) {
                    formData += "&_method=PUT";
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });
        $(document).ready(function() {
            $("#muskuloskeletalForm").submit(function(event) {
                event.preventDefault();

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined && form.attr("data-id") !== "";
                let id = form.attr("data-id");
                let url = isEdit ? `/muskuloskeletal/${id}` : "{{ route('form.saveMuskuloskeletal') }}";

                if (isEdit) {
                    formData += "&_method=PUT";
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {
            $("#neurosensoriForm").submit(function(event) {
                event.preventDefault();

                let form = $(this);
                let formData = form.serialize();
                let csrfToken = $('meta[name="csrf-token"]').attr("content");

                let isEdit = form.attr("data-id") !== undefined && form.attr("data-id") !== "";
                let id = form.attr("data-id");
                let url = isEdit ? `/neurosensori/${id}` : "{{ route('form.saveNeurosensori') }}";

                if (isEdit) {
                    formData += "&_method=PUT";
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    headers: {
                        "X-CSRF-TOKEN": csrfToken
                    },
                    success: function(response) {
                        alert(isEdit ? "Data berhasil diperbarui!" : "Data berhasil disimpan!");
                        console.log(response);
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan. Cek di console.");
                        console.error("AJAX Error:", xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
