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
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                   class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label fw-semibold">Tanggal Akhir</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                   class="form-control">
                        </div>
                        @php
                            $checkuser = auth()->user()->pustu()->first();
                        @endphp
                        @if (!$checkuser)
                            <div class="col-md-3">
                                <label for="district_id" class="form-label fw-semibold">Kecamatan</label>
                                <select id="district_id" name="district_id" class="form-select">
                                    <option value="">Semua Kecamatan</option>
                                    @foreach($districts as $id => $name)
                                        <option value="{{ $id }}" {{ request('district_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-funnel me-2"></i>Filter
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Data Sasaran Overview -->
            <div class="row mb-4">
                <div class="col-12">
                    <h4 class="text-muted mb-3">
                        <i class="bi bi-people-fill me-2"></i>Data Sasaran Overview
                    </h4>
                </div>
            </div>

            <div class="row g-4">
                @php
                    $sasaran_data = [
                        ['title' => 'Keseluruhan', 'value' => $data_sasaran_saat_ini, 'icon' => 'bi-people', 'bg' => 'bg-primary', 'text' => 'text-white'],
                        ['title' => 'Saat Ini', 'value' => $data_sasaran_saat_ini, 'icon' => 'bi-person-check', 'bg' => 'bg-primary', 'text' => 'text-white'],
                        ['title' => 'Sudah Dijadwalkan', 'value' => $data_sasaran_sudah_dijadwalkan, 'icon' => 'bi-calendar-check', 'bg' => 'bg-success', 'text' => 'text-white'],
                        ['title' => 'Belum Dijadwalkan', 'value' => $data_sasaran_belum_dijadwalkan, 'icon' => 'bi-calendar-x', 'bg' => 'bg-warning', 'text' => 'text-dark'],
                        ['title' => 'Sudah Dikunjungi', 'value' => $data_sasaran_sudah_dikunjungi, 'icon' => 'bi-house-check', 'bg' => 'bg-success', 'text' => 'text-white'],
                        ['title' => 'Belum Dikunjungi', 'value' => $data_sasaran_belum_dikunjungi, 'icon' => 'bi-house-x', 'bg' => 'bg-danger', 'text' => 'text-white'],
                        ['title' => 'Henti Layanan', 'value' => $data_sasaran_henti_layanan, 'icon' => 'bi-person-x', 'bg' => 'bg-danger', 'text' => 'text-white'],
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
                                        <p class="mb-0">Data Sasaran {{ $data['title'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light">
                                <a href="{{ route('pasiens.index') }}"
                                   class="text-primary text-decoration-none">
                                    More info <i class="bi bi-arrow-right-circle"></i>
                                </a>
                            </div>
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
                        ['title' => 'Belum Selesai', 'value' => $jumlah_kunjungan_belum_selesai, 'icon' => 'bi-hourglass-split', 'bg' => 'bg-warning', 'text' => 'text-dark'],
                        ['title' => 'Kunjungan Awal', 'value' => $jumlah_kunjungan_awal, 'icon' => 'bi-play-circle', 'bg' => 'bg-primary', 'text' => 'text-white'],
                        ['title' => 'Kunjungan Lanjutan', 'value' => $jumlah_kunjungan_lanjutan, 'icon' => 'bi-arrow-repeat', 'bg' => 'bg-primary', 'text' => 'text-white'],
                        ['title' => 'Sudah Selesai', 'value' => $jumlah_kunjungan_selesai, 'icon' => 'bi-check-circle', 'bg' => 'bg-success', 'text' => 'text-white'],
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
                            <div class="card-footer bg-light">
                                <a href="{{ route('visitings.index') }}"
                                   class="text-primary text-decoration-none">
                                    More info <i class="bi bi-arrow-right-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
