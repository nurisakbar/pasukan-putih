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
            <div class="col-sm-7">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h5 class="card-title">Tambah Users</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="no_wa">No Whatsapp</label>
                                        <input type="number" class="form-control @error('no_wa') is-invalid @enderror" name="no_wa" value="{{ old('no_wa') }}" required>
                                        @error('no_wa')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="keterangan">Keterangan </label>
                                        <input type="text" class="form-control @error('keterangan') is-invalid @enderror" name="keterangan" value="{{ old('keterangan') }}" required>
                                        @error('keterangan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirm Password</label>
                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required>
                                        @error('password_confirmation')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select class="form-control @error('role') is-invalid @enderror" name="role" required>
                                            <option value="">-- Pilih Role --</option>
                                            @if(Auth::user()->role == 'superadmin')
                                                <option value="superadmin">Super Admin</option>
                                                <option value="puskesmas">Puskesmas</option>
                                                <option value="pustu">Pustu</option>
                                                {{-- <option value="dinkes">Dinkes</option> --}}
                                            @elseif(Auth::user()->role == 'puskesmas')
                                                <option value="pustu">Pustu</option>
                                            @elseif(Auth::user()->role == 'pustu')
                                                <option value="perawat">Perawat</option>
                                                <option value="caregiver">Caregiver</option>
                                            @endif
                                        </select>
                                        @error('role')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    
                                    @if(Auth::user()->role == 'superadmin')
                                    <div class="form-group parent-field" id="parent-field" style="display: none;">
                                        <label for="parent_id">Parent</label>
                                        <select class="form-control @error('parent_id') is-invalid @enderror" name="parent_id">
                                            <option value="">-- Pilih Parent --</option>
                                            @foreach($parents as $parent)
                                                <option value="{{ $parent->id }}">{{ $parent->name }} ({{ ucfirst($parent->role) }})</option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    @else
                                    <input type="hidden" name="parent_id" value="{{ Auth::user()->id }}">
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 mt-3">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
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

@push('scripts')
<script>
    $(document).ready(function() {
        // Show/hide parent field based on role selection (for superadmin only)
        @if(Auth::user()->role == 'superadmin')
        $('select[name="role"]').on('change', function() {
            var selectedRole = $(this).val();
            if (selectedRole == 'pustu' || selectedRole == 'dokter' || 
                selectedRole == 'perawat' || selectedRole == 'farmasi' || 
                selectedRole == 'pendaftaran') {
                $('#parent-field').show();
            } else {
                $('#parent-field').hide();
            }
        });
        @endif
    });
</script>
@endpush
@endsection