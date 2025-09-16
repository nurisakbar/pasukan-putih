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

<!-- Filter Section - Only for Administrators -->
@if(auth()->user()->role === 'superadmin')
<div class="app-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label for="district_filter" class="form-label">Filter Kecamatan:</label>
                                <select id="district_filter" class="form-select">
                                    <option value="">Semua Kecamatan</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search_input" class="form-label">Pencarian:</label>
                                <input type="text" id="search_input" class="form-control" placeholder="Cari nama, NIK, atau alamat...">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="apply_filter" class="btn btn-primary mt-4">
                                    <i class="fas fa-filter me-1"></i> Terapkan Filter
                                </button>
                                <button type="button" id="clear_filter" class="btn btn-secondary mt-4 ms-2">
                                    <i class="fas fa-times me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<!-- Search Section for Non-Administrators -->
<div class="app-content">
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label for="search_input" class="form-label">Pencarian:</label>
                                <input type="text" id="search_input" class="form-control" placeholder="Cari nama, NIK, atau alamat...">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="apply_filter" class="btn btn-primary mt-4">
                                    <i class="fas fa-search me-1"></i> Cari
                                </button>
                                <button type="button" id="clear_filter" class="btn btn-secondary mt-4 ms-2">
                                    <i class="fas fa-times me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive-sm">
                                <table id="pasiens-table" class="table table-bordered table-striped dataTable-responsive">
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
                                        <!-- Data will be loaded via AJAX -->
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable with server-side processing
            var table = $('#pasiens-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pasiens.data') }}",
                    data: function (d) {
                        // Only include district filter for administrators
                        @if(auth()->user()->role === 'superadmin')
                        d.district_filter = $('#district_filter').val();
                        @endif
                        d.search_input = $('#search_input').val();
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'nik', name: 'nik' },
                    { data: 'jenis_kelamin', name: 'jenis_kelamin' },
                    { data: 'alamat', name: 'alamat' },
                    { data: 'rt_rw', name: 'rt_rw', orderable: false },
                    { data: 'regency_name', name: 'regencies.name' },
                    { data: 'district_name', name: 'districts.name' },
                    { data: 'village_name', name: 'villages.name' }
                ],
                responsive: true,
                autoWidth: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    emptyTable: "Belum ada data untuk ditampilkan",
                    processing: "Memproses data..."
                },
                order: [[1, 'asc']], // Order by name ascending
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
            });

            // Apply filter button
            $('#apply_filter').click(function() {
                table.ajax.reload();
            });

            // Clear filter button
            $('#clear_filter').click(function() {
                @if(auth()->user()->role === 'superadmin')
                $('#district_filter').val('');
                @endif
                $('#search_input').val('');
                table.ajax.reload();
            });

            // Auto-reload when district filter changes (only for administrators)
            @if(auth()->user()->role === 'superadmin')
            $('#district_filter').change(function() {
                table.ajax.reload();
            });
            @endif

            // Search functionality with debounce
            let searchTimeout;
            $('#search_input').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    table.ajax.reload();
                }, 500); // Wait 500ms after user stops typing
            });
        });
    </script>

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
                    // Submit the delete form
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
