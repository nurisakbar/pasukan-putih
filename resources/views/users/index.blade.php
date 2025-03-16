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
                             </div>
                         </div>
                     </div>
                     <div class="card-body">
                         <table id="example1" class="table table-bordered table-striped">
                             <thead>
                                 <tr>
                                     <th>No</th>
                                     <th>Nama</th>  
                                     <th>Email</th>
                                     <th>Role</th>
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
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
@endsection