@extends('layouts.app')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <!-- Filter Form -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Filter Data</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                   class="form-control filter-input">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                   class="form-control filter-input">
                        </div>
                        @php
                            $checkuser = auth()->user()->pustu()->first();
                        @endphp
                        @if (!$checkuser)
                            <div class="col-md-4">
                                <label for="district_id" class="form-label fw-semibold">Kecamatan</label>
                                <select id="district_id" name="district_id" class="form-select filter-input">
                                    <option value="">Semua Kecamatan</option>
                                    @foreach($districts as $id => $name)
                                        <option value="{{ $id }}" {{ request('district_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-2">
                            <label for="data_source" class="form-label fw-semibold">Sumber Data</label>
                            <select id="data_source" name="data_source" class="form-select filter-input">
                                <option value="">Semua Sumber</option>
                                <option value="carik" {{ request('data_source') == 'carik' ? 'selected' : '' }}>Si Carik</option>
                                <option value="manual" {{ request('data_source') == 'manual' ? 'selected' : '' }}>Input Manual</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <div class="d-flex gap-2">
                                <button type="button" id="resetFilter" class="btn btn-outline-secondary flex-fill">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center mb-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data...</p>
            </div>

            <!-- Data Sasaran Overview -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="text-muted mb-3">
                        <i class="bi bi-people-fill me-2"></i>Data Sasaran Overview
                    </h4>
                </div>
            </div>

            <div id="dashboardContent">
                <div class="row g-4">
                    @php
                        $sasaran_data = [
                            [
                                'title' => 'Total Semua Data Sasaran',
                                'value' => $carik_data['total_pasien'] + $manual_data['total_pasien'],
                                'icon' => 'bi-people',
                                'bg' => 'bg-info',
                                'text' => 'text-white'
                            ],
                            ['title' => 'Sasaran dari Si Carik', 'value' => $carik_data['total_pasien'], 'icon' => 'bi-cloud-download', 'bg' => 'bg-primary', 'text' => 'text-white'],
                            ['title' => 'Sasaran Input Manual', 'value' => $manual_data['total_pasien'], 'icon' => 'bi-pencil-square', 'bg' => 'bg-secondary', 'text' => 'text-white'],
                            ['title' => 'Sasaran Sudah Memiliki Jadwal Kunjungan', 'value' => $data_sasaran_sudah_dijadwalkan, 'icon' => 'bi-calendar-check', 'bg' => 'bg-success', 'text' => 'text-white'],
                            ['title' => 'Sasaran Belum Memiliki Jadwal Kunjungan', 'value' => $data_sasaran_belum_dijadwalkan, 'icon' => 'bi-calendar-x', 'bg' => 'bg-warning', 'text' => 'text-dark'],
                            ['title' => 'Sasaran Sudah Dikunjungi', 'value' => $data_sasaran_sudah_dikunjungi, 'icon' => 'bi-house-check', 'bg' => 'bg-success', 'text' => 'text-white'],
                            ['title' => 'Sasaran Belum Dikunjungi', 'value' => $data_sasaran_belum_dikunjungi, 'icon' => 'bi-house-x', 'bg' => 'bg-danger', 'text' => 'text-white'],
                            ['title' => 'Sasaran Henti Layanan', 'value' => $data_sasaran_henti_layanan, 'icon' => 'bi-person-x', 'bg' => 'bg-danger', 'text' => 'text-white'],
                        ];
                    @endphp
                    @foreach($sasaran_data as $data)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body {{ $data['bg'] }} {{ $data['text'] }}">
                                    <div class="d-flex align-items-center">
                                        <i class="bi {{ $data['icon'] }} fs-3 me-3"></i>
                                        <div>
                                            <h3 class="mb-1">{{ $data['value'] }}</h3>
                                            <p class="mb-0">{{ $data['title'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="card-footer bg-light">
                                    @if($data['title'] == 'Pasien dari Si Carik')
                                        <a href="{{ route('pasiens.index', ['flag_sicarik' => 1]) }}"
                                           class="text-primary text-decoration-none">
                                            More info <i class="bi bi-arrow-right-circle"></i>
                                        </a>
                                    @elseif($data['title'] == 'Pasien Input Manual')
                                        <a href="{{ route('pasiens.index', ['flag_sicarik' => 0]) }}"
                                           class="text-primary text-decoration-none">
                                            More info <i class="bi bi-arrow-right-circle"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('pasiens.index') }}"
                                           class="text-primary text-decoration-none">
                                            More info <i class="bi bi-arrow-right-circle"></i>
                                        </a>
                                    @endif
                                </div> --}}
                            </div>
                        </div>
                    @endforeach
                </div>

            <!-- Statistik Kunjungan -->
            <div class="row mt-5 mb-4">
                <div class="col-12">
                    <h4 class="text-muted mb-3">
                        <i class="bi bi-house-door-fill me-2"></i>Statistik Kunjungan
                    </h4>
                </div>
            </div>

            <div class="row g-4">
                @php
                    $kunjungan_data = [
                        ['title' => 'Total Kunjungan', 'value' => $jumlah_kunjungan, 'icon' => 'bi-bar-chart', 'bg' => 'bg-primary', 'text' => 'text-white'],
                        ['title' => 'Kunjungan Berkelanjutan', 'value' => $jumlah_kunjungan_belum_selesai, 'icon' => 'bi-hourglass-split', 'bg' => 'bg-success', 'text' => 'text-white'],
                        ['title' => 'Kunjungan Pertama', 'value' => $jumlah_kunjungan_awal, 'icon' => 'bi-play-circle', 'bg' => 'bg-secondary', 'text' => 'text-white'],
                        ['title' => 'Yang Mendapatkan Kunjungan Lanjutan', 'value' => $jumlah_kunjungan_lanjutan, 'icon' => 'bi-arrow-repeat', 'bg' => 'bg-success', 'text' => 'text-white'],
                        ['title' => 'Yang Berhenti Layanan', 'value' => $jumlah_kunjungan_selesai, 'icon' => 'bi-check-circle', 'bg' => 'bg-danger', 'text' => 'text-white'],
                    ];
                @endphp
                @foreach($kunjungan_data as $data)
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body {{ $data['bg'] }} {{ $data['text'] }}">
                                <div class="d-flex align-items-center">
                                    <i class="bi {{ $data['icon'] }} fs-3 me-3"></i>
                                    <div>
                                        <h3 class="mb-1">{{ $data['value'] }}</h3>
                                        <p class="mb-0">{{ $data['title'] }}</p>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="card-footer bg-light">
                                <a href="{{ route('visitings.index') }}"
                                   class="text-primary text-decoration-none">
                                    More info <i class="bi bi-arrow-right-circle"></i>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let filterTimeout;
            
            // Add event listeners to all filter inputs
            document.querySelectorAll('.filter-input').forEach(function(input) {
                input.addEventListener('change', function() {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(function() {
                        applyFilters();
                    }, 500); // Debounce 500ms
                });
            });
            
            // Reset button
            document.getElementById('resetFilter').addEventListener('click', function() {
                document.getElementById('filterForm').reset();
                applyFilters();
            });
            
            function applyFilters() {
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams();
                
                // Add all form data to params
                for (let [key, value] of formData.entries()) {
                    if (value) {
                        params.append(key, value);
                    }
                }
                
                // Show loading indicator
                document.getElementById('loadingIndicator').style.display = 'block';
                document.getElementById('dashboardContent').style.opacity = '0.5';
                
                // Make AJAX request
                fetch('{{ route("home") }}?' + params.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Parse the response and extract dashboard content
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('dashboardContent');
                    
                    if (newContent) {
                        document.getElementById('dashboardContent').innerHTML = newContent.innerHTML;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memuat data. Silakan refresh halaman.');
                })
                .finally(() => {
                    // Hide loading indicator
                    document.getElementById('loadingIndicator').style.display = 'none';
                    document.getElementById('dashboardContent').style.opacity = '1';
                });
            }
        });
    </script>
@endsection
