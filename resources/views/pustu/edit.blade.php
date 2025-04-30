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
                                    <h5 class="card-title text-primary mb-0">Edit Data Pustu</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('pustu.update', $pustu->id) }}" method="POST" class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <label for="nama_pustu" class="form-label fw-bold">Nama Pustu<span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input type="text" class="form-control @error('nama_pustu') is-invalid @enderror" id="nama_pustu" name="nama_pustu" value="{{ old('nama_pustu', $pustu->nama_pustu) }}" placeholder="Masukkan nama pustu" required>
                                        @error('nama_pustu')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-12 col-md-4 col-lg-2 mb-2">
                                        <label for="alamat" class="form-label fw-bold">Alamat Pustu <span class="text-danger">*</span></label>
                                    </div>
                                
                               
                                    <div class="col-12 col-md-8 col-lg-10 mb-2">
                                        <select id="village_search" name="village_search" class="form-select custom-select-height" required>
                                             @if($selectedVillage)
                                                 <option 
                                                     value="{{ $selectedVillage->village_id }}" 
                                                     data-full="{{ json_encode($selectedVillage) }}" 
                                                     selected>
                                                     @if($selectedVillage->village_name)
                                                         {{ $selectedVillage->village_name }}, 
                                                     @endif
                                                     {{ $selectedVillage->district_name }}, 
                                                     {{ $selectedVillage->regency_name }}, 
                                                     {{ $selectedVillage->province_name }}
                                                 </option>
                                             @endif
                                         </select>
                                         <input type="hidden" name="village_id" id="village_id" value="{{ $pustu->village_id }}">
                                         <input type="hidden" name="district_id" id="district_id" value="{{ $pustu->district_id }}">                                         
                                    </div>                                    
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-2 col-md-4">
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-save me-2"></i>Update
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
                $('#district_id').val(data.district_id);
            });

            // Saat load, jika ada opsi default
          const selectedOption = $('#village_search').find('option:selected');
          if (selectedOption.length && selectedOption.data('full')) {
          const data = JSON.parse(selectedOption.attr('data-full'));
          const option = new Option(
               `${data.village_name ? data.village_name + ', ' : ''}${data.district_name}, ${data.regency_name}, ${data.province_name}`,
               data.village_id,
               true,
               true
          );
          option.fullData = data;
          $('#village_search').append(option).trigger('change');
          $('#village_id').val(data.village_id);
          $('#district_id').val(data.district_id);
          }
        });
    </script>
@endpush
