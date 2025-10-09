@extends('layouts.app')

@php
    use Carbon\Carbon;
@endphp


@section('content')
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 col-12 mb-2 mb-md-0">
                    <h3 class="mb-0">Kunjungan</h3>
                </div>
                <div class="col-md-6 col-12 text-md-end text-start">
                    <a href="{{ route('visitings.create') }}" class="btn btn-primary btn-md btn-sm shadow-sm ">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Kunjungan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <form method="GET" action="{{ route('visitings.index') }}" class="row g-2 align-items-center">
                        <div class="col-md-3 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari Nama/NIK"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" name="tanggal_awal" class="form-control" placeholder="Dari Tanggal"
                                    value="{{ request('tanggal_awal', Carbon::today()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="col-md-3 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" name="tanggal_akhir" class="form-control" placeholder="Sampai Tanggal"
                                    value="{{ request('tanggal_akhir', Carbon::today()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="col-md-3 col-12 text-md-end text-start">
                            <button type="submit" class="btn btn-sm btn-primary me-1">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('visitings.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                    <!-- Baris baru untuk tombol export -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="btn-group d-flex flex-wrap gap-2">
                                <a href="{{ route('kunjungan.export') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Kunjungan
                                </a>
                                <a href="{{ route('export.kohort-hs', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> KOHORT HS
                                </a>
                                <a href="{{ route('export.sasaran-bulanan', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Sasaran Bulanan
                                </a>
                                <a href="{{ route('export.jumlah-sasaran') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Jumlah Sasaran
                                </a>
                                <a href="{{ route('export.kunjungan-awal', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Kunjungan Awal
                                </a>
                                <a href="{{ route('export.summary-kunjungan-awal', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Summary Awal
                                </a>
                                <a href="{{ route('export.kunjungan-lanjutan', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Kunjungan Lanjutan
                                </a>
                                <a href="{{ route('export.summary-kunjungan-lanjutan', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Summary Lanjutan
                                </a>
                                <a href="{{ route('export.henti-layanan', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Henti Layanan
                                </a>
                                <a href="{{ route('export.summary-henti-layanan', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-export me-1"></i> Summary Henti
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Instruction Note -->
                    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Petunjuk:</strong>
                                <span class="desktop-note">Double-click pada baris untuk melakukan pemeriksaan</span>
                                <span class="mobile-note d-none">Tap pada baris untuk melakukan pemeriksaan</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <div class="table-responsive-sm">
                        <table id="example2" class="table table-bordered table-striped dataTable-responsive">
                            <thead class="table-light">
                                <tr>
                                    <th>NAMA PASIEN</th>
                                    <th>TANGGAL</th>
                                    <th>JENIS KUNJUNGAN</th>
                                    <th>STATUS</th>
                                    <th>OPERATOR</th>
                                    <th>ALAMAT</th>
                                    <th>RT/ RW</th>
                                    <th>KABUPATEN</th>
                                    <th>KECAMATAN</th>
                                    <th>KELURAHAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($visitings as $kunjungan)
                                    <tr class="clickable-row" data-url="{{ route('visitings.dashboard', $kunjungan->id) }}">
                                        <td class="align-middle">{{ $kunjungan->pasien->name }}</td>
                                        <td class="align-middle">{{ \Carbon\Carbon::parse($kunjungan->tanggal)->format('d M Y') }}</td>
                                        <td class="align-middle">{{ $kunjungan->status }}</td>
                                        <td class="align-middle">{{ $kunjungan->selesai==1?'SELESAI':'BELUM' }}</td>
                                        <td class="align-middle">{{ $kunjungan->operator ? $kunjungan->operator->name : '-' }}</td>
                                        <td class="align-middle text-truncate" style="max-width: 150px;">
                                            {{ $kunjungan->pasien->alamat }}
                                        </td>
                                        <td class="align-middle">{{ $kunjungan->pasien->rt }} / {{ $kunjungan->pasien->rw }}</td>
                                        <td class="align-middle">{{ $kunjungan->pasien->village->district->regency->name; }}</td>
                                        <td class="align-middle">{{ $kunjungan->pasien->village->district->name }}</td>
                                        <td class="align-middle">{{ $kunjungan->pasien->village->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                                <h5 class="text-muted">Tidak ada data kunjungan</h5>
                                                <p class="text-muted">Silakan tambahkan kunjungan baru</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($kunjungans) && method_exists($kunjungans, 'links'))
                <div class="card-footer bg-white">
                    <div class="float-end">
                        {{ $kunjungans->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
    }
    
    .clickable-row:hover {
        background-color: #f8f9fa !important;
    }
    
    .clickable-row:active {
        background-color: #e9ecef !important;
    }
    
    /* Mobile-specific styles */
    @media (max-width: 768px) {
        .clickable-row {
            -webkit-tap-highlight-color: rgba(0, 123, 255, 0.2);
        }
        
        .clickable-row:active {
            background-color: #cce7ff !important;
            transform: scale(0.98);
        }
    }
</style>
@endpush

@push('script')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@if ($visitings->count() > 0)
    <script>
        $(function () {
            $('#example2').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 25, // Show 25 records per page instead of default 10
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]], // Add length menu options
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    emptyTable: "Belum ada data untuk ditampilkan"
                }
            });
            
        });
    </script>
@endif
<script>
    // Initialize tooltips and show device-specific note
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Show appropriate instruction note
        showDeviceSpecificNote();
    });


    // Mobile detection
    function isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || 
               ('ontouchstart' in window) || 
               (navigator.maxTouchPoints > 0);
    }

    // Show appropriate instruction note based on device
    function showDeviceSpecificNote() {
        const desktopNote = document.querySelector('.desktop-note');
        const mobileNote = document.querySelector('.mobile-note');
        
        if (isMobileDevice()) {
            desktopNote.classList.add('d-none');
            mobileNote.classList.remove('d-none');
        } else {
            desktopNote.classList.remove('d-none');
            mobileNote.classList.add('d-none');
        }
    }

    // Click handler for table rows (single-click on mobile, double-click on desktop)
    if (isMobileDevice()) {
        // Single-click for mobile devices
        document.addEventListener('click', function(event) {
            // Prevent click if clicking on interactive elements
            if (event.target.closest('button, a, input, select, textarea, .dropdown-menu')) {
                return;
            }
            
            const row = event.target.closest('.clickable-row');
            if (row) {
                const url = row.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            }
        });
    } else {
        // Double-click for desktop devices
        document.addEventListener('dblclick', function(event) {
            // Prevent double-click if clicking on interactive elements
            if (event.target.closest('button, a, input, select, textarea, .dropdown-menu')) {
                return;
            }
            
            const row = event.target.closest('.clickable-row');
            if (row) {
                const url = row.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            }
        });
    }

</script>
@endpush
