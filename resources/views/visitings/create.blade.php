@extends('layouts.app')

@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded" id="error-alert">
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
                <div class="col-md-8 mb-4 mt-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Tambah Kunjungan</h5>
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
                            <form action="{{ route('visitings.store') }}" method="POST">
                                @csrf
                                
                                <input type="hidden" class="form-control id" name="pasien_id" value="">
                                <input type="hidden" class="form-control id" name="user_id" value="{{ auth()->id() }}">

                                <div class="form-group mb-3">
                                   <label for="inputDay" class="form-label">Tanggal Kunjungan</label>
                                   <input type="date" id="inputDay" class="form-control" name="tanggal" 
                                       placeholder="Tanggal" value="{{ old('tanggal', date('Y-m-d')) }}">
                               </div>

                               <div class="form-group mb-3" style="display: none">
                                   <label class="form-label">Kalender Jadwal</label>
                                   <div class="calendar-container border rounded p-2">
                                       <div class="d-flex justify-content-between align-items-center mb-2">
                                           <button type="button" class="btn btn-sm btn-outline-secondary" onclick="prevMonth()">&laquo;</button>
                                           <strong id="monthYear">&nbsp;</strong>
                                           <button type="button" class="btn btn-sm btn-outline-secondary" onclick="nextMonth()">&raquo;</button>
                                       </div>
                                       <div id="calendar" class="calendar"></div>
                                   </div>
                               </div>
                               @if (auth()->user()->role == 'operator')
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Kategori Kunjungan</label>
                                    <select name="status" id="status" class="form-select" disabled>
                                        <option value="Kunjungan Lanjutan" selected>Kunjungan Lanjutan</option>
                                    </select>
                                    <input type="hidden" name="status" value="Kunjungan Lanjutan">
                                </div>
                               @else
                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Kategori Kunjungan</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="" selected> -- Pilih Status -- </option>
                                        <option value="Kunjungan Awal" {{ old('status') == 'Kunjungan Awal' ? 'selected' : '' }}>Kunjungan Awal</option>
                                        <option value="Kunjungan Lanjutan" {{ old('status') == 'Kunjungan Lanjutan' ? 'selected' : '' }}>Kunjungan Lanjutan</option>
                                    </select>
                                </div>
                               @endif

                               @if (auth()->user()->role == 'perawat')
                               <div class="form-group mb-3">
                                   <label for="operator_id" class="form-label">Nama Petugas Pelayanan Kesehatan Oleg  Warga</label>
                                   <select id="operator_search" name="operator_id" class="form-control custom-select-height" style="width: 100%">
                                       <option value="">-- Pilih Operator --</option>
                                   </select>
                                   @error('operator_id')
                                       <div class="text-danger">{{ $message }}</div>
                                   @enderror
                               </div>
                               @endif

                                <div class="form-group mb-3">
                                    <label for="nik" class="form-label">Nama Pasien</label>
                                    <select id="nik_search" name="nik" class="form-control custom-select-height" style="width: 100%"></select>
                                    @error('nik')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                
                                
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-primary px-4">Simpan</button>
                                    <a href="{{ route('visitings.index') }}" class="btn btn-outline-secondary px-4">Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mt-2">
                    <div id="scheduled-card" class="card shadow-sm d-none">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Jadwal RencanaKunjungan Pasien</h6>
                            <button type="button" class="btn-close" aria-label="Close" onclick="document.getElementById('scheduled-card').classList.add('d-none')"></button>
                        </div>
                        <div class="card-body">
                            <div id="scheduled-dates-container" class="border rounded p-2 bg-light">
                                <div id="scheduled-dates-empty" class="text-muted">Belum ada jadwal.</div>
                                <ul id="scheduled-dates-list" class="mb-0"></ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        if ($('#error-alert').length > 0) {
            setTimeout(function() {
                $('#error-alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 4000); // 4 seconds
        }
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let currentSelectedDay = null;

        // Map of YYYY-MM-DD => true for dates returned from server (scheduled follow-ups)
        let scheduledDates = {};

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
                const isScheduled = !!scheduledDates[fullDate];
                const dayDiv = document.createElement("div");

                dayDiv.classList.add("day");
                if (isScheduled) {
                    // Mark scheduled dates visually and disable picking to avoid duplicate scheduling
                    dayDiv.classList.add("disabled");
                }

                const dateSpan = document.createElement("strong");
                dateSpan.textContent = date;
                
                const quotaSpan = document.createElement("span");
                quotaSpan.classList.add("quota");
                quotaSpan.textContent = isScheduled ? "Terjadwal" : "";
                
                dayDiv.appendChild(dateSpan);
                dayDiv.appendChild(quotaSpan);

                if (!isScheduled) {
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
        $('#nik_search').select2({
            placeholder: 'Masukan NIK atau Nama Pasien',
            minimumInputLength: 3,
            ajax: {
                url: "{{ route('pasiens.nik') }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });
        $('#nik_search').on('select2:select', function (e) {
            const pasien = e.params.data.fullData;
            $('.id').val(pasien.id);
            // Fetch scheduled dates for selected pasien
            if (pasien.id) {
                toggleLoading(true);
                fetch(`{{ url('/pasiens') }}/${pasien.id}/scheduled-dates`)
                    .then(resp => resp.json())
                    .then(data => {
                        scheduledDates = {};
                        if (data && Array.isArray(data.dates)) {
                            data.dates.forEach(d => { scheduledDates[d] = true; });
                        }
                        // Re-render calendar for current month to reflect scheduled dates
                        renderCalendar(currentMonth, currentYear);
                        // Render list of scheduled dates in the div
                        renderScheduledDatesList();
                        // Show the right-side card if there is at least one date
                        const hasDates = Object.keys(scheduledDates).length > 0;
                        const cardEl = document.getElementById('scheduled-card');
                        if (cardEl) {
                            if (hasDates) {
                                cardEl.classList.remove('d-none');
                            } else {
                                cardEl.classList.add('d-none');
                            }
                        }
                    })
                    .catch(() => { /* silently fail */ })
                    .finally(() => toggleLoading(false));
            }
        });

        function renderScheduledDatesList() {
            const listEl = document.getElementById('scheduled-dates-list');
            const emptyEl = document.getElementById('scheduled-dates-empty');
            if (!listEl || !emptyEl) return;

            listEl.innerHTML = '';
            const dates = Object.keys(scheduledDates).sort();
            if (!dates.length) {
                emptyEl.classList.remove('d-none');
                return;
            }
            emptyEl.classList.add('d-none');
            dates.forEach(d => {
                const li = document.createElement('li');
                li.textContent = formatDateId(d);
                listEl.appendChild(li);
            });
        }

        function formatDateId(yyyyMmDd) {
            const [y, m, d] = yyyyMmDd.split('-').map(Number);
            const dt = new Date(y, (m - 1), d);
            return dt.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        // Handle Operator input for autofill
        $('#operator_search').select2({
            placeholder: 'Pilih Operator',
            ajax: {
                url: "{{ route('users.operators') }}",
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });
    </script>
@endpush