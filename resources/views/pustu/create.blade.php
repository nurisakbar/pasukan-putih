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
                                    <h5 class="card-title text-primary mb-0">Daftar Data Pustu</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('pustu.store') }}" method="POST" class="needs-validation" novalidate>
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nama_pustu" class="form-label fw-bold">Nama Pustu<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('nama_pustu') is-invalid @enderror"
                                            id="nama_pustu" name="nama_pustu" value="{{ old('nama_pustu') }}"
                                            placeholder="Masukkan nama pustu" required>
                                            <div class="invalid-feedback">
                                                Nama pustu wajib diisi 
                                            </div>
                                    </div>
                                </div>


                                <div class="row mb-4">
                                   <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="name" class="form-label fw-bold">Alamat Pustu<span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-12 col-md-8 col-lg-10 mb-2">
                                        <select id="village_search" name="village_search"
                                            class="form-select custom-select-height" required></select>
                                        <input type="hidden" name="village_id" id="village_id">
                                        <input type="hidden" name="district_id" id="district_id">
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-2 col-md-4">
                                    </div>
                                    <div class="col-lg-9 col-md-8">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-save me-2"></i>Simpan
                                        </button>
                                        <a href="{{ route('pustu.index') }}" class="btn btn-secondary ms-2">
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
                    url: '{{ url('/search-village') }}',
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

                $('#district_id').val(data.district_id);
                $('#village_id').val(data.village_id);
                setTimeout(function() {
                    $('#district').val(data.district_id).trigger('change');

                    setTimeout(function() {
                        $('#village').val(data.village_id).trigger('change');
                    }, 300);
                }, 300);
            });

            function kosongkanForm() {
                $('#id').val('');
                $('#name').val('');
                $('#district').html('<option value="">Pilih Kecamatan</option>');
                $('#village').html('<option value="">Pilih Kelurahan</option>');
            }
        });
    </script>
@endpush
