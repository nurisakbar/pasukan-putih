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
                                    <h5 class="card-title text-primary mb-0">Daftar Data Sasara</h5>
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
                            <form action="{{ route('pasiens.store') }}" method="POST" class="needs-validation" novalidate id="pasien-form">
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

                                <!-- Nomor WhatsApp Section -->
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nomor_whatsapp" class="form-label fw-bold">Nomor WhatsApp</label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('nomor_whatsapp') is-invalid @enderror"
                                            id="nomor_whatsapp" name="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}"
                                            placeholder="Contoh: 081234567890" pattern="[0-9]{10,13}">
                                        @error('nomor_whatsapp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="form-text">Masukkan nomor WhatsApp (10-13 digit angka)</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nama Pendamping Section -->
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nama_pendamping" class="form-label fw-bold">Nama Pendamping</label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('nama_pendamping') is-invalid @enderror"
                                            id="nama_pendamping" name="nama_pendamping" value="{{ old('nama_pendamping') }}"
                                            placeholder="Masukkan nama pendamping (jika ada)">
                                        @error('nama_pendamping')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @else
                                            <div class="form-text">Nama pendamping untuk pasien yang membutuhkan bantuan</div>
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
                                        <button type="submit" class="btn btn-primary px-4" id="submit-btn">
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
                $('#village_search').empty().trigger('change'); 
                $('#village_id').val('');
                $('#district_id').val('');
                $('#regency_id').val('');
                $('#province_id').val('');
                $('#jenis_ktp').val('').trigger('change');
                $('#nomor_whatsapp').val('');
                $('#nama_pendamping').val('');
            }

            function populateVillageData(kelurahanName) {
                if (!kelurahanName) return;

                $.ajax({
                    url: `{{ route('search.village') }}`,
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

            function populateVillageFromData(villageData) {
                if (!villageData || !villageData.village_id) return;

                // Clear existing options
                $('#village_search').empty();
                
                // Create option for Select2 with full data
                const option = new Option(
                    `${villageData.village_name}, ${villageData.district_name}, ${villageData.regency_name}, ${villageData.province_name}`,
                    villageData.village_id,
                    true,
                    true
                );
                
                // Add option and trigger change
                $('#village_search').append(option).trigger('change');
                
                // Populate hidden fields
                $('#village_id').val(villageData.village_id);
                $('#district_id').val(villageData.district_name);
                $('#regency_id').val(villageData.regency_name);
                $('#province_id').val(villageData.province_name);
            }

            // Function to search pasien by NIK
            function searchPasienByNik() {
                const nik = $('#nik').val().trim();

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
                        url: '{{ route("pasiens.search") }}',
                        method: 'GET',
                        data: { nik: nik },
                        success: function (res) {
                            if (res.error) {
                                Swal.fire('Data Tidak Ditemukan', res.error, 'warning');
                                clearFormFields();
                            } else {
                                $('#name').val(res.name || '');
                                $('#jenis_kelamin').val(res.jenis_kelamin || '').trigger('change');
                                $('#alamat').val(res.alamat || '');
                                $('#nomor_whatsapp').val(res.nomor_whatsapp || '');
                                $('#nama_pendamping').val(res.nama_pendamping || '');
                                $('#jenis_ktp').val(res.jenis_ktp || '').trigger('change');
                                $('#tanggal_lahir').val(res.tanggal_lahir || '');
                                $('#rt').val(res.rt || '');
                                $('#rw').val(res.rw || '');
                                
                                // Populate village data if available
                                if (res.village_data) {
                                    populateVillageFromData(res.village_data);
                                }
                                
                                Swal.close();
                                Swal.fire('Berhasil!', 'Data berhasil ditemukan di database.', 'success');
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
            }

            // Trigger search only when search button is clicked
            $('#search-button').on('click', function(e) {
                e.preventDefault();
                searchPasienByNik();
            });

            // Initialize Select2 for village search
            $('#village_search').select2({
                placeholder: 'Cari kelurahan/desa...',
                minimumInputLength: 3,
                allowClear: true,
                ajax: {
                    url: `{{ route('search.village') }}`,
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
                    $('#village_id').val(data.village_id);
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

            // Real-time validation for input fields
            function validateField(fieldId, value, fieldName, rules) {
                const field = $(`#${fieldId}`);
                const feedback = field.siblings('.invalid-feedback');
                
                for (const rule of rules) {
                    if (!rule.test(value)) {
                        field.addClass('is-invalid');
                        feedback.text(rule.message).show();
                        return false;
                    }
                }
                
                field.removeClass('is-invalid');
                feedback.hide();
                return true;
            }

            // NIK validation
            $('#nik').on('input', function() {
                const value = $(this).val().trim();
                const rules = [
                    {
                        test: (val) => val.length === 0 || val.length === 16,
                        message: 'NIK harus 16 digit'
                    },
                    {
                        test: (val) => val.length === 0 || /^\d+$/.test(val),
                        message: 'NIK harus berupa angka'
                    }
                ];
                validateField('nik', value, 'NIK', rules);
            });

            // Name validation
            $('#name').on('input', function() {
                const value = $(this).val().trim();
                const rules = [
                    {
                        test: (val) => val.length === 0 || val.length >= 2,
                        message: 'Nama minimal 2 karakter'
                    }
                ];
                validateField('name', value, 'Nama', rules);
            });

            // RT validation
            $('#rt').on('input', function() {
                const value = $(this).val().trim();
                const rules = [
                    {
                        test: (val) => val.length === 0 || /^\d+$/.test(val),
                        message: 'RT harus berupa angka'
                    }
                ];
                validateField('rt', value, 'RT', rules);
            });

            // RW validation
            $('#rw').on('input', function() {
                const value = $(this).val().trim();
                const rules = [
                    {
                        test: (val) => val.length === 0 || /^\d+$/.test(val),
                        message: 'RW harus berupa angka'
                    }
                ];
                validateField('rw', value, 'RW', rules);
            });

            // WhatsApp validation
            $('#nomor_whatsapp').on('input', function() {
                const value = $(this).val().trim();
                const rules = [
                    {
                        test: (val) => val.length === 0 || /^[0-9]{10,13}$/.test(val),
                        message: 'Nomor WhatsApp harus 10-13 digit angka'
                    }
                ];
                validateField('nomor_whatsapp', value, 'Nomor WhatsApp', rules);
            });

            // Tanggal Lahir validation
            $('#tanggal_lahir').on('change', function() {
                const value = $(this).val();
                const field = $(this);
                const feedback = field.siblings('.invalid-feedback');
                
                if (value) {
                    const birthDate = new Date(value);
                    const today = new Date();
                    const age = today.getFullYear() - birthDate.getFullYear();
                    
                    if (age < 0 || age > 120) {
                        field.addClass('is-invalid');
                        feedback.text('Tanggal Lahir tidak valid').show();
                    } else {
                        field.removeClass('is-invalid');
                        feedback.hide();
                    }
                } else {
                    field.removeClass('is-invalid');
                    feedback.hide();
                }
            });



            // Frontend validation function
            function validateForm() {
                let isValid = true;
                let errorMessages = [];
                
                // Validate NIK
                const nik = $('#nik').val().trim();
                if (!nik) {
                    errorMessages.push('• NIK wajib diisi');
                    isValid = false;
                } else if (nik.length !== 16 || !/^\d+$/.test(nik)) {
                    errorMessages.push('• NIK harus terdiri dari 16 digit angka');
                    isValid = false;
                }
                
                // Validate Name
                const name = $('#name').val().trim();
                if (!name) {
                    errorMessages.push('• Nama wajib diisi');
                    isValid = false;
                } else if (name.length < 2) {
                    errorMessages.push('• Nama minimal 2 karakter');
                    isValid = false;
                }
                
                // Validate Jenis KTP
                const jenisKtp = $('#jenis_ktp').val();
                if (!jenisKtp) {
                    errorMessages.push('• Jenis KTP wajib diisi');
                    isValid = false;
                }
                
                // Validate Tanggal Lahir
                const tanggalLahir = $('#tanggal_lahir').val();
                if (!tanggalLahir) {
                    errorMessages.push('• Tanggal Lahir wajib diisi');
                    isValid = false;
                } else {
                    const birthDate = new Date(tanggalLahir);
                    const today = new Date();
                    const age = today.getFullYear() - birthDate.getFullYear();
                    if (age < 0 || age > 120) {
                        errorMessages.push('• Tanggal Lahir tidak valid');
                        isValid = false;
                    }
                }
                
                // Validate Jenis Kelamin
                const jenisKelamin = $('#jenis_kelamin').val();
                if (!jenisKelamin) {
                    errorMessages.push('• Jenis Kelamin wajib diisi');
                    isValid = false;
                }
                
                // Validate Alamat
                const alamat = $('#alamat').val().trim();
                if (!alamat) {
                    errorMessages.push('• Alamat wajib diisi');
                    isValid = false;
                }
                
                // Validate RT
                const rt = $('#rt').val().trim();
                if (!rt) {
                    errorMessages.push('• RT wajib diisi');
                    isValid = false;
                } else if (!/^\d+$/.test(rt)) {
                    errorMessages.push('• RT harus berupa angka');
                    isValid = false;
                }
                
                // Validate RW
                const rw = $('#rw').val().trim();
                if (!rw) {
                    errorMessages.push('• RW wajib diisi');
                    isValid = false;
                } else if (!/^\d+$/.test(rw)) {
                    errorMessages.push('• RW harus berupa angka');
                    isValid = false;
                }
                
                // Validate Village
                const villageId = $('#village_id').val();
                if (!villageId) {
                    errorMessages.push('• Kelurahan/Desa wajib dipilih');
                    isValid = false;
                }
                
                // Validate Nomor WhatsApp (if filled)
                const nomorWhatsapp = $('#nomor_whatsapp').val().trim();
                if (nomorWhatsapp && !/^[0-9]{10,13}$/.test(nomorWhatsapp)) {
                    errorMessages.push('• Nomor WhatsApp harus 10-13 digit angka');
                    isValid = false;
                }
                
                return { isValid, errorMessages };
            }

            // Handle form submission with SweetAlert
            $('#pasien-form').on('submit', function(e) {
                e.preventDefault();
                
                // Frontend validation
                const validation = validateForm();
                if (!validation.isValid) {
                    Swal.fire({
                        title: 'Validasi Error',
                        html: `<div class="text-start">${validation.errorMessages.join('<br>')}</div>`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                
                const formData = new FormData(this);
                
                Swal.fire({
                    title: 'Menyimpan Data...',
                    text: 'Harap tunggu sebentar.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data pasien berhasil disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("pasiens.index") }}';
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        
                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = [];
                            
                            for (const field in errors) {
                                errorMessages.push(`• ${errors[field].join(', ')}`);
                            }
                            
                            Swal.fire({
                                title: 'Validasi Error',
                                html: `<div class="text-start">${errorMessages.join('<br>')}</div>`,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // Other errors
                            const errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.';
                            Swal.fire({
                                title: 'Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
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
