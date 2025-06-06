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
                    <a href="{{ route('kunjungans.create') }}" class="btn btn-primary btn-md btn-sm shadow-sm ">
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
                    <form method="GET" action="{{ route('kunjungans.index') }}" class="row g-2 align-items-center">
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
                                    value="{{ request('tanggal_akhir') }}">
                            </div>
                        </div>
                        <div class="col-md-3 col-12 text-md-end text-start">
                            <button type="submit" class="btn btn-sm btn-primary me-1">
                                <i class="fas fa-search me-1"></i> Cari
                            </button>
                            <a href="{{ route('kunjungans.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="180">Aksi</th>
                                    <th>Nama Pasien</th>
                                    <th>Tanggal</th>
                                    <th>NIK</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Jenis KTP</th>
                                    <th>Alamat</th>
                                    {{-- <th>Nomor Hp</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kunjungans as $kunjungan)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-cogs"></i> Aksi
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a href="{{ route('kunjungan.skriningAdl', $kunjungan->id) }}" 
                                                               class="dropdown-item">
                                                                <i class="fas fa-clipboard-list me-2"></i> Skrining ADL
                                                            </a>
                                                        </li>
                                                        @if (auth()->user()->role == 'perawat' || auth()->user()->role == 'superadmin')    
                                                        <li>
                                                            <a href="{{ route('ttv.edit', $kunjungan->id) }}" 
                                                               class="dropdown-item">
                                                                <i class="fas fa-edit me-2"></i> Edit TTV
                                                            </a>
                                                        </li>
                                                        @endif
                                                        {{-- <li>
                                                            <a href="{{ route('kunjungans.edit', $kunjungan->id) }}" 
                                                               class="dropdown-item">
                                                                <i class="fas fa-edit me-2"></i> Edit Kunjungan
                                                            </a>
                                                        </li> --}}
                                                        <li>
                                                            <button class="dropdown-item text-danger delete-btn" 
                                                                    data-id="{{ $kunjungan->id }}"
                                                                    data-nama="{{ $kunjungan->pasien->name }}">
                                                                <i class="fas fa-trash me-2"></i> Hapus Kunjungan
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        
                                            <form id="delete-form-{{ $kunjungan->id }}" action="{{ route('kunjungans.destroy', $kunjungan->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                        
                                        <td class="align-middle">{{ $kunjungan->pasien->name }}</td>
                                        <td class="align-middle">{{ \Carbon\Carbon::parse($kunjungan->tanggal)->format('d M Y') }}</td>
                                        <td class="align-middle">{{ $kunjungan->pasien->nik }}</td>
                                        <td class="align-middle">{{ $kunjungan->pasien->jenis_kelamin }}</td>
                                        <td class="align-middle">{{ $kunjungan->pasien->jenis_ktp }}</td>
                                        <td class="align-middle">{{ $kunjungan->pasien->alamat }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
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

@push('script')

{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const id = this.getAttribute('data-id');
            const pasienNama = this.getAttribute('data-nama'); 
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus data pasien ${pasienNama}. Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus data ini!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });

</script>
@endpush