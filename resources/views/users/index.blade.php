@extends('layouts.app')

@push('style')
    <style>
        /* Make DataTable wrapper scrollable on small screens */
        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        /* Optional: Prevent table from breaking layout */
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
        }

        /* Optional: Ensure table cells behave well */
        table.dataTable th,
        table.dataTable td {
            white-space: nowrap;
        }

        @media screen and (max-width: 768px) {
            .dataTable-responsive {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
@endpush

@section('content')
        <div class="app-content-header py-3">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-md-6 col-12 mb-2 mb-md-0">
                        <h3 class="mb-0">Pengguna</h3>
                    </div>
                    <div class="col-md-6 col-12 text-md-end text-start">
                        <button type="button" class="btn btn-outline-success btn-md btn-sm shadow-sm me-2"
                            data-bs-toggle="modal" data-bs-target="#importModal">
                            IMPORT DATA
                        </button>
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-md btn-sm shadow-sm ">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Pengguna
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
                            {{-- <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h5 class="card-title">DAFTAR PENGGUNA</h5>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="{{ route('users.create') }}" class="btn btn-primary float-end">TAMBAH
                                            PENGGUNA</a>
                                        <button type="button" class="btn btn-outline-success float-end me-2"
                                            data-bs-toggle="modal" data-bs-target="#importModal">
                                            IMPORT DATA
                                        </button>
                                    </div>
                                </div>
                                <form style="display: none" method="GET" action="{{ route('users.index') }}"
                                    class="mt-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Cari nama/email" value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ request('start_date', \Carbon\Carbon::now()->toDateString()) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ request('end_date') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-outline-primary">
                                                <i class="fas fa-search"></i> Cari
                                            </button>
                                            <a href="{{ route('users.index') }}" class="btn btn-outline-danger">
                                                Reset
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div> --}}
                            <div class="card-body">
                                <ul class="nav nav-tabs" style="margin-bottom:10px">
                                    <li class="nav-item">
                                        <a class="nav-link {{ $_GET['role'] == 'superadmin' ? 'active' : '' }}"
                                            aria-current="page" href="users?role=superadmin"><i
                                                class="fa-solid fa-user-secret"></i> SUPER ADMIN</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $_GET['role'] == 'perawat' ? 'active' : '' }}"
                                            href="users?role=perawat"><i class="fa-solid fa-users"></i> PERAWAT</a>
                                    </li>
                                </ul>


                                <table id="example1" class="table table-bordered table-striped dataTable-responsive"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>No Whatsapp</th>
                                            @if ($_GET['role'] == 'perawat')
                                                <th>NAMA PUSTU</th>
                                            @endif
                                            <th widht="100px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $data->name }}</td>
                                                <td>{{ $data->email }}</td>
                                                <td>{{ strtoupper($data->role) }}</td>
                                                <td>{{ $data->no_wa ?? '-' }}</td>
                                                @if ($_GET['role'] == 'perawat')
                                                    <td>{{ $data->pustu->nama_pustu ?? '-' }}</td>
                                                @endif
                                                <td width="100px">
                                                    <form action="{{ route('users.destroy', $data->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')

                                                        <a href="{{ route('users.edit', $data->id) }}"
                                                            class="btn btn-danger btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus"
                                                            onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Data Pengguna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="importForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Pilih File Excel</label>
                                <input type="file" name="file" id="file" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success">Import</button>
                        </form>
                        <div id="loadingIndicator" class="mt-3"></div>
                        <div id="importResult" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <!-- DataTables CSS via CDN -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script>
            $(function() {
                $("#example1").DataTable();
            });
            $(document).ready(function() {


                $("#importForm").submit(function(e) {
                    e.preventDefault();

                    let formData = new FormData(this);

                    // Disable submit button and show loading
                    $("#submitImport").prop('disabled', true);
                    $("#importResult").html(''); // Clear previous results
                    $("#loadingIndicator").html(`
                <div class="d-flex justify-content-center align-items-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ml-2">Sedang mengimpor data...</span>
                </div>
            `);

                    $.ajax({
                        url: "{{ route('import.users') }}",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        xhr: function() {
                            let xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener("progress", function(evt) {
                                if (evt.lengthComputable) {
                                    let percentComplete = evt.loaded / evt.total;
                                    percentComplete = parseInt(percentComplete * 100);
                                    $("#loadingIndicator").html(`
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <span class="ml-2">Mengimpor data... ${percentComplete}%</span>
                                </div>
                            `);
                                }
                            }, false);
                            return xhr;
                        },
                        success: function(response) {
                            // Close the modal
                            $('#importModal').modal('hide');

                            // Show SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.success || 'Data berhasil diimpor',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Optional: Reload the datatable or refresh the page
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            $("#loadingIndicator").html('');
                            let errorMsg = xhr.responseJSON.error ||
                                "Terjadi kesalahan saat mengimpor data.";
                            $("#importResult").html('<div class="alert alert-danger">' + errorMsg +
                                '</div>');
                            $("#submitImport").prop('disabled', false);
                        }
                    });
                });
            });
        </script>
    @endpush
