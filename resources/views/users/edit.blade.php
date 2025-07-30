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

    /* Password Strength Indicator Styles */
    .password-strength-meter {
        height: 6px;
        background-color: #e9ecef;
        border-radius: 3px;
        margin-top: 8px;
        overflow: hidden;
    }

    .password-strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 3px;
    }

    .strength-weak {
        background-color: #dc3545;
        width: 25%;
    }

    .strength-fair {
        background-color: #fd7e14;
        width: 50%;
    }

    .strength-good {
        background-color: #ffc107;
        width: 75%;
    }

    .strength-strong {
        background-color: #198754;
        width: 100%;
    }

    .password-requirements {
        font-size: 0.8rem;
        margin-top: 6px;
    }

    .requirement {
        display: flex;
        align-items: center;
        margin-bottom: 3px;
    }

    .requirement i {
        margin-right: 6px;
        font-size: 0.7rem;
        width: 12px;
    }

    .requirement.met {
        color: #198754;
    }

    .requirement.unmet {
        color: #6c757d;
    }

    .password-strength-text {
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 4px;
    }

    .text-weak { color: #dc3545; }
    .text-fair { color: #fd7e14; }
    .text-good { color: #ffc107; }
    .text-strong { color: #198754; }

    .password-match-message {
        font-size: 0.8rem;
        margin-top: 6px;
    }
</style>
@endpush

@section('content')
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 mt-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5 class="card-title">EDIT PENGGUNA</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-12">
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        {{-- Name --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="name" class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-6 col-md-4 col-lg-5">
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name', $user->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            @if($user->role=='perawat' && auth()->user()->role != 'superadmin')
                                                <div class="col-6 col-md-4 col-lg-5">
                                                    <input disabled type="text" class="form-control @error('name') is-invalid @enderror"
                                                        value="{{ old('name', $user->pustu->nama_pustu ?? '') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Email --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ old('email', $user->email) }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- No Whatsapp --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="no_wa" class="form-label fw-bold">No Whatsapp <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="number" name="no_wa"
                                                    class="form-control @error('no_wa') is-invalid @enderror"
                                                    value="{{ old('no_wa', $user->no_wa) }}" placeholder="Masukkan no whatsapp" required>
                                                @error('no_wa')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Keterangan --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="keterangan" class="form-label fw-bold">Keterangan <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="text" name="keterangan"
                                                    class="form-control @error('keterangan') is-invalid @enderror"
                                                    value="{{ old('keterangan', $user->keterangan) }}" placeholder="Keterangan" required>
                                                @error('keterangan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Password --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="password" class="form-label fw-bold">Password</label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="password" name="password" id="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    value="{{ old('password') }}" placeholder="Kosongkan jika tidak ingin mengubah password">
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                
                                                <!-- Password Strength Indicator -->
                                                <div id="passwordStrengthContainer" style="display: none;">
                                                    <div class="password-strength-meter">
                                                        <div id="passwordStrengthBar" class="password-strength-bar"></div>
                                                    </div>
                                                    <div id="passwordStrengthText" class="password-strength-text"></div>
                                                    
                                                    <div class="password-requirements">
                                                        <div class="requirement unmet" id="req-length">
                                                            <i class="fas fa-times"></i>
                                                            <span>Min 8 karakter</span>
                                                        </div>
                                                        <div class="requirement unmet" id="req-uppercase">
                                                            <i class="fas fa-times"></i>
                                                            <span>Min 1 huruf besar</span>
                                                        </div>
                                                        <div class="requirement unmet" id="req-lowercase">
                                                            <i class="fas fa-times"></i>
                                                            <span>Min 1 huruf kecil</span>
                                                        </div>
                                                        <div class="requirement unmet" id="req-number">
                                                            <i class="fas fa-times"></i>
                                                            <span>Min 1 angka</span>
                                                        </div>
                                                        <div class="requirement unmet" id="req-special">
                                                            <i class="fas fa-times"></i>
                                                            <span>Min 1 karakter khusus</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Password Confirmation --}}
                                        <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <input type="password" name="password_confirmation" id="password_confirmation"
                                                    class="form-control"
                                                    value="{{ old('password_confirmation') }}" placeholder="Konfirmasi password baru">
                                                <div id="passwordMatchMessage" class="password-match-message" style="display: none;"></div>
                                            </div>
                                        </div>

                                        {{-- Role --}}
                                        {{-- <div class="row mb-4">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="role" class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-12 col-md-8 col-lg-10">
                                                <select class="form-control @error('role') is-invalid @enderror" name="role" required>
                                                    <option value="">-- Pilih Role --</option>
                                                    @if (Auth::user()->role == 'superadmin')
                                                        <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                                        <option value="puskesmas" {{ old('role', $user->role) == 'puskesmas' ? 'selected' : '' }}>Puskesmas</option>
                                                        <option value="pustu" {{ old('role', $user->role) == 'pustu' ? 'selected' : '' }}>Pustu</option>
                                                    @elseif(Auth::user()->role == 'puskesmas')
                                                        <option value="pustu" {{ old('role', $user->role) == 'pustu' ? 'selected' : '' }}>Pustu</option>
                                                    @elseif(Auth::user()->role == 'sudinkes')
                                                        <option value="perawat" {{ old('role', $user->role) == 'perawat' ? 'selected' : '' }}>Perawat</option>
                                                    @elseif(Auth::user()->role == 'pustu')
                                                        <option value="perawat" {{ old('role', $user->role) == 'perawat' ? 'selected' : '' }}>Perawat</option>
                                                        <option value="caregiver" {{ old('role', $user->role) == 'caregiver' ? 'selected' : '' }}>Caregiver</option>
                                                    @endif
                                                </select>
                                                @error('role')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        {{-- Parent (jika superadmin) --}}
                                        @if (Auth::user()->role == 'superadmin')
                                            <div class="row mb-4 parent-field {{ old('role', $user->role) != 'superadmin' ? '' : 'd-none' }}" id="parent-field">
                                                <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                    <label for="pustu_id" class="form-label fw-bold">Pustu</label>
                                                </div>
                                                <div class="col-12 col-md-8 col-lg-10">
                                                    <select class="form-control select2 @error('pustu_id') is-invalid @enderror" name="pustu_id">
                                                        <option value="">-- Pilih Pustu --</option>
                                                        @foreach ($pustus as $parent)
                                                            <option value="{{ $parent->id }}"
                                                                {{ old('pustu_id', $user->pustu_id) == $parent->id ? 'selected' : '' }}>
                                                                {{ $parent->nama_pustu }} 
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('pustu_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                                    id="no_wa" name="no_wa" value="{{ old('no_wa', $user->no_wa) }}"
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
                                                    id="status_pegawai" name="status_pegawai" value="{{ old('status_pegawai', $user->status_pegawai) }}"
                                                    placeholder="Masukkan status pegawai" required>
                                                @error('status_pegawai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Alamat dan Desa --}}
                                        <div class="row mb-4" style="display: none;">
                                            <div class="col-12 col-md-4 col-lg-2 mb-2">
                                                <label for="alamat" class="form-label fw-bold">Alamat, Nama Desa <span class="text-danger">*</span></label>
                                            </div>

                                            <div class="col-12 col-md-8 col-lg-3 mb-2">
                                                <input class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat"
                                                    placeholder="Alamat Jalan (Opsional)" value="{{ old('alamat', $user->alamat) }}">
                                                @error('alamat')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-12 col-md-8 col-lg-7 mb-2">
                                                <select id="village_search" name="village_search" class="form-select custom-select-height"></select>
                                                <input type="hidden" name="village" id="village_id">
                                                <input type="hidden" name="district" id="district_id">
                                                <input type="hidden" name="regency" id="regency_id">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 mt-3">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <a href="{{ route('users.index', ['role' => $user->role]) }}" class="btn btn-secondary">Kembali</a>
                                    </div>
                                </div>
                            </form>

                    </div>
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

        // Password Strength Checker
        function checkPasswordStrength(password) {
            let score = 0;
            let feedback = '';
            let className = '';
            
            // Kriteria password
            const hasLength = password.length >= 8;
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);
            const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
            
            // Update requirement indicators
            updateRequirement('req-length', hasLength);
            updateRequirement('req-uppercase', hasUppercase);
            updateRequirement('req-lowercase', hasLowercase);
            updateRequirement('req-number', hasNumber);
            updateRequirement('req-special', hasSpecial);
            
            // Calculate score
            if (hasLength) score++;
            if (hasUppercase) score++;
            if (hasLowercase) score++;
            if (hasNumber) score++;
            if (hasSpecial) score++;
            
            // Determine strength level
            switch (score) {
                case 0:
                case 1:
                    feedback = 'Sangat Lemah';
                    className = 'strength-weak text-weak';
                    break;
                case 2:
                    feedback = 'Lemah';
                    className = 'strength-weak text-weak';
                    break;
                case 3:
                    feedback = 'Cukup';
                    className = 'strength-fair text-fair';
                    break;
                case 4:
                    feedback = 'Baik';
                    className = 'strength-good text-good';
                    break;
                case 5:
                    feedback = 'Sangat Kuat';
                    className = 'strength-strong text-strong';
                    break;
            }
            
            return { score, feedback, className };
        }
        
        function updateRequirement(elementId, isMet) {
            const element = document.getElementById(elementId);
            const icon = element.querySelector('i');
            
            if (isMet) {
                element.classList.remove('unmet');
                element.classList.add('met');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-check');
            } else {
                element.classList.remove('met');
                element.classList.add('unmet');
                icon.classList.remove('fa-check');
                icon.classList.add('fa-times');
            }
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const messageDiv = document.getElementById('passwordMatchMessage');
            
            if (confirmPassword === '') {
                messageDiv.style.display = 'none';
                return;
            }
            
            messageDiv.style.display = 'block';
            
            if (password === confirmPassword) {
                messageDiv.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Password sudah cocok</small>';
            } else {
                messageDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Password tidak cocok</small>';
            }
        }

        // Event listeners untuk password strength
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const container = document.getElementById('passwordStrengthContainer');
            
            if (password === '') {
                container.style.display = 'none';
                return;
            }
            
            container.style.display = 'block';
            
            const strength = checkPasswordStrength(password);
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            
            // Update strength bar
            strengthBar.className = 'password-strength-bar ' + strength.className.split(' ')[0];
            
            // Update strength text
            strengthText.textContent = 'Kekuatan: ' + strength.feedback;
            strengthText.className = 'password-strength-text ' + strength.className.split(' ')[1];
            
            // Check password match when password changes
            checkPasswordMatch();
        });
        
        document.getElementById('password_confirmation').addEventListener('input', function() {
            checkPasswordMatch();
        });

        //select 2
        $(document).ready(function() {
            $('.select2').select2({
            });
        });

        $(document).ready(function() {
            // Show/hide parent field based on role selection (for superadmin only)
            @if(Auth::user()->role == 'superadmin')
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
        });
    </script>
@endpush