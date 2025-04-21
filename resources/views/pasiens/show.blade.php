@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h3 class="mb-0"></i> Detail Data Sasaran</h3>
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
                            </tbody>
                        </table>
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
                                        <span class="badge {{ $item->healthForms->kunjungan_lanjutan == 'ya' ? 'bg-success' : 'bg-warning' }}">{{ $item->healthForms->kunjungan_lanjutan ?? 'Belum ada' }}</span>
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
@endsection
