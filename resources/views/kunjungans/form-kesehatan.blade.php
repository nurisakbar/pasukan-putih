@extends('layouts.app')

@section('content')
<div class="container-fluid health-form-container">
    <form action="{{ route('ttv.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        <div class="card form-card border-0 shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="fas fa-notes-medical me-2"></i>
                <h5 class="mb-0">Form Pemeriksaan Kesehatan</h5>
            </div>
            
            <div class="card-body p-4">

                <!-- Riwayat Penyakit -->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-history me-2"></i>
                        <h4>Riwayat Penyakit</h4>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="no-disease" name="no_disease">
                                <label class="form-check-label" for="no-disease">Tidak Ada Riwayat Penyakit</label>
                            </div>
                        </div>
                    </div>
                    <div class="row disease-checkboxes">
                         @php
                        $diseases = [
                            'diabetes' => 'Diabetes Melitus',
                            'kidney-failure' => 'Gagal Ginjal',
                            'heart-failure' => 'Gagal Jantung',
                            'hiv-aids' => 'HIV/AIDS',
                            'leprosy' => 'Kusta',
                            'stroke' => 'Stroke'
                        ];
                        @endphp

                        @foreach($diseases as $key => $label)
                        <div class="col-md-4 col-lg-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input disease-checkbox" type="checkbox" id="{{ $key }}" name="diseases[]" value="{{ $key }}">
                                <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach

                        <!-- Kanker -->
                        <div class="col-md-4 col-lg-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="cancer" name="cancer">
                                <label class="form-check-label" for="cancer">Kanker</label>
                            </div>
                            <select class="form-select conditional-field cancer-type" name="cancer_type" style="display: none">
                                <option value="">Pilih Jenis Kanker</option>
                                <option value="breast">Kanker Payudara</option>
                                <option value="cervical">Kanker Leher Rahim</option>
                                <option value="lung">Kanker Paru</option>
                                <option value="colorectal">Kanker Kolorektal</option>
                                <option value="liver">Kanker Hati</option>
                                <option value="nasopharyngeal">Kanker Nasofaring</option>
                                <option value="lymphoma">Limfoma Non Hodgkin</option>
                                <option value="leukemia">Leukemia</option>
                                <option value="ovarian">Kanker Ovarium</option>
                                <option value="other">Kanker Lainnya</option>
                            </select>
                        </div>

                        <!-- Penyakit Paru -->
                        <div class="col-md-4 col-lg-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="lung-disease" name="lung_disease">
                                <label class="form-check-label" for="lung-disease">Penyakit Paru</label>
                            </div>
                            <select class="form-select conditional-field lung-disease-type" name="lung_disease_type" style="display: none">
                                <option value="">Pilih Penyakit Paru</option>
                                <option value="tbc">TBC</option>
                                <option value="pneumonia">Pneumonia</option>
                                <option value="ppok">PPOK</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Skrining ILP -->
                <div class="form-section mb-4">
                    <div class="section-header mb-3 d-flex">
                        <i class="fas fa-chart-line me-2"></i>
                        <h4>Skrining ILP</h4>
                    </div>
                    <div class="row">
                         @php
                         $screenings = [
                             ["id" => "obesity", "label" => "Skrining Obesitas"],
                             ["id" => "hypertension", "label" => "Skrining Hipertensi"],
                             ["id" => "diabetes", "label" => "Skrining Diabetes Melitus"],
                             ["id" => "stroke", "label" => "Skrining Faktor Risiko Stroke"],
                             ["id" => "heart-disease", "label" => "Skrining Faktor Risiko Penyakit Jantung"],
                             ["id" => "breast-cancer", "label" => "Skrining Kanker Payudara"],
                             ["id" => "cervical-cancer", "label" => "Skrining Kanker Leher Rahim"],
                             ["id" => "lung-cancer", "label" => "Skrining Kanker Paru"],
                             ["id" => "colorectal-cancer", "label" => "Skrining Kanker Kolorektal"],
                             ["id" => "mental-health", "label" => "Skrining Kesehatan Jiwa"],
                             ["id" => "ppo", "label" => "Skrining Penyakit Paru Obstruktif Kronis (PPOK)"],
                             ["id" => "tbc", "label" => "Skrining TBC"],
                             ["id" => "vision", "label" => "Skrining Indera Penglihatan/Mata"],
                             ["id" => "hearing", "label" => "Skrining Indera Pendengaran"],
                             ["id" => "fitness", "label" => "Skrining Kebugaran"],
                             ["id" => "dental", "label" => "Skrining Kesehatan Gigi dan Mulut"],
                             ["id" => "elderly", "label" => "Skrining Lansia Sederhana (SKILAS)"]
                         ];
                         @endphp
 
                        @foreach($screenings as $screening)
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ $screening["label"] }}</label>
                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="{{ $screening['id'] }}" id="{{ $screening['id'] }}-yes" value="1">
                                    <label class="form-check-label" for="{{ $screening['id'] }}-yes">Ya</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="{{ $screening['id'] }}" id="{{ $screening['id'] }}-no" value="0">
                                    <label class="form-check-label" for="{{ $screening['id'] }}-no">Tidak</label>
                                </div>
                            </div>
                            <select class="form-select conditional-field {{ $screening['id'] }}-status" name="{{ $screening['id'] }}_status" style="display: none">
                                <option value="">Pilih Status</option>
                                <option value="penderita">Penderita</option>
                                <option value="bukan_penderita">Bukan Penderita</option>
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i>
                        <span>Simpan Data</span>
                    </button>
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
        const noDiseaseCheckbox = document.getElementById('no-disease');
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
        const lungDiseaseCheckbox = document.getElementById('lung-disease');
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
            const radios = document.querySelectorAll(`input[name="${id}"]`);
            const statusDropdown = document.querySelector(`.${id}-status`);

            radios.forEach(radio => {
                radio.addEventListener("change", function () {
                    statusDropdown.style.display = this.value === "1" ? "block" : "none";
                    if (this.value !== "1") statusDropdown.value = "";
                });
            });
        });

        // Enhanced Form Validation
        const form = document.querySelector('form.needs-validation');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Mohon lengkapi semua field yang diperlukan.'
                });
            }
            form.classList.add('was-validated');
        });

        // Smooth Conditional Field Toggle
        const conditionalFields = document.querySelectorAll('.conditional-field');
        conditionalFields.forEach(field => {
            field.addEventListener('change', function() {
                this.style.display = this.value ? 'block' : 'none';
            });
        });
    });
</script>
@endpush