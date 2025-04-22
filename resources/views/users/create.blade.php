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
                                    <h5 class="card-title text-primary mb-0">TAMBAH DATA PENGGUNA</h5>
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
                                        <label for="role" class="form-label fw-bold ">Role Pengguna <span
                                                class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <select class="form-control @error('role') is-invalid @enderror" name="role"
                                            required>
                                            <option value="">-- Pilih Role --</option>
                                            @if (Auth::user()->role == 'superadmin')
                                                <option value="superadmin">Super Admin</option>
                                                <option value="sudinkes">Sudinkes</option>
                                                <option value="puskesmas">Puskesmas</option>
                                                <option value="pustu">Pustu</option>
                                                <option value="perawat">Perawat</option>

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
                                    <div class="col-lg-6 col-md-6">
                                        <select class="form-control" name="pustu_id" id="pustus">
                                            @foreach(\App\Models\Pustu::all() as $pustu)
                                            <option value="{{$pustu->id}}">{{$pustu->nama_pustu}}</option>
                                            @endforeach
                                        </select>

                                        <select class="form-control" name="regency_id" id="kabupaten">
                                            @foreach(\App\Models\Regency::where('province_id',31)->get() as $regency)
                                            <option value="{{$regency->id}}">{{$regency->name}}</option>
                                            @endforeach
                                        </select>
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


                                @if (Auth::user()->role == 'superadmin')
                                    <div class="row mb-4" id="puskesmas-field" style="display: none;">
                                        <div class="col-lg-2 col-md-4 mb-2">
                                            <label for="nama pustu/puskesmas" class="form-label fw-bold">Nama Pustu/Puskesmas <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-10 col-md-8">
                                            <select class="form-control @error('pustu_id') is-invalid @enderror"
                                                    name="pustu_id">
                                                    <option value="">-- Pilih Parent --</option>
                                                    @foreach ($parents as $parent)
                                                        <option value="{{ $parent->id }}">{{ $parent->name }}
                                                            ({{ ucfirst($parent->role) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('pustu_id')
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
                                        <select name="status_pegawai" class="form-control">
                                            <option value="PNS">PNS</option>
                                            <option value="NON PNS">NON PNS</option>
                                        </select>
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
            $('#pustu').select2();

            $('select[name="role"]').on('change', function() {
                var selectedRole = $(this).val(); // Ambil nilai yang dipilih
                console.log("Role yang dipilih: " + selectedRole); // Cetak ke console (bisa diganti dengan logika lain)

                // Contoh aksi lain (misalnya menampilkan alert atau manipulasi elemen):


                // if (selectedRole === 'superadmin') {
                //     $("#pustu").hide();
                //     $("#kabupaten").hide();
                // }

                if (selectedRole === 'pustu') {
                    $("#kabupaten").hide();
                    $("#pustus").show();
                }else if (selectedRole === 'sudinkes') {
                    $("#pustus").hide();
                    $("#kabupaten").show();
                }

                // Tambahkan aksi lain sesuai kebutuhan
            });

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
