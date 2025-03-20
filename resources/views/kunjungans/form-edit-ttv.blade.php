@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-5">
        <div class="card border-0 shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Form Pemeriksaan Kesehatan</h4>
            </div>
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
                                 <input type="text" class="form-control" id="tension" placeholder="120/80" name="blood_pressure" value="{{ old('blood_pressure', $ttv->blood_pressure) }}">
                                 <span class="input-group-text">mmHg</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="pulse" class="form-label">Nadi</label>
                             <div class="input-group">
                                 <input type="number" class="form-control" id="pulse" placeholder="80" name="pulse" value="{{ old('pulse', $ttv->pulse) }}">
                                 <span class="input-group-text">bpm</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="respiration" class="form-label">Pernapasan</label>
                             <div class="input-group">
                                 <input type="number" class="form-control" id="respiration" placeholder="18" name="respiration" value="{{ old('respiration', $ttv->respiration) }}">
                                 <span class="input-group-text">x/menit</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="temperature" class="form-label">Suhu</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="temperature" placeholder="36.8" name="temperature" value="{{ old('temperature', $ttv->temperature) }}">
                                 <span class="input-group-text">°C</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="oxygen" class="form-label">Saturasi Oksigen</label>
                             <div class="input-group">
                                 <input type="number" class="form-control" id="oxygen" placeholder="98" name="oxygen_saturation" value="{{ old('oxygen_saturation', $ttv->oxygen_saturation) }}">
                                 <span class="input-group-text">%</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="fetal_heart" class="form-label">Detak Jantung Janin</label>
                             <div class="input-group">
                                 <input type="number" class="form-control" id="fetal_heart" placeholder="140" name="fetal_heart" value="{{ old('fetal_heart', $ttv->fetal_heart) }}">
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
                                 <input type="number" step="0.1" class="form-control" id="weight" placeholder="65.5" name="weight" value="{{ old('weight', $ttv->weight) }}">
                                 <span class="input-group-text">kg</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="height" class="form-label">Tinggi Badan</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="height" placeholder="170.0" name="height" value="{{ old('height', $ttv->height) }}">
                                 <span class="input-group-text">cm</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="w_waist" class="form-label">Lingkar Pinggang</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="w_waist" placeholder="80.0" name="w_waist" value="{{ old('w_waist', $ttv->w_waist) }}">
                                 <span class="input-group-text">cm</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="w_bust" class="form-label">Lingkar Dada</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="w_bust" placeholder="90.0" name="w_bust" value="{{ old('w_bust', $ttv->w_bust) }}">
                                 <span class="input-group-text">cm</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="w_hip" class="form-label">Lingkar Pinggul</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="w_hip" placeholder="95.0" name="w_hip" value="{{ old('w_hip', $ttv->w_hip) }}">
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
                                                 <input type="number" step="0.01" class="form-control" id="bmi-result"
                                                     placeholder="22.84" readonly name="bmi" value="{{ old('bmi', number_format($ttv->bmi, 2)) }}">
                                                 <span class="input-group-text">kg/m²</span>
                                             </div>
                                         </div>
                                         <div class="col-md-8">
                                             <input type="hidden" id="bmi-category-value" name="bmi_category">
                                             <div class="form-control bg-white" id="bmi-category">Status: Normal</div>
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

                     <!-- Pemeriksaan Laboratorium -->
                     <div class="row mb-4">
                         <div class="col-12">
                             <h5 class="border-bottom pb-2 text-primary">Pemeriksaan Laboratorium</h5>
                         </div>
                         
                         <!-- Pemeriksaan Darah -->
                         <div class="col-12 mb-3">
                             <h6 class="text-secondary">Pemeriksaan Darah</h6>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="blood_sugar" class="form-label">Gula Darah</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="blood_sugar" placeholder="100" name="blood_sugar" value="{{ old('blood_sugar', $ttv->blood_sugar) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="uric_acid" class="form-label">Asam Urat</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="uric_acid" placeholder="5.0" name="uric_acid" value="{{ old('uric_acid', $ttv->uric_acid) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="tcho" class="form-label">Kolesterol Total</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="tcho" placeholder="180" name="tcho" value="{{ old('tcho', $ttv->tcho) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="triglyceride" class="form-label">Trigliserida</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="triglyceride" placeholder="150" name="triglyceride" value="{{ old('triglyceride', $ttv->triglyceride) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="high_density_protein" class="form-label">HDL</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="high_density_protein" placeholder="50" name="high_density_protein" value="{{ old('high_density_protein', $ttv->high_density_protein) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="low_density_protein" class="form-label">LDL</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="low_density_protein" placeholder="100" name="low_density_protein" value="{{ old('low_density_protein', $ttv->low_density_protein) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="hemoglobin" class="form-label">Hemoglobin</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="hemoglobin" placeholder="14.0" name="hemoglobin" value="{{ old('hemoglobin', $ttv->hemoglobin) }}">
                                 <span class="input-group-text">g/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="white_corpuscle" class="form-label">Sel Darah Putih</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="white_corpuscle" placeholder="7.5" name="white_corpuscle" value="{{ old('white_corpuscle', $ttv->white_corpuscle) }}">
                                 <span class="input-group-text">10³/μL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="red_corpuscle" class="form-label">Sel Darah Merah</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="red_corpuscle" placeholder="5.0" name="red_corpuscle" value="{{ old('red_corpuscle', $ttv->red_corpuscle) }}">
                                 <span class="input-group-text">10⁶/μL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="creatinine" class="form-label">Kreatinin</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="creatinine" placeholder="0.9" name="creatinine" value="{{ old('creatinine', $ttv->creatinine) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="proportion" class="form-label">Proporsi</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="proportion" placeholder="1.0" name="proportion" value="{{ old('proportion', $ttv->proportion) }}">
                                 <span class="input-group-text">-</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="albumin" class="form-label">Albumin</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="albumin" placeholder="4.0" name="albumin" value="{{ old('albumin', $ttv->albumin) }}">
                                 <span class="input-group-text">g/dL</span>
                             </div>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="calcium" class="form-label">Kalsium</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="calcium" placeholder="9.5" name="calcium" value="{{ old('calcium', $ttv->calcium) }}">
                                 <span class="input-group-text">mg/dL</span>
                             </div>
                         </div>

                         <!-- Pemeriksaan Urin -->
                         <div class="col-12 mb-3 mt-3">
                             <h6 class="text-secondary">Pemeriksaan Urin</h6>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="nitrous_acid" class="form-label">Asam Nitrat</label>
                             <select class="form-select" id="nitrous_acid" name="nitrous_acid">
                                <option value="" {{ !isset($ttv->nitrous_acid) ? 'selected' : '' }}>Pilih hasil</option>
                                <option value="Positif" {{ isset($ttv) && $ttv->nitrous_acid === 'Positif' ? 'selected' : '' }}>Positif</option>
                                <option value="Negative" {{ isset($ttv) && $ttv->nitrous_acid === 'Negative' ? 'selected' : '' }}>Negative</option>
                             </select>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="ketone_body" class="form-label">Badan Keton</label>
                             <select class="form-select" id="ketone_body" name="ketone_body">
                                 <option value="{{ !isset($ttv->ketone_body) ? 'selected' : '' }}" selected>Pilih hasil</option>
                                 <option value="Positif" {{ isset($ttv) && $ttv->ketone_body === 'Positif' ? 'selected' : '' }}>Positif</option>
                                 <option value="Negative" {{ isset($ttv) && $ttv->ketone_body === 'Negative' ? 'selected' : '' }}>Negative</option>
                             </select>
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                            <label for="urobilinogen" class="form-label">Urobilinogen</label>
                            <select class="form-select" id="urobilinogen" name="urobilinogen">
                                <option value="" {{ !isset($ttv->urobilinogen) ? 'selected' : '' }}>Pilih hasil</option>
                                <option value="Normal" {{ isset($ttv) && $ttv->urobilinogen === 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Meningkat" {{ isset($ttv) && $ttv->urobilinogen === 'Meningkat' ? 'selected' : '' }}>Meningkat</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="bilirubin" class="form-label">Bilirubin</label>
                            <select class="form-select" id="bilirubin" name="bilirubin">
                                <option value="" {{ !isset($ttv->bilirubin) ? 'selected' : '' }}>Pilih hasil</option>
                                <option value="Positif" {{ isset($ttv) && $ttv->bilirubin === 'Positif' ? 'selected' : '' }}>Positif</option>
                                <option value="Negative" {{ isset($ttv) && $ttv->bilirubin === 'Negative' ? 'selected' : '' }}>Negative</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="protein" class="form-label">Protein</label>
                            <select class="form-select" id="protein" name="protein">
                                <option value="" {{ !isset($ttv->protein) ? 'selected' : '' }}>Pilih hasil</option>
                                <option value="Positif" {{ isset($ttv) && $ttv->protein === 'Positif' ? 'selected' : '' }}>Positif</option>
                                <option value="Negative" {{ isset($ttv) && $ttv->protein === 'Negative' ? 'selected' : '' }}>Negative</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-3">
                            <label for="glucose" class="form-label">Glukosa</label>
                            <select class="form-select" id="glucose" name="glucose">
                                <option value="" {{ !isset($ttv->glucose) ? 'selected' : '' }}>Pilih hasil</option>
                                <option value="Normal" {{ isset($ttv) && $ttv->glucose === 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Meningkat" {{ isset($ttv) && $ttv->glucose === 'Meningkat' ? 'selected' : '' }}>Meningkat</option>
                                <option value="Menurun" {{ isset($ttv) && $ttv->glucose === 'Menurun' ? 'selected' : '' }}>Menurun</option>
                            </select>
                        </div>
                        
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="ph" class="form-label">pH</label>
                             <input type="number" step="0.1" class="form-control" id="ph" placeholder="7.0" name="ph" value="{{ old('ph', $ttv->ph) }}">
                         </div>
                         <div class="col-md-6 col-lg-3 mb-3">
                             <label for="vitamin_c" class="form-label">Vitamin C</label>
                             <div class="input-group">
                                 <input type="number" step="0.1" class="form-control" id="vitamin_c" placeholder="80" name="vitamin_c" value="{{ old('vitamin_c', $ttv->vitamin_c) }}">
                                 <span class="input-group-text">mg</span>
                             </div>
                         </div>
                     </div>

                     <!-- Pemeriksaan Lainnya -->
                     <div class="row mb-4">
                         <div class="col-12">
                             <h5 class="border-bottom pb-2 text-primary">Pemeriksaan Lainnya</h5>
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                            <label for="jaundice" class="form-label">Jaundice</label>
                            <div class="input-group">
                                <input type="number" step="0.1" class="form-control" id="jaundice" placeholder="80" name="jaundice" value="{{ old('jaundice', $ttv->jaundice) }}">
                                <span class="input-group-text">mg</span>
                            </div>
                        </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="ecg" class="form-label">EKG</label>
                             <input type="text" class="form-control" id="ecg" placeholder="Hasil EKG" name="ecg" value="{{ old('ecg', $ttv->ecg) }}">
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="ultrasound" class="form-label">USG</label>
                             <input type="text" class="form-control" id="ultrasound" placeholder="Hasil USG" name="ultrasound" value="{{ old('ultrasound', $ttv->ultrasound) }}">
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="ultrasound" class="form-label">USG</label>
                             <input type="text" class="form-control" id="ultrasound" placeholder="Hasil USG" name="ultrasound" value="{{ old('ultrasound', $ttv->ultrasound) }}">
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="white_corpuscle" class="form-label">White Corpuscle</label>
                             <input type="text" class="form-control" id="white_corpuscle" placeholder="Hasil USG" name="white_corpuscle" value="{{ old('white_corpuscle', $ttv->white_corpuscle) }}">
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                             <label for="red_corpuscle" class="form-label">Red Corpuscle</label>
                             <input type="text" class="form-control" id="red_corpuscle" placeholder="Hasil USG" name="red_corpuscle" value="{{ old('red_corpuscle', $ttv->red_corpuscle) }}">
                         </div>
                         <div class="col-md-6 col-lg-4 mb-3">
                            <label for="nitrous_acid" class="form-label">Nitrous Acid</label>
                            <select class="form-select" id="nitrous_acid" name="nitrous_acid">
                                <option value="" @selected(!isset($ttv->nitrous_acid))>Pilih hasil</option>
                                <option value="Positif" @selected(isset($ttv) && $ttv->nitrous_acid === 'Positif')>Positif</option>
                                <option value="Negative" @selected(isset($ttv) && $ttv->nitrous_acid === 'Negative')>Negative</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label for="ketone_body" class="form-label">Ketone Body</label>
                            <select class="form-select" id="ketone_body" name="ketone_body">
                                <option value="" @selected(!isset($ttv->ketone_body))>Pilih hasil</option>
                                <option value="Positif" @selected(isset($ttv) && $ttv->ketone_body === 'Positif')>Positif</option>
                                <option value="Negative" @selected(isset($ttv) && $ttv->ketone_body === 'Negative')>Negative</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label for="ph" class="form-label">PH</label>
                            <input type="number" class="form-control" id="ph" placeholder="PH" name="ph" value="{{ old('ph', $ttv->ph ?? '') }}">
                        </div>
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label for="creatinine" class="form-label">Creatinine</label>
                            <input type="number" class="form-control" id="creatinine" placeholder="Creatinine" name="creatinine" value="{{ old('creatinine', $ttv->creatinine ?? '') }}">
                        </div>
                        
                        @php
                        $fields = [
                            'urobilinogen' => ['Normal', 'Meningkat', 'Menurun'],
                            'bilirubin' => ['Positif', 'Negative'],
                            'protein' => ['Positif', 'Negative'],
                            'glucose' => ['Normal', 'Meningkat', 'Menurun'],
                            'vitamin_c' => ['Normal', 'Meningkat', 'Menurun'],
                            'proportion' => ['Normal', 'Meningkat', 'Menurun'],
                            'albumin' => ['Normal', 'Meningkat', 'Menurun']
                        ];
                        @endphp
                        
                        @foreach($fields as $field => $options)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label for="{{ $field }}" class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                            <select class="form-select" id="{{ $field }}" name="{{ $field }}">
                                <option value="" @selected(!isset($ttv->$field))>Pilih hasil</option>
                                @foreach($options as $option)
                                    <option value="{{ $option }}" @selected(isset($ttv) && $ttv->$field === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                        
                        <div class="col-md-6 col-lg-4 mb-3">
                            <label for="calcium" class="form-label">Calcium</label>
                            <input type="number" class="form-control" id="calcium" placeholder="calcium" name="calcium" value="{{ old('calcium', $ttv->calcium) }}">
                        </div>
                     </div>
     
                     <!-- Buttons -->
                     <div class="row mt-4">
                         <div class="col-12 d-flex justify-content-end">
                             <a href="{{ route('kunjungans.index') }}" class="btn btn-secondary me-2">Kembali</a>
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
    </script>
@endpush