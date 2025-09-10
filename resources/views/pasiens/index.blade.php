@extends('layouts.app')

@section('content')
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <h3 class="mb-0">Data Sasaran</h3>
            </div>
            <div class="col-md-6 col-12 text-md-end text-start">
                @if (auth()->user()->role === 'superadmin')
                    <a href="javascript:void(0)" id="startSync" class="btn btn-primary btn-md btn-sm shadow-sm">
                        <i class="fas fa-sync me-1"></i> Sinkronisasi Si CARIK
                    </a>
                @endif
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
                                            <th class="text-center" width="90">Aksi</th>
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

@if ($pasiens->count() > 0)
    <script>
        $(function () {
            $('#example3').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    emptyTable: "Belum ada data untuk ditampilkan"
                }
            });
        });
    </script>
@endif

<script>
    $(document).ready(function () {

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
        // $('#btn-sinkron-carik').on('click', function(e) {
        //     e.preventDefault();

        //     Swal.fire({
        //         title: 'Memproses...',
        //         text: 'Sedang sinkronisasi data dari Si CARIK.',
        //         allowOutsideClick: false,
        //         didOpen: () => {
        //             Swal.showLoading()
        //         }
        //     });

        //     $.ajax({
        //         url: $(this).attr('href'),
        //         type: 'GET', // atau 'POST' jika perlu
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         },
        //         success: function(response) {
        //             console.log(response);
        //             Swal.close();

        //             if (response.success) {
        //                 Swal.fire({
        //                     icon: 'success',
        //                     title: 'Berhasil!',
        //                     text: response.message || 'Data berhasil disinkronisasi.'
        //                 }).then(() => {
        //                     location.reload(); // reload halaman setelah berhasil
        //                 });
        //             } else {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     title: 'Gagal!',
        //                     text: response.message || 'Sinkronisasi gagal.'
        //                 });
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             Swal.close();
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Error!',
        //                 text: 'Terjadi kesalahan saat menghubungi server.'
        //             });
        //             console.error(error);
        //         }
        //     });
        // });
    });
</script>
<script>
        $(document).ready(function () {
            // Set CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle click on Start Sync button
            $('#startSync').click(function () {
                // Show initial loading SweetAlert
                Swal.fire({
                    title: 'Memulai Sinkronisasi...',
                    html: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Start synchronization
                $.ajax({
                    url: '{{ route("syncronisasi.carik") }}',
                    method: 'POST',
                    success: function (response) {
                        if (response.success) {
                            // Get sync_id from response
                            let syncId = response.sync_id;

                            // Update SweetAlert to show progress
                            Swal.fire({
                                title: 'Progres Sinkronisasi',
                                html: '<div id="progressText">Memulai sinkronisasi...</div>' +
                                      '<div class="mt-4"><progress id="progressBar" value="0" max="100" class="w-full"></progress></div>' +
                                      '<div id="progressDetails" class="mt-2 text-sm"></div>',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                showCancelButton: true,
                                cancelButtonText: 'Tutup',
                                didOpen: () => {
                                    // Start polling for progress
                                    let interval = setInterval(function () {
                                        $.ajax({
                                            url: `{{ url('sync-progress/${syncId}') }}`,
                                            method: 'GET',
                                            success: function (progressResponse) {
                                                if (progressResponse.success) {
                                                    let progress = progressResponse.progress;
                                                    let percentage = progress.total_pages > 0
                                                        ? Math.round((progress.current_page / progress.total_pages) * 100)
                                                        : 0;
                                                    console.log(percentage);
                                                    // Update progress text and bar
                                                    $('#progressText').text(progress.message);
                                                    $('#progressBar').val(percentage);
                                                    $('#progressDetails').html(
                                                        `Data Diproses: ${progress.processed_records}<br>` +
                                                        (progress.failed_pages.length > 0
                                                            ? `Halaman Gagal: ${progress.failed_pages.join(', ')}`
                                                            : '')
                                                    );

                                                    // Check if synchronization is complete
                                                    if (percentage === 100) {
                                                        clearInterval(interval);
                                                        Swal.update({
                                                            showConfirmButton: true,
                                                            confirmButtonText: 'OK',
                                                            showCancelButton: false
                                                        });
                                                        Swal.getConfirmButton().focus();
                                                    }
                                                } else {
                                                    // Progress not found
                                                    clearInterval(interval);
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: progressResponse.message,
                                                        confirmButtonText: 'OK'
                                                    });
                                                }
                                            },
                                            error: function () {
                                                clearInterval(interval);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: 'Gagal memeriksa progres sinkronisasi.',
                                                    confirmButtonText: 'OK'
                                                });
                                            }
                                        });
                                    }, 3000); // Poll every 3 seconds
                                },
                                willClose: () => {
                                    // Clear interval when modal is closed
                                    clearInterval(interval);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memulai sinkronisasi: ' + (xhr.responseJSON?.message || 'Unknown error'),
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>

@endpush
