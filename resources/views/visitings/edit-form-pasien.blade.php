@extends('layouts.app')

@section('content')
    <div class="app-content-header py-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12">
                    <h3 class="mb-0">Edit Kunjungan</h3>
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
                                <h5 class="card-title mb-0">Form Edit Kunjungan</h5>
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
                            <form action="{{ route('visitings.update', $visiting->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                {{-- <input type="hidden" class="form-control id" name="pasien_id" value="{{ $visiting->pasien_id }}"> --}}
                                {{-- <input type="hidden" class="form-control id" name="user_id" value="{{ auth()->id() }}"> --}}

                                <div class="form-group mb-3">
                                   <label for="inputDay" class="form-label">Tanggal Kunjungan</label>
                                   <input type="date" class="form-control" name="tanggal" 
                                   placeholder="Tanggal" value="{{ old('tanggal', isset($visiting->tanggal) ? $visiting->tanggal->format('Y-m-d') : '') }}">
                               
                               </div>

                               <div class="form-group mb-3">
                                   <label for="status" class="form-label">Kategori Kunjungan</label>
                                   <select name="status" id="status" class="form-select">
                                       <option value="" selected> -- Pilih Status -- </option>
                                       <option value="Kunjungan Awal" {{ old('status', $visiting->status) == 'Kunjungan Awal' ? 'selected' : '' }}>Kunjungan Awal</option>
                                       <option value="Kunjungan Lanjutan" {{ old('status', $visiting->status) == 'Kunjungan Lanjutan' ? 'selected' : '' }}>Kunjungan Lanjutan</option>
                                   </select>
                               </div>
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" class="form-control name" id="name" name="name" 
                                        placeholder="Nama" value="{{ old('name', $visiting->pasien->name) }}" disabled>
                                    @error('name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-primary px-4">Update</button>
                                    <a href="{{ route('pasiens.show', $visiting->pasien_id) }}" class="btn btn-outline-secondary px-4">Kembali</a>
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
                                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box available"></div>
                                            <span class="ms-2 small">Tersedia</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box selected"></div>
                                            <span class="ms-2 small">Dipilih</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="legend-box disabled"></div>
                                            <span class="ms-2 small">Pasien Terjadwal</span>
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

    <!-- Modal untuk Detail Pasien Terjadwal -->
    <div class="modal fade" id="scheduledPatientsModal" tabindex="-1" aria-labelledby="scheduledPatientsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduledPatientsModalLabel">Detail Pasien Terjadwal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="scheduled-patients-content">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
            -webkit-overflow-scrolling: touch;
        }
        
        .calendar-container::-webkit-scrollbar {
            height: 6px;
        }
        
        .calendar-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .calendar-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .calendar-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
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
            padding: 4px 2px;
            text-align: center;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            font-size: 0.85rem;
            min-height: 60px;
            position: relative;
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
        
        .day-info {
            font-size: 0.65rem;
            margin-top: 2px;
            display: flex;
            flex-direction: column;
            gap: 1px;
            width: 100%;
        }
        
        .scheduled-count {
            color: #0d6efd;
            font-weight: 600;
            font-size: 0.75rem;
            line-height: 1.2;
            padding: 2px 4px;
            border-radius: 4px;
            display: inline-block;
            min-width: 20px;
            text-align: center;
        }
        
        .patient-scheduled {
            color: #dc3545;
            font-weight: 500;
            font-size: 0.6rem;
            line-height: 1;
        }
        
        /* Modal and patient card styles */
        .border-left-primary {
            border-left: 4px solid #0d6efd !important;
        }
        
        .avatar-sm {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .modal-lg {
            max-width: 800px;
        }
        
        .scheduled-count:hover {
            background-color: rgba(13, 110, 253, 0.15);
            border-radius: 4px;
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
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
        
        /* Mobile layout adjustments */
        @media (max-width: 991.98px) {
            .col-lg-4 {
                order: -1; /* Kalender di atas form di mobile */
            }
        }
        
        /* Make page responsive */
        @media (max-width: 767.98px) {
            .calendar-container {
                min-width: 280px;
            }
            
            .calendar-header, 
            .calendar {
                gap: 3px;
                min-width: 280px;
            }
            
            .day {
                font-size: 0.75rem;
                padding: 3px 1px;
                min-height: 50px;
                border-radius: 4px;
            }
            
            .day-info {
                font-size: 0.55rem;
                margin-top: 1px;
            }
            
            .scheduled-count {
                font-size: 0.65rem;
                padding: 1px 3px;
                min-width: 18px;
            }
            
            .patient-scheduled {
                font-size: 0.5rem;
            }
            
            .weekday {
                font-size: 0.7rem;
                padding: 6px 0;
            }
            
            .calendar-legend {
                margin-top: 2rem !important;
            }
            
            .calendar-legend .d-flex {
                gap: 2rem !important;
            }
            
            .legend-box {
                width: 16px;
                height: 16px;
            }
            
            .calendar-legend span {
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .calendar-container {
                min-width: 250px;
            }
            
            .calendar-header, 
            .calendar {
                gap: 2px;
                min-width: 250px;
            }
            
            .day {
                font-size: 0.7rem;
                padding: 2px 1px;
                min-height: 45px;
            }
            
            .day-info {
                font-size: 0.5rem;
            }
            
            .scheduled-count {
                font-size: 0.6rem;
                padding: 1px 2px;
                min-width: 16px;
            }
            
            .patient-scheduled {
                font-size: 0.45rem;
            }
            
            .weekday {
                font-size: 0.65rem;
                padding: 4px 0;
            }
            
            .calendar-legend .d-flex {
                gap: 1.5rem !important;
                flex-wrap: wrap;
            }
            
            .legend-box {
                width: 14px;
                height: 14px;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let currentSelectedDay = null;

        // Map of YYYY-MM-DD => true for dates returned from server (scheduled follow-ups)
        let scheduledDates = {};
        
        // Map of YYYY-MM-DD => count of scheduled patients for that date
        let scheduledCounts = {};

        // Simulasi jumlah orang yang sudah terjadwal (0-10 orang)
        const year = new Date().getFullYear();
        const month = new Date().getMonth();

        // Generate dummy scheduled counts data for demonstration
        for (let m = 0; m < 12; m++) {
            for (let day = 1; day <= 31; day++) {
                const fullDate = `${year}-${(m + 1).toString().padStart(2, "0")}-${day.toString().padStart(2, "0")}`;
                scheduledCounts[fullDate] = Math.floor(Math.random() * 11);
            }
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
                const isScheduled = !!scheduledDates[fullDate];
                const scheduledCount = scheduledCounts[fullDate] || 0;
                
                const dayDiv = document.createElement("div");
                dayDiv.classList.add("day");
                
                if (isScheduled) {
                    // Mark scheduled dates visually
                    dayDiv.classList.add("disabled");
                }

                const dateSpan = document.createElement("strong");
                dateSpan.textContent = date;
                
                // Container for scheduled info
                const infoContainer = document.createElement("div");
                infoContainer.classList.add("day-info");
                
                // Show scheduled count if > 0
                if (scheduledCount > 0) {
                    const countSpan = document.createElement("span");
                    countSpan.classList.add("scheduled-count");
                    countSpan.textContent = `📅 ${scheduledCount} Orang`;
                    countSpan.style.cursor = "pointer";
                    countSpan.title = "Klik untuk melihat detail";
                    
                    // Add click event to show patient details
                    countSpan.onclick = (e) => {
                        e.stopPropagation(); // Prevent day click
                        showScheduledPatients(fullDate, scheduledCount);
                    };
                    
                    infoContainer.appendChild(countSpan);
                }
                
                // Show scheduled status for specific patient
                if (isScheduled) {
                    const statusSpan = document.createElement("span");
                    statusSpan.classList.add("patient-scheduled");
                    statusSpan.textContent = "Terjadwal";
                    infoContainer.appendChild(statusSpan);
                }
                
                dayDiv.appendChild(dateSpan);
                dayDiv.appendChild(infoContainer);

                if (!isScheduled) {
                    dayDiv.onclick = () => {
                        if (currentSelectedDay) {
                            currentSelectedDay.classList.remove("today");
                        }
                        
                        // Set selected date in the input
                        document.querySelector('input[name="tanggal"]').value = fullDate;
                        
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
            
            // Set current visiting date as default
            const currentDate = document.querySelector('input[name="tanggal"]').value;
            if (currentDate) {
                // Highlight current date on calendar
                setTimeout(() => {
                    const days = document.querySelectorAll('.day');
                    days.forEach(day => {
                        const dayNum = day.querySelector('strong');
                        if (dayNum && parseInt(dayNum.textContent) === new Date(currentDate).getDate()) {
                            day.classList.add('today');
                            currentSelectedDay = day;
                        }
                    });
                }, 100);
            }
            
            // Fetch scheduled counts for all dates on page load
            fetch(`{{ url('/visitings/scheduled-counts') }}`)
                .then(resp => resp.json())
                .then(data => {
                    if (data && typeof data === 'object') {
                        scheduledCounts = data;
                        // Re-render calendar to show counts
                        renderCalendar(currentMonth, currentYear);
                        
                        // Highlight current date on calendar after re-render
                        setTimeout(() => {
                            const currentDate = document.querySelector('input[name="tanggal"]').value;
                            if (currentDate) {
                                const days = document.querySelectorAll('.day');
                                days.forEach(day => {
                                    const dayNum = day.querySelector('strong');
                                    if (dayNum && parseInt(dayNum.textContent) === new Date(currentDate).getDate()) {
                                        day.classList.add('today');
                                        currentSelectedDay = day;
                                    }
                                });
                            }
                        }, 100);
                    }
                })
                .catch(() => { 
                    // If API fails, just render with dummy data
                    setTimeout(() => {
                        const currentDate = document.querySelector('input[name="tanggal"]').value;
                        if (currentDate) {
                            const days = document.querySelectorAll('.day');
                            days.forEach(day => {
                                const dayNum = day.querySelector('strong');
                                if (dayNum && parseInt(dayNum.textContent) === new Date(currentDate).getDate()) {
                                    day.classList.add('today');
                                    currentSelectedDay = day;
                                }
                            });
                        }
                    }, 100);
                });
        });

        // Function to show scheduled patients modal
        function showScheduledPatients(date, count) {
            const modal = new bootstrap.Modal(document.getElementById('scheduledPatientsModal'));
            const modalTitle = document.getElementById('scheduledPatientsModalLabel');
            const modalContent = document.getElementById('scheduled-patients-content');
            
            // Update modal title
            const formattedDate = formatDateId(date);
            modalTitle.textContent = `Detail Pasien Terjadwal - ${formattedDate}`;
            
            // Show loading
            modalContent.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data pasien...</p>
                </div>
            `;
            
            // Show modal
            modal.show();
            
            // Fetch patient details
            fetch(`{{ url('/visitings/scheduled-patients') }}?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.patients) {
                        renderScheduledPatientsList(data.patients, date);
                    } else {
                        modalContent.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Tidak ada data pasien untuk tanggal ini.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching scheduled patients:', error);
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            Gagal memuat data pasien. Silakan coba lagi.
                        </div>
                    `;
                });
        }

        // Function to render scheduled patients list
        function renderScheduledPatientsList(patients, date) {
            const modalContent = document.getElementById('scheduled-patients-content');
            
            if (!patients || patients.length === 0) {
                modalContent.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Tidak ada pasien yang terjadwal pada tanggal ini.
                    </div>
                `;
                return;
            }

            let html = `
                <div class="mb-3">
                    <h6 class="text-primary">
                        <i class="fas fa-calendar-day"></i>
                        ${patients.length} Pasien Terjadwal
                    </h6>
                    <small class="text-muted">${formatDateId(date)}</small>
                </div>
                <div class="row">
            `;

            patients.forEach((patient, index) => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">${patient.name || 'Nama tidak tersedia'}</h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-id-card"></i> NIK: ${patient.nik || '-'}
                                        </p>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-map-marker-alt"></i> ${patient.alamat || 'Alamat tidak tersedia'}
                                        </p>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-user-md"></i> Status: ${patient.status || '-'}
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="fas fa-user-check"></i> Diperiksa oleh: ${patient.diperiksa_oleh || 'Tidak diketahui'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
            modalContent.innerHTML = html;
        }

        function formatDateId(yyyyMmDd) {
            const [y, m, d] = yyyyMmDd.split('-').map(Number);
            const dt = new Date(y, (m - 1), d);
            return dt.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }
    </script>
@endpush