@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0"><i class="fas fa-user-circle me-2"></i> Detail Data Sasaran</h3>
            </div>
            <div class="col-sm-6 text-end">
                
                <a href="{{ route('pasiens.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Informasi Pasien di Kiri -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Data Sasaran</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-address-card text-primary" style="font-size: 100px" ></i>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $pasien->name }}</td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td>{{ $pasien->nik }}</td>
                                </tr>
                                <tr>
                                    <th>Umur</th>
                                    <td>{{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->age }} tahun</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>{{ $pasien->jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $pasien->alamat }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis KTP</th>
                                    <td>{{ $pasien->jenis_ktp }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor WhatsApp</th>
                                    <td>{{ $pasien->nomor_whatsapp ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Pendamping</th>
                                    <td>{{ $pasien->nama_pendamping ?: '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            @if (auth()->user()->role != 'sudinkes')
                                <a href="{{ route('pasiens.asuhanKeluarga', $pasien->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus-circle me-1"></i> Asuhan Keluarga
                                </a>
                                <a href="{{ route('pasiens.edit', $pasien->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit me-1"></i> Edit Data
                                </a>
                                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $pasien->id }}" data-nama="{{ $pasien->name }}">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Kunjungan di Kanan -->
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white d-flex align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Riwayat Kunjungan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Perawat</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Kunjungan</th>
                                    <th>Status</th>
                                    <th>Skor Aks</th>
                                    {{-- <th>Skor Aks Setelah Kunjungan</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kunjungan as $item)
                                <tr>
                                    <td class="align-middle">
                                        <a href="{{ route('visitings.editKunjunganFromPasiens', $item->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    <td class="align-middle">{{ $item->user->name ?? 'Belum ada' }}</td>
                                    <td class="align-middle">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') ?? 'Belum ada' }}</td>
                                    <td class="align-middle">{{ $item->status ?? 'Belum ada' }}</td>
                                    <td class="align-middle">
                                        @php
                                            $kunjungan = $item->healthForms->kunjungan_lanjutan ?? null;
                                        @endphp
                                        <span class="badge 
                                            {{ $kunjungan === 'ya' ? 'bg-success' : 
                                            ($kunjungan === 'tidak' ? 'bg-warning' : 'bg-secondary') }}">
                                            {{ $kunjungan ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">{{ $item->healthForms->skor_aks ?? 'Belum ada' }}</td>
                                    {{-- <td class="align-middle">{{ $item->skriningAdl->total_score ?? 'Belum ada' }}</td> --}}
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
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="delete-form-{{ $pasien->id }}" action="{{ route('pasiens.destroy', $pasien->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Handle delete button click
    $('.delete-btn').on('click', function(event) {
        event.preventDefault();
        const id = $(this).data('id');
        const pasienNama = $(this).data('nama');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: `Anda akan menghapus data pasien ${pasienNama}. Tindakan ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus data ini!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the delete form
                document.getElementById('delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endpush
