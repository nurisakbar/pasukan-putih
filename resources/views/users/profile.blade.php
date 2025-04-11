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
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-center">Edit Profil</h3>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.updateProfile') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nama</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_wa" class="form-label fw-bold">No WhatsApp</label>
                            <input type="text" id="no_wa" name="no_wa" value="{{ old('no_wa', $user->no_wa) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label fw-bold">Keterangan</label>
                            <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $user->keterangan) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="status_pegawai" class="form-label fw-bold">Status Pegawai</label>
                            <input type="text" id="status_pegawai" name="status_pegawai" value="{{ old('status_pegawai', $user->status_pegawai) }}" class="form-control">
                        </div>

                        <div class="row mb-4">
                            <label for="alamat" class="form-label fw-bold fw-bold">Alamat, Nama Desa <span class="text-danger">*</span></label>
                            <div class="col-12 col-md-8 col-lg-5 mb-2">
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

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password Baru (Opsional)</label>
                            <input type="password" id="password" name="password" class="form-control" value="{{ old('password', $user->password) }}">
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" value="{{ old('password', $user->password) }}">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-3 py-2">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
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

            
    </script>
@endpush