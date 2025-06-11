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
                                    <h5 class="card-title text-primary mb-0">Daftar Data Sasaran</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" id="error-alert">
                                    <strong>Validasi Error!</strong>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <form action="{{ route('pasiens.store') }}" method="POST" class="needs-validation" novalidate>
                                @csrf
                                
                                <!-- NIK Section -->
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nik" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8 col-md-6">
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                            id="nik" name="nik" value="{{ old('nik') }}"
                                            placeholder="Masukkan NIK 16 digit" minlength="16" maxlength="16" 
                                            pattern="[0-9]{16}" required>
                                        @error('nik')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-2 col-md-2">
                                        <button type="button" class="btn btn-primary" id="search-button">
                                            <i class="bi bi-search me-1"></i>Cari
                                        </button>
                                    </div>
                                </div>

                                <!-- Loading Indicator (Single) -->
                                <div id="loading-indicator" class="p-3 d-none">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="spinner-border text-primary spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-3 fs-5 text-secondary">Mencari data...</span>
                                    </div>
                                </div>

                                <!-- Name Section -->
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="name" class="form-label fw-bold">Nama <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Masukkan nama lengkap" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Nama wajib diisi</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Jenis KTP Section -->
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="jenis_ktp" class="form-label fw-bold">Jenis KTP <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <select class="form-select @error('jenis_ktp') is-invalid @enderror" id="jenis_ktp" name="jenis_ktp" required>
                                            <option value="" selected disabled>Pilih jenis KTP</option>
                                            <option value="DKI" {{ old('jenis_ktp') == 'DKI' ? 'selected' : '' }}>DKI Jakarta</option>
                                            <option value="Non DKI" {{ old('jenis_ktp') == 'Non DKI' ? 'selected' : '' }}>Non DKI Jakarta</option>
                                        </select>
                                        @error('jenis_ktp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Jenis KTP wajib diisi</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tanggal Lahir Section -->
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="tanggal_lahir" class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                            id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                        @error('tanggal_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Tanggal Lahir wajib diisi</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Jenis Kelamin Section -->
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="jenis_kelamin" class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <select class="form-select @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" name="jenis_kelamin" required>
                                            <option value="" selected disabled>Pilih jenis kelamin</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Jenis Kelamin wajib diisi</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Address Section -->
                                <div class="row mb-4">
                                    <div class="col-12 col-md-4 col-lg-2 mb-2">
                                        <label for="alamat" class="form-label fw-bold">Alamat, Nama Desa <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-8 col-lg-3 mb-2">
                                        <input class="form-control @error('alamat') is-invalid @enderror" id="alamat"
                                            name="alamat" placeholder="Alamat Jalan" value="{{ old('alamat') }}">
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2 mb-2">
                                        <input type="text" class="form-control @error('rt') is-invalid @enderror"
                                            id="rt" name="rt" value="{{ old('rt') }}" placeholder="RT" required>
                                        @error('rt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-6 col-md-4 col-lg-2 mb-2">
                                        <input type="text" class="form-control @error('rw') is-invalid @enderror"
                                            id="rw" name="rw" value="{{ old('rw') }}" placeholder="RW" required>
                                        @error('rw')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-8 col-lg-3 mb-2">
                                        <select id="village_search" name="village_search" class="form-select" required>
                                            <option value="">Pilih Kelurahan/Desa</option>
                                        </select>
                                        <input type="hidden" name="village_id" id="village_id">
                                        <input type="hidden" name="district_id" id="district_id">
                                        <input type="hidden" name="regency_id" id="regency_id">
                                        <input type="hidden" name="province_id" id="province_id">
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row mt-4">
                                    <div class="col-lg-2 col-md-4"></div>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function clearFormFields() {
                $('#name').val('');
                $('#jenis_kelamin').val('').trigger('change');
                $('#alamat').val('');
                $('#tanggal_lahir').val('');
                $('#rt').val('');
                $('#rw').val('');
                $('#village_search').val(null).trigger('change'); 
                $('#village_id').val('');
                $('#district_id').val('');
                $('#regency_id').val('');
                $('#province_id').val('');
                $('#jenis_ktp').val('').trigger('change');
            }

            function populateVillageData(kelurahanName) {
                if (!kelurahanName) return;

                $.ajax({
                    url: `{{ url('search-village') }}`,
                    method: 'GET',
                    data: { q: kelurahanName },
                    success: function(villages) {
                        if (villages && villages.length > 0) {
                            const village = villages[0]; 
                            
                            // Create option for Select2
                            const option = new Option(
                                `${village.village_name}, ${village.district_name}, ${village.regency_name}, ${village.province_name}`,
                                village.village_id,
                                true,
                                true
                            );
                            
                            // Add option and trigger change
                            $('#village_search').append(option).trigger('change');
                            
                            // Populate hidden fields
                            $('#village_id').val(village.village_name);
                            $('#district_id').val(village.district_name);
                            $('#regency_id').val(village.regency_name);
                            $('#province_id').val(village.province_name);
                        }
                    },
                    error: function(xhr) {
                        console.log('Error searching village:', xhr.responseText);
                    }
                });
            }

            $('#nik').on('change blur', function () {
                const nik = $(this).val().trim();

                if (nik.length === 16 && /^\d+$/.test(nik)) {
                    Swal.fire({
                        title: 'Mencari data...',
                        text: 'Harap tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route("pasiens.carik") }}',
                        method: 'GET',
                        data: { nik: nik },
                        success: function (res) {
                            // console.log('Carik API Response:', res);
                            
                            if (res.error) {
                                Swal.fire('Data Tidak Ditemukan', res.error, 'warning');
                                clearFormFields();
                            } else {
                                $('#name').val(res.nama || '');
                                $('#jenis_kelamin').val(res.jenis_kelamin || '').trigger('change');
                                $('#alamat').val(res.alamat || '');
                                
                                // Set jenis KTP based on city
                                if (res.nama_kota && res.nama_kota.toLowerCase().includes('jakarta')) {
                                    $('#jenis_ktp').val('DKI').trigger('change');
                                } else {
                                    $('#jenis_ktp').val('Non DKI').trigger('change');
                                }

                                if (res.nama_kelurahan) {
                                    populateVillageData(res.nama_kelurahan);
                                }
                                Swal.close();
                                Swal.fire('Berhasil!', 'Data berhasil diisi dari Si CARIK.', 'success');
                            }
                        },
                        error: function (xhr) {
                            Swal.close();
                            const errorMessage = xhr.responseJSON?.error || 'Gagal mengambil data dari server.';
                            Swal.fire('Terjadi Kesalahan', errorMessage, 'error');
                            clearFormFields();
                        }
                    });
                } else if (nik.length === 0) {
                    // Clear form if NIK is empty
                    clearFormFields();
                } else if (nik.length > 0 && nik.length !== 16) {
                    Swal.fire('Format NIK Salah', 'NIK harus terdiri dari 16 digit angka.', 'warning');
                    clearFormFields();
                }
            });

            // Also trigger search when search button is clicked
            $('#search-button').on('click', function(e) {
                e.preventDefault();
                $('#nik').trigger('change');
            });

            // Initialize Select2 for village search
            $('#village_search').select2({
                placeholder: 'Cari kelurahan/desa...',
                minimumInputLength: 3,
                allowClear: true,
                ajax: {
                    url: `{{ url('search-village') }}`,
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

            // Handle village selection
            $('#village_search').on('select2:select', function(e) {
                const data = e.params.data.fullData;
                
                if (data) {
                    $('#village_id').val(data.village_name);
                    $('#district_id').val(data.district_name);
                    $('#regency_id').val(data.regency_name);
                    $('#province_id').val(data.province_name);
                }
            });

            // Clear hidden fields when village is cleared
            $('#village_search').on('select2:clear', function(e) {
                $('#village_id').val('');
                $('#district_id').val('');
                $('#regency_id').val('');
                $('#province_id').val('');
            });
        });
    </script>
    <script>

        if ($('#error-alert').length > 0) {
            setTimeout(function() {
                $('#error-alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 4000); // 4 seconds
        }

    </script>
@endpush
