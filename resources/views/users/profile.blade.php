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
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 4px;
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
            font-size: 0.875rem;
            margin-top: 8px;
        }

        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }

        .requirement i {
            margin-right: 6px;
            font-size: 0.75rem;
        }

        .requirement.met {
            color: #198754;
        }

        .requirement.unmet {
            color: #6c757d;
        }

        .password-strength-text {
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 4px;
        }

        .text-weak { color: #dc3545; }
        .text-fair { color: #fd7e14; }
        .text-good { color: #ffc107; }
        .text-strong { color: #198754; }
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

                        <div class="mb-3" style="display: none">
                            <label for="keterangan" class="form-label fw-bold">Keterangan</label>
                            <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan', $user->keterangan) }}" class="form-control">
                        </div>

                        <div class="mb-3" style="display: none">
                            <label for="status_pegawai" class="form-label fw-bold">Status Pegawai</label>
                            <input type="text" id="status_pegawai" name="status_pegawai" value="{{ old('status_pegawai', $user->status_pegawai) }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password Baru (Opsional)</label>
                            <input type="password" id="password" name="password" class="form-control" value="{{ old('password') }}">
                            
                            <!-- Password Strength Indicator -->
                            <div id="passwordStrengthContainer" style="display: none;">
                                <div class="password-strength-meter">
                                    <div id="passwordStrengthBar" class="password-strength-bar"></div>
                                </div>
                                <div id="passwordStrengthText" class="password-strength-text"></div>
                                
                                <div class="password-requirements">
                                    <div class="requirement unmet" id="req-length">
                                        <i class="fas fa-times"></i>
                                        <span>Minimal 8 karakter</span>
                                    </div>
                                    <div class="requirement unmet" id="req-uppercase">
                                        <i class="fas fa-times"></i>
                                        <span>Minimal 1 huruf besar</span>
                                    </div>
                                    <div class="requirement unmet" id="req-lowercase">
                                        <i class="fas fa-times"></i>
                                        <span>Minimal 1 huruf kecil</span>
                                    </div>
                                    <div class="requirement unmet" id="req-number">
                                        <i class="fas fa-times"></i>
                                        <span>Minimal 1 angka</span>
                                    </div>
                                    <div class="requirement unmet" id="req-special">
                                        <i class="fas fa-times"></i>
                                        <span>Minimal 1 karakter khusus (!@#$%^&*)</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" value="{{ old('password') }}">
                            <div id="passwordMatchMessage" class="mt-2" style="display: none;"></div>
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
@endsection

@push('script')
    <script>
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

        // Event listeners
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
            strengthText.textContent = 'Kekuatan Password: ' + strength.feedback;
            strengthText.className = 'password-strength-text ' + strength.className.split(' ')[1];
            
            // Check password match when password changes
            checkPasswordMatch();
        });
        
        document.getElementById('password_confirmation').addEventListener('input', function() {
            checkPasswordMatch();
        });

        // Village search functionality (existing code)
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
    </script>
@endpush