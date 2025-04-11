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
                            <form action="{{ route('pasiens.store') }}" method="POST" class="needs-validation" novalidate>
                                @csrf

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nik" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
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
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="name" class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="jenis_ktp" class="form-label fw-bold ">Jenis KTP <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
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
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="tanggal_lahir" class="form-label fw-bold ">Tanggal Lahir <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="jenis_kelamin" class="form-label fw-bold ">Jenis Kelamin <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
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
                                    <div class="col-12 col-md-4 col-lg-2 mb-2">
                                        <label for="alamat" class="form-label fw-bold">Alamat, Nama Desa <span class="text-danger">*</span></label>
                                    </div>
                                
                                    <div class="col-12 col-md-8 col-lg-3 mb-2">
                                        <input class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" placeholder="Alamat Jalan (Opsional)" required value="{{ old('alamat') }}">
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                    <div class="col-6 col-md-4 col-lg-2 mb-2">
                                        <input type="text" class="form-control @error('rt') is-invalid @enderror" id="rt" name="rt" value="{{ old('rt') }}" placeholder="RT" required>
                                        @error('rt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                    <div class="col-6 col-md-4 col-lg-2 mb-2">
                                        <input type="text" class="form-control @error('rw') is-invalid @enderror" id="rw" name="rw" value="{{ old('rw') }}" placeholder="RW" required>
                                        @error('rw')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                    <div class="col-12 col-md-8 col-lg-3 mb-2">
                                        <select id="village_search" name="village_search"  class="form-select custom-select-height" required></select>
                                        <input type="hidden" name="village_id" id="village_id">
                                        <input type="hidden" name="district_id" id="district_id">
                                        <input type="hidden" name="regency_id" id="regency_id">
                                        <input type="hidden" name="province_id" id="province_id">

                                    </div>
                                </div>
                                

                                <div class="row mt-4">
                                    <div class="col-lg-2 col-md-4">
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
                                text: `${item.village_name}, ${item.district_name}, ${item.regency_name}, ${item.province_name}`,
                                fullData: item
                            };
                        })
                    };
                },
                cache: true
            }
        });

        // Setelah desa dipilih, isi semua select otomatis
        $('#village_search').on('select2:select', function (e) {
            const data = e.params.data.fullData;

            $('#province_id').val(data.province_name);
            $('#regency_id').val(data.regency_name);
            $('#district_id').val(data.district_name);
            $('#village_id').val(data.village_name);
            
            // Set Provinsi dan trigger change
            $('#province').val(data.province_id).trigger('change');

            // Tunggu request Ajax kabupaten selesai dulu
            setTimeout(function () {
                $('#regency').val(data.regency_id).trigger('change');

                setTimeout(function () {
                    $('#district').val(data.district_id).trigger('change');

                    setTimeout(function () {
                        $('#village').val(data.village_id).trigger('change');
                    }, 300);
                }, 300);
            }, 300);
        });

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
            // const loader = document.getElementById('loading-indicator');
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
