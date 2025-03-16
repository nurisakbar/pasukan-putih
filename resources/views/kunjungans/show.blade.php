@extends('layouts.app')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Detail Data Pasien</h3>
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
                            <!-- Patient Information Display -->
                            <div class="patient-info mb-4">
                                <div class="row mb-2">
                                    <div class="col-md-3 col-sm-4 fw-bold">Nama:</div>
                                    <div class="col-md-9 col-sm-8">{{ $kunjungan->pasien->name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 col-sm-4 fw-bold">NIK:</div>
                                    <div class="col-md-9 col-sm-8">{{ $kunjungan->pasien->nik }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 col-sm-4 fw-bold">Jenis KTP:</div>
                                    <div class="col-md-9 col-sm-8">
                                        <!-- You can replace this with actual data field -->
                                        DKI
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 col-sm-4 fw-bold">Jenis Kelamin:</div>
                                    <div class="col-md-9 col-sm-8">{{ $kunjungan->pasien->jenis_kelamin }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 col-sm-4 fw-bold">Alamat:</div>
                                    <div class="col-md-9 col-sm-8">{{ $kunjungan->pasien->alamat }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 col-sm-4 fw-bold">Detail Alamat:</div>
                                    <div class="col-md-9 col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-6 col-md-4 mb-2">
                                                <div class="d-flex">
                                                    <span class="me-2">RT:</span>
                                                    <span>00</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 mb-2">
                                                <div class="d-flex">
                                                    <span class="me-2">RW:</span>
                                                    <span>00</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 col-md-4 mb-2">
                                                <div class="d-flex">
                                                    <span class="me-2">Kelurahan:</span>
                                                    <span>{{ $kunjungan->pasien->village_id }}</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 mb-2">
                                                <div class="d-flex">
                                                    <span class="me-2">Kecamatan:</span>
                                                    <span>{{ $kunjungan->pasien->district_id }}</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-4 mb-2">
                                                <div class="d-flex">
                                                    <span class="me-2">Kabupaten/Kota:</span>
                                                    <span>{{ $kunjungan->pasien->regency_id }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2 d-md-flex mt-3">
                                    <a href="{{ route('skriningAdl.create', $kunjungan->id) }}"
                                        class="btn btn-primary px-4">Tambah Skrining ADL</a>
                                    <a href="{{ route('kunjungans.index') }}" class="btn btn-outline-secondary px-4">Kembali</a>
                                </div>
                            </div>
                            
                            <hr class="my-4">

                            {{-- Table History --}}

                            <p class="fw-bold mb-2">Riwayat Kunjungan</p>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" width="180">Aksi</th>
                                            <th>Tanggal Kunjungan</th>
                                            <th>Diperiksa Oleh</th>
                                            <th>Total Score</th>
                                            <th>Sasaran Home Service</th>
                                            {{-- <th>Nomor Hp</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($skriningAdl as $data)
                                            <tr>
                                                <td class="text-center">
                                                    <a href="{{ route('skriningAdl.edit', $data->id) }}"
                                                        class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d M Y') }}</td>
                                                <td>{{ $data->pemeriksa->name ?? '-' }}</td>
                                                {{-- format tanggal nya menjadi tanggal indonesia --}}
                                                <td>{{ $data->total_score ?? '-' }}</td>
                                                <td>
                                                    @if($data->sasaran_home_service == 1)
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Data Kosong</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Questionnaire -->
                           
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
        document.getElementById('quis').addEventListener('change', function() {
            var aksElement = document.querySelector('.aks');
            if (this.value === 'Ya') {
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
@endpush()