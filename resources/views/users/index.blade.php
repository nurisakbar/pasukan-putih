@extends('layouts.app')

@section('content')
<div class="app-content-header">
     <div class="container-fluid">
         <div class="row">
             <div class="col-sm-6">
                 <h3 class="mb-0">Users</h3>
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
                         <div class="row">
                             <div class="col-sm-6">
                                 <h5 class="card-title">Data Users</h5>
                             </div>
                             <div class="col-sm-6">
                                 <a href="{{ route('users.create') }}" class="btn btn-primary float-end">Tambah Users</a>
                                 <button type="button" class="btn btn-outline-success float-end me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                    Import Pengguna
                                </button>
                             </div>
                         </div>
                         <form method="GET" action="{{ route('users.index') }}" class="mt-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Cari nama/email" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-info w-100">Cari</button>
                                </div>
                            </div>
                        </form>
                     </div>
                     <div class="card-body">
                         <table id="example1" class="table table-bordered table-striped">
                             <thead>
                                 <tr>
                                     <th>No</th>
                                     <th>Nama</th>  
                                     <th>Email</th>
                                     <th>Role</th>
                                     <th>No Whatsapp</th>
                                     <th>Keterangan</th>
                                     <th widht="300px">Aksi</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @foreach ($users as $data)
                                     <tr>
                                         <td>{{ $loop->iteration }}</td>
                                         <td>{{ $data->name }}</td>
                                         <td>{{ $data->email }}</td>
                                         <td>{{ $data->role }}</td>
                                         <td>{{ $data->no_wa ?? '-' }}</td>
                                         <td>{{ $data->keterangan }}</td>
                                         <td width="300px">
                                             <form action="{{ route('users.destroy', $data->id) }}" method="POST">
                                                 @csrf
                                                 @method('DELETE')
                                                 <a href="{{ route('users.edit', $data->id) }}" class="btn btn-warning">Edit</a>
                                                 <button type="submit" class="btn btn-danger">Hapus</button>
                                             </form>
                                         </td>
                                     </tr>
                                 @endforeach
                             </tbody>
                             
                         </table>
                         <div class="mt-4 float-end">
                            {{ $users->links() }}
                         </div>
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
<script>
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
                    let errorMsg = xhr.responseJSON.error || "Terjadi kesalahan saat mengimpor data.";
                    $("#importResult").html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    $("#submitImport").prop('disabled', false);
                }
            });
        });
    });
</script>
@endpush