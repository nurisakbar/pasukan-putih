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
                <div class="col-lg-12 col-md-12 mt-2">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title text-primary mb-0">Daftar Data Pasien</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Terjadi kesalahan!</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('users.store') }}" method="POST" class="needs-validation" novalidate>
                                @csrf

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nik" class="form-label fw-bold">NIK <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror nik"
                                            id="nik" name="nik" value="{{ old('nik') }}"
                                            placeholder="Masukkan NIK" required>
                                        @error('nik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div id="loading-indicator" class="p-3 d-none">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-3 fs-5 text-secondary">Mencari data...</span>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="name" class="form-label fw-bold">Nama <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Masukkan nama lengkap" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="email" class="form-label fw-bold">Email <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}"
                                            placeholder="Masukkan Email" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="jenis_ktp" class="form-label fw-bold ">Jenis KTP <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <select class="form-select @error('jenis_ktp') is-invalid @enderror" id="jenis_ktp"
                                            name="jenis_ktp" required>
                                            <option value="" selected disabled>Pilih jenis KTP</option>
                                            <option value="DKI" {{ old('jenis_ktp') == 'DKI' ? 'selected' : '' }}>DKI
                                            </option>
                                            <option value="Non DKI" {{ old('jenis_ktp') == 'Non DKI' ? 'selected' : '' }}>
                                                Non DKI</option>
                                        </select>
                                        @error('jenis_ktp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="tanggal_lahir" class="form-label fw-bold ">Tanggal Lahir <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="date"
                                            class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                            id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                            required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="jenis_kelamin" class="form-label fw-bold ">Jenis Kelamin <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="" selected disabled>Pilih jenis kelamin</option>
                                            <option value="Laki-laki"
                                                {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                                            </option>
                                            <option value="Perempuan"
                                                {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan
                                            </option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="role" class="form-label fw-bold ">Role <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <select class="form-control @error('role') is-invalid @enderror" name="role"
                                            required>
                                            <option value="">-- Pilih Role --</option>
                                            @if (Auth::user()->role == 'superadmin')
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
                                </div>

                                @if (Auth::user()->role == 'superadmin')
                                    <div class="row mb-4" id="puskesmas-field" style="display: none;">
                                        <div class="col-lg-2 col-md-4 mb-2">
                                            <label for="nama pustu/puskesmas" class="form-label fw-bold">Nama Pustu/Puskesmas <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-10 col-md-8">
                                            <select class="form-control @error('parent_id') is-invalid @enderror"
                                                    name="parent_id">
                                                    <option value="">-- Pilih Parent --</option>
                                                    @foreach ($parents as $parent)
                                                        <option value="{{ $parent->id }}">{{ $parent->name }}
                                                            ({{ ucfirst($parent->role) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('parent_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                        </div>
                                    </div>
                                @endif

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="no_wa" class="form-label fw-bold">No Whatsapp <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="no_wa" class="form-control @error('no_wa') is-invalid @enderror"
                                            id="no_wa" name="no_wa" value="{{ old('no_wa') }}"
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
                                            id="status_pegawai" name="status_pegawai" value="{{ old('status_pegawai') }}"
                                            placeholder="Masukkan status pegawai" required>
                                        @error('status_pegawai')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="keterangan" class="form-label fw-bold">Keterangan <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="keterangan" class="form-control @error('keterangan') is-invalid @enderror"
                                            id="keterangan" name="keterangan" value="{{ old('keterangan') }}"
                                            placeholder="Masukkan keterangan" required>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-12 col-md-4 col-lg-2 mb-2">
                                        <label for="alamat" class="form-label fw-bold">Alamat, Nama Desa <span
                                                class="text-danger">*</span></label>
                                    </div>

                                    <div class="col-12 col-md-8 col-lg-3 mb-2">
                                        <input class="form-control @error('alamat') is-invalid @enderror" id="alamat"
                                            name="alamat" placeholder="Alamat Jalan (Opsional)"
                                            value="{{ old('alamat') }}">
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-8 col-lg-7 mb-2">
                                        <select id="village_search" name="village_search"
                                            class="form-select custom-select-height" required></select>
                                        <input type="hidden" name="village" id="village_id">
                                        <input type="hidden" name="district" id="district_id">
                                        <input type="hidden" name="regency" id="regency_id">

                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="password" class="form-label fw-bold">Password <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" placeholder="Masukkan password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password
                                            <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="Ulangi password" required>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-2 col-md-4">
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-save me-2"></i>Simpan
                                        </button>
                                        <a href="/users?role=perawat" class="btn btn-secondary ms-2">
                                            <i class="bi bi-arrow-left me-2"></i>Kembali
                                        </a>
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

@push('script')
    <script>
        $(document).ready(function() {
            // Show/hide parent field based on role selection (for superadmin only)
            @if (Auth::user()->role == 'superadmin')
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

        $('#village_search').select2({
            placeholder: 'Cari kelurahan/desa...',
            minimumInputLength: 3,
            ajax: {
                url: '{{ url('/apps/pasukanputih/search-village') }}',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(item) {
                            return {
                                id: item.village_id,
                                text: `${item.village_name}, ${item.district_name}, ${item.regency_name}, ${item.province_name}`,
                                fullData: item
                            };
                        })
                    };
                },
                cache: true
            }
        });

        $('#village_search').on('select2:select', function(e) {
            const data = e.params.data.fullData;

            $('#province_id').val(data.province_name);
            $('#regency_id').val(data.regency_name);
            $('#district_id').val(data.district_name);
            $('#village_id').val(data.village_name);

            // Jika kamu punya dropdown provinsi yang menampilkan teks (bukan id), dan ingin langsung sinkron
            $('#province').val(data.province_name).trigger('change');

            setTimeout(function() {
                $('#regency').val(data.regency_name).trigger('change');

                setTimeout(function() {
                    $('#district').val(data.district_name).trigger('change');

                    setTimeout(function() {
                        $('#village').val(data.village_name).trigger('change');
                    }, 300);
                }, 300);
            }, 300);
        });
    </script>
@endpush
