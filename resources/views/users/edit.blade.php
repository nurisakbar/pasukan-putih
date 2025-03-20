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
                                                    <input type="text" class="form-control mt-1 mb-2" name="name"
                                                        value="{{ old('name', $user->name) }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label for="email">Email</label>
                                                </div>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control mt-1 mb-2" name="email"
                                                        value="{{ old('email', $user->email) }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-lg-2 col-md-4 mb-2">
                                                <label for="telepon" class="form-label ">No Whatsapp <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-lg-10 col-md-8">
                                                <input type="number" name="no_wa"
                                                    class="form-control @error('no_wa') is-invalid @enderror" id=""
                                                    value="{{ old('no_wa', $user->no_wa) }}"
                                                    placeholder="Masukkan no whatsapp" required>
                                                @error('no_wa')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-lg-2 col-md-4 mb-2">
                                                <label for="keterangan" class="form-label ">Keterangan <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-lg-10 col-md-8">
                                                <input type="text" name="keterangan"
                                                    class="form-control @error('keterangan') is-invalid @enderror"
                                                    id="" value="{{ old('keterangan', $user->keterangan) }}"
                                                    placeholder="Keterangan" required>
                                                @error('keterangan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label for="password">Password </label>
                                                </div>
                                                <div class="col-sm-10">
                                                    <input type="password" class="form-control mt-1 mb-2" name="password"
                                                        value="{{ old('password', $user->password) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label for="password_confirmation">Konfirmasi Password</label>
                                                </div>
                                                <div class="col-sm-10">
                                                    <input type="password" class="form-control mt-1 mb-2"
                                                        name="password_confirmation"
                                                        value="{{ old('password', $user->password) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <label for="role">Role</label>
                                                </div>
                                                <div class="col-sm-10">
                                                    <select class="form-control @error('role') is-invalid @enderror"
                                                        name="role" required>
                                                        <option value="">-- Pilih Role --</option>
                                                        @if (Auth::user()->role == 'superadmin')
                                                            <option value="superadmin"
                                                                {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>
                                                                Super Admin</option>
                                                            <option value="puskesmas"
                                                                {{ old('role', $user->role) == 'puskesmas' ? 'selected' : '' }}>
                                                                Puskesmas</option>
                                                            <option value="pustu"
                                                                {{ old('role', $user->role) == 'pustu' ? 'selected' : '' }}>
                                                                Pustu</option>
                                                            {{-- <option value="dinkes">Dinkes</option> --}}
                                                        @elseif(Auth::user()->role == 'puskesmas')
                                                            <option value="pustu"
                                                                {{ old('role', $user->role) == 'pustu' ? 'selected' : '' }}>
                                                                Pustu</option>
                                                        @elseif(Auth::user()->role == 'pustu')
                                                            <option value="perawat"
                                                                {{ old('role', $user->role) == 'perawat' ? 'selected' : '' }}>
                                                                Perawat</option>
                                                            <option value="caregiver"
                                                                {{ old('role', $user->role) == 'caregiver' ? 'selected' : '' }}>
                                                                Caregiver</option>
                                                        @endif
                                                    </select>
                                                    @error('role')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                @if (Auth::user()->role == 'superadmin')
                                                    <div class="form-group parent-field" id="parent-field"
                                                        style="{{ old('role', $user->role) == 'superadmin' ? '' : 'display: none;' }}">
                                                        <label for="parent_id">Parent</label>
                                                        <select
                                                            class="form-control @error('parent_id') is-invalid @enderror"
                                                            name="parent_id">
                                                            <option value="">-- Pilih Parent --</option>
                                                            @foreach ($parents as $parent)
                                                                <option value="{{ $parent->id }}"
                                                                    {{ old('parent_id', $user->parent_id) == $parent->id ? 'selected' : '' }}>
                                                                    {{ $parent->name }} ({{ ucfirst($parent->role) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('parent_id')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                @else
                                                    <input type="hidden" name="parent_id"
                                                        value="{{ old('parent_id', Auth::user()->id) }}">
                                                @endif
                                            </div>
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
