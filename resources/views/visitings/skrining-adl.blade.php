@extends('layouts.app')

@section('content')
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12">
                <h3 class="mb-0">Skrining ADL - {{ $visiting->pasien->name }}</h3>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-4 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li>- {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>
                            Skrining Activities of Daily Living (ADL) - Barthel Index
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('visitings.storeSkriningAdl', $visiting->id) }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Mengendalikan rangsangan BAB</label>
                                        <select name="bab_control" class="form-select">
                                            <option value="">Pilih Skor</option>
                                            <option value="0" {{ old('bab_control', $visiting->skriningAdl->bab_control ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('bab_control', $visiting->skriningAdl->bab_control ?? '') == '1' ? 'selected' : '' }}>1 - Kadang-kadang tidak mampu</option>
                                            <option value="2" {{ old('bab_control', $visiting->skriningAdl->bab_control ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Mengendalikan rangsangan BAK</label>
                                        <select name="bak_control" class="form-select">
                                            <option value="">Pilih Skor</option>
                                            <option value="0" {{ old('bak_control', $visiting->skriningAdl->bak_control ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('bak_control', $visiting->skriningAdl->bak_control ?? '') == '1' ? 'selected' : '' }}>1 - Kadang-kadang tidak mampu</option>
                                            <option value="2" {{ old('bak_control', $visiting->skriningAdl->bak_control ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
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
                                            <option value="0" {{ old('eating', $visiting->skriningAdl->eating ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('eating', $visiting->skriningAdl->eating ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('eating', $visiting->skriningAdl->eating ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Naik turun tangga</label>
                                        <select name="stairs" class="form-select">
                                            <option value="">Pilih Skor</option>
                                            <option value="0" {{ old('stairs', $visiting->skriningAdl->stairs ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('stairs', $visiting->skriningAdl->stairs ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('stairs', $visiting->skriningAdl->stairs ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
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
                                            <option value="0" {{ old('bathing', $visiting->skriningAdl->bathing ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('bathing', $visiting->skriningAdl->bathing ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('bathing', $visiting->skriningAdl->bathing ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Bergerak dari kursi roda ke tempat tidur</label>
                                        <select name="transfer" class="form-select">
                                            <option value="">Pilih Skor</option>
                                            <option value="0" {{ old('transfer', $visiting->skriningAdl->transfer ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('transfer', $visiting->skriningAdl->transfer ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('transfer', $visiting->skriningAdl->transfer ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
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
                                            <option value="0" {{ old('walking', $visiting->skriningAdl->walking ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('walking', $visiting->skriningAdl->walking ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('walking', $visiting->skriningAdl->walking ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Berpakaian</label>
                                        <select name="dressing" class="form-select">
                                            <option value="">Pilih Skor</option>
                                            <option value="0" {{ old('dressing', $visiting->skriningAdl->dressing ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('dressing', $visiting->skriningAdl->dressing ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('dressing', $visiting->skriningAdl->dressing ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
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
                                            <option value="0" {{ old('grooming', $visiting->skriningAdl->grooming ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('grooming', $visiting->skriningAdl->grooming ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('grooming', $visiting->skriningAdl->grooming ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Penggunaan WC</label>
                                        <select name="toilet_use" class="form-select">
                                            <option value="">Pilih Skor</option>
                                            <option value="0" {{ old('toilet_use', $visiting->skriningAdl->toilet_use ?? '') == '0' ? 'selected' : '' }}>0 - Tidak mampu</option>
                                            <option value="1" {{ old('toilet_use', $visiting->skriningAdl->toilet_use ?? '') == '1' ? 'selected' : '' }}>1 - Perlu bantuan</option>
                                            <option value="2" {{ old('toilet_use', $visiting->skriningAdl->toilet_use ?? '') == '2' ? 'selected' : '' }}>2 - Mampu</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Butuh Orang</label>
                                        <input type="text" name="butuh_orang" class="form-control" 
                                               value="{{ old('butuh_orang', $visiting->skriningAdl->butuh_orang ?? '') }}" 
                                               placeholder="Keterangan">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Pendamping Tetap</label>
                                        <input type="text" name="pendamping_tetap" class="form-control" 
                                               value="{{ old('pendamping_tetap', $visiting->skriningAdl->pendamping_tetap ?? '') }}" 
                                               placeholder="Keterangan">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Sasaran Home Service</label>
                                        <input type="text" name="sasaran_home_service" class="form-control" 
                                               value="{{ old('sasaran_home_service', $visiting->skriningAdl->sasaran_home_service ?? '') }}" 
                                               placeholder="Keterangan">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="fw-bold">Total Skor: <span id="totalScore">{{ $visiting->skriningAdl->total_score ?? 0 }}</span></h6>
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

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('visitings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Skrining ADL
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('select[name^="bab_control"], select[name^="bak_control"], select[name^="eating"], select[name^="stairs"], select[name^="bathing"], select[name^="transfer"], select[name^="walking"], select[name^="dressing"], select[name^="grooming"], select[name^="toilet_use"]');
    const totalScoreElement = document.getElementById('totalScore');
    
    function calculateTotalScore() {
        let total = 0;
        selects.forEach(select => {
            if (select.value !== '') {
                total += parseInt(select.value);
            }
        });
        totalScoreElement.textContent = total;
    }
    
    selects.forEach(select => {
        select.addEventListener('change', calculateTotalScore);
    });
    
    // Calculate initial score
    calculateTotalScore();
});
</script>
@endpush
