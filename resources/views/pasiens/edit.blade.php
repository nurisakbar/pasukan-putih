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
                                    <h5 class="card-title text-primary mb-0">Edit Data Sasaran</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('pasiens.update', $pasien->id) }}" method="POST" class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nik" class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('nik') is-invalid @enderror nik" id="nik" name="nik" value="{{ old('nik', $pasien->nik) }}" placeholder="Masukkan NIK" required>
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
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pasien->name) }}" placeholder="Masukkan nama lengkap" required>
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
                                            <option value="" disabled>Pilih jenis KTP</option>
                                            <option value="DKI" {{ old('jenis_ktp', $pasien->jenis_ktp) == 'DKI' ? 'selected' : '' }}>DKI</option>
                                            <option value="Non DKI" {{ old('jenis_ktp', $pasien->jenis_ktp) == 'Non DKI' ? 'selected' : '' }}>Non DKI</option>
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
                                        <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pasien->tanggal_lahir) }}" required>
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
                                            <option value="" disabled>Pilih jenis kelamin</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
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
                                        <input class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" placeholder="Alamat Jalan (Opsional)" required value="{{ old('alamat', $pasien->alamat) }}">
                                        @error('alamat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                    <div class="col-6 col-md-4 col-lg-2 mb-2">
                                        <input type="text" class="form-control @error('rt') is-invalid @enderror" id="rt" name="rt" value="{{ old('rt', $pasien->rt) }}" placeholder="RT" required>
                                        @error('rt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                    <div class="col-6 col-md-4 col-lg-2 mb-2">
                                        <input type="text" class="form-control @error('rw') is-invalid @enderror" id="rw" name="rw" value="{{ old('rw', $pasien->rw) }}" placeholder="RW" required>
                                        @error('rw')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                
                                    <div class="col-12 col-md-8 col-lg-3 mb-2">
                                        <select id="village_search" name="village_search" class="form-select custom-select-height" required>
                                            @if($selectedVillage)
                                                <option 
                                                    value="{{ $selectedVillage->village_id }}" 
                                                    data-full="{{ json_encode($selectedVillage) }}" 
                                                    selected>
                                                    {{ $selectedVillage->village_name }}
                                                </option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="village_id" id="village_id" value="{{ $pasien->village_id }}">
                                    </div>                                    
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-2 col-md-4">
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-save me-2"></i>Update
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
        
        $(document).ready(function() {
            // Initialize select2 with better styling
            $('.select2').select2({
                width: '100%',
            });

            // Form validation
            (function() {
                'use strict';
                var forms = document.querySelectorAll('.needs-validation');
                Array.prototype.slice.call(forms).forEach(function(form) {
                    form.addEventListener('submit', function(event) {
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
                    url: '{{ route('search.village') }}',
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

            // Saat user pilih dari hasil pencarian
            $('#village_search').on('select2:select', function (e) {
                const data = e.params.data.fullData;
                $('#village_id').val(data.village_id);
            });

            // Saat page load: inject fullData dari option default
            const selectedOption = $('#village_search').find('option:selected');
            if (selectedOption.length && selectedOption.data('full')) {
                const data = JSON.parse(selectedOption.attr('data-full'));
                const option = new Option(
                    `${data.village_name}, ${data.district_name}, ${data.regency_name}, ${data.province_name}`,
                    data.village_id,
                    true,
                    true
                );
                option.fullData = data; // inject agar select2:select bisa jalan
                $('#village_search').append(option).trigger('change');
                $('#village_id').val(data.village_id);
            }
        });
    </script>
@endpush
