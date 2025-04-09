@extends('layouts.app')

@section('content')
    <div class="app-content-header bg-light py-3 mb-4">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h3 class="mb-0 text-primary">Pasien</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="card-title text-primary mb-0">Daftar Data Pasien</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('pasiens.store') }}" method="POST" class="needs-validation" novalidate>
                                @csrf

                                <div class="row mb-4">
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <label for="nik" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror nik" id="nik" name="nik" value="{{ old('nik') }}" placeholder="Masukkan NIK" required>
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
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <label for="name" class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <label for="jenis_ktp" class="form-label fw-bold ">Jenis KTP <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <select class="form-select @error('jenis_ktp') is-invalid @enderror" id="jenis_ktp" name="jenis_ktp" required>
                                            <option value="" selected disabled>Pilih jenis KTP</option>
                                            <option value="DKI" {{ old('jenis_ktp') == 'DKI' ? 'selected' : '' }}>DKI</option>
                                            <option value="Non DKI" {{ old('jenis_ktp') == 'Non DKI' ? 'selected' : '' }}>Non DKI</option>
                                        </select>
                                        @error('jenis_ktp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <label for="tanggal_lahir" class="form-label fw-bold ">Tanggal Lahir <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <label for="jenis_kelamin" class="form-label fw-bold ">Jenis Kelamin <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="" selected disabled>Pilih jenis kelamin</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row mb-4">
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <label for="alamat" class="form-label fw-bold ">Alamat <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap" required>{{ old('alamat') }}</textarea>
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>                                
                                <div class="row mb-4">
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <label class="form-label fw-bold">Detail Wilayah</label>
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body p-3">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="province" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                                        <select name="province_id" id="province" class="form-select select2 @error('province_id') is-invalid @enderror" required>
                                                            <option value="">Pilih Provinsi</option>
                                                            @foreach ($provinces as $province)
                                                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                                    {{ $province->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('province_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <label for="regency" class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                                                        <select name="regency_id" id="regency" class="form-select select2 @error('regency_id') is-invalid @enderror" required>
                                                            <option value="">Pilih Kabupaten/Kota</option>
                                                        </select>
                                                        @error('regency_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <label for="district" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                                        <select name="district_id" id="district" class="form-select select2 @error('district_id') is-invalid @enderror" required>
                                                            <option value="">Pilih Kecamatan</option>
                                                        </select>
                                                        @error('district_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <label for="village" class="form-label">Kelurahan <span class="text-danger">*</span></label>
                                                        <select name="village_id" id="village" class="form-select select2 @error('village_id') is-invalid @enderror" required>
                                                            <option value="">Pilih Kelurahan</option>
                                                        </select>
                                                        @error('village_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-lg-3 col-md-4">
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-save me-2"></i>Simpan
                                        </button>
                                        <a href="{{ route('pasiens.index') }}" class="btn btn-secondary ms-2">
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
    $(document).ready(function () {
        const defaultProvinceId = 31; // DKI Jakarta
        const defaultRegencyId = 3173; // Jakarta Pusat

        // Initialize select2
        $('.select2').select2({
            width: '100%',
        });

        // Form validation
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Load Kabupaten/Kota berdasarkan Provinsi
        $('#province').change(function () {
            var province_id = $(this).val();
            $('#regency').html('<option value="">Pilih Kabupaten/Kota</option>');
            $('#district').html('<option value="">Pilih Kecamatan</option>');
            $('#village').html('<option value="">Pilih Kelurahan</option>');

            if (province_id) {
                $.ajax({
                    url: '/get-regencies/' + province_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $.each(data, function (index, regency) {
                            $('#regency').append('<option value="' + regency.id + '">' + regency.name + '</option>');
                        });
                        $('#regency').prop('disabled', false);

                        // Jika default province, set default regency
                        if (province_id == defaultProvinceId) {
                            $('#regency').val(defaultRegencyId).trigger('change');
                        }
                    },
                    error: function () {
                        alert('Terjadi kesalahan saat memuat data kabupaten/kota');
                    }
                });
            } else {
                $('#regency').prop('disabled', true);
                $('#district').prop('disabled', true);
                $('#village').prop('disabled', true);
            }
        });

        // Load Kecamatan berdasarkan Kabupaten/Kota
        $('#regency').change(function () {
            var regency_id = $(this).val();
            $('#district').html('<option value="">Pilih Kecamatan</option>');
            $('#village').html('<option value="">Pilih Kelurahan</option>');

            if (regency_id) {
                $.ajax({
                    url: '/get-districts/' + regency_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $.each(data, function (index, district) {
                            $('#district').append('<option value="' + district.id + '">' + district.name + '</option>');
                        });
                        $('#district').prop('disabled', false);
                    },
                    error: function () {
                        alert('Terjadi kesalahan saat memuat data kecamatan');
                    }
                });
            } else {
                $('#district').prop('disabled', true);
                $('#village').prop('disabled', true);
            }
        });

        // Load Kelurahan berdasarkan Kecamatan
        $('#district').change(function () {
            var district_id = $(this).val();
            $('#village').html('<option value="">Pilih Kelurahan</option>');

            if (district_id) {
                $.ajax({
                    url: '/get-villages/' + district_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $.each(data, function (index, village) {
                            $('#village').append('<option value="' + village.id + '">' + village.name + '</option>');
                        });
                        $('#village').prop('disabled', false);
                    },
                    error: function () {
                        alert('Terjadi kesalahan saat memuat data kelurahan');
                    }
                });
            } else {
                $('#village').prop('disabled', true);
            }
        });

        // Set default DKI Jakarta & Jakarta Pusat
        $('#province').val(defaultProvinceId).trigger('change');

        // Nik Autoload
        $('.nik').on('input', function () {
            const nik = $(this).val().trim();

            if (nik.length > 0) {
                toggleLoading(true);

                $.ajax({
                    url: "{{ route('pasiens.nik') }}",
                    type: "GET",
                    data: { nik: nik },
                    dataType: "json",
                    success: function (response) {
                        if (response.message === "Pasien ditemukan") {
                            $('#name').val(response.data.name);
                            $('#alamat').val(response.data.alamat);
                            $('#jenis_kelamin').val(response.data.jenis_kelamin);
                            $('#jenis_ktp').val(response.data.jenis_ktp);
                            $('#tanggal_lahir').val(response.data.tanggal_lahir);
                            $('#province').val(response.data.province_id).trigger('change');

                            setTimeout(function () {
                                $('#regency').val(response.data.regency_id).trigger('change');

                                setTimeout(function () {
                                    $('#district').val(response.data.district_id).trigger('change');

                                    setTimeout(function () {
                                        $('#village').val(response.data.village_id);
                                    }, 300);
                                }, 300);
                            }, 300);
                        } else {
                            kosongkanForm();
                        }
                    },
                    error: function () {
                        kosongkanForm();
                    },
                    complete: function () {
                        toggleLoading(false);
                    }
                });
            }
        });

        function toggleLoading(show) {
            const loader = document.getElementById('loading-indicator');
            if (show) {
                loader.classList.remove('d-none');
            } else {
                loader.classList.add('d-none');
            }
        }

        function kosongkanForm() {
            $('#id').val('');
            $('#name').val('');
            $('#alamat').val('');
            $('#province').val('').trigger('change');
            $('#regency').html('<option value="">Pilih Kabupaten/Kota</option>');
            $('#district').html('<option value="">Pilih Kecamatan</option>');
            $('#village').html('<option value="">Pilih Kelurahan</option>');
        }
    });
</script>
@endpush
