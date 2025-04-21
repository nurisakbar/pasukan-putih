@extends('layouts.app')

@section('content')
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <h3 class="mb-0">Data Sasaran</h3>
            </div>
            <div class="col-md-6 col-12 text-md-end text-start">
                <a href="{{ route('pasiens.create') }}" class="btn btn-primary btn-md btn-sm shadow-sm ">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Data Sasaran
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
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table id="example3" class="table table-bordered table-striped dataTable-responsive">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="110">Aksi</th>
                                            <th>NAMA</th>
                                            <th>NIK</th>
                                            <th>JENIS KELAMIN</th>
                                            <th>NAMA JALAN</th>
                                            <th>RT/ RW</th>
                                            <th>KABUPATEN</th>
                                            <th>KECAMATAN</th>
                                            <th>KELURAHAN</th>
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
                                                                <li style="display: none">
                                                                    <a href="{{ route('pasiens.asuhanKeluarga', $pasien->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-plus-minus me-2"></i> Tambah Asuhan Keluarga
                                                                    </a>
                                                                </li>

                                                                <li>
                                                                    <a href="{{ route('pasiens.show', $pasien->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-eye me-2"></i> Detail Data Sasaran
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="{{ route('pasiens.edit', $pasien->id) }}" class="dropdown-item">
                                                                        <i class="fas fa-edit me-2"></i> Edit Data Sasaran
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <button class="dropdown-item text-danger delete-btn"
                                                                            data-id="{{ $pasien->id }}"
                                                                            data-nama="{{ $pasien->name }}">
                                                                        <i class="fas fa-trash me-2"></i> Hapus Data Sasaran
                                                                    </button>
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
                                                <td>{{ $pasien->rt }}/{{ $pasien->rw }}</td>
                                                <td>{{ $pasien->regency_name }}</td>
                                                <td>{{ $pasien->district_name }}</td>
                                                <td>{{ $pasien->village_name }}</td>
                                            </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                                    <h5 class="text-muted">Tidak ada Data Sasaran</h5>
                                                    <p class="text-muted">Silakan tambahkan Data Sasaran baru</p>
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
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content border-0 shadow-lg rounded-lg">
            <div class="modal-header bg-primary text-white rounded-top">
              <h5 class="modal-title" id="importModalLabel">Import Excel</h5>
              <button type="button" class="btn-close text-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-5 py-4">
              <p class="text-muted mb-4">Untuk memastikan proses impor berjalan lancar dengan format yang benar, silakan  <a href="/" target="_blank" class="text-primary text-decoration-underline">unduh template Excel</a> yang tersedia.</p>
              <form action="pasien/import" method="POST" enctype="multipart/form-data">
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

@push('script')
    <!-- DataTables JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('#example3').DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                emptyTable: "Belum ada data untuk ditampilkan"
            }
        });

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

@endpush
