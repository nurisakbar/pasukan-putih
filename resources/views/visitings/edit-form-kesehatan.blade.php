@extends('layouts.app')

@section('content')
<div class="container-fluid health-form-container">
    <form action="{{ route('health-form.update', $healthForm->id) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT')
        <div class="card form-card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center mt-2">
                <i class="fas fa-notes-medical me-2"></i>
                <h5 class="mb-0">Form Permasalahan Kesehatan</h5>
            </div>
            
            <div class="card-body p-4">
                <!-- Riwayat Penyakit Caregiver & perawat -->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-history me-2"></i>
                        <h5>Riwayat Penyakit</h5>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="no_disease" name="no_disease" value="1" {{ $healthForm->no_disease ? 'checked' : '' }}>
                                <label class="form-check-label" for="no_disease">Tidak Ada Riwayat Penyakit</label>
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
                                'stroke' => 'Stroke'
                            ];
                            $selectedDiseases = $healthForm->diseases ?? []; // fallback array kosong
                            $cancerType = $healthForm->cancer_type ?? '';
                            $lungDiseaseType = $healthForm->lung_disease_type ?? '';
                        @endphp

                        @foreach($diseases as $key => $label)
                            <div class="col-md-6 col-lg-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input disease-checkbox" type="checkbox"
                                        id="{{ $key }}"
                                        name="diseases[]"
                                        value="{{ $key }}"
                                        {{ in_array($key, $selectedDiseases) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach

                        <!-- Kanker -->
                        <div class="col-md-4 col-lg-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input disease-checkbox" type="checkbox"
                                    id="cancer"
                                    name="diseases[]"
                                    value="cancer"
                                    {{ in_array('cancer', $selectedDiseases) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cancer">Kanker</label>
                            </div>
                            <select class="form-select conditional-field cancer-type mt-2" name="cancer_type"
                                style="display: {{ in_array('cancer', $selectedDiseases) ? 'block' : 'none' }}">
                                <option value="">Pilih Jenis Kanker</option>
                                <option value="breast" {{ $cancerType == 'breast' ? 'selected' : '' }}>Kanker Payudara</option>
                                <option value="cervical" {{ $cancerType == 'cervical' ? 'selected' : '' }}>Kanker Leher Rahim</option>
                                <option value="lung" {{ $cancerType == 'lung' ? 'selected' : '' }}>Kanker Paru</option>
                                <option value="colorectal" {{ $cancerType == 'colorectal' ? 'selected' : '' }}>Kanker Kolorektal</option>
                                <option value="liver" {{ $cancerType == 'liver' ? 'selected' : '' }}>Kanker Hati</option>
                                <option value="nasopharyngeal" {{ $cancerType == 'nasopharyngeal' ? 'selected' : '' }}>Kanker Nasofaring</option>
                                <option value="lymphoma" {{ $cancerType == 'lymphoma' ? 'selected' : '' }}>Limfoma Non Hodgkin</option>
                                <option value="leukemia" {{ $cancerType == 'leukemia' ? 'selected' : '' }}>Leukemia</option>
                                <option value="ovarian" {{ $cancerType == 'ovarian' ? 'selected' : '' }}>Kanker Ovarium</option>
                                <option value="other" {{ $cancerType == 'other' ? 'selected' : '' }}>Kanker Lainnya</option>
                            </select>
                        </div>

                        <!-- Penyakit Paru -->
                        <div class="col-md-4 col-lg-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input disease-checkbox" type="checkbox"
                                    id="lung_disease"
                                    name="diseases[]"
                                    value="lung_disease"
                                    {{ in_array('lung_disease', $selectedDiseases) ? 'checked' : '' }}>
                                <label class="form-check-label" for="lung_disease">Penyakit Paru</label>
                            </div>
                            <select class="form-select conditional-field lung-disease-type mt-2" name="lung_disease_type"
                                style="display: {{ in_array('lung_disease', $selectedDiseases) ? 'block' : 'none' }}">
                                <option value="">Pilih Penyakit Paru</option>
                                <option value="tbc" {{ $lungDiseaseType == 'tbc' ? 'selected' : '' }}>TBC</option>
                                <option value="pneumonia" {{ $lungDiseaseType == 'pneumonia' ? 'selected' : '' }}>Pneumonia</option>
                                <option value="ppok" {{ $lungDiseaseType == 'ppok' ? 'selected' : '' }}>PPOK</option>
                            </select>
                        </div>


                @if (auth()->user()->role == 'perawat' || auth()->user()->role == 'superadmin')
                <!-- Skrining ILP  Perawat-->
                
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-chart-line me-2"></i>
                        <h5>Skrining ILP</h5>
                    </div>
                    <div class="row">
                        @foreach($screenings as $screening)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">{{ $screening["label"] }}</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="screening_{{ $screening['id'] }}" id="{{ $screening['id'] }}_yes" value="1" {{ $healthForm->{'screening_' . $screening['id']} == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $screening['id'] }}_yes">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="screening_{{ $screening['id'] }}" id="{{ $screening['id'] }}_no" value="0" {{ $healthForm->{'screening_' . $screening['id']} == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $screening['id'] }}_no">Tidak</label>
                                </div>
                            </div>
                            <select class="form-select conditional-field {{ $screening['id'] }}-status" name="{{ $screening['id'] }}_status" style="display: {{ $healthForm->{'screening_' . $screening['id']} == 1 ? 'block' : 'none' }}">
                                <option value="">Pilih Status</option>
                                <option value="penderita" {{ $healthForm->{'screening_' . $screening['id'] . '_status'} == 'penderita' ? 'selected' : '' }}>Penderita</option>
                                <option value="bukan_penderita" {{ $healthForm->{'screening_' . $screening['id'] . '_status'} == 'bukan_penderita' ? 'selected' : '' }}>Bukan Penderita</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Skor AKS Caregiver & perawat-->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-chart-bar me-2"></i>
                        <h5>Skor AKS</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-medium">Skor AKS</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="skor_aks" id="skor_aks_mandiri" value="mandiri" {{ $healthForm->skor_aks == 'mandiri' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skor_aks_mandiri">20 : Mandiri</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="skor_aks" id="skor_aks_ringan" value="ketergantungan_ringan" {{ $healthForm->skor_aks == 'ketergantungan_ringan' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skor_aks_ringan">12 - 19 : Ketergantungan ringan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="skor_aks" id="skor_aks_sedang" value="ketergantungan_sedang" {{ $healthForm->skor_aks == 'ketergantungan_sedang' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skor_aks_sedang">9 - 11 : Ketergantungan sedang</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="skor_aks" id="skor_aks_berat" value="ketergantungan_berat" {{ $healthForm->skor_aks == 'ketergantungan_berat' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skor_aks_berat">5 - 8 : Ketergantungan berat</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="skor_aks" id="skor_aks_total" value="ketergantungan_total" {{ $healthForm->skor_aks == 'ketergantungan_total' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skor_aks_total">0 - 4 : Ketergantungan total</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Jenis gangguan fungsional Caregiver & perawat-->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <h5>Jenis gangguan fungsional yang dialami</h5>
                    </div>
                    <div class="row">
                        @php
                         $gangguans = [
                             ["id" => "gangguan_komunikasi", "label" => "Gangguan komunikasi"],
                             ["id" => "kesulitan_makan", "label" => "Kesulitan makan (feeding problem)"],
                             ["id" => "gangguan_fungsi_kardiorespirasi", "label" => "Gangguan fungsi kardiorespirasi"],
                             ["id" => "gangguan_fungsi_berkemih", "label" => "Gangguan fungsi berkemih"],
                             ["id" => "gangguan_mobilisasi", "label" => "Gangguan mobilisasi"],
                             ["id" => "gangguan_partisipasi", "label" => "Gangguan aktifitas kehidupan sehari-hari/partisipasi"],
                         ];
                         @endphp
                        @foreach($gangguans as $gangguan)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">{{ ucfirst($gangguan["label"]) }}</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="{{ $gangguan['id'] }}" id="{{ $gangguan['id'] }}_yes" value="1" {{ $healthForm->{$gangguan['id']} == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $gangguan['id'] }}_yes">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="{{ $gangguan['id'] }}" id="{{ $gangguan['id'] }}_no" value="0" {{ $healthForm->{$gangguan['id']} == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $gangguan['id'] }}_no">Tidak</label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if ( auth()->user()->role == 'perawat' || auth()->user()->role == 'superadmin')
                <!-- Perawatan only perawat-->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-hand-holding-medical me-2"></i>
                        <h5>Perawatan Yang Dilakukan</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <textarea name="perawatan" class="form-control" placeholder="Masukkan perawatan yang dilakukan" rows="3">{{ $healthForm->perawatan }}</textarea>
                        </div>
                    </div>
                </div>
                @endif

                @if (auth()->user()->role == 'caregiver' || auth()->user()->role == 'superadmin')    
                {{-- perawatan umum yang di lakukan Caregiver --}}
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-chart-line me-2"></i>
                        <h5>Perawatan Umum Yang Dilakukan</h5>
                    </div>
                    <div class="row">
                        @php
                         $perawatans = [
                             ["id" => "hygiene", "label" => "Pemeliharaan kebersihan diri"],
                             ["id" => "skin_care", "label" => "Pencegahan Masalah Kesehatan Kulit"],
                             ["id" => "environment", "label" => "Pemeliharaan Kebersihan dan Keamanan Lingkungan"],
                             ["id" => "welfare", "label" => "Mempertahankan Tingkat Kemandirian warga jakarta yang membutuhkan"],
                             ["id" => "sunlight", "label" => "Tercukupinya pajanan Sinar Matahari"],
                             ["id" => "communication", "label" => "Komunikasi dengan baik"],
                             ["id" => "recreation", "label" => "Motivasi untuk melaksanakan Kegiatan Rekreasi"],
                             ["id" => "penamtauan_obat", "label" => "Pemantauan Penggunaan Obat"],
                             ["id" => "ibadah", "label" => "Motivasi untuk Pelaksanaan Ibadah"],
                         ];
                         @endphp
                        @foreach($perawatans as $perawatan)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">{{ $perawatan["label"] }}</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="perawatan_{{ $perawatan['id'] }}" id="{{ $perawatan['id'] }}_yes" value="1" {{ $healthForm->{'perawatan_' . $perawatan['id']} == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $perawatan['id'] }}_yes">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="perawatan_{{ $perawatan['id'] }}" id="{{ $perawatan['id'] }}_no" value="0" {{ $healthForm->{'perawatan_' . $perawatan['id']} == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $perawatan['id'] }}_no">Tidak</label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- perawatan khusus yang dilakukan Caregiver --}}
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-chart-line me-2"></i>
                        <h5>Perawatan Khusus Yang Dilakukan</h5>
                    </div>
                    <div class="row">
                        @php
                         $perawatans = [
                             ["id" => "membantu_warga", "label" => "Membantu warga jakarta yang membutuhkan yang Mengalami Gangguan Gerak"],
                             ["id" => "monitoring_gizi", "label" => "Monitoring dan Edukasi Pemenuhan Gizi yang baik"],
                             ["id" => "membantu_bak_bab", "label" => "Membantu Buang Air Kecil (BAK) dan Buang Air Besar (BAB)"],
                             ["id" => "menangani_gangguan", "label" => "Menangani Gangguan Perilaku dengan Pikun/Demensial"],
                             ["id" => "pengelolaan_stres", "label" => "Pengelolaan Stres"],
                         ];
                         @endphp
                        @foreach($perawatans as $perawatan)
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">{{ $perawatan["label"] }}</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="perawatan_{{ $perawatan['id'] }}" id="{{ $perawatan['id'] }}_yes" value="1" {{ $healthForm->{'perawatan_' . $perawatan['id']} == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $perawatan['id'] }}_yes">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="perawatan_{{ $perawatan['id'] }}" id="{{ $perawatan['id'] }}_no" value="0" {{ $healthForm->{'perawatan_' . $perawatan['id']} == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $perawatan['id'] }}_no">Tidak</label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Keluaran perawatan Caregiver & perawat -->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-clipboard-check me-2"></i>
                        <h5>Keluaran dari perawatan yang dilakukan</h5>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-medium">Keluaran Perawatan</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="keluaran" id="keluaran_meningkat" value="1" {{ $healthForm->keluaran == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="keluaran_meningkat">Meningkat</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="keluaran" id="keluaran_tetap" value="2" {{ $healthForm->keluaran == 2 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="keluaran_tetap">Tetap</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="keluaran" id="keluaran_menurun" value="3" {{ $healthForm->keluaran == 3 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="keluaran_menurun">Menurun</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-medium">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan hasil perawatan" value="{{ $healthForm->keterangan }}">
                        </div>
                    </div>
                </div>

                <!-- Pembinaan keluarga Caregiver & perawat -->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-users me-2"></i>
                        <h5>Dilakukan pembinaan keluarga</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Pembinaan Keluarga</label>
                            <select name="pembinaan" id="pembinaan" class="form-select">
                                <option value="ya" {{ $healthForm->pembinaan == 'ya' ? 'selected' : '' }}>Ya</option>
                                <option value="tidak" {{ $healthForm->pembinaan == 'tidak' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tingkat Kemandirian Keluarga  Caregiver & perawat-->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-hand-holding-heart me-2"></i>
                        <h5>Tingkat Kemandirian Keluarga</h5>
                    </div>
                    <div class="row kemandirian-checkboxes">
                        @php
                            $tingkat_kemandirian = [
                                'menerima_petugas' => 'Menerima petugas Perawatan Kesehatan Masyarakat',
                                'menerima_pelayanan' => 'Menerima pelayanan keperawatan yang diberikan sesuai dengan rencana keperawatan',
                                'mengenal_masalah' => 'Tahu dan dapat mengungkapkan masalah kesehatannya secara benar',
                                'manfaatkan_fasilitas' => 'Memanfaatkan fasilitas pelayanan sesuai anjuran',
                                'melakukan_perawatan' => 'Melakukan perawatan sederhana sesuai yang dianjurkan',
                                'melakukan_pencegahan' => 'Melaksanakan tindakan pencegahan secara aktif',
                                'melakukan_promotif' => 'Melaksanakan tindakan promotif secara aktif'
                            ];
                            $selectedKemandirian = $healthForm->kemandirian ?? [];
                        @endphp
                        @foreach($tingkat_kemandirian as $key => $label)
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input kemandirian-checkbox" type="checkbox"
                                    id="{{ $key }}"
                                    name="kemandirian[]"
                                    value="{{ $key }}"
                                    {{ in_array($key, $selectedKemandirian) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach

                        <div class="col-12 mt-3 p-3 bg-light rounded">
                            <strong>Tingkat Kemandirian:</strong>
                            <span id="tingkatKemandirianLabel" class="fw-bold ms-2">Belum Ditentukan</span>
                        </div>
                    </div>
                </div>


                @if (auth()->user()->role == 'caregiver' || auth()->user()->role == 'superadmin')
                <!-- Perawatan only caregiver-->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-hand-holding-medical me-2"></i>
                        <h5>Catatan Keperawatan</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <textarea name="catatan_keperawatan" class="form-control" placeholder="Masukkan Catatan Keperawatan" rows="3">{{ $healthForm->catatan_keperawatan }}</textarea>
                        </div>
                    </div>
                </div>
                @endif

                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-calendar-check me-2"></i>
                        <h5>KUNJUNGAN LANJUTAN</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Apakah akan dikunjungi kembali oleh perawat?</label>
                            <select name="kunjungan_lanjutan" id="kunjungan_lanjutan" class="form-select">
                                <option value="">Pilih...</option>
                                <option value="ya" {{ $healthForm->kunjungan_lanjutan == 'ya' ? 'selected' : '' }}>Ya</option>
                                <option value="tidak" {{ $healthForm->kunjungan_lanjutan == 'tidak' ? 'selected' : '' }}>Tidak</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="detail_kunjungan_lanjutan" style="display: {{ $healthForm->kunjungan_lanjutan == 'ya' ? 'block' : 'none' }}">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-medium">Permasalahan kesehatan yang perlu kunjungan lanjutan</label>
                                <textarea class="form-control" id="permasalahan_lanjutan" name="permasalahan_lanjutan" rows="3" placeholder="Tuliskan permasalahan kesehatan yang memerlukan kunjungan lanjutan">{{ $healthForm->permasalahan_lanjutan }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-medium">Tanggal kunjungan lanjutan</label>
                                <input type="date" class="form-control" name="tanggal_kunjungan" placeholder="Tanggal" value="{{ old('tanggal_kunjungan', isset($healthForm->tanggal_kunjungan) ? $healthForm->tanggal_kunjungan->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>
                        <span>Simpan Data</span>
                    </button>
                    <a href="{{ route('visitings.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-2"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // No Disease Checkbox
        const noDiseaseCheckbox = document.getElementById('no_disease');
        const diseaseCheckboxes = document.querySelectorAll('.disease-checkbox');

        noDiseaseCheckbox.addEventListener('change', function() {
            diseaseCheckboxes.forEach(checkbox => {
                checkbox.disabled = this.checked;
                checkbox.checked = false;
            });
        });

        // Conditional Fields
        const cancerCheckbox = document.getElementById('cancer');
        const cancerTypeSelect = document.querySelector('.cancer-type');
        const lungDiseaseCheckbox = document.getElementById('lung_disease');
        const lungDiseaseTypeSelect = document.querySelector('.lung-disease-type');

        // Toggle Cancer Type Dropdown
        cancerCheckbox.addEventListener('change', function() {
            cancerTypeSelect.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) cancerTypeSelect.value = '';
        });

        // Toggle Lung Disease Type Dropdown
        lungDiseaseCheckbox.addEventListener('change', function() {
            lungDiseaseTypeSelect.style.display = this.checked ? 'block' : 'none';
            if (!this.checked) lungDiseaseTypeSelect.value = '';
        });

        // Screening Dropdown Logic
        const screenings = <?php echo json_encode(array_column($screenings, "id")); ?>;
        
        screenings.forEach(id => {
            const radios = document.querySelectorAll(`input[name="screening_${id}"]`);
            const statusDropdown = document.querySelector(`.${id}-status`);

            radios.forEach(radio => {
                radio.addEventListener("change", function () {
                    statusDropdown.style.display = this.value === "1" ? "block" : "none";
                    if (this.value !== "1") statusDropdown.value = "";
                });
            });
        });

        // Kunjungan Lanjutan Logic
        const kunjunganLanjutanSelect = document.getElementById('kunjungan_lanjutan');
        const detailKunjunganLanjutan = document.getElementById('detail_kunjungan_lanjutan');
        const permasalahanLanjutan = document.getElementById('permasalahan_lanjutan');
        const tanggalKunjungan = document.getElementById('tanggal_kunjungan');

        kunjunganLanjutanSelect.addEventListener('change', function() {
            if (this.value === 'ya') {
                detailKunjunganLanjutan.style.display = 'block';
                permasalahanLanjutan.required = true;
                tanggalKunjungan.required = true;
            } else {
                detailKunjunganLanjutan.style.display = 'none';
                permasalahanLanjutan.required = false;
                tanggalKunjungan.required = false;
                permasalahanLanjutan.value = '';
                tanggalKunjungan.value = '';
            }
        });
    });
</script>
@endpush
