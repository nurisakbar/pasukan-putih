@extends('layouts.app')

@section('content')
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 col-12 mb-2 mb-md-0">
                <h3 class="mb-0">Data Sasaran</h3>
            </div>
            <div class="col-md-6 col-12 text-md-end text-start">
                {{-- @if (auth()->user()->role === 'superadmin')
                    <a href="javascript:void(0)" id="startSync" class="btn btn-primary btn-md btn-sm shadow-sm">
                        <i class="fas fa-sync me-1"></i> Sinkronisasi Si CARIK
                    </a>
                @endif --}}
                @if (auth()->user()->role !== 'sudinkes' && auth()->user()->role !== 'operator')
                    <a href="{{ route('pasiens.create') }}" class="btn btn-primary btn-md btn-sm shadow-sm ">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Data Sasaran
                    </a>
                @endif
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
                            <div class="col d-flex align-items-end gap-2 mt-4">
                                <button type="button" id="exportPasien" class="btn btn-success">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
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
                                <button type="button" id="exportPasien" class="btn btn-success mt-4 ms-2">
                                    <i class="fas fa-file-excel me-1"></i> Export Excel
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
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Tips:</strong> Double-click pada baris data untuk melihat detail pasien.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <div class="table-responsive">
                                <table id="pasiens-table" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    
    <!-- Custom CSS for responsive table -->
    <style>
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        .table th {
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .table td {
            font-size: 0.875rem;
            vertical-align: middle;
        }
        
        /* Number column styling */
        #pasiens-table thead th:first-child,
        #pasiens-table tbody td:first-child {
            text-align: center;
            width: 50px;
            max-width: 50px;
            min-width: 50px;
        }
        
        /* Responsive table styling */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.8rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
            }
            
            .table-responsive::-webkit-scrollbar {
                height: 8px;
            }
            
            .table-responsive::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }
            
            .table-responsive::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 4px;
            }
            
            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
            
            .btn-group .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
            
            .dropdown-menu {
                font-size: 0.8rem;
            }
            
            /* Ensure table doesn't break on mobile */
            .table {
                min-width: 600px;
                margin-bottom: 0;
            }
            
            .table th,
            .table td {
                white-space: nowrap;
                padding: 0.5rem 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .table-responsive {
                font-size: 0.75rem;
            }
            
            .table th,
            .table td {
                padding: 0.4rem 0.6rem;
            }
            
            .table {
                min-width: 500px;
            }
        }
        
        /* Action button styling */
        .btn-group .btn {
            border-radius: 0.25rem;
        }
        
        /* Table row hover effect */
        #pasiens-table tbody tr:hover {
            background-color: #f8f9fa !important;
            cursor: pointer;
        }
        
        /* Double-click hint */
        #pasiens-table tbody tr {
            transition: background-color 0.2s ease;
        }
        
        /* DataTables responsive modal */
        .dtr-details {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
        }
        
        .dtr-details table {
            margin-bottom: 0;
        }
        
        .dtr-details td:first-child {
            font-weight: 600;
            width: 40%;
        }
        
        /* DataTables Pagination Styling - Compact */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 0.5rem;
            text-align: center;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.25rem 0.5rem;
            margin: 0 0.05rem;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            background-color: #fff;
            color: #6c757d;
            text-decoration: none;
            display: inline-block;
            font-size: 0.8rem;
            line-height: 1.2;
            transition: all 0.15s ease-in-out;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
            color: #495057;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }
        
        /* DataTables Info Styling */
        .dataTables_wrapper .dataTables_info {
            padding-top: 0.75rem;
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        /* DataTables Length Styling */
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
        }
        
        .dataTables_wrapper .dataTables_length select {
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            background-color: #fff;
            font-size: 0.875rem;
        }
        
        /* DataTables Filter Styling */
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            padding: 0.375rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            width: 200px;
        }
        
        /* Mobile Responsive Pagination */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_paginate {
                margin-top: 0.5rem;
                text-align: center;
                overflow-x: auto;
                white-space: nowrap;
                padding: 0 0.5rem;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.3rem 0.5rem;
                margin: 0 0.1rem;
                font-size: 0.8rem;
                min-width: 35px;
                text-align: center;
                display: inline-block;
            }
            
            .dataTables_wrapper .dataTables_info {
                font-size: 0.8rem;
                text-align: center;
                margin-bottom: 0.5rem;
                padding: 0.5rem;
            }
            
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: center;
                margin-bottom: 0.75rem;
            }
            
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                max-width: 200px;
                margin: 0 auto;
            }
            
            .dataTables_wrapper .dataTables_length select {
                width: auto;
                margin: 0 auto;
            }
        }
        
        @media (max-width: 576px) {
            .dataTables_wrapper .dataTables_paginate {
                padding: 0 1rem;
                overflow-x: auto;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.25rem 0.4rem;
                margin: 0 0.05rem;
                font-size: 0.75rem;
                min-width: 30px;
            }
            
            .dataTables_wrapper .dataTables_info {
                font-size: 0.75rem;
                padding: 0.25rem;
            }
            
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                max-width: 150px;
            }
            
            /* Hide some pagination buttons on very small screens */
            .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):not(.previous):not(.next):not(.first):not(.last) {
                display: none;
            }
            
            .dataTables_wrapper .dataTables_paginate .paginate_button.first,
            .dataTables_wrapper .dataTables_paginate .paginate_button.last {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.2rem 0.3rem;
                margin: 0 0.03rem;
                font-size: 0.7rem;
                min-width: 25px;
            }
            
            .dataTables_wrapper .dataTables_info {
                font-size: 0.7rem;
                line-height: 1.2;
            }
            
            /* Show only essential buttons on very small screens */
            .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):not(.previous):not(.next) {
                display: none;
            }
        }
    </style>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable with server-side processing
            var table = $('#pasiens-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pasiens.data') }}",
                    type: 'POST',
                    data: function (d) {
                        // Only include district filter for administrators
                        @if(auth()->user()->role === 'superadmin')
                        d.district_filter = $('#district_filter').val();
                        @endif
                        d.search_input = $('#search_input').val();
                        d._token = '{{ csrf_token() }}';
                    }
                },
                columns: [
                    { 
                        data: null,
                        name: 'no',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { 
                        data: 'name', 
                        name: 'name',
                        responsivePriority: 2
                    },
                    { 
                        data: 'nik', 
                        name: 'nik',
                        responsivePriority: 2
                    },
                    { 
                        data: 'jenis_kelamin', 
                        name: 'jenis_kelamin',
                        responsivePriority: 3
                    },
                    { 
                        data: 'alamat', 
                        name: 'alamat',
                        responsivePriority: 4
                    },
                    { 
                        data: 'rt_rw', 
                        name: 'rt_rw', 
                        orderable: false,
                        responsivePriority: 5
                    },
                    { 
                        data: 'regency_name', 
                        name: 'regencies.name',
                        responsivePriority: 6
                    },
                    { 
                        data: 'district_name', 
                        name: 'districts.name',
                        responsivePriority: 7
                    },
                    { 
                        data: 'village_name', 
                        name: 'villages.name',
                        responsivePriority: 8
                    }
                ],
                responsive: {
                    breakpoints: [
                        { name: 'bigdesktop', width: Infinity },
                        { name: 'meddesktop', width: 1480 },
                        { name: 'smalldesktop', width: 1280 },
                        { name: 'medium', width: 1024 },
                        { name: 'tabletl', width: 768 },
                        { name: 'btwtabllandp', width: 640 },
                        { name: 'mobilel', width: 480 },
                        { name: 'mobilep', width: 320 }
                    ],
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal({
                            header: function (row) {
                                var data = row.data();
                                return 'Detail Data: ' + data.name;
                            }
                        }),
                        renderer: function (api, rowIdx, columns) {
                            var data = $.map(columns, function (col, i) {
                                return col.hidden ?
                                    '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                                    '<td class="fw-bold">' + col.title + ':</td> ' +
                                    '<td>' + col.data + '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');

                            return data ?
                                $('<table class="table table-sm"/>').append('<tbody>' + data + '</tbody>') :
                                false;
                        }
                    }
                },
                autoWidth: false,
                scrollX: true,
                scrollCollapse: true,
                order: [[1, 'asc']], // Order by name ascending
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                    emptyTable: "Belum ada data untuk ditampilkan",
                    processing: "Memproses data...",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    search: "Cari:",
                    zeroRecords: "Tidak ada data yang cocok ditemukan"
                },
                drawCallback: function() {
                    // Add tooltip to action buttons
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    
                    // Add double-click handler to table rows
                    $('#pasiens-table tbody tr').off('dblclick').on('dblclick', function() {
                        const data = table.row(this).data();
                        if (data && data.id) {
                            window.location.href = `{{ url('pasiens') }}/${data.id}`;
                        }
                    });
                    
                    // Add hover effect for better UX
                    $('#pasiens-table tbody tr').css('cursor', 'pointer');
                }
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
                                            url: `{{ url('sync-progress') }}/${syncId}`,
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

    <!-- Export functionality -->
    <script>
        $(document).ready(function () {
            // Handle export button click
            $('#exportPasien').click(function () {
                // Show initial loading SweetAlert
                Swal.fire({
                    title: 'Memulai Export...',
                    html: 'Mohon tunggu...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get current filters
                let filters = {
                    search_input: $('#search_input').val()
                };

                @if(auth()->user()->role === 'superadmin')
                filters.district_filter = $('#district_filter').val();
                @endif

                // Start export
                $.ajax({
                    url: '{{ route("pasiens.export") }}',
                    method: 'POST',
                    data: filters,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            // Check if direct export completed immediately
                            if (response.file_url) {
                                // Direct export completed - close loading and show success
                                Swal.close();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Export Berhasil!',
                                    html: `<div class="text-left">
                                        <p><strong>File:</strong> ${response.file_name}</p>
                                        <p><strong>Total Data:</strong> ${response.total_records} records</p>
                                    </div>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'Download File',
                                    cancelButtonText: 'Tutup'
                                }).then((result) => {
                                    if (result.isConfirmed && response.file_url) {
                                        // Try multiple download methods
                                        try {
                                            // Method 1: Use direct download route
                                            const downloadUrl = `{{ url('pasiens/download') }}/${response.file_name}`;
                                            const link = document.createElement('a');
                                            link.href = downloadUrl;
                                            link.download = response.file_name || 'export_pasien.xlsx';
                                            link.target = '_blank';
                                            document.body.appendChild(link);
                                            link.click();
                                            document.body.removeChild(link);
                                        } catch (e) {
                                            // Method 2: Fallback to original URL
                                            console.log('Download route failed, trying original URL:', e);
                                            try {
                                                const link = document.createElement('a');
                                                link.href = response.file_url;
                                                link.download = response.file_name || 'export_pasien.xlsx';
                                                link.target = '_blank';
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);
                                            } catch (e2) {
                                                // Method 3: Final fallback to window.open
                                                console.log('All methods failed, using window.open:', e2);
                                                window.open(response.file_url, '_blank');
                                            }
                                        }
                                    }
                                });
                                return;
                            }

                            // Get export_id from response for progress tracking
                            let exportId = response.export_id;

                            // Update SweetAlert to show progress
                            Swal.fire({
                                title: 'Progres Export',
                                html: '<div class="text-left">' +
                                      '<div id="exportProgressText" class="mb-3 font-weight-bold">Memulai export...</div>' +
                                      '<div class="mb-3"><progress id="exportProgressBar" value="0" max="100" class="w-100" style="height: 20px;"></progress></div>' +
                                      '<div id="exportProgressDetails" class="mb-3"></div>' +
                                      '<div class="table-responsive">' +
                                      '<table class="table table-sm table-bordered">' +
                                      '<thead class="table-light">' +
                                      '<tr><th>Status</th><th>Pesan</th><th>Waktu</th></tr>' +
                                      '</thead>' +
                                      '<tbody id="exportProgressTable">' +
                                      '<tr><td><span class="badge bg-primary">Processing</span></td><td>Memulai export...</td><td>' + new Date().toLocaleTimeString() + '</td></tr>' +
                                      '</tbody>' +
                                      '</table>' +
                                      '</div>' +
                                      '</div>',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                showCancelButton: true,
                                cancelButtonText: 'Tutup',
                                width: '600px',
                                didOpen: () => {
                                    // Start polling for progress
                                    let interval = setInterval(function () {
                                        $.ajax({
                                            url: `{{ url('export-progress') }}/${exportId}`,
                                            method: 'GET',
                                            success: function (progressResponse) {
                                                if (progressResponse.success) {
                                                    let progress = progressResponse.progress;
                                                    let percentage = progress.percentage || 0;
                                                    
                                                    // Update progress text and bar
                                                    $('#exportProgressText').text(progress.message);
                                                    $('#exportProgressBar').val(percentage);
                                                    
                                                    // Show additional details if available
                                                    if (progress.data && progress.data.total_records) {
                                                        $('#exportProgressDetails').html(
                                                            `<div class="alert alert-info mb-0">
                                                                <strong>Total Data:</strong> ${progress.data.total_records} records<br>
                                                                <strong>File:</strong> ${progress.data.file_name || 'Sedang diproses...'}
                                                            </div>`
                                                        );
                                                    }

                                                    // Update progress table
                                                    let statusBadge = '';
                                                    switch(progress.status) {
                                                        case 'success':
                                                            statusBadge = '<span class="badge bg-success">Success</span>';
                                                            break;
                                                        case 'error':
                                                            statusBadge = '<span class="badge bg-danger">Error</span>';
                                                            break;
                                                        case 'warning':
                                                            statusBadge = '<span class="badge bg-warning">Warning</span>';
                                                            break;
                                                        default:
                                                            statusBadge = '<span class="badge bg-primary">Processing</span>';
                                                    }

                                                    // Add new row to progress table
                                                    let newRow = `<tr>
                                                        <td>${statusBadge}</td>
                                                        <td>${progress.message}</td>
                                                        <td>${new Date().toLocaleTimeString()}</td>
                                                    </tr>`;
                                                    
                                                    // Add to top of table
                                                    $('#exportProgressTable').prepend(newRow);
                                                    
                                                    // Keep only last 5 rows
                                                    let rows = $('#exportProgressTable tr');
                                                    if (rows.length > 5) {
                                                        rows.slice(5).remove();
                                                    }

                                                    // Check if export is complete
                                                    if (progress.status === 'success' && percentage === 100) {
                                                        clearInterval(interval);
                                                        Swal.update({
                                                            showConfirmButton: true,
                                                            confirmButtonText: 'Download File',
                                                            showCancelButton: false
                                                        });
                                                        
                                                        // Handle download button click
                                                        Swal.getConfirmButton().onclick = function() {
                                                            if (progress.data && progress.data.file_url) {
                                                                // Try multiple download methods
                                                                try {
                                                                    // Method 1: Use direct download route
                                                                    const downloadUrl = `{{ url('pasiens/download') }}/${progress.data.file_name}`;
                                                                    const link = document.createElement('a');
                                                                    link.href = downloadUrl;
                                                                    link.download = progress.data.file_name || 'export_pasien.xlsx';
                                                                    link.target = '_blank';
                                                                    document.body.appendChild(link);
                                                                    link.click();
                                                                    document.body.removeChild(link);
                                                                } catch (e) {
                                                                    // Method 2: Fallback to original URL
                                                                    console.log('Download route failed, trying original URL:', e);
                                                                    try {
                                                                        const link = document.createElement('a');
                                                                        link.href = progress.data.file_url;
                                                                        link.download = progress.data.file_name || 'export_pasien.xlsx';
                                                                        link.target = '_blank';
                                                                        document.body.appendChild(link);
                                                                        link.click();
                                                                        document.body.removeChild(link);
                                                                    } catch (e2) {
                                                                        // Method 3: Final fallback to window.open
                                                                        console.log('All methods failed, using window.open:', e2);
                                                                        window.open(progress.data.file_url, '_blank');
                                                                    }
                                                                }
                                                            }
                                                            Swal.close();
                                                        };
                                                    } else if (progress.status === 'error') {
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
                                            error: function (xhr) {
                                                clearInterval(interval);
                                                let errorMessage = 'Gagal memeriksa progres export.';
                                                
                                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                                    errorMessage = xhr.responseJSON.message;
                                                }
                                                
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Error',
                                                    text: errorMessage,
                                                    confirmButtonText: 'OK'
                                                });
                                            }
                                        });
                                    }, 2000); // Poll every 2 seconds
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
                            text: 'Gagal memulai export: ' + (xhr.responseJSON?.message || 'Unknown error'),
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>

@endpush
