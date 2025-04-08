@extends('layouts.app')

@section('content')
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12">
                    <h3 class="mb-0">Tambah Kunjungan</h3>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                            <div class="d-flex align-items-center">
                                <div class="spinner-border text-primary spinner-border-sm" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="ms-2">Mencari data...</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('visitings.store') }}" method="POST">
                                @csrf
                                
                                <input type="hidden" class="form-control id" name="pasien_id" value="">
                                <input type="hidden" class="form-control id" name="user_id" value="{{ auth()->id() }}">

                                <div class="form-group mb-3">
                                   <label for="inputDay" class="form-label">Tanggal Kunjungan</label>
                                   <input type="date" id="inputDay" class="form-control" name="tanggal" 
                                       placeholder="Tanggal" value="{{ old('tanggal', date('Y-m-d')) }}">
                               </div>

                               <div class="form-group mb-3">
                                   <label for="status" class="form-label">Kategori Kunjungan</label>
                                   <select name="status" id="status" class="form-select">
                                       <option value="" selected> -- Pilih Status -- </option>
                                       <option value="Kunjungan Awal" {{ old('status') == 'Kunjungan Awal' ? 'selected' : '' }}>Kunjungan Awal</option>
                                       <option value="Kunjungan Lanjutan" {{ old('status') == 'Kunjungan Lanjutan' ? 'selected' : '' }}>Kunjungan Lanjutan</option>
                                   </select>
                               </div>

                                <div class="form-group mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control nik" id="nik" name="nik" placeholder="NIK" value="{{ old('nik') }}">
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
                                
                                <div class="form-group mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <input type="text" class="form-control alamat" id="alamat" name="alamat"
                                        placeholder="Alamat" value="{{ old('alamat') }}">
                                    @error('alamat')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-6 col-md-3">
                                        <label for="rt" class="form-label">RT</label>
                                        <input type="number" class="form-control rt" id="rt" name="rt"
                                            placeholder="RT" value="{{ old('rt', '00') }}">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label for="rw" class="form-label">RW</label>
                                        <input type="number" class="form-control rw" id="rw" name="rw"
                                            placeholder="RW" value="{{ old('rw', '00') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kelurahan" class="form-label">Kelurahan</label>
                                        <input type="text" class="form-control kelurahan" id="kelurahan" name="kelurahan"
                                            placeholder="Kelurahan" value="{{ old('kelurahan') }}">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="kecamatan" class="form-label">Kecamatan</label>
                                        <input type="text" class="form-control kecamatan" id="kecamatan" name="kecamatan"
                                            placeholder="Kecamatan" value="{{ old('kecamatan') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                                        <input type="text" class="form-control kabupaten" id="kabupaten" name="kabupaten"
                                            placeholder="Kabupaten/Kota" value="{{ old('kabupaten') }}">
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                   <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                                   <input type="number" step="0.1" class="form-control" id="berat_badan" name="berat_badan"
                                       placeholder="Masukkan berat badan" value="{{ old('berat_badan') }}" oninput="hitungIMT()">
                               </div>
                               
                               <div class="form-group mb-3">
                                   <label for="tinggi_badan" class="form-label">Tinggi Badan (cm)</label>
                                   <input type="number" step="0.1" class="form-control" id="tinggi_badan" name="tinggi_badan"
                                       placeholder="Masukkan tinggi badan" value="{{ old('tinggi_badan') }}" oninput="hitungIMT()">
                               </div>
                               
                               <div class="form-group mb-3">
                                   <label for="imt" class="form-label">Indeks Massa Tubuh (IMT)</label>
                                   <input type="text" class="form-control" id="imt" name="imt" readonly>
                               </div>
                                
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                                    <a href="{{ route('visitings.index') }}" class="btn btn-outline-secondary px-4">Kembali</a>
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

        function hitungIMT() {
               let berat = parseFloat(document.getElementById('berat_badan').value);
               let tinggi = parseFloat(document.getElementById('tinggi_badan').value);
               
               if (berat > 0 && tinggi > 0) {
                    let tinggiMeter = tinggi / 100;
                    let imt = berat / (tinggiMeter * tinggiMeter);
                    document.getElementById('imt').value = imt.toFixed(2);
               } else {
                    document.getElementById('imt').value = '';
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
                        data: { nik: nik },
                        dataType: "json",
                        success: function(response) {
                            if (response.message === "Pasien ditemukan") {
                                $('.id').val(response.data.id);
                                $('.name').val(response.data.name);
                                $('.alamat').val(response.data.alamat);
                                $('.rt').val('00');
                                $('.rw').val('00');
                                $('.kelurahan').val(response.data.village_id);
                                $('.kecamatan').val(response.data.district_id);
                                $('.kabupaten').val(response.data.regency_id);
                            } else {
                                console.log('Data tidak ditemukan');
                            }
                        },
                        error: function(xhr, status, error) {
                            $('.id').val('');
                            $('.name').val('');
                            $('.alamat').val('');
                            $('.rt').val('');
                            $('.rw').val('');
                            $('.kelurahan').val('');
                            $('.kecamatan').val('');
                            $('.kabupaten').val('');
                        },
                        complete: function() {
                            toggleLoading(false);
                        }
                    });
                }
            });
        });
    </script>
@endpush