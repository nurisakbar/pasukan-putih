@extends('layouts.app')

@section('content')
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Edit User</h3>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-9">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h5 class="card-title">Edit User</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="name">Name</label>
                                            </div>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control mt-1 mb-2" name="name" value="{{ old('name', $user->name) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="email">Email</label>
                                            </div>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control mt-1 mb-2" name="email" value="{{ old('email', $user->email) }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="password">Password </label>
                                            </div>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control mt-1 mb-2" name="password" value="{{ old('password', $user->password) }}" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <labelfor="password_confirmation">Konfirmasi Password</label>
                                            </div>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control mt-1 mb-2" name="password_confirmation" value="{{ old('password', $user->password) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <label for="role">Role</label>
                                            </div>
                                            <div class="col-sm-10">
                                                <select name="role" class="form-control mt-1 mb-2">
                                                    @if (Auth::user()->parent_id == null)
                                                        <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                                        <option value="puskesmas" {{ $user->role == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                                        <option value="pustu" {{ $user->role == 'pustu' ? 'selected' : '' }}>Pustu</option>
                                                        <option value="klinik" {{ $user->role == 'klinik' ? 'selected' : '' }}>Klinik</option>
                                                        <option value="dinkes" {{ $user->role == 'dinkes' ? 'selected' : '' }}>Dinkes</option>
                                                    @else
                                                        <option value="perawat" {{ $user->role == 'perawat' ? 'selected' : '' }}>Perawat</option>
                                                        <option value="dokter" {{ $user->role == 'dokter' ? 'selected' : '' }}>Dokter</option>
                                                        <option value="farmasi" {{ $user->role == 'farmasi' ? 'selected' : '' }}>Farmasi</option>
                                                        <option value="pendaftaran" {{ $user->role == 'pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

