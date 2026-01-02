@extends('layouts.app')

@php
    use Carbon\Carbon;
@endphp


@section('content')
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-10 col-12 mb-2 mb-md-0">
                    <h3 class="mb-0">Kunjungan</h3>
                </div>
                @if(auth()->user()->role !== 'operator')
                <div class="col-md-2 col-12 text-md-end text-start">
                    <a href="{{ route('visitings.create') }}" class="btn btn-primary btn-md btn-sm shadow-sm d-block d-md-inline-block w-100 w-md-auto">
                        <i class="fas fa-plus-circle me-1"></i> <span class="d-none d-sm-inline">Tambah </span>Kunjungan
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    {{-- <div class="row mb-2">
                        <div class="col-12">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Filter Pencarian:</strong> Gunakan rentang tanggal untuk memfilter data kunjungan berdasarkan periode tertentu
                            </small>
                        </div>
                    </div> --}}
                    <form method="GET" action="{{ route('visitings.index') }}" class="row g-2 align-items-end">
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label small text-muted mb-1">Cari Nama/NIK</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari Nama/NIK"
                                    value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label small text-muted mb-1">Tanggal Mulai</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" name="tanggal_awal" class="form-control" placeholder="Tanggal Mulai"
                                    value="{{ request('tanggal_awal', Carbon::today()->toDateString()) }}"
                                    title="Pilih tanggal mulai untuk rentang pencarian data kunjungan (dari tanggal)">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <label class="form-label small text-muted mb-1">Tanggal Akhir</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" name="tanggal_akhir" class="form-control" placeholder="Tanggal Akhir"
                                    value="{{ request('tanggal_akhir', Carbon::today()->toDateString()) }}"
                                    title="Pilih tanggal akhir untuk rentang pencarian data kunjungan (sampai tanggal)">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                    <i class="fas fa-search me-1"></i> <span class="d-none d-sm-inline">Cari</span>
                                </button>
                                <a href="{{ route('visitings.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="fas fa-sync-alt me-1"></i> <span class="d-none d-sm-inline">Reset</span>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Baris baru untuk tombol export -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-1 gap-md-2">
                                <a href="{{ route('visitings.export', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0" style="display: none;">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Export Kunjungan</span><span class="d-md-none">Export</span>
                                </a>
                                <a href="{{ route('kunjungan.export') }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Kunjungan</span><span class="d-md-none">Kunj</span>
                                </a>
                                <a href="{{ route('export.kohort-hs', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">KOHORT HS</span><span class="d-md-none">KHS</span>
                                </a>
                                <a href="{{ route('export.sasaran-bulanan', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Sasaran Bulanan</span><span class="d-md-none">Sasaran</span>
                                </a>
                                <a href="{{ route('export.jumlah-sasaran') }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Jumlah Sasaran</span><span class="d-md-none">Jumlah</span>
                                </a>
                                <a href="{{ route('export.kunjungan-awal', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Kunjungan Awal</span><span class="d-md-none">Awal</span>
                                </a>
                                <a href="{{ route('export.summary-kunjungan-awal', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Summary Awal</span><span class="d-md-none">Sum Awal</span>
                                </a>
                                <a href="{{ route('export.kunjungan-lanjutan', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Kunjungan Lanjutan</span><span class="d-md-none">Lanjutan</span>
                                </a>
                                <a href="{{ route('export.summary-kunjungan-lanjutan', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Summary Lanjutan</span><span class="d-md-none">Sum Lanj</span>
                                </a>
                                <a href="{{ route('export.henti-layanan', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Henti Layanan</span><span class="d-md-none">Henti</span>
                                </a>
                                <a href="{{ route('export.summary-henti-layanan', request()->query()) }}" class="btn btn-outline-success btn-sm flex-fill flex-md-grow-0">
                                    <i class="fas fa-file-export me-1"></i> <span class="d-none d-md-inline">Summary Henti</span><span class="d-md-none">Sum Henti</span>
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
                    
                    <div class="table-responsive">
                        <table id="example2" class="table table-bordered table-striped dataTable-responsive">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">NO</th>
                                    <th class="d-none d-md-table-cell">NAMA PASIEN</th>
                                    <th class="d-none d-md-table-cell">TANGGAL</th>
                                    <th class="d-none d-md-table-cell">JENIS KUNJUNGAN</th>
                                    {{-- <th>STATUS</th> --}}
                                    {{-- <th>OPERATOR</th> --}}
                                    <th class="d-none d-lg-table-cell">ALAMAT</th>
                                    <th class="d-none d-lg-table-cell">RT/ RW</th>
                                    <th class="d-none d-xl-table-cell">KABUPATEN</th>
                                    <th class="d-none d-xl-table-cell">KECAMATAN</th>
                                    <th class="d-none d-xl-table-cell">KELURAHAN</th>
                                    <!-- Mobile view columns -->
                                    <th class="d-md-none">INFO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($visitings as $index => $kunjungan)
                                    <tr class="clickable-row" data-url="{{ route('visitings.dashboard', $kunjungan->id) }}">
                                        <td class="align-middle text-center">{{ $index + 1 }}</td>
                                        <td class="align-middle d-none d-md-table-cell">{{ $kunjungan->pasien->name }}</td>
                                        <td class="align-middle d-none d-md-table-cell">{{ \Carbon\Carbon::parse($kunjungan->tanggal)->format('d M Y') }}</td>
                                        <td class="align-middle d-none d-md-table-cell">{{ $kunjungan->status }}</td>
                                        {{-- <td class="align-middle">{{ $kunjungan->selesai==1?'SELESAI':'BELUM' }}</td> --}}
                                        {{-- <td class="align-middle">{{ $kunjungan->operator ? $kunjungan->operator->name : '-' }}</td> --}}
                                        <td class="align-middle text-truncate d-none d-lg-table-cell" style="max-width: 150px;">
                                            {{ $kunjungan->pasien->alamat }}
                                        </td>
                                        <td class="align-middle d-none d-lg-table-cell">{{ $kunjungan->pasien->rt }} / {{ $kunjungan->pasien->rw }}</td>
                                        <td class="align-middle d-none d-xl-table-cell">{{ $kunjungan->pasien->village->district->regency->name; }}</td>
                                        <td class="align-middle d-none d-xl-table-cell">{{ $kunjungan->pasien->village->district->name }}</td>
                                        <td class="align-middle d-none d-xl-table-cell">{{ $kunjungan->pasien->village->name }}</td>
                                        <!-- Mobile view cell -->
                                        <td class="align-middle d-md-none">
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold text-primary">{{ $kunjungan->pasien->name }}</div>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($kunjungan->tanggal)->format('d M Y') }}</small>
                                                <span class="badge bg-info text-dark">{{ $kunjungan->status }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
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
                    <div class="d-flex justify-content-center justify-content-md-end">
                        <div class="pagination-wrapper">
                            {{ $kunjungans->links() }}
                        </div>
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
        
        /* Mobile table improvements */
        .table-responsive {
            border: none;
        }
        
        .table td, .table th {
            padding: 0.5rem 0.25rem;
            font-size: 0.875rem;
        }
        
        /* Mobile pagination */
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Mobile button improvements */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        /* Mobile card improvements */
        .card-header {
            padding: 0.75rem;
        }
        
        .card-body {
            padding: 0.5rem;
        }
    }
    
    /* Extra small devices */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .btn-group .btn {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        
        .table td, .table th {
            padding: 0.25rem 0.125rem;
            font-size: 0.8rem;
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
            var table = $('#example2').DataTable({
                responsive: true,
                autoWidth: false,
                pageLength: 25, // Show 25 records per page instead of default 10
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]], // Add length menu options
                order: [[2, 'asc']], // Sort by date column (3rd column, 0-indexed) in descending order
                columnDefs: [
                    { orderable: false, targets: 0 }, // Disable sorting on the NO column
                    { orderable: false, targets: 4 }, // Disable sorting on the ALAMAT column
                    { orderable: false, targets: 5 }, // Disable sorting on the RT/RW column
                    { orderable: false, targets: 6 }, // Disable sorting on the KABUPATEN column
                    { orderable: false, targets: 7 }, // Disable sorting on the KECAMATAN column
                    { orderable: false, targets: 8 }  // Disable sorting on the KELURAHAN column
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    emptyTable: "Belum ada data untuk ditampilkan"
                },
                drawCallback: function(settings) {
                    // Reset nomor urut setiap kali tabel di-render ulang
                    var api = this.api();
                    var start = api.page.info().start;
                    
                    api.rows({page: 'current'}).every(function(rowIdx, tableLoop, rowLoop) {
                        var cell = this.cell(rowIdx, 0).node();
                        $(cell).html(start + rowLoop + 1);
                    });
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
