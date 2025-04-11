@extends('layouts.app')

@push('style')
<style>
    .custom-select-height {
        height: calc(2.375rem + 2px);
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
    }

    .select2-container {
    width: 100% !important;
    }

    .select2-selection--single {
        height: calc(2.375rem + 2px) !important;
        display: flex !important;
        align-items: center;
        padding-left: 0.75rem;
        font-size: 1rem;
        border-radius: 0.375rem;
    }

    /* Pastikan teks di tengah */
    .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0 !important;
        margin-left: 0 !important;
    }

</style>
@endpush

@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5 class="card-title">EDIT PENGGUNA</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-12">

                                        {{-- Name --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="name" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name', $user->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Email --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ old('email', $user->email) }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- No Whatsapp --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="no_wa" class="form-label fw-bold">No Whatsapp <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="number" name="no_wa"
                                                    class="form-control @error('no_wa') is-invalid @enderror"
                                                    value="{{ old('no_wa', $user->no_wa) }}" placeholder="Masukkan no whatsapp" required>
                                                @error('no_wa')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Keterangan --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="keterangan" class="form-label fw-bold">Keterangan <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="text" name="keterangan"
                                                    class="form-control @error('keterangan') is-invalid @enderror"
                                                    value="{{ old('keterangan', $user->keterangan) }}" placeholder="Keterangan" required>
                                                @error('keterangan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="password" class="form-label fw-bold">Password</label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    value="{{ old('password', $user->password) }}">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password Confirmation --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="password" name="password_confirmation"
                                                    class="form-control"
                                                    value="{{ old('password_confirmation', $user->password) }}">
                                            </div>
                                        </div>

                                        {{-- Role --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <select class="form-control @error('role') is-invalid @enderror" name="role" required>
                                                    <option value="">-- Pilih Role --</option>
                                                    @if (Auth::user()->role == 'superadmin')
                                                        <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                                        <option value="puskesmas" {{ old('role', $user->role) == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                                        <option value="pustu" {{ old('role', $user->role) == 'pustu' ? 'selected' : '' }}>Pustu</option>
                                                    @elseif(Auth::user()->role == 'puskesmas')
                                                        <option value="pustu" {{ old('role', $user->role) == 'pustu' ? 'selected' : '' }}>Pustu</option>
                                                    @elseif(Auth::user()->role == 'pustu')
                                                        <option value="perawat" {{ old('role', $user->role) == 'perawat' ? 'selected' : '' }}>Perawat</option>
                                                        <option value="caregiver" {{ old('role', $user->role) == 'caregiver' ? 'selected' : '' }}>Caregiver</option>
                                                    @endif
                                                </select>
                                                @error('role')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Parent (jika superadmin) --}}
                                        @if (Auth::user()->role == 'superadmin')
                                            <div class="row mb-4 parent-field" id="parent-field"
                                                style="{{ old('role', $user->role) == 'superadmin' ? '' : 'display: none;' }}">
                                                <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                    <label for="parent_id" class="form-label fw-bold">Parent</label>
                                                </div>
                                                <div class="col-12 col-md-8 col-lg-10">
                                                    <select class="form-control @error('parent_id') is-invalid @enderror" name="parent_id">
                                                        <option value="">-- Pilih Parent --</option>
                                                        @foreach ($parents as $parent)
                                                            <option value="{{ $parent->id }}"
                                                                {{ old('parent_id', $user->parent_id) == $parent->id ? 'selected' : '' }}>
                                                                {{ $parent->name }} ({{ ucfirst($parent->role) }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('parent_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @else
                                            <input type="hidden" name="parent_id" value="{{ old('parent_id', Auth::user()->id) }}">
                                        @endif

                                        <div class="row mb-4">
                                            <div class="col-lg-2 col-md-4 mb-2">
                                                <label for="no_wa" class="form-label fw-bold">No Whatsapp <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-lg-10 col-md-8">
                                                <input type="no_wa" class="form-control @error('no_wa') is-invalid @enderror"
                                                    id="no_wa" name="no_wa" value="{{ old('no_wa', $user->no_wa) }}"
                                                    placeholder="Masukkan no whatsapp" required>
                                                @error('no_wa')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-lg-2 col-md-4 mb-2">
                                                <label for="status_pegawai" class="form-label fw-bold">Status Pegawai <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-lg-10 col-md-8">
                                                <input type="status_pegawai" class="form-control @error('status_pegawai') is-invalid @enderror"
                                                    id="status_pegawai" name="status_pegawai" value="{{ old('status_pegawai', $user->status_pegawai) }}"
                                                    placeholder="Masukkan status pegawai" required>
                                                @error('status_pegawai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Alamat dan Desa --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="alamat" class="form-label fw-bold">Alamat, Nama Desa <span class="text-danger">*</span></label>
                                            </div>

                                            <div class="col-12 col-md-8 col-lg-3 mb-2">
                                                <input class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                                                    placeholder="Alamat Jalan (Opsional)" value="{{ old('alamat', $user->alamat) }}">
                                                @error('alamat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-8 col-lg-7 mb-2">
                                                <select id="village_search" name="village_search" class="form-select custom-select-height" required></select>
                                                <input type="hidden" name="village" id="village_id">
                                                <input type="hidden" name="district" id="district_id">
                                                <input type="hidden" name="regency" id="regency_id">
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
    @php
        $villageOld = [
            'village_name' => old('village_name', $user->village ?? ''),
            'district_name' => old('district_name', $user->district ?? ''),
            'regency_name' => old('regency_name', $user->regency ?? ''),
        ];
    @endphp
@endsection

@push('script')
    <script>
        const oldVillage = @json($villageOld);

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
            $('#village_search').select2({
                placeholder: 'Cari kelurahan/desa...',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ url("/apps/pasukanputih/search-village") }}',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.village_id,
                                    text: `${item.village_name}, ${item.district_name}, ${item.regency_name}`,
                                    fullData: item
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Isi form saat user pilih desa
            $('#village_search').on('select2:select', function (e) {
                const data = e.params.data.fullData;
                $('#regency_id').val(data.regency_name);
                $('#district_id').val(data.district_name);
                $('#village_id').val(data.village_name);
            });

            // Isi value lama jika ada
            if (oldVillage) {
                const option = new Option(
                    `${oldVillage.village_name}, ${oldVillage.district_name}, ${oldVillage.regency_name}`,
                    oldVillage.village_id,
                    true,
                    true
                );

                $('#village_search').append(option).trigger('change');

                $('#regency_id').val(oldVillage.regency_name);
                $('#district_id').val(oldVillage.district_name);
                $('#village_id').val(oldVillage.village_name);
            }
        });

    </script>
@endpush
