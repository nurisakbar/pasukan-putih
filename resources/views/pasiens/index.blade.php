@extends('layouts.app')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 col-12 mb-2 mb-md-0">
                    <h3 class="mb-0">Pasien</h3>
                </div>
                <div class="col-md-6 col-12 text-md-end text-start">
                    <a href="{{ route('pasiens.create') }}" class="btn btn-primary btn-md btn-sm shadow-sm ">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Pasien
                    </a>
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
                            <form method="GET" action="{{ route('pasiens.index') }}" class="row g-2 align-items-center">
                                <div class="col-md-4 col-12">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
                                        <input type="text" name="search" class="form-control" placeholder="Cari Nama/NIK"
                                            value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar-alt"></i></span>
                                        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                                    </div>
                                </div>
                                <div class="col-md-4 col-12 text-md-end text-start">
                                    <button type="submit" class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-search me-1"></i> Cari
                                    </button>
                                    <a href="{{ route('pasiens.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-sync-alt me-1"></i> Reset
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="fas fa-file-import me-1"></i> Import
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="180">Aksi</th>
                                            <th>Nama</th>
                                            <th>NIK</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Alamat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pasiens as $pasien)
                                            <tr>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-cogs"></i> Aksi
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a href="{{ route('pasiens.asuhanKeluarga', $pasien->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-plus-minus me-2"></i> Tambah Asuhan Keluarga
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="{{ route('pasiens.show', $pasien->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-eye me-2"></i> Detail Pasien
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('pasiens.edit', $pasien->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-edit me-2"></i> Edit Pasien
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <form action="{{ route('pasiens.destroy', $pasien->id) }}" method="post">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fas fa-trash me-2"></i> Hapus Pasien
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                
                                                    <form id="delete-form-{{ $pasien->id }}" action="{{ route('pasiens.destroy', $pasien->id) }}" method="POST" style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </td>
                                                
                                                <td>{{ $pasien->name }}</td>
                                                <td>{{ $pasien->nik }}</td>
                                                <td>{{ $pasien->jenis_kelamin }}</td>
                                                <td>{{ $pasien->alamat }}</td>
                                            </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                                    <h5 class="text-muted">Tidak ada data Pasien</h5>
                                                    <p class="text-muted">Silakan tambahkan Pasien baru</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="float-end">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination">
                                        @if ($pasiens->currentPage() > 1)
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $pasiens->previousPageUrl() }}" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        @endif
                                        
                                        @foreach ($pasiens->getUrlRange(max(1, $pasiens->currentPage() - 1), min($pasiens->lastPage(), $pasiens->currentPage() + 2)) as $page => $url)
                                            <li class="page-item {{ $page == $pasiens->currentPage() ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endforeach
                                        
                                        @if ($pasiens->currentPage() < $pasiens->lastPage())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $pasiens->nextPageUrl() }}" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-primary text-white rounded-top">
              <h5 class="modal-title" id="importModalLabel">Import Excel</h5>
              <button type="button" class="btn-close text-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-5 py-4">
              <p class="text-muted mb-4">Untuk memastikan proses impor berjalan lancar dengan format yang benar, silakan  <a href="{{ route('pasiens.downloadTemplate') }}" target="_blank" class="text-primary text-decoration-underline">unduh template Excel</a> yang tersedia.</p>
              <form action="{{ route('pasiens.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                  <label for="file" class="form-label text-dark fs-5">Pilih File Excel</label>
                  <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv" required>
                  <div class="form-text text-muted mt-2">Hanya file Excel (.xlsx, .xls) atau CSV yang diperbolehkan.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mt-3">Import</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      
@endsection

@section('scripts')
    <script>
          $(document).ready(function() {
            // Event delegation untuk tombol delete di dalam dropdown
            $('body').on('click', '.delete-btn', function(event) {
                event.preventDefault();
                const id = $(this).data('id');
                const pasienNama = $(this).data('nama'); 

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
@endsection
