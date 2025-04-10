@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-center">Edit Profil</h3>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.updateProfile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_wa" class="form-label">No WhatsApp</label>
                            <input type="text" id="no_wa" name="no_wa" value="{{ old('no_wa', $user->no_wa) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $user->keterangan) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="status_pegawai" class="form-label">Status Pegawai</label>
                            <input type="text" id="status_pegawai" name="status_pegawai" value="{{ old('status_pegawai', $user->status_pegawai) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru (Opsional)</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-3 py-2">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
