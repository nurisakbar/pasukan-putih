@extends('layouts.app')

@section('content')
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12">
                    <h3 class="mb-0">Tambah Kunjungan</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Form Tambah Kunjungan</h5>
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
                        <div class="card-body">
                            <form action="{{ route('kunjungans.store') }}" method="POST">
                                @csrf

                                <input type="hidden" class="form-control id" name="pasien_id" value="">
                                <input type="hidden" class="form-control " name="user_id" value="{{ auth()->user()->id }}">

                                <div class="form-group mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control nik" id="nik" name="nik"
                                        placeholder="NIK" value="{{ old('nik') }}" required>
                                    @error('nik')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" class="form-control name" id="name" name="name"
                                        placeholder="Nama" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- <div class="form-group mb-3">
                                    <label for="jenis_ktp" class="form-label">Jenis KTP</label>
                                    <select name="jenis_ktp" id="jenis_ktp" class="form-control">
                                        <option value="DKI" {{ old('status') == 'DKI' ? 'selected' : '' }}>DKI</option>
                                        <option value="Non DKI" {{ old('status') == 'Non DKI' ? 'selected' : '' }}>Non DKI
                                        </option>
                                    </select>
                                    @error('jenis_ktp')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div> --}}

                                <div class="form-group mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control alamat" id="alamat" name="alamat"
                                        placeholder="Alamat" value="{{ old('alamat') }}">
                                    @error('alamat')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="alamat" class="form-label">SKOR AKS - DATA SASARAN</label>
                                    <input type="text" class="form-control"  name="skor_aks_data_sasaran"
                                        placeholder="1-20" value="{{ old('skor_aks_data_sasaran') }}" required>
                                    @error('skor_aks_data_sasaran')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- <div class="mb-3">
                                    <label for="skor_aks" class="form-label">Skor Aks</label>
                                    <input type="number" class="form-control" name="skor_aks" placeholder="1-20">
                                </div> --}}
                                <div class="form-group mb-3">
                                    <label for="inputDay" class="form-label">Tanggal Kunjungan</label>
                                    <input type="date" id="inputDay" class="form-control" name="tanggal"
                                        placeholder="Tanggal" value="{{ old('tanggal', date('Y-m-d')) }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="Belum" {{ old('status') == 'Belum' ? 'selected' : '' }}>Belum
                                        </option>
                                        <option value="Sudah" {{ old('status') == 'Sudah' ? 'selected' : '' }}>Sudah
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="jenis" class="form-label">Jenis Kunjungan</label>
                                    <select name="jenis" id="jenis" class="form-select">
                                        <option value="awal" {{ old('jenis') == 'awal' ? 'selected' : '' }}>Awal
                                        </option>
                                        <option value="rencana" {{ old('jenis') == 'rencana' ? 'selected' : '' }}>Rencana
                                        </option>
                                        <option value="lanjutan" {{ old('jenis') == 'lanjutan' ? 'selected' : '' }}>
                                            Lanjutan</option>
                                    </select>
                                </div>
                        
                                <div class="mb-3">
                                    <label for="lanjut_kunjungan" class="form-label">Lanjut Kunjungan</label>
                                    <select class="form-control" name="lanjut_kunjungan">
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>
                        
                                <div class="mb-3">
                                    <label for="rencana_kunjungan_lanjutan" class="form-label">Rencana Kunjungan Lanjutan</label>
                                    <input type="date" id="inputDay" class="form-control" name="rencana_kunjungan_lanjutan"
                                        placeholder="Tanggal Kunjungan Lanjutan" value="{{ old('rencana_kunjungan_lanjutan', date('Y-m-d')) }}">
                                </div>
                        
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="henti_layanan" value="henti_layanan_kenaikan_aks">
                                    <label class="form-check-label">Kenaikan Aks</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="henti_layanan" value="henti_layanan_meninggal">
                                    <label class="form-check-label">Meninggal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="henti_layanan" value="henti_layanan_menolak">
                                    <label class="form-check-label">Menolak</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="henti_layanan" value="henti_layanan_pindah_domisili">
                                    <label class="form-check-label">Pindah Domisili</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="henti_layanan" value="" checked>
                                    <label class="form-check-label">Tidak Ada</label>
                                </div>
                                
                                
                        
                                <div class="mb-3">
                                    <label for="rujukan" class="form-label">Rujukan</label>
                                    <select class="form-control" name="rujukan">
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>
                        
                                <div class="mb-3">
                                    <input type="radio" name="konversi_data_ke_sasaran_kunjungan_lanjutan" value="1">
                                    <label for="konversi_data_ke_sasaran_kunjungan_lanjutan" class="form-label">Konversi Data Ke Sasaran Kunjungan Lanjutan</label>
                                </div>

                            
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                                    <a href="{{ route('kunjungans.index') }}"
                                        class="btn btn-outline-secondary px-4">Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Tanggal Kunjungan yang tersedia</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div id="calendar-header" class="text-center mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button class="btn btn-outline-secondary btn-sm" onclick="prevMonth()">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <span id="monthYear" class="fw-bold h5 mb-0"></span>
                                        <button class="btn btn-outline-secondary btn-sm" onclick="nextMonth()">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="calendar-container">
                                    <div class="calendar-header text-center">
                                        <div class="weekday"><strong>Min</strong></div>
                                        <div class="weekday"><strong>Sen</strong></div>
                                        <div class="weekday"><strong>Sel</strong></div>
                                        <div class="weekday"><strong>Rab</strong></div>
                                        <div class="weekday"><strong>Kam</strong></div>
                                        <div class="weekday"><strong>Jum</strong></div>
                                        <div class="weekday"><strong>Sab</strong></div>
                                    </div>
                                    <div id="calendar" class="calendar mt-2 mb-2"></div>
                                </div>

                                <div class="calendar-legend mt-4">
                                    <div class="d-flex justify-content-center gap-4">
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box available"></div>
                                            <span class="ms-2">Tersedia</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box selected"></div>
                                            <span class="ms-2">Dipilih</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box disabled"></div>
                                            <span class="ms-2">Penuh</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
    <style>
        /* Form styles */
        .form-control:focus,
        .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        /* Calendar styles */
        .calendar-container {
            overflow-x: auto;
        }

        .calendar-header,
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            min-width: 350px;
        }

        .weekday {
            padding: 8px 0;
            text-align: center;
        }

        .day {
            aspect-ratio: 1;
            padding: 8px;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 0.9rem;
        }

        .day:hover:not(.disabled) {
            background-color: #e9ecef;
            transform: scale(1.05);
        }

        .disabled {
            background-color: #f1f1f1;
            color: #aaa;
            pointer-events: none;
            border: 1px dashed #ddd;
        }

        .today {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            border-color: #0d6efd;
        }

        .day .quota {
            font-size: 0.75rem;
            margin-top: 3px;
        }

        /* Calendar Legend */
        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .legend-box.available {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
        }

        .legend-box.selected {
            background-color: #0d6efd;
        }

        .legend-box.disabled {
            background-color: #f1f1f1;
            border: 1px dashed #ddd;
        }

        /* Loading indicator */
        #loading-indicator {
            border-radius: 4px;
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Card styles */
        .card {
            transition: box-shadow 0.3s ease;
            border: none;
            border-radius: 0.5rem;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        /* Make page responsive */
        @media (max-width: 767.98px) {
            .day {
                font-size: 0.8rem;
                padding: 4px;
            }

            .day .quota {
                font-size: 0.7rem;
            }

            .weekday {
                font-size: 0.8rem;
            }
        }

        /* Menambahkan animasi untuk spinner */
        @keyframes pulse {
            0% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.5;
            }
        }

        /* Menambahkan efek bayangan dan transisi untuk elemen loading */
        #loading-indicator {
            background-color: rgba(255, 255, 255, 0.8);
            /* Latar belakang transparan */
            position: fixed;
            /* Fixed position agar tetap terlihat di halaman */
            top: 50%;
            /* Memposisikan di tengah */
            left: 50%;
            transform: translate(-50%, -50%);
            /* Memastikan loading indicator ada di tengah */
            border-radius: 8px;
            /* Sudut yang lebih halus */
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            /* Efek bayangan */
            padding: 20px;
            z-index: 9999;
            /* Pastikan loading berada di atas konten lainnya */
        }

        /* Menambahkan animasi pulse untuk spinner */
        .spinner-border {
            animation: pulse 1.5s infinite ease-in-out;
        }

        /* Responsif: Ukuran teks lebih kecil pada layar kecil */
        @media (max-width: 576px) {
            #loading-indicator {
                width: 90%;
                padding: 15px;
            }

            .spinner-border-sm {
                width: 2rem;
                height: 2rem;
            }

            .ms-3 {
                font-size: 14px;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let currentSelectedDay = null;

        const visitQuota = {};
        const year = new Date().getFullYear();
        const month = new Date().getMonth();

        for (let day = 1; day <= 31; day++) {
            const fullDate = `${year}-${(month + 1).toString().padStart(2, "0")}-${day.toString().padStart(2, "0")}`;
            visitQuota[fullDate] = Math.random() < 0.2 ? 0 : Math.floor(Math.random() * 10) + 1;
        }

        function renderCalendar(month, year) {
            const calendarContainer = document.getElementById("calendar");
            calendarContainer.innerHTML = "";

            const firstDay = new Date(year, month, 1).getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();

            document.getElementById("monthYear").innerText =
                new Date(year, month).toLocaleString("id-ID", {
                    month: "long",
                    year: "numeric"
                });

            // Add empty cells for days of the week before the first day of the month
            for (let i = 0; i < firstDay; i++) {
                const emptyDiv = document.createElement("div");
                calendarContainer.appendChild(emptyDiv);
            }

            // Create cells for each day of the month
            for (let date = 1; date <= lastDate; date++) {
                const fullDate = `${year}-${(month + 1).toString().padStart(2, "0")}-${date.toString().padStart(2, "0")}`;
                const isFull = visitQuota[fullDate] === 0;
                const dayDiv = document.createElement("div");

                dayDiv.classList.add("day");
                if (isFull) dayDiv.classList.add("disabled");

                const dateSpan = document.createElement("strong");
                dateSpan.textContent = date;

                const quotaSpan = document.createElement("span");
                quotaSpan.classList.add("quota");
                quotaSpan.textContent = isFull ? "Penuh" : (visitQuota[fullDate] ? `Kuota: ${visitQuota[fullDate]}` : "");

                dayDiv.appendChild(dateSpan);
                dayDiv.appendChild(quotaSpan);

                if (!isFull) {
                    dayDiv.onclick = () => {
                        if (currentSelectedDay) {
                            currentSelectedDay.classList.remove("today");
                        }

                        // Set selected date in the input
                        document.getElementById("inputDay").value = fullDate;

                        dayDiv.classList.add("today");
                        currentSelectedDay = dayDiv;
                    };
                }

                calendarContainer.appendChild(dayDiv);
            }
        }

        function prevMonth() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        }

        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
        }

        // Show or hide loading indicator
        function toggleLoading(show) {
            const loader = document.getElementById('loading-indicator');
            if (show) {
                loader.classList.remove('d-none');
            } else {
                loader.classList.add('d-none');
            }
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            renderCalendar(currentMonth, currentYear);

            // Set today's date as default
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            document.getElementById("inputDay").value = formattedDate;

            // Highlight today on calendar
            setTimeout(() => {
                const days = document.querySelectorAll('.day');
                days.forEach(day => {
                    const dayNum = day.querySelector('strong');
                    if (dayNum && parseInt(dayNum.textContent) === today.getDate()) {
                        day.classList.add('today');
                        currentSelectedDay = day;
                    }
                });
            }, 100);
        });

        // Handle NIK input for autofill
        $(document).ready(function() {
            $('.nik').on('input', function() {
                const nik = $(this).val().trim();

                if (nik.length > 0) {
                    toggleLoading(true);

                    $.ajax({
                        url: "{{ route('pasiens.nik') }}",
                        type: "GET",
                        data: {
                            nik: nik
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.message === "Pasien ditemukan") {
                                $('.id').val(response.data.id);
                                $('.name').val(response.data.name);
                                $('.alamat').val(response.data.alamat);
                                $('.rt').val(response.data.rt || '00');
                                $('.rw').val(response.data.rw || '00');

                                $('#province').val(response.data.province_id).trigger('change');
                                $('#regency').html('<option value="' + response.data
                                    .regency_id + '" selected>' + response.data
                                    .regency_name + '</option>');
                                $('#district').html('<option value="' + response.data
                                    .district_id + '" selected>' + response.data
                                    .district_name + '</option>');
                                $('#village').html('<option value="' + response.data
                                    .village_id + '" selected>' + response.data
                                    .village_name + '</option>');

                            } else {
                                kosongkanForm();
                            }
                        },
                        error: function(xhr, status, error) {
                            kosongkanForm();
                        },
                        complete: function() {
                            toggleLoading(false);
                        }
                    });
                }
            });

            function kosongkanForm() {
                $('.id').val('');
                $('.name').val('');
                $('.alamat').val('');
                $('.rt').val('');
                $('.rw').val('');
                $('#province').val('').trigger('change');
                $('#regency').html('<option value="">Pilih Kabupaten/Kota</option>');
                $('#district').html('<option value="">Pilih Kecamatan</option>');
                $('#village').html('<option value="">Pilih Kelurahan</option>');
            }
        });


        $(document).ready(function() {
            $('.select2').select2({
                // theme: "bootstrap-5",
                width: '100%'
            });

            // Load Kabupaten/Kota berdasarkan Provinsi
            $('#province').change(function() {
                var province_id = $(this).val();
                $('#regency').html('<option value="">Pilih Kabupaten/Kota</option>');
                $('#district').html('<option value="">Pilih Kecamatan</option>');
                $('#village').html('<option value="">Pilih Kelurahan</option>');

                if (province_id) {
                    $.get('/get-regencies/' + province_id, function(data) {
                        $.each(data, function(index, regency) {
                            $('#regency').append('<option value="' + regency.id + '">' +
                                regency.name + '</option>');
                        });
                    });
                }
            });

            // Load Kecamatan berdasarkan Kabupaten/Kota
            $('#regency').change(function() {
                var regency_id = $(this).val();
                $('#district').html('<option value="">Pilih Kecamatan</option>');
                $('#village').html('<option value="">Pilih Kelurahan</option>');

                if (regency_id) {
                    $.get('/get-districts/' + regency_id, function(data) {
                        $.each(data, function(index, district) {
                            $('#district').append('<option value="' + district.id + '">' +
                                district.name + '</option>');
                        });
                    });
                }
            });

            // Load Kelurahan berdasarkan Kecamatan
            $('#district').change(function() {
                var district_id = $(this).val();
                $('#village').html('<option value="">Pilih Kelurahan</option>');

                if (district_id) {
                    $.get('/get-villages/' + district_id, function(data) {
                        $.each(data, function(index, village) {
                            $('#village').append('<option value="' + village.id + '">' +
                                village.name + '</option>');
                        });
                    });
                }
            });
        });
    </script>
@endpush
