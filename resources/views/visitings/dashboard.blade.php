@extends('layouts.app')

@push('styles')
<style>
    /* Mobile responsive styles for patient info card */
    @media (max-width: 768px) {
        .patient-info .info-item {
            margin-bottom: 1rem;
        }
        
        .patient-info .info-item small {
            font-size: 0.75rem;
        }
        
        .patient-info .info-item span {
            font-size: 0.9rem;
            word-break: break-word;
        }
        
        .patient-avatar {
            width: 60px !important;
            height: 60px !important;
            font-size: 1.5rem !important;
        }
        
        .card-header h5 {
            font-size: 1rem;
        }
        
        .btn-sm {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
        }
    }
    
    @media (max-width: 576px) {
        .patient-info h5 {
            font-size: 1.1rem;
        }
        
        .patient-avatar {
            width: 50px !important;
            height: 50px !important;
            font-size: 1.25rem !important;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .d-grid .btn {
            font-size: 0.8rem;
        }
    }
    
    /* Ensure card doesn't stretch on mobile */
    .card {
        max-width: 100%;
    }
</style>
@endpush

@php
    // Helper function to safely get array from JSON string or array
    function getArrayFromJsonOrArray($data)
    {
        if (is_array($data)) {
            return $data;
        } elseif (is_string($data) && !empty($data)) {
            $decoded = json_decode($data, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
@endphp

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12">
                    <h3 class="mb-0">Form Pemeriksaan Kunjungan</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <!-- Main Content Layout -->
            <div class="row mt-1">
                <!-- Patient Information Sidebar -->
                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4 order-2 order-lg-1">
                    <div class="card" style="height: fit-content;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>Informasi Pasien
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="patient-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px; font-size: 2rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>

                            <div class="patient-info">
                                <h5 class="mb-3" style="text-align: center;">{{ $visiting->pasien->name }}</h5>

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">NIK</small>
                                    <span class="fw-bold text-break">{{ $visiting->pasien->nik ?? '-' }}</span>
                                </div>

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">Tanggal Lahir</small>
                                    <span
                                        class="fw-bold">{{ $visiting->pasien->tanggal_lahir ? \Carbon\Carbon::parse($visiting->pasien->tanggal_lahir)->format('d M Y') : '-' }}</span>
                                </div>

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">Jenis Kelamin</small>
                                    <span class="fw-bold">{{ $visiting->pasien->jenis_kelamin ?? '-' }}</span>
                                </div>

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">Alamat</small>
                                    <span class="fw-bold text-break">{{ $visiting->pasien->alamat ?? '-' }}, 
                                                                     {{ $visiting->pasien->village->name ?? '-' }}
                                                                     {{ $visiting->pasien->village->district->name ?? '-' }}
                                                                     {{ $visiting->pasien->village->district->regency->name ?? '-' }}
                                                                    </span>
                                </div>

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">No. Telepon</small>
                                    <span class="fw-bold">{{ $visiting->pasien->nomor_whatsapp ?? '-' }}</span>
                                </div>

                                <hr class="my-3">

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">Tanggal Kunjungan</small>
                                    <span
                                        class="fw-bold text-success">{{ \Carbon\Carbon::parse($visiting->tanggal)->format('d M Y') }}</span>
                                </div>

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">Status</small>
                                    <span
                                        class="badge bg-{{ $visiting->status == 'selesai' ? 'success' : ($visiting->status == 'proses' ? 'warning' : 'info') }}">
                                        {{ ucfirst($visiting->status) }}
                                    </span>
                                </div>

                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">Petugas</small>
                                    <span class="fw-bold">{{ $visiting->user->name ?? '-' }}</span>
                                </div>

                                <hr class="my-3">

                                <!-- Riwayat Kunjungan (menampilkan total skor AKS) -->
                                <div class="info-item mb-2">
                                    <small class="text-muted d-block">Riwayat Kunjungan</small>
                                    @if(isset($visitHistory) && $visitHistory->count())
                                        <ul class="list-group list-group-flush">
                                            @foreach($visitHistory as $vh)
                                                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                                    <a href="{{ route('visitings.dashboard', $vh->id) }}" class="text-decoration-none">
                                                        {{ \Carbon\Carbon::parse($vh->tanggal)->format('d M Y') }}
                                                    </a>
                                                    <span class="badge bg-info">Skor AKS: {{ optional($vh->skriningAdl)->total_score ?? '-' }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">Belum ada riwayat kunjungan.</span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('visitings.edit', $visiting->id) }}" 
                                       class="btn btn-warning btn-sm text-white w-100">
                                        <i class="fas fa-edit me-1"></i><span class="d-none d-sm-inline">Edit Kunjungan</span><span class="d-sm-none">Edit</span>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger btn-sm w-100" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            data-visiting-id="{{ $visiting->id }}"
                                            data-pasien-name="{{ $visiting->pasien->name }}">
                                        <i class="fas fa-trash me-1"></i><span class="d-none d-sm-inline">Hapus Kunjungan</span><span class="d-sm-none">Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="col-12 col-md-6 col-lg-8 col-xl-9 order-1 order-lg-2">
                    <!-- Navigation Tabs -->
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="visitingTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="ttv-tab" data-bs-toggle="tab"
                                        data-bs-target="#ttv" type="button" role="tab">
                                        <i class="fas fa-thermometer-half me-2"></i>Tanda Tanda Vital
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="skrining-adl-tab" data-bs-toggle="tab"
                                        data-bs-target="#skrining-adl" type="button" role="tab">
                                        <i class="fas fa-clipboard-list me-2"></i>Skrining AKS
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="health-form-tab" data-bs-toggle="tab"
                                        data-bs-target="#health-form" type="button" role="tab">
                                        <i class="fas fa-notes-medical me-2"></i>Form Kesehatan
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content" id="visitingTabContent">
                            <!-- TTV Tab -->
                            <div class="tab-pane fade show active" id="ttv" role="tabpanel">
                                <div class="card-body">
                                    <form id="ttvForm" data-visiting-id="{{ $visiting->id }}">
                                        @csrf

                                        <!-- Tanda-Tanda Vital -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="border-bottom pb-2 text-primary">Tanda-Tanda Vital</h5>
                                            </div>
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <label for="blood_pressure" class="form-label">Tekanan Darah</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="blood_pressure"
                                                        placeholder="120/80" name="blood_pressure"
                                                        value="{{ $visiting->ttvs->first()->blood_pressure ?? '' }}">
                                                    <span class="input-group-text">mmHg</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <label for="pulse" class="form-label">Nadi</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="pulse"
                                                        placeholder="80" name="pulse"
                                                        value="{{ $visiting->ttvs->first()->pulse ?? '' }}">
                                                    <span class="input-group-text">bpm</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <label for="temperature" class="form-label">Suhu</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.1" class="form-control"
                                                        id="temperature" placeholder="36.8" name="temperature"
                                                        value="{{ $visiting->ttvs->first()->temperature ?? '' }}">
                                                    <span class="input-group-text">°C</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 mb-3">
                                                <label for="oxygen" class="form-label">Saturasi Oksigen</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="oxygen"
                                                        placeholder="98" name="oxygen_saturation"
                                                        value="{{ $visiting->ttvs->first()->oxygen_saturation ?? '' }}">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Antropometri -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <h5 class="border-bottom pb-2 text-primary">Antropometri</h5>
                                            </div>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <label for="weight" class="form-label">Berat Badan</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.1" class="form-control"
                                                        id="weight" placeholder="65.5" name="weight"
                                                        value="{{ $visiting->ttvs->first()->weight ?? '' }}">
                                                    <span class="input-group-text">kg</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <label for="height" class="form-label">Tinggi Badan</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.1" class="form-control"
                                                        id="height" placeholder="170.0" name="height"
                                                        value="{{ $visiting->ttvs->first()->height ?? '' }}">
                                                    <span class="input-group-text">cm</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <label for="bmi" class="form-label">IMT</label>
                                                <div class="input-group">
                                                    <input type="number" step="0.1" class="form-control"
                                                        id="bmi" name="bmi" readonly
                                                        value="{{ $visiting->ttvs->first()->bmi ?? '' }}">
                                                    <span class="input-group-text">kg/m²</span>
                                                </div>
                                                <div id="bmi-category" class="form-text"></div>
                                                <input type="hidden" id="bmi-category-value" name="bmi_category">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="autosave-status" id="ttv-status">
                                                <small class="text-muted">
                                                    <i class="fas fa-circle text-success me-1"></i>
                                                    <span>Auto-save aktif</span>
                                                </small>
                                            </div>
                                            <button type="submit" class="btn btn-success text-white">
                                                <i class="fas fa-save me-2"></i>Simpan TTV
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Health Form Tab -->
                            <div class="tab-pane fade" id="health-form" role="tabpanel">
                                <div class="card-body">
                                    <form id="healthForm" data-visiting-id="{{ $visiting->id }}">
                                        @csrf

                                        <!-- Riwayat Penyakit -->
                                        <div class="mb-4 @if(auth()->user()->role == 'operator') d-none @endif">
                                            <h4 class="text-primary mb-3">
                                                Riwayat Penyakit
                                            </h4>
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <div class="alert alert-info">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="no_disease" name="no_disease" value="1"
                                                                {{ $visiting->healthForms->no_disease ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="no_disease">
                                                                Tidak Ada Riwayat Penyakit
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row disease-checkboxes">
                                                @php
                                                    $diseases = [
                                                        'diabetes' => 'Diabetes Melitus',
                                                        'kidney_failure' => 'Gagal Ginjal',
                                                        'heart_failure' => 'Gagal Jantung',
                                                        'hiv_aids' => 'HIV/AIDS',
                                                        'leprosy' => 'Kusta',
                                                        'stroke' => 'Stroke',
                                                    ];
                                                    $selectedDiseases = getArrayFromJsonOrArray(
                                                        $visiting->healthForms->diseases ?? [],
                                                    );
                                                    $cancerType = $visiting->healthForms->cancer_type ?? '';
                                                    $lungDiseaseType = $visiting->healthForms->lung_disease_type ?? '';
                                                @endphp

                                                @foreach ($diseases as $key => $label)
                                                    <div class="col-md-6 col-lg-3 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input disease-checkbox"
                                                                type="checkbox" id="{{ $key }}"
                                                                name="diseases[]" value="{{ $key }}"
                                                                {{ in_array($key, $selectedDiseases) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="{{ $key }}">{{ $label }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <!-- Kanker -->
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input disease-checkbox" type="checkbox"
                                                            id="cancer" name="diseases[]" value="cancer"
                                                            {{ in_array('cancer', $selectedDiseases) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="cancer">Kanker</label>
                                                    </div>
                                                    <select class="form-control conditional-field cancer-type mt-2"
                                                        name="cancer_type"
                                                        style="display: {{ in_array('cancer', $selectedDiseases) ? 'block' : 'none' }}">
                                                        <option value="">Pilih Jenis Kanker</option>
                                                        <option value="breast"
                                                            {{ $cancerType == 'breast' ? 'selected' : '' }}>Kanker Payudara
                                                        </option>
                                                        <option value="cervical"
                                                            {{ $cancerType == 'cervical' ? 'selected' : '' }}>Kanker Leher
                                                            Rahim</option>
                                                        <option value="lung"
                                                            {{ $cancerType == 'lung' ? 'selected' : '' }}>Kanker Paru
                                                        </option>
                                                        <option value="colorectal"
                                                            {{ $cancerType == 'colorectal' ? 'selected' : '' }}>Kanker
                                                            Kolorektal</option>
                                                        <option value="liver"
                                                            {{ $cancerType == 'liver' ? 'selected' : '' }}>Kanker Hati
                                                        </option>
                                                        <option value="nasopharyngeal"
                                                            {{ $cancerType == 'nasopharyngeal' ? 'selected' : '' }}>Kanker
                                                            Nasofaring</option>
                                                        <option value="lymphoma"
                                                            {{ $cancerType == 'lymphoma' ? 'selected' : '' }}>Limfoma Non
                                                            Hodgkin</option>
                                                        <option value="leukemia"
                                                            {{ $cancerType == 'leukemia' ? 'selected' : '' }}>Leukemia
                                                        </option>
                                                        <option value="ovarian"
                                                            {{ $cancerType == 'ovarian' ? 'selected' : '' }}>Kanker Ovarium
                                                        </option>
                                                        <option value="other"
                                                            {{ $cancerType == 'other' ? 'selected' : '' }}>Kanker Lainnya
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- Penyakit Paru -->
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input disease-checkbox" type="checkbox"
                                                            id="lung_disease" name="diseases[]" value="lung_disease"
                                                            {{ in_array('lung_disease', $selectedDiseases) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="lung_disease">Penyakit
                                                            Paru</label>
                                                    </div>
                                                    <select class="form-control conditional-field lung-disease-type mt-2"
                                                        name="lung_disease_type"
                                                        style="display: {{ in_array('lung_disease', $selectedDiseases) ? 'block' : 'none' }}">
                                                        <option value="">Pilih Penyakit Paru</option>
                                                        <option value="tbc"
                                                            {{ $lungDiseaseType == 'tbc' ? 'selected' : '' }}>TBC</option>
                                                        <option value="pneumonia"
                                                            {{ $lungDiseaseType == 'pneumonia' ? 'selected' : '' }}>
                                                            Pneumonia</option>
                                                        <option value="ppok"
                                                            {{ $lungDiseaseType == 'ppok' ? 'selected' : '' }}>PPOK
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        @if (auth()->user()->role == 'perawat' || auth()->user()->role == 'superadmin')
                                            <!-- Skrining ILP Perawat -->
                                            <div class="mb-4">
                                                <h4 class="text-success mb-3">
                                                    Skrining ILP
                                                </h4>
                                                <div class="row">
                                                    @foreach ($screenings as $screening)
                                                        <div class="col-md-6 col-lg-4 mb-3">
                                                            <div class="form-group">
                                                                <label
                                                                    class="form-label">{{ $screening['label'] }}</label>
                                                                <div class="mb-2">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="screening_{{ $screening['id'] }}"
                                                                            id="{{ $screening['id'] }}_yes"
                                                                            value="1"
                                                                            {{ $visiting->healthForms->{'screening_' . $screening['id']} == 1 ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $screening['id'] }}_yes">Ya</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="screening_{{ $screening['id'] }}"
                                                                            id="{{ $screening['id'] }}_no" value="0"
                                                                            {{ $visiting->healthForms->{'screening_' . $screening['id']} == 0 ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $screening['id'] }}_no">Tidak</label>
                                                                    </div>
                                                                </div>
                                                                <select
                                                                    class="form-control conditional-field {{ $screening['id'] }}-status"
                                                                    name="{{ $screening['id'] }}_status"
                                                                    style="display: {{ $visiting->healthForms->{'screening_' . $screening['id']} == 1 ? 'block' : 'none' }}">
                                                                    <option value="">Pilih Status</option>
                                                                    <option value="penderita"
                                                                        {{ $visiting->healthForms->{'screening_' . $screening['id'] . '_status'} == 'penderita' ? 'selected' : '' }}>
                                                                        Penderita</option>
                                                                    <option value="bukan_penderita"
                                                                        {{ $visiting->healthForms->{'screening_' . $screening['id'] . '_status'} == 'bukan_penderita' ? 'selected' : '' }}>
                                                                        Bukan Penderita</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Skor AKS -->
                                        <div class="mb-4" style="display: none">
                                            <h4 class="text-info mb-3">
                                                Skor AKS (Activities of Daily Living)
                                            </h4>
                                            <div class="row">
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="skor_aks"
                                                            id="skor_aks_mandiri" value="mandiri"
                                                            {{ $visiting->healthForms->skor_aks == 'mandiri' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="skor_aks_mandiri">20 :
                                                            Mandiri</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="skor_aks"
                                                            id="skor_aks_ringan" value="ketergantungan_ringan"
                                                            {{ $visiting->healthForms->skor_aks == 'ketergantungan_ringan' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="skor_aks_ringan">12 - 19 :
                                                            Ketergantungan ringan</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="skor_aks"
                                                            id="skor_aks_sedang" value="ketergantungan_sedang"
                                                            {{ $visiting->healthForms->skor_aks == 'ketergantungan_sedang' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="skor_aks_sedang">9 - 11 :
                                                            Ketergantungan sedang</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="skor_aks"
                                                            id="skor_aks_berat" value="ketergantungan_berat"
                                                            {{ $visiting->healthForms->skor_aks == 'ketergantungan_berat' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="skor_aks_berat">5 - 8 :
                                                            Ketergantungan berat</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-lg-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="skor_aks"
                                                            id="skor_aks_total" value="ketergantungan_total"
                                                            {{ $visiting->healthForms->skor_aks == 'ketergantungan_total' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="skor_aks_total">0 - 4 :
                                                            Ketergantungan total</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dukungan Keluarga/Pendamping -->
                                        <div class="mb-4 @if(auth()->user()->role == 'operator') d-none @endif">
                                            <h4 class="text-warning mb-3">
                                                DUKUNGAN KELUARGA / PENDAMPING
                                            </h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="caregiver_availability">Apakah ada keluarga/pendamping
                                                            yang membantu?</label>
                                                        <select name="caregiver_availability" id="caregiver_availability"
                                                            class="form-control">
                                                            <option value="">Pilih...</option>
                                                            <option value="selalu"
                                                                {{ $visiting->healthForms->caregiver_availability == 'selalu' ? 'selected' : '' }}>
                                                                Selalu ada</option>
                                                            <option value="kadang"
                                                                {{ $visiting->healthForms->caregiver_availability == 'kadang' ? 'selected' : '' }}>
                                                                Tidak selalu ada</option>
                                                            <option value="tidak"
                                                                {{ $visiting->healthForms->caregiver_availability == 'tidak' ? 'selected' : '' }}>
                                                                Tidak ada</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Permasalahan di Luar Kesehatan -->
                                        <div class="mb-4 @if(auth()->user()->role == 'operator') d-none @endif">
                                            <h4 class="text-secondary mb-3">
                                                PERMASALAHAN DI LUAR KESEHATAN
                                            </h4>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label>Apakah ada permasalahan di luar kesehatan yang mempengaruhi
                                                            kondisi pasien?</label>
                                                        <select name="non_medical_issues_status"
                                                            id="non_medical_issues_status" class="form-control">
                                                            <option value="">Pilih...</option>
                                                            <option value="1"
                                                                {{ $visiting->healthForms->non_medical_issues_status == 1 ? 'selected' : '' }}>
                                                                Ya</option>
                                                            <option value="0"
                                                                {{ $visiting->healthForms->non_medical_issues_status == 0 ? 'selected' : '' }}>
                                                                Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="non_medical_issues_text_wrapper" class="mb-3"
                                                style="display: {{ $visiting->healthForms->non_medical_issues_status == 1 ? 'block' : 'none' }};">
                                                <div class="form-group">
                                                    <label for="non_medical_issues_text">Tuliskan permasalahan di luar
                                                        kesehatan</label>
                                                    <textarea name="non_medical_issues_text" id="non_medical_issues_text" class="form-control" rows="3">{{ $visiting->healthForms->non_medical_issues_text ?? '' }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Jenis Gangguan Fungsional -->
                                        <div class="mb-4 @if(auth()->user()->role == 'operator') d-none @endif">
                                            <h4 class="text-danger mb-3">
                                                Jenis gangguan fungsional yang dialami
                                            </h4>
                                            <div class="row">
                                                @php
                                                    $gangguans = [
                                                        [
                                                            'id' => 'gangguan_komunikasi',
                                                            'label' => 'Gangguan komunikasi',
                                                        ],
                                                        [
                                                            'id' => 'kesulitan_makan',
                                                            'label' => 'Kesulitan makan (feeding problem)',
                                                        ],
                                                        [
                                                            'id' => 'gangguan_fungsi_kardiorespirasi',
                                                            'label' => 'Gangguan fungsi kardiorespirasi',
                                                        ],
                                                        [
                                                            'id' => 'gangguan_fungsi_berkemih',
                                                            'label' => 'Gangguan fungsi berkemih',
                                                        ],
                                                        [
                                                            'id' => 'gangguan_mobilisasi',
                                                            'label' => 'Gangguan mobilisasi',
                                                        ],
                                                        [
                                                            'id' => 'gangguan_partisipasi',
                                                            'label' =>
                                                                'Gangguan aktifitas kehidupan sehari-hari/partisipasi',
                                                        ],
                                                    ];
                                                @endphp
                                                @foreach ($gangguans as $gangguan)
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label>{{ ucfirst($gangguan['label']) }}</label>
                                                            <div class="mb-2">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="{{ $gangguan['id'] }}"
                                                                        id="{{ $gangguan['id'] }}_yes" value="1"
                                                                        {{ $visiting->healthForms->{$gangguan['id']} == 1 ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="{{ $gangguan['id'] }}_yes">Ya</label>
                                                                </div>
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="{{ $gangguan['id'] }}"
                                                                        id="{{ $gangguan['id'] }}_no" value="0"
                                                                        {{ $visiting->healthForms->{$gangguan['id']} == 0 ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="{{ $gangguan['id'] }}_no">Tidak</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @if (auth()->user()->role == 'superadmin' || auth()->user()->role == 'operator')
                                            <!-- Perawatan Yang Dilakukan (Perawat) -->
                                            <div class="mb-4">
                                                <h4 class="text-primary mb-3">
                                                    Perawatan Yang Dilakukan
                                                </h4>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label>Perawatan yang dilakukan</label>
                                                            <textarea name="perawatan" class="form-control" placeholder="Masukkan perawatan yang dilakukan" rows="3">{{ $visiting->healthForms->perawatan ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (auth()->user()->role == 'perawat' || auth()->user()->role == 'superadmin')
                                            <!-- Perawatan Umum Yang Dilakukan (Operator) -->
                                            <div class="mb-4">
                                                <h4 class="text-success mb-3">
                                                    Perawatan Umum Yang Dilakukan
                                                </h4>
                                                <div class="row">
                                                    @php
                                                        $perawatans = [
                                                            [
                                                                'id' => 'hygiene',
                                                                'label' => 'Pemeliharaan kebersihan diri',
                                                            ],
                                                            [
                                                                'id' => 'skin_care',
                                                                'label' => 'Pencegahan Masalah Kesehatan Kulit',
                                                            ],
                                                            [
                                                                'id' => 'environment',
                                                                'label' =>
                                                                    'Pemeliharaan Kebersihan dan Keamanan Lingkungan',
                                                            ],
                                                            [
                                                                'id' => 'welfare',
                                                                'label' =>
                                                                    'Mempertahankan Tingkat Kemandirian warga jakarta yang membutuhkan',
                                                            ],
                                                            [
                                                                'id' => 'sunlight',
                                                                'label' => 'Tercukupinya pajanan Sinar Matahari',
                                                            ],
                                                            [
                                                                'id' => 'communication',
                                                                'label' => 'Komunikasi dengan baik',
                                                            ],
                                                            [
                                                                'id' => 'recreation',
                                                                'label' =>
                                                                    'Motivasi untuk melaksanakan Kegiatan Rekreasi',
                                                            ],
                                                            [
                                                                'id' => 'penamtauan_obat',
                                                                'label' => 'Pemantauan Penggunaan Obat',
                                                            ],
                                                            [
                                                                'id' => 'ibadah',
                                                                'label' => 'Motivasi untuk Pelaksanaan Ibadah',
                                                            ],
                                                        ];
                                                    @endphp
                                                    @foreach ($perawatans as $perawatan)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-group">
                                                                <label>{{ $perawatan['label'] }}</label>
                                                                <div class="mb-2">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="perawatan_{{ $perawatan['id'] }}"
                                                                            id="{{ $perawatan['id'] }}_yes"
                                                                            value="1"
                                                                            {{ $visiting->healthForms->{'perawatan_' . $perawatan['id']} == 1 ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $perawatan['id'] }}_yes">Ya</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="perawatan_{{ $perawatan['id'] }}"
                                                                            id="{{ $perawatan['id'] }}_no" value="0"
                                                                            {{ $visiting->healthForms->{'perawatan_' . $perawatan['id']} == 0 ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $perawatan['id'] }}_no">Tidak</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Perawatan Khusus Yang Dilakukan (Operator) -->
                                            <div class="mb-4">
                                                <h4 class="text-warning mb-3">
                                                    Perawatan Khusus Yang Dilakukan
                                                </h4>
                                                <div class="row">
                                                    @php
                                                        $perawatans = [
                                                            [
                                                                'id' => 'membantu_warga',
                                                                'label' =>
                                                                    'Membantu warga jakarta yang membutuhkan yang Mengalami Gangguan Gerak',
                                                            ],
                                                            [
                                                                'id' => 'monitoring_gizi',
                                                                'label' =>
                                                                    'Monitoring dan Edukasi Pemenuhan Gizi yang baik',
                                                            ],
                                                            [
                                                                'id' => 'membantu_bak_bab',
                                                                'label' =>
                                                                    'Membantu Buang Air Kecil (BAK) dan Buang Air Besar (BAB)',
                                                            ],
                                                            [
                                                                'id' => 'menangani_gangguan',
                                                                'label' =>
                                                                    'Menangani Gangguan Perilaku dengan Pikun/Demensial',
                                                            ],
                                                            [
                                                                'id' => 'pengelolaan_stres',
                                                                'label' => 'Pengelolaan Stres',
                                                            ],
                                                        ];
                                                    @endphp
                                                    @foreach ($perawatans as $perawatan)
                                                        <div class="col-md-6 mb-3">
                                                            <div class="form-group">
                                                                <label>{{ $perawatan['label'] }}</label>
                                                                <div class="mb-2">
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="perawatan_{{ $perawatan['id'] }}"
                                                                            id="{{ $perawatan['id'] }}_yes"
                                                                            value="1"
                                                                            {{ $visiting->healthForms->{'perawatan_' . $perawatan['id']} == 1 ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $perawatan['id'] }}_yes">Ya</label>
                                                                    </div>
                                                                    <div class="form-check form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="perawatan_{{ $perawatan['id'] }}"
                                                                            id="{{ $perawatan['id'] }}_no" value="0"
                                                                            {{ $visiting->healthForms->{'perawatan_' . $perawatan['id']} == 0 ? 'checked' : '' }}>
                                                                        <label class="form-check-label"
                                                                            for="{{ $perawatan['id'] }}_no">Tidak</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Keluaran dari perawatan yang dilakukan -->
                                        <div class="mb-4 @if(auth()->user()->role == 'operator') d-none @endif" >
                                            <h4 class="text-info mb-3">
                                                Keluaran dari perawatan yang dilakukan
                                            </h4>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label fw-bold">Keluaran Perawatan</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="keluaran" id="keluaran_meningkat" value="1"
                                                                {{ $visiting->healthForms->keluaran == 1 ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="keluaran_meningkat">Meningkat</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="keluaran" id="keluaran_tetap" value="2"
                                                                {{ $visiting->healthForms->keluaran == 2 ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="keluaran_tetap">Tetap</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="keluaran" id="keluaran_menurun" value="3"
                                                                {{ $visiting->healthForms->keluaran == 3 ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="keluaran_menurun">Menurun</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label for="keterangan"
                                                            class="form-label fw-bold">Keterangan</label>
                                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="4"
                                                            placeholder="Keterangan hasil perawatan">{{ $visiting->healthForms->keterangan ?? '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pembinaan keluarga -->
                                        <div class="mb-4">
                                            <h4 class="text-primary mb-3">
                                                Dilakukan pembinaan keluarga
                                            </h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="pembinaan" class="form-label fw-bold">Pembinaan
                                                            Keluarga</label>
                                                        <select name="pembinaan" id="pembinaan" class="form-select">
                                                            <option value="">Pilih...</option>
                                                            <option value="ya"
                                                                {{ $visiting->healthForms->pembinaan == 'ya' ? 'selected' : '' }}>
                                                                Ya</option>
                                                            <option value="tidak"
                                                                {{ $visiting->healthForms->pembinaan == 'tidak' ? 'selected' : '' }}>
                                                                Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tingkat Kemandirian Keluarga -->
                                        <div class="mb-4 @if(auth()->user()->role == 'operator') d-none @endif">
                                            <h4 class="text-success mb-3">
                                                Tingkat Kemandirian Keluarga
                                            </h4>
                                            <div class="row kemandirian-checkboxes">
                                                @php
                                                    $tingkat_kemandirian = [
                                                        'menerima_petugas' =>
                                                            'Menerima petugas Perawatan Kesehatan Masyarakat',
                                                        'menerima_pelayanan' =>
                                                            'Menerima pelayanan keperawatan yang diberikan sesuai dengan rencana keperawatan',
                                                        'mengenal_masalah' =>
                                                            'Tahu dan dapat mengungkapkan masalah kesehatannya secara benar',
                                                        'manfaatkan_fasilitas' =>
                                                            'Memanfaatkan fasilitas pelayanan sesuai anjuran',
                                                        'melakukan_perawatan' =>
                                                            'Melakukan perawatan sederhana sesuai yang dianjurkan',
                                                        'melakukan_pencegahan' =>
                                                            'Melaksanakan tindakan pencegahan secara aktif',
                                                        'melakukan_promotif' =>
                                                            'Melaksanakan tindakan promotif secara aktif',
                                                    ];
                                                    $selectedKemandirian = getArrayFromJsonOrArray(
                                                        $visiting->healthForms->kemandirian ?? [],
                                                    );
                                                @endphp
                                                @foreach ($tingkat_kemandirian as $key => $label)
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input kemandirian-checkbox"
                                                                type="checkbox" id="{{ $key }}"
                                                                name="kemandirian[]" value="{{ $key }}"
                                                                {{ in_array($key, $selectedKemandirian) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="{{ $key }}">{{ $label }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                <div class="col-12 mt-3">
                                                    <div class="alert alert-info">
                                                        <strong>Tingkat Kemandirian:</strong>
                                                        <span id="tingkatKemandirianLabel" class="fw-bold ms-2">Belum
                                                            Ditentukan</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if (auth()->user()->role == 'perawat' || auth()->user()->role == 'superadmin')
                                            <!-- Catatan Keperawatan (Operator) -->
                                            <div class="mb-4">
                                                <h4 class="text-info mb-3">
                                                    Catatan Keperawatan
                                                </h4>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label for="catatan_keperawatan">Catatan Keperawatan</label>
                                                            <textarea name="catatan_keperawatan" class="form-control" placeholder="Masukkan Catatan Keperawatan" rows="3">{{ $visiting->healthForms->catatan_keperawatan ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Kunjungan Lanjutan -->
                                        <div class="mb-4 @if(auth()->user()->role == 'operator' && $visiting->status == 'Kunjungan Awal') d-none @endif">
                                            <h4 class="text-primary mb-3">
                                                KUNJUNGAN LANJUTAN
                                            </h4>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label class="form-label fw-bold">Apakah akan dikunjungi kembali
                                                            oleh perawat?</label>
                                                        <select name="kunjungan_lanjutan" id="kunjungan_lanjutan"
                                                            class="form-select">
                                                            <option value="">Pilih...</option>
                                                            <option value="ya"
                                                                {{ $visiting->healthForms->kunjungan_lanjutan == 'ya' ? 'selected' : '' }}>
                                                                Ya</option>
                                                            <option value="tidak"
                                                                {{ $visiting->healthForms->kunjungan_lanjutan == 'tidak' ? 'selected' : '' }}>
                                                                Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="detail_kunjungan_lanjutan"
                                                style="display: {{ $visiting->healthForms->kunjungan_lanjutan == 'ya' ? 'block' : 'none' }}">
                                                <div class="row">
                                                    <div class="col-md-8 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label fw-bold">Permasalahan kesehatan yang
                                                                perlu kunjungan lanjutan</label>
                                                            <textarea class="form-control" id="permasalahan_lanjutan" name="permasalahan_lanjutan" rows="3"
                                                                placeholder="Tuliskan permasalahan kesehatan yang memerlukan kunjungan lanjutan">{{ $visiting->healthForms->permasalahan_lanjutan ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label fw-bold">Tanggal kunjungan
                                                                lanjutan</label>
                                                            <input type="date" class="form-control"
                                                                name="tanggal_kunjungan" placeholder="Tanggal"
                                                                value="{{ $visiting->healthForms->tanggal_kunjungan ? $visiting->healthForms->tanggal_kunjungan->format('Y-m-d') : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="form_henti_layanan"
                                                style="display: {{ $visiting->healthForms->kunjungan_lanjutan == 'tidak' ? 'block' : 'none' }}">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-group">
                                                            <label class="form-label fw-bold">Alasan Henti Layanan</label>
                                                            <select name="henti_layanan" id="alasan_henti_layanan"
                                                                class="form-select">
                                                                <option value="">Pilih...</option>
                                                                <option value="kenaikan_nilai_aks"
                                                                    {{ $visiting->healthForms->henti_layanan == 'kenaikan_nilai_aks' ? 'selected' : '' }}>
                                                                    KENAIKAN NILAI AKS</option>
                                                                <option value="meninggal"
                                                                    {{ $visiting->healthForms->henti_layanan == 'meninggal' ? 'selected' : '' }}>
                                                                    MENINGGAL</option>
                                                                <option value="menolak"
                                                                    {{ $visiting->healthForms->henti_layanan == 'menolak' ? 'selected' : '' }}>
                                                                    MENOLAK</option>
                                                                <option value="pindah_domisili"
                                                                    {{ $visiting->healthForms->henti_layanan == 'pindah_domisili' ? 'selected' : '' }}>
                                                                    PINDAH DOMISILI</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="autosave-status" id="health-status">
                                                <small class="text-muted">
                                                    <i class="fas fa-circle text-success mr-1"></i>
                                                    <span>Auto-save aktif</span>
                                                </small>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                Simpan Form Kesehatan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Skrining ADL Tab -->
                            <div class="tab-pane fade" id="skrining-adl" role="tabpanel">
                                <div class="card-body">
                                    <form id="skriningAdlForm" data-visiting-id="{{ $visiting->id }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Mengendalikan rangsangan BAB</label>
                                                    <select name="bab_control" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bab_control == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bab_control == 1 ? 'selected' : '' }}>
                                                            1 - Kadang-kadang tidak mampu</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bab_control == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Mengendalikan rangsangan BAK</label>
                                                    <select name="bak_control" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bak_control == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bak_control == 1 ? 'selected' : '' }}>
                                                            1 - Kadang-kadang tidak mampu</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bak_control == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Makan minum</label>
                                                    <select name="eating" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->eating == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->eating == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->eating == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Naik turun tangga</label>
                                                    <select name="stairs" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->stairs == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->stairs == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->stairs == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Mandi</label>
                                                    <select name="bathing" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bathing == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bathing == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->bathing == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Bergerak dari kursi roda ke tempat
                                                        tidur</label>
                                                    <select name="transfer" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->transfer == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->transfer == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->transfer == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Berjalan di tempat rata</label>
                                                    <select name="walking" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->walking == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->walking == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->walking == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Berpakaian</label>
                                                    <select name="dressing" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->dressing == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->dressing == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->dressing == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Membersihkan diri</label>
                                                    <select name="grooming" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->grooming == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->grooming == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->grooming == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label class="form-label fw-bold">Penggunaan WC</label>
                                                    <select name="toilet_use" class="form-select">
                                                        <option value="">Pilih Skor</option>
                                                        <option value="0"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->toilet_use == 0 ? 'selected' : '' }}>
                                                            0 - Tidak mampu</option>
                                                        <option value="1"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->toilet_use == 1 ? 'selected' : '' }}>
                                                            1 - Perlu bantuan</option>
                                                        <option value="2"
                                                            {{ $visiting->skriningAdl && $visiting->skriningAdl->toilet_use == 2 ? 'selected' : '' }}>
                                                            2 - Mampu</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-info">
                                                    <h6 class="fw-bold">Total Skor: <span
                                                            id="totalScore">{{ $visiting->skriningAdl ? $visiting->skriningAdl->total_score : 0 }}</span>
                                                    </h6>
                                                    <small>
                                                        <strong>Interpretasi:</strong><br>
                                                        • 20: Mandiri<br>
                                                        • 12-19: Ketergantungan ringan<br>
                                                        • 9-11: Ketergantungan sedang<br>
                                                        • 5-8: Ketergantungan berat<br>
                                                        • 0-4: Ketergantungan total
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="autosave-status" id="skriningAdl-status">
                                                <small class="text-muted">
                                                    <i class="fas fa-circle text-success me-1"></i>
                                                    <span>Auto-save aktif</span>
                                                </small>
                                            </div>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i>Simpan Skrining AKS
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12" style="text-align: right">
                            <a href="{{ route('visitings.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Kunjungan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        /* Autosave Status */
        .autosave-status {
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: #f8f9fa;
        }

        .autosave-status i {
            transition: all 0.3s ease;
        }

        .autosave-status .text-success {
            color: #28a745 !important;
        }

        .autosave-status .text-warning {
            color: #ffc107 !important;
        }

        .autosave-status .text-danger {
            color: #dc3545 !important;
        }

        .autosave-status small {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Button loading state */
        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Form validation styling */
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Tab content animation */
        .tab-pane {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Card spacing */
        .card {
            margin-bottom: 1rem;
        }

        /* Form group spacing */
        .form-group {
            margin-bottom: 1rem;
        }

        /* Patient Information Sidebar */
        .patient-avatar {
            background: linear-gradient(135deg, #007bff, #0056b3);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .patient-info .info-item {
            border-left: 3px solid #e9ecef;
            padding-left: 0.75rem;
            transition: all 0.3s ease;
        }

        .patient-info .info-item:hover {
            border-left-color: #007bff;
            background-color: #f8f9fa;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
        }

        /* Tab Styling */
        .nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link:hover {
            border-bottom-color: #dee2e6;
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            border-bottom-color: #007bff;
            color: #007bff;
            background-color: transparent;
        }

        /* Card Header Tabs */
        .card-header-tabs {
            border-bottom: none;
            margin-bottom: 0;
        }

        /* Form Styling */
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Button Styling */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Autosave Status */
        .autosave-status {
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: #f8f9fa;
        }

        .autosave-status i {
            transition: all 0.3s ease;
        }

        .autosave-status .text-success {
            color: #28a745 !important;
        }

        .autosave-status .text-warning {
            color: #ffc107 !important;
        }

        .autosave-status .text-danger {
            color: #dc3545 !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .patient-info .info-item {
                padding-left: 0.5rem;
            }

            .nav-tabs .nav-link {
                font-size: 0.875rem;
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
@endpush

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Konfirmasi Hapus Kunjungan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kunjungan untuk pasien <strong id="pasienName"></strong>?</p>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data kunjungan terkait.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus Kunjungan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tabs
            let currentTabIndex = 0;

            // Auto-save functionality
            function initializeAutosave() {
                console.log('Initializing autosave...');

                const forms = document.querySelectorAll('form[id$="Form"]');
                console.log('Found forms:', forms.length);

                forms.forEach(form => {
                    console.log('Form ID:', form.id);
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        console.log('Form submitted:', form.id);
                        saveFormData(this);
                    });
                });

                // Auto-save on input change
                const inputs = document.querySelectorAll('input, select, textarea');
                console.log('Found inputs:', inputs.length);

                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        console.log('Input changed:', this.name, this.value);
                        const form = this.closest('form');
                        if (form) {
                            console.log('Found parent form:', form.id);
                            debouncedSave(form);
                        } else {
                            console.log('No parent form found for input:', this.name);
                        }
                    });

                    // Also save on input for text fields
                    if (input.type === 'text' || input.tagName === 'TEXTAREA') {
                        input.addEventListener('input', function() {
                            console.log('Input typing:', this.name, this.value);
                            const form = this.closest('form');
                            if (form) {
                                console.log('Found parent form:', form.id);
                                debouncedSave(form);
                            } else {
                                console.log('No parent form found for input:', this.name);
                            }
                        });
                    }
                });
            }

            // Auto-save on input change with debouncing
            let saveTimeouts = {};

            function debouncedSave(form) {
                const formId = form.id;
                console.log('Debounced save for form:', formId);

                if (saveTimeouts[formId]) {
                    clearTimeout(saveTimeouts[formId]);
                }

                saveTimeouts[formId] = setTimeout(() => {
                    console.log('Executing save for form:', formId);
                    saveFormData(form);
                }, 1000); // 1 second delay
            }

            // Initialize autosave
            initializeAutosave();

            // Progress indicator functionality
            function updateFormProgress() {
                const form = document.getElementById('healthForm');
                if (!form) return;

                const inputs = form.querySelectorAll('input, select, textarea');
                let filledInputs = 0;
                let totalInputs = 0;

                inputs.forEach(input => {
                    if (input.type === 'hidden' || input.name === '_token') return;

                    totalInputs++;

                    if (input.type === 'checkbox' || input.type === 'radio') {
                        if (input.checked) filledInputs++;
                    } else if (input.type === 'select-one') {
                        if (input.value && input.value !== '') filledInputs++;
                    } else {
                        if (input.value && input.value.trim() !== '') filledInputs++;
                    }
                });

                const progressPercentage = totalInputs > 0 ? Math.round((filledInputs / totalInputs) * 100) : 0;

                const progressBar = document.getElementById('progress-bar');
                const progressText = document.getElementById('form-progress');

                if (progressBar) {
                    progressBar.style.width = progressPercentage + '%';
                }

                if (progressText) {
                    progressText.textContent = progressPercentage + '%';

                    // Update badge color based on progress
                    progressText.className = 'badge';
                    if (progressPercentage < 30) {
                        progressText.classList.add('bg-danger');
                    } else if (progressPercentage < 70) {
                        progressText.classList.add('bg-warning');
                    } else {
                        progressText.classList.add('bg-success');
                    }
                }
            }

            // Update progress on form changes
            const healthForm = document.getElementById('healthForm');
            if (healthForm) {
                healthForm.addEventListener('change', updateFormProgress);
                healthForm.addEventListener('input', updateFormProgress);

                // Initial progress calculation
                updateFormProgress();
            }

            // BMI Calculation
            const weightInput = document.getElementById('weight');
            const heightInput = document.getElementById('height');
            const bmiResult = document.getElementById('bmi');
            const bmiCategory = document.getElementById('bmi-category');
            const bmiCategoryValue = document.getElementById('bmi-category-value');

            function calculateBMI() {
                if (weightInput && heightInput && weightInput.value && heightInput.value) {
                    const weight = parseFloat(weightInput.value);
                    const height = parseFloat(heightInput.value) / 100; // convert cm to m
                    const bmi = weight / (height * height);
                    bmiResult.value = bmi.toFixed(2);

                    let category = '';
                    if (bmi < 17) {
                        category = 'Kurus';
                    } else if (bmi <= 18.4) {
                        category = 'Kurus';
                    } else if (bmi <= 25) {
                        category = 'Normal';
                    } else {
                        category = 'Gemuk';
                    }

                    bmiCategory.textContent = 'Status: ' + category;
                    bmiCategoryValue.value = category;
                }
            }

            if (weightInput && heightInput) {
                weightInput.addEventListener('input', calculateBMI);
                heightInput.addEventListener('input', calculateBMI);
            }

            // Disease checkbox logic
            const noDiseaseCheckbox = document.getElementById('no_disease');
            const diseaseCheckboxes = document.querySelectorAll('.disease-checkbox');

            if (noDiseaseCheckbox) {
                noDiseaseCheckbox.addEventListener('change', function() {
                    diseaseCheckboxes.forEach(checkbox => {
                        checkbox.disabled = this.checked;
                        if (this.checked) checkbox.checked = false;
                    });
                });
            }

            // Conditional fields for cancer and lung disease
            const cancerCheckbox = document.getElementById('cancer');
            const cancerTypeSelect = document.querySelector('.cancer-type');
            const lungDiseaseCheckbox = document.getElementById('lung_disease');
            const lungDiseaseTypeSelect = document.querySelector('.lung-disease-type');

            if (cancerCheckbox && cancerTypeSelect) {
                cancerCheckbox.addEventListener('change', function() {
                    cancerTypeSelect.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) cancerTypeSelect.value = '';
                });
            }

            if (lungDiseaseCheckbox && lungDiseaseTypeSelect) {
                lungDiseaseCheckbox.addEventListener('change', function() {
                    lungDiseaseTypeSelect.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) lungDiseaseTypeSelect.value = '';
                });
            }

            // Skrining ILP logic
            const screeningRadios = document.querySelectorAll('input[name^="screening_"]');
            screeningRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const screeningId = this.name.replace('screening_', '');
                    const statusSelect = document.querySelector(`.${screeningId}-status`);
                    if (statusSelect) {
                        statusSelect.style.display = this.value === '1' ? 'block' : 'none';
                        if (this.value !== '1') statusSelect.value = '';
                    }
                });
            });

            // Tingkat Kemandirian Logic
            function updateKemandirianLevel() {
                const checkedCount = document.querySelectorAll('.kemandirian-checkbox:checked').length;
                let level = 'Belum Ditentukan';
                if (checkedCount >= 7) {
                    level = 'Keluarga IV';
                } else if (checkedCount === 6) {
                    level = 'Keluarga III';
                } else if (checkedCount === 5) {
                    level = 'Keluarga II';
                } else if (checkedCount >= 1 && checkedCount <= 4) {
                    level = 'Keluarga I';
                }
                const labelElement = document.getElementById('tingkatKemandirianLabel');
                if (labelElement) {
                    labelElement.textContent = level;
                }
            }

            const kemandirianCheckboxes = document.querySelectorAll('.kemandirian-checkbox');
            kemandirianCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateKemandirianLevel);
            });

            // Kunjungan Lanjutan Logic
            const kunjunganLanjutanSelect = document.getElementById('kunjungan_lanjutan');
            if (kunjunganLanjutanSelect) {
                kunjunganLanjutanSelect.addEventListener('change', function() {
                    const val = this.value;
                    const detailDiv = document.getElementById('detail_kunjungan_lanjutan');
                    const formHentiDiv = document.getElementById('form_henti_layanan');
                    const permasalahanTextarea = document.getElementById('permasalahan_lanjutan');
                    const tanggalInput = document.querySelector('input[name="tanggal_kunjungan"]');
                    const alasanSelect = document.getElementById('alasan_henti_layanan');

                    if (val === 'ya') {
                        if (detailDiv) detailDiv.style.display = 'block';
                        if (formHentiDiv) formHentiDiv.style.display = 'none';
                        if (permasalahanTextarea) permasalahanTextarea.required = true;
                        if (tanggalInput) tanggalInput.required = true;
                        if (alasanSelect) {
                            alasanSelect.required = false;
                            alasanSelect.value = '';
                        }
                    } else if (val === 'tidak') {
                        if (detailDiv) detailDiv.style.display = 'none';
                        if (formHentiDiv) formHentiDiv.style.display = 'block';
                        if (permasalahanTextarea) {
                            permasalahanTextarea.required = false;
                            permasalahanTextarea.value = '';
                        }
                        if (tanggalInput) {
                            tanggalInput.required = false;
                            tanggalInput.value = '';
                        }
                        if (alasanSelect) alasanSelect.required = true;
                    } else {
                        if (detailDiv) detailDiv.style.display = 'none';
                        if (formHentiDiv) formHentiDiv.style.display = 'none';
                        if (permasalahanTextarea) {
                            permasalahanTextarea.required = false;
                            permasalahanTextarea.value = '';
                        }
                        if (tanggalInput) {
                            tanggalInput.required = false;
                            tanggalInput.value = '';
                        }
                        if (alasanSelect) {
                            alasanSelect.required = false;
                            alasanSelect.value = '';
                        }
                    }
                });
            }

            // Non Medical Issues Logic
            const nonMedicalIssuesSelect = document.getElementById('non_medical_issues_status');
            if (nonMedicalIssuesSelect) {
                nonMedicalIssuesSelect.addEventListener('change', function() {
                    const wrapper = document.getElementById('non_medical_issues_text_wrapper');
                    const textarea = document.getElementById('non_medical_issues_text');

                    if (this.value === '1') {
                        if (wrapper) wrapper.style.display = 'block';
                    } else {
                        if (wrapper) wrapper.style.display = 'none';
                        if (textarea) textarea.value = '';
                    }
                });
            }

            // Calculate total score for Skrining ADL
            const adlSelects = document.querySelectorAll(
                '#skrining-adl select[name$="_control"], #skrining-adl select[name="eating"], #skrining-adl select[name="stairs"], #skrining-adl select[name="bathing"], #skrining-adl select[name="transfer"], #skrining-adl select[name="walking"], #skrining-adl select[name="dressing"], #skrining-adl select[name="grooming"], #skrining-adl select[name="toilet_use"]'
                );
            adlSelects.forEach(select => {
                select.addEventListener('change', calculateTotalScore);
            });

            function calculateTotalScore() {
                let total = 0;
                adlSelects.forEach(select => {
                    if (select.value !== '') {
                        total += parseInt(select.value);
                    }
                });
                const totalScoreElement = document.getElementById('totalScore');
                if (totalScoreElement) {
                    totalScoreElement.textContent = total;
                }
            }

            // Initial calculation on page load
            calculateTotalScore();

            function updateAutosaveStatus(formType, status, message) {
                const statusElement = document.getElementById(`${formType}-status`);
                if (statusElement) {
                    const icon = statusElement.querySelector('i');
                    const text = statusElement.querySelector('span');

                    if (status === 'saving') {
                        icon.className = 'fas fa-spinner fa-spin text-warning me-1';
                        text.textContent = 'Menyimpan...';
                    } else if (status === 'success') {
                        icon.className = 'fas fa-check-circle text-success me-1';
                        text.textContent = message || 'Tersimpan';
                    } else if (status === 'error') {
                        icon.className = 'fas fa-exclamation-circle text-danger me-1';
                        text.textContent = message || 'Gagal menyimpan';
                    } else if (status === 'active') {
                        icon.className = 'fas fa-circle text-success me-1';
                        text.textContent = 'Auto-save aktif';
                    }
                }
            }

            function saveFormData(form) {
                console.log('saveFormData called for form:', form.id);

                const formData = new FormData(form);
                const visitingId = form.dataset.visitingId;
                let formType = form.id.replace('Form', '');

                console.log('Form data:', formData);
                console.log('Visiting ID:', visitingId);
                console.log('Form type before mapping:', formType);

                // Map form IDs to correct endpoints
                if (formType === 'ttv') {
                    formType = 'ttv';
                } else if (formType === 'health') {
                    formType = 'health-form';
                } else if (formType === 'skriningAdl') {
                    formType = 'skrining-adl-ajax';
                }

                console.log('Saving form:', formType, 'for visiting:', visitingId);
                console.log('Endpoint URL:', `/visitings/${visitingId}/${formType}`);

                // Update status to saving
                updateAutosaveStatus(formType, 'saving', 'Menyimpan...');

                // Add visual indicator that saving is in progress
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton ? submitButton.innerHTML : '';
                if (submitButton) {
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
                    submitButton.disabled = true;
                }

                console.log('Making fetch request to:', `/visitings/${visitingId}/${formType}`);

                fetch("{{ url('/visitings') }}/" + visitingId + "/" + formType, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        console.log('Response received:', response.status, response.statusText);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            updateAutosaveStatus(formType, 'success', 'Tersimpan');
                            showNotification('Data berhasil disimpan', 'success');

                            // Reset to active status after 2 seconds
                            setTimeout(() => {
                                updateAutosaveStatus(formType, 'active');
                            }, 2000);
                        } else {
                            updateAutosaveStatus(formType, 'error', 'Gagal menyimpan');
                            showNotification(data.message || 'Gagal menyimpan data', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        updateAutosaveStatus(formType, 'error', 'Terjadi kesalahan');
                        showNotification('Terjadi kesalahan: ' + error.message, 'error');
                    })
                    .finally(() => {
                        // Restore button state
                        if (submitButton) {
                            submitButton.innerHTML = originalText;
                            submitButton.disabled = false;
                        }
                    });
            }

            let lastNotification = null;
            let notificationTimeout = null;

            function showNotification(message, type) {
                // Remove existing notification
                if (lastNotification) {
                    lastNotification.remove();
                }
                if (notificationTimeout) {
                    clearTimeout(notificationTimeout);
                }

                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const notification = document.createElement('div');
                notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
                notification.style.cssText =
                    'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);';
                notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                document.body.appendChild(notification);

                lastNotification = notification;

                // Auto remove after 3 seconds
                notificationTimeout = setTimeout(() => {
                    if (notification && notification.parentNode) {
                        notification.remove();
                        lastNotification = null;
                    }
                }, 3000);
            }


            // Tab click handlers
            const tabs = ['ttv', 'health-form', 'skrining-adl'];
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    const tabName = this.id.replace('-tab', '');
                    currentTabIndex = tabs.indexOf(tabName);
                });
            });

            // Delete modal handlers
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const visitingId = button.getAttribute('data-visiting-id');
                    const pasienName = button.getAttribute('data-pasien-name');
                    
                    // Update modal content
                    document.getElementById('pasienName').textContent = pasienName;
                    
                    // Update form action
                    const deleteForm = document.getElementById('deleteForm');
                    deleteForm.action = route('visitings.destroy', visitingId);
                });
            }
        });
    </script>
@endpush
