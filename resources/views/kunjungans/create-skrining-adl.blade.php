@extends('layouts.app')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Skrining ADL</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Data Pasien</h5>
                        </div>
                        <div class="card-body">
                             <form action="{{ route('kunjungan.storeSkriningAdl', $kunjungan->id) }}" method="POST">
                              @csrf

                            <!-- Questionnaire -->
                            <div class="row mb-3">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="butuh_orang" class="form-label">Apakah Dalam Kegiatan sehari-hari Membutuhkan Orang?</label>
                                </div>
                                <div class="col-lg-8 col-md-6 col-sm-12">
                                    <select class="form-select" id="butuh_orang" name="butuh_orang" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label for="pendamping_tetap" class="form-label">Apakah memiliki pendamping tetap yang setiap saat membantu Anda untuk melakukan aktivitas sehari-hari?</label>
                                </div>
                                <div class="col-lg-8 col-md-6 col-sm-12">
                                    <select class="form-select" id="pendamping_tetap" name="pendamping_tetap" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>
                            </div>

                            <!-- ADL Assessment Form -->
                            <div class="aks" style="display: none;">
                                <div class="card-header bg-light my-3">
                                    <h5 class="mb-0">ACTIVITY OF DAILY LIVING</h5>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-8 col-md-7 col-sm-12">
                                        <input type="hidden" name="pasien_id" value="{{ $kunjungan->pasien->id }}">
                                        
                                        <!-- ADL Questions -->
                                        <div class="mb-3">
                                            <label for="bab_control" class="form-label fw-bold">Mengendalikan rangsangan BAB</label>
                                            <select class="form-select bab" id="bab_control" name="bab_control" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tidak terkendali/ tak teratur (perlu pencahar)</option>
                                                <option value="1">Kadang - kadang tak terkendali (1x/ minggu)</option>
                                                <option value="2">Terkendali teratur</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="bak_control" class="form-label fw-bold">Mengendalikan rangsangan BAK</label>
                                            <select class="form-select bab" id="bak_control" name="bak_control" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tak terkendali atau pakai kateter</option>
                                                <option value="1">Kadang-kadang tidak terkendali (1x/24 jam)</option>
                                                <option value="2">Mandiri</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="eating" class="form-label fw-bold">Makan minum</label>
                                            <select class="form-select bab" id="eating" name="eating" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tidak mampu</option>
                                                <option value="1">Perlu ditolong memotong makanan</option>
                                                <option value="2">Mandiri</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="stairs" class="form-label fw-bold">Naik turun tangga</label>
                                            <select class="form-select bab" id="stairs" name="stairs" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tidak mampu</option>
                                                <option value="1">Butuh pertolongan</option>
                                                <option value="2">Mandiri</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="bathing" class="form-label fw-bold">Mandi</label>
                                            <select class="form-select bab" id="bathing" name="bathing" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tergantung orang lain</option>
                                                <option value="1">Mandiri</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="transfer" class="form-label fw-bold">Bergerak dari kursi roda ke tempat tidur</label>
                                            <select class="form-select bab" id="transfer" name="transfer" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tidak mampu</option>
                                                <option value="1">Bantuan 2 orang</option>
                                                <option value="2">Bantuan minimal 1 orang</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="walking" class="form-label fw-bold">Berjalan di tempat rata</label>
                                            <select class="form-select bab" id="walking" name="walking" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tidak mampu</option>
                                                <option value="1">Bisa dengan kursi roda</option>
                                                <option value="2">Berjalan dengan bantuan</option>
                                                <option value="3">Mandiri</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="dressing" class="form-label fw-bold">Berpakaian</label>
                                            <select class="form-select bab" id="dressing" name="dressing" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tergantung orang lain</option>
                                                <option value="1">Sebagian dibantu</option>
                                                <option value="2">Mandiri</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="grooming" class="form-label fw-bold">Membersihkan diri</label>
                                            <select class="form-select bab" id="grooming" name="grooming" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="1">Butuh pertolongan</option>
                                                <option value="2">Mandiri</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="toilet_use" class="form-label fw-bold">Penggunaan WC</label>
                                            <select class="form-select bab" id="toilet_use" name="toilet_use" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="0">Tergantung orang lain</option>
                                                <option value="1">Sebagian dibantu</option>
                                                <option value="2">Mandiri</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Score Card -->
                                    <div class="col-lg-4 col-md-5 col-sm-12">
                                        <div class="card text-center sticky-top" style="top: 20px">
                                            <div class="card-header bg-primary text-white">
                                                <h5 class="mb-0">Total Skor</h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text display-4 fw-bold" id="total-score">0</p>
                                                <input type="hidden" name="total_score" id="total-score-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex mt-3">
                                <button type="submit" class="btn btn-primary px-4">Simpan</button>
                                <a href="{{ route('kunjungans.index') }}" class="btn btn-outline-secondary px-4">Kembali</a>
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
        document.getElementById('butuh_orang').addEventListener('change', function() {
            var aksElement = document.querySelector('.aks');
            if (this.value === '1') { // '1' for 'Ya'
                aksElement.style.display = 'block';
            } else {
                aksElement.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Ambil semua dropdown dengan class 'bab'
            const babSelectors = document.querySelectorAll('.bab');

            // Fungsi untuk menghitung skor total
            function calculateScore() {
                let totalScore = 0;

                babSelectors.forEach(select => {
                    const value = parseInt(select.value);
                    if (!isNaN(value)) {
                        totalScore += value;
                    }
                });

                document.getElementById('total-score').innerText = totalScore;
                document.getElementById('total-score-input').value = totalScore;
            }

            babSelectors.forEach(select => {
                select.addEventListener('change', calculateScore);
            });

            // Initial calculation
            calculateScore();
        });
    </script>
@endpush