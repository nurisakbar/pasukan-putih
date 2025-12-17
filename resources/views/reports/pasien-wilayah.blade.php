@extends('layouts.app')

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="app-content-header py-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-10 col-12 mb-2 mb-md-0">
                <h3 class="mb-0">Report Pasien Berdasarkan Wilayah</h3>
                <small class="text-muted">Kunjungan dan Pemeriksaan Terakhir per Pasien</small>
            </div>
            <div class="col-md-2 col-12 text-md-end text-start">
                <button type="button" id="btnExport" class="btn btn-success btn-md btn-sm shadow-sm d-block d-md-inline-block w-100 w-md-auto">
                    <i class="fas fa-file-excel me-1"></i> <span class="d-none d-sm-inline">Export Excel</span><span class="d-sm-none">Export</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <!-- Filter Section -->
        <div class="card shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.pasien-wilayah') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6 col-12">
                            <label for="group_by" class="form-label">Kelompokkan Berdasarkan</label>
                            <select name="group_by" id="group_by" class="form-select">
                                <option value="village" {{ request('group_by') == 'village' ? 'selected' : '' }}>Kelurahan/Desa</option>
                                <option value="district" {{ request('group_by') == 'district' || !request('group_by') ? 'selected' : '' }}>Kecamatan</option>
                                <option value="regency" {{ request('group_by') == 'regency' ? 'selected' : '' }}>Kabupaten/Kota</option>
                                <option value="province" {{ request('group_by') == 'province' ? 'selected' : '' }}>Provinsi</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12">
                            <label for="wilayah_id" class="form-label" id="wilayah_label">Filter Wilayah</label>
                            <select name="wilayah_id" id="wilayah_id" class="form-select">
                                <option value="">Semua Wilayah</option>
                                @if(isset($wilayahOptions) && $wilayahOptions)
                                    @foreach($wilayahOptions as $wilayah)
                                        <option value="{{ $wilayah['id'] }}" {{ request('wilayah_id') == $wilayah['id'] ? 'selected' : '' }}>
                                            {{ $wilayah['name'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-12 col-12 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('reports.pasien-wilayah') }}" class="btn btn-outline-secondary flex-fill">
                                <i class="fas fa-sync-alt me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data...</p>
        </div>

        <!-- Data Section -->
        <div id="dataSection">
            @if(isset($groupedData) && count($groupedData) > 0)
                @php
                    $currentGroupBy = request('group_by', 'district');
                    $groupLabels = [
                        'village' => 'Kelurahan/Desa',
                        'district' => 'Kecamatan',
                        'regency' => 'Kabupaten/Kota',
                        'province' => 'Provinsi'
                    ];
                @endphp

                @foreach($groupedData as $wilayahKey => $wilayahData)
                    <div class="card shadow-sm rounded-3 mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ $groupLabels[$currentGroupBy] }}: {{ $wilayahData['wilayah_name'] }}
                                <span class="badge bg-light text-primary ms-2">{{ count($wilayahData['pasien']) }} Pasien</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">NIK</th>
                                            <th width="20%">Nama Pasien</th>
                                            <th width="15%">Alamat</th>
                                            <th width="10%">RT/RW</th>
                                            <th width="12%">Tanggal Kunjungan Terakhir</th>
                                            <th width="23%">Pemeriksaan Terakhir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wilayahData['pasien'] as $index => $pasien)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $pasien['nik'] ?? '-' }}</td>
                                                <td>
                                                    <strong>{{ $pasien['nama_pasien'] }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        @if($currentGroupBy != 'village')
                                                            {{ $pasien['village_name'] ?? '-' }}
                                                        @endif
                                                        @if($currentGroupBy == 'province')
                                                            <br>{{ $pasien['regency_name'] ?? '-' }} > {{ $pasien['district_name'] ?? '-' }}
                                                        @endif
                                                    </small>
                                                </td>
                                                <td>{{ $pasien['alamat'] ?? '-' }}</td>
                                                <td>
                                                    @if($pasien['rt'] || $pasien['rw'])
                                                        RT {{ $pasien['rt'] ?? '-' }}/RW {{ $pasien['rw'] ?? '-' }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($pasien['kunjungan']['tanggal'])
                                                        <span class="badge bg-info">
                                                            {{ Carbon::parse($pasien['kunjungan']['tanggal'])->format('d/m/Y') }}
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">
                                                            Status: {{ $pasien['kunjungan']['status'] ?? '-' }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="pemeriksaan-detail">
                                                        @if($pasien['pemeriksaan']['ttv'])
                                                            <div class="mb-1">
                                                                <small class="text-primary">
                                                                    <i class="fas fa-heartbeat me-1"></i><strong>TTV:</strong>
                                                                    @if($pasien['pemeriksaan']['ttv']['blood_pressure'])
                                                                        TD: {{ $pasien['pemeriksaan']['ttv']['blood_pressure'] }}
                                                                    @endif
                                                                    @if($pasien['pemeriksaan']['ttv']['pulse'])
                                                                        | Nadi: {{ $pasien['pemeriksaan']['ttv']['pulse'] }}
                                                                    @endif
                                                                    @if($pasien['pemeriksaan']['ttv']['temperature'])
                                                                        | Suhu: {{ $pasien['pemeriksaan']['ttv']['temperature'] }}°C
                                                                    @endif
                                                                    @if($pasien['pemeriksaan']['ttv']['bmi'])
                                                                        | BMI: {{ number_format($pasien['pemeriksaan']['ttv']['bmi'], 1) }}
                                                                        ({{ $pasien['pemeriksaan']['ttv']['bmi_category'] ?? '-' }})
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if($pasien['pemeriksaan']['skrining_adl'])
                                                            <div class="mb-1">
                                                                <small class="text-success">
                                                                    <i class="fas fa-check-circle me-1"></i><strong>ADL:</strong>
                                                                    Skor: {{ $pasien['pemeriksaan']['skrining_adl']['total_score'] ?? '-' }}/100
                                                                    @if($pasien['pemeriksaan']['skrining_adl']['butuh_orang'])
                                                                        | Butuh Orang
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if($pasien['pemeriksaan']['health_form'])
                                                            <div class="mb-1">
                                                                <small class="text-warning">
                                                                    <i class="fas fa-file-medical me-1"></i><strong>Health Form:</strong>
                                                                    @if($pasien['pemeriksaan']['health_form']['skor_aks'])
                                                                        Skor AKS: {{ $pasien['pemeriksaan']['health_form']['skor_aks'] }}
                                                                    @endif
                                                                    @if($pasien['pemeriksaan']['health_form']['tingkat_kemandirian'])
                                                                        | {{ $pasien['pemeriksaan']['health_form']['tingkat_kemandirian'] }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @endif

                                                        @if(!$pasien['pemeriksaan']['ttv'] && !$pasien['pemeriksaan']['skrining_adl'] && !$pasien['pemeriksaan']['health_form'])
                                                            <span class="text-muted">Tidak ada data pemeriksaan</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Summary Card -->
                <div class="card shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h4 class="text-primary">{{ count($groupedData) }}</h4>
                                <p class="text-muted mb-0">{{ $groupLabels[$currentGroupBy] }}</p>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-success">
                                    @php
                                        $totalPasien = 0;
                                        foreach($groupedData as $data) {
                                            $totalPasien += count($data['pasien']);
                                        }
                                        echo $totalPasien;
                                    @endphp
                                </h4>
                                <p class="text-muted mb-0">Total Pasien</p>
                            </div>
                            <div class="col-md-4">
                                <h4 class="text-info">
                                    @php
                                        $totalKunjungan = 0;
                                        foreach($groupedData as $data) {
                                            foreach($data['pasien'] as $pasien) {
                                                if($pasien['kunjungan']['tanggal']) {
                                                    $totalKunjungan++;
                                                }
                                            }
                                        }
                                        echo $totalKunjungan;
                                    @endphp
                                </h4>
                                <p class="text-muted mb-0">Total Kunjungan Terakhir</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm rounded-3">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data ditemukan</h5>
                        <p class="text-muted">Coba ubah filter atau pilih wilayah lainnya.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Export Progress Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                    <i class="fas fa-file-excel me-2"></i>Export Data
                </h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span id="exportProgressText">Memproses...</span>
                        <span id="exportProgressPercentage" class="fw-bold">0%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div id="exportProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                             role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a id="exportDownloadBtn" href="#" class="btn btn-success btn-lg" style="display: none;" download>
                        <i class="fas fa-download me-2"></i>Download File Excel
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="exportCloseBtn">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Update label berdasarkan group_by yang dipilih
        function updateWilayahLabel() {
            const groupBy = $('#group_by').val();
            const labels = {
                'village': 'Filter Kelurahan/Desa',
                'district': 'Filter Kecamatan',
                'regency': 'Filter Kabupaten/Kota',
                'province': 'Filter Provinsi'
            };
            $('#wilayah_label').text(labels[groupBy] || 'Filter Wilayah');
        }

        // Load wilayah options ketika group_by berubah
        $('#group_by').on('change', function() {
            updateWilayahLabel();
            const groupBy = $(this).val();
            
            // Reset wilayah_id
            $('#wilayah_id').html('<option value="">Memuat...</option>');
            
            // Load wilayah options via AJAX
            $.ajax({
                url: '{{ route("reports.pasien-wilayah.wilayah-options") }}',
                method: 'GET',
                data: {
                    group_by: groupBy
                },
                success: function(response) {
                    if (response.success && response.options) {
                        let html = '<option value="">Semua Wilayah</option>';
                        response.options.forEach(function(option) {
                            html += '<option value="' + option.id + '">' + option.name + '</option>';
                        });
                        $('#wilayah_id').html(html);
                    }
                },
                error: function() {
                    $('#wilayah_id').html('<option value="">Error memuat data</option>');
                }
            });
        });

        // Initial label update
        updateWilayahLabel();

        // Export button with progress tracking
        $('#btnExport').on('click', function() {
            const groupBy = $('#group_by').val();
            const wilayahId = $('#wilayah_id').val();
            
            // Disable button
            $(this).prop('disabled', true);
            
            // Show export modal
            showExportModal();
            
            // Start export
            $.ajax({
                url: '{{ route("reports.pasien-wilayah.export") }}',
                method: 'POST',
                data: {
                    group_by: groupBy,
                    wilayah_id: wilayahId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Start polling for progress
                        pollExportProgress(response.export_id);
                    } else {
                        hideExportModal();
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Export gagal',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        $('#btnExport').prop('disabled', false);
                        isProcessing = false;
                    }
                },
                error: function(xhr) {
                    hideExportModal();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memulai export',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    $('#btnExport').prop('disabled', false);
                    isProcessing = false;
                }
            });
        });

        // Poll export progress
        let pollInterval = null;
        let isProcessing = false;
        let exportCompleted = false; // Flag to prevent multiple completion handlers

        function pollExportProgress(exportId) {
            if (isProcessing || exportCompleted) return; // Prevent multiple polling
            isProcessing = true;
            exportCompleted = false;

            pollInterval = setInterval(function() {
                // Double check to prevent execution if already completed
                if (exportCompleted) {
                    clearInterval(pollInterval);
                    return;
                }

                $.ajax({
                    url: '{{ route("reports.pasien-wilayah.export-progress", ":id") }}'.replace(':id', exportId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.progress) {
                            const progress = response.progress;
                            updateExportProgress(progress.percentage, progress.message, progress.status);
                            
                            if (progress.status === 'success' || progress.status === 'error' || progress.status === 'warning') {
                                // Stop polling immediately
                                if (pollInterval) {
                                    clearInterval(pollInterval);
                                    pollInterval = null;
                                }
                                // Prevent duplicate execution - check flag before proceeding
                                if (exportCompleted) return;
                                exportCompleted = true;
                                isProcessing = false;
                                
                                if (progress.status === 'success') {
                                    // Set download button
                                    const downloadUrl = progress.data.file_url;
                                    const fileName = progress.data.file_name;
                                    
                                    // Update download button
                                    $('#exportDownloadBtn').attr('href', downloadUrl);
                                    $('#exportDownloadBtn').attr('download', fileName);
                                    
                                    // Auto download file immediately
                                    setTimeout(function() {
                                        // Create temporary link and trigger download
                                        const link = document.createElement('a');
                                        link.href = downloadUrl;
                                        link.download = fileName;
                                        link.style.display = 'none';
                                        document.body.appendChild(link);
                                        link.click();
                                        
                                        // Clean up after a short delay
                                        setTimeout(function() {
                                            document.body.removeChild(link);
                                        }, 100);
                                        
                                        // Hide modal after download starts
                                        setTimeout(function() {
                                            hideExportModal();
                                            
                                            // Show toast notification (non-blocking)
                                            Swal.fire({
                                                title: 'Berhasil!',
                                                text: 'File Excel berhasil didownload.',
                                                icon: 'success',
                                                timer: 3000,
                                                timerProgressBar: true,
                                                showConfirmButton: false,
                                                toast: true,
                                                position: 'top-end'
                                            });
                                            
                                            $('#btnExport').prop('disabled', false);
                                        }, 800);
                                    }, 300);
                                } else {
                                    // Error or warning
                                    hideExportModal();
                                    Swal.fire({
                                        title: progress.status === 'error' ? 'Error!' : 'Peringatan!',
                                        text: progress.message,
                                        icon: progress.status === 'error' ? 'error' : 'warning',
                                        confirmButtonText: 'OK'
                                    });
                                    $('#btnExport').prop('disabled', false);
                                }
                            }
                        }
                    },
                    error: function() {
                        if (pollInterval) {
                            clearInterval(pollInterval);
                            pollInterval = null;
                        }
                        isProcessing = false;
                        exportCompleted = false;
                        hideExportModal();
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal mendapatkan progres export',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        $('#btnExport').prop('disabled', false);
                    }
                });
            }, 1000); // Poll every 1 second
        }

        // Show export modal
        function showExportModal() {
            // Reset state
            isProcessing = false;
            exportCompleted = false;
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
            
            $('#exportModal').modal('show');
            $('#exportProgressBar').css('width', '0%');
            $('#exportProgressBar').removeClass('bg-success bg-danger bg-warning').addClass('bg-info');
            $('#exportProgressBar').addClass('progress-bar-animated');
            $('#exportProgressPercentage').text('0%');
            $('#exportProgressText').text('Memulai export...');
            $('#exportDownloadBtn').hide();
        }

        // Hide export modal
        function hideExportModal() {
            $('#exportModal').modal('hide');
        }

        // Update export progress
        function updateExportProgress(percentage, message, status) {
            $('#exportProgressBar').css('width', percentage + '%');
            $('#exportProgressBar').attr('aria-valuenow', percentage);
            $('#exportProgressPercentage').text(percentage + '%');
            $('#exportProgressText').text(message);
            
            if (status === 'success') {
                $('#exportProgressBar').removeClass('bg-info bg-warning bg-danger progress-bar-animated').addClass('bg-success');
            } else if (status === 'error') {
                $('#exportProgressBar').removeClass('bg-info bg-warning bg-success progress-bar-animated').addClass('bg-danger');
            } else if (status === 'warning') {
                $('#exportProgressBar').removeClass('bg-info bg-success bg-danger progress-bar-animated').addClass('bg-warning');
            }
        }

        // Loading indicator saat form submit
        $('#filterForm').on('submit', function() {
            $('#loadingIndicator').show();
            $('#dataSection').hide();
        });

        // Hide close button on export modal when processing
        $('#exportModal').on('show.bs.modal', function() {
            $('#exportCloseBtn').hide();
        });

        $('#exportModal').on('hide.bs.modal', function() {
            // Stop polling if modal is closed manually
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
            isProcessing = false;
            exportCompleted = false;
            $('#exportCloseBtn').show();
        });
    });
</script>
@endpush

@push('style')
<style>
    .pemeriksaan-detail {
        font-size: 0.85rem;
    }
    
    .pemeriksaan-detail small {
        display: block;
        margin-bottom: 2px;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
    }
    
    .card-header.bg-primary {
        background-color: #0d6efd !important;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush
