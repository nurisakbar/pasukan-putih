@extends('layouts.app')

@push('style')
    <style>
        .custom-placeholder::placeholder {
            color: #b8b8b8; /* warna abu-abu seperti Bootstrap .text-muted */
            opacity: 1;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid mt-5">
        <div class="card border-0 shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Form Pemeriksaan Kesehatan</h4>
            </div>
            @if ($errors->any())
                <div class="bg-red-500 text-danger p-3">
                    <strong>Validasi gagal! Periksa kembali input Anda:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('ttv.update', $ttv->id) }}" method="post">
               @csrf
               @method('PUT')
               <input type="hidden" name="kunjungan_id" value="{{ $ttv->kunjungan_id  }}">
                 <div class="card-body">
                     <!-- Tanda-Tanda Vital -->
                     <div class="row mb-4">
                         <div class="col-12">
                             <h5 class="border-bottom pb-2 text-primary">Tanda-Tanda Vital</h5>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="tension" class="form-label">Tekanan Darah</label>
                             <div class="input-group">
                                 <input type="text" class="form-control custom-placeholder" id="tension" placeholder="120/80" name="blood_pressure" value="{{ old('blood_pressure', $ttv->blood_pressure) }}">
                                 <span class="input-group-text">mmHg</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="pulse" class="form-label">Nadi</label>
                             <div class="input-group">
                                 <input type="number" class="form-control custom-placeholder" id="pulse" placeholder="80" name="pulse" value="{{ old('pulse', $ttv->pulse) }}">
                                 <span class="input-group-text">bpm</span>
                             </div>
                         </div>
                         {{-- <div class="col-md-6 col-lg-3 mb-3">
                             <label for="respiration" class="form-label">Pernapasan</label>
                             <div class="input-group">
                                 <input type="number" class="form-control custom-placeholder" id="respiration" placeholder="18" name="respiration" value="{{ old('respiration', $ttv->respiration) }}">
                                 <span class="input-group-text">x/menit</span>
                             </div>
                         </div> --}}
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="temperature" class="form-label">Suhu</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control custom-placeholder" id="temperature" placeholder="36.8" name="temperature" value="{{ old('temperature', $ttv->temperature) }}">
                                 <span class="input-group-text">°C</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="oxygen" class="form-label">Saturasi Oksigen</label>
                             <div class="input-group">
                                 <input type="number" class="form-control custom-placeholder" id="oxygen" placeholder="98" name="oxygen_saturation" value="{{ old('oxygen_saturation', $ttv->oxygen_saturation) }}">
                                 <span class="input-group-text">%</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="fetal_heart" class="form-label">Detak Jantung Janin</label>
                             <div class="input-group">
                                 <input type="number" class="form-control custom-placeholder" id="fetal_heart" placeholder="140" name="fetal_heart" value="{{ old('fetal_heart', $ttv->fetal_heart) }}">
                                 <span class="input-group-text">bpm</span>
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
                                 <input type="number" step="0.1" class="form-control custom-placeholder" id="weight" placeholder="65.5" name="weight" value="{{ old('weight', $ttv->weight) }}">
                                 <span class="input-group-text">kg</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="height" class="form-label">Tinggi Badan</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control custom-placeholder" id="height" placeholder="170.0" name="height" value="{{ old('height', $ttv->height) }}">
                                 <span class="input-group-text">cm</span>
                             </div>
                         </div>
                     </div>
     
                     <!-- Status Gizi / BMI Calculator -->
                     <div class="row mb-4">
                         <div class="col-12">
                             <h5 class="border-bottom pb-2 text-primary">Status Gizi</h5>
                         </div>
                         <div class="col-md-8">
                             <div class="card bg-light">
                                 <div class="card-body">
                                     <p class="mb-2">Indeks Massa Tubuh (IMT) = Berat badan (kg) / [Tinggi badan (m)]²</p>
                                     <div class="row g-2">
                                         <div class="col-md-4">
                                             <div class="input-group">
                                                 <input type="number" step="0.01" class="form-control bg-body-secondary" id="bmi-result"
                                                     placeholder="22.84" readonly name="bmi" value="{{ old('bmi', number_format($ttv->bmi, 2)) }}">
                                                 <span class="input-group-text">kg/m²</span>
                                             </div>
                                         </div>
                                         <div class="col-md-8">
                                             <input type="hidden" id="bmi-category-value" name="bmi_category">
                                             <div class="form-control bg-body-secondary" id="bmi-category">Status: Normal</div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-4">
                             <div class="card mt-3 mt-md-0">
                                 <div class="card-body p-3">
                                     <p class="mb-1"><small>Kategori IMT:</small></p>
                                     <ul class="list-unstyled mb-0 small">
                                         <li>• &lt; 17 - 18,4 (Kurus)</li>
                                         <li>• 18,5 - 25 (Normal)</li>
                                         <li>• 25,1 - &gt; 27 (Gemuk)</li>
                                     </ul>
                                 </div>
                             </div>
                         </div>
                     </div>

                     <!-- Buttons -->
                     <div class="row mt-4">
                         <div class="col-12 d-flex justify-content-end">
                             <a href="{{ route('visitings.index') }}" class="btn btn-secondary me-2">Kembali</a>
                             <button type="submit" class="btn btn-primary">Simpan Data</button>
                         </div>
                     </div>
                 </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weightInput = document.getElementById('weight');
            const heightInput = document.getElementById('height');
            const bmiResult = document.getElementById('bmi-result');
            const bmiCategory = document.getElementById('bmi-category');
            const bmiCategoryValue = document.getElementById('bmi-category-value');

            function calculateBMI() {
                if (weightInput.value && heightInput.value) {
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

            weightInput.addEventListener('input', calculateBMI);
            heightInput.addEventListener('input', calculateBMI);
        });

        function toggleFields() {
            let status = document.getElementById("lanjut_kunjungan").value;
            document.getElementById("rencana_kunjungan").style.display = (status === "lanjut") ? "block" : "none";
            document.getElementById("henti_layanan").style.display = (status === "henti") ? "block" : "none";
            document.getElementById("alasan_rujukan").style.display = (status === "rujukan") ? "block" : "none";
        }

        document.addEventListener("DOMContentLoaded", function() {
            toggleFields();
        });
    </script>
@endpush