@extends('layouts.app')

@php
    use Carbon\Carbon;
@endphp

@push('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endpush

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
                            <button type="button" id="btnFilter" class="btn btn-primary flex-fill">
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

        <!-- Data Section -->
        <div id="dataSection">
            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pasien-wilayah-table" class="table table-bordered table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="12%">{{ request('group_by') == 'village' ? 'Kelurahan' : (request('group_by') == 'district' ? 'Kecamatan' : (request('group_by') == 'regency' ? 'Kabupaten/Kota' : 'Provinsi')) }}</th>
                                    <th width="15%">NIK</th>
                                    <th width="18%">Nama Pasien</th>
                                    <th width="15%">Alamat</th>
                                    <th width="8%">RT/RW</th>
                                    <th width="12%">Tanggal Kunjungan</th>
                                    <th width="17%">Pemeriksaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTable AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
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

        // Initialize DataTable
        var table = $('#pasien-wilayah-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("reports.pasien-wilayah.data") }}',
                type: 'POST',
                data: function(d) {
                    d.group_by = $('#group_by').val();
                    d.wilayah_id = $('#wilayah_id').val();
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [
                { 
                    data: null,
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'wilayah_name', name: 'wilayah_name' },
                { 
                    data: 'nik', 
                    name: 'nik',
                    render: function(data) {
                        return data || '-';
                    }
                },
                { 
                    data: 'nama_pasien', 
                    name: 'nama_pasien',
                    render: function(data, type, row) {
                        let html = '<strong>' + (data || '-') + '</strong>';
                        const groupBy = $('#group_by').val();
                        if (groupBy !== 'village' && row.village_name) {
                            html += '<br><small class="text-muted">' + row.village_name + '</small>';
                        }
                        if (groupBy === 'province') {
                            if (row.regency_name || row.district_name) {
                                html += '<br><small class="text-muted">' + 
                                    (row.regency_name || '') + 
                                    (row.regency_name && row.district_name ? ' > ' : '') +
                                    (row.district_name || '') + 
                                    '</small>';
                            }
                        }
                        return html;
                    }
                },
                { 
                    data: 'alamat', 
                    name: 'alamat',
                    render: function(data) {
                        return data || '-';
                    }
                },
                { 
                    data: 'rt_rw', 
                    name: 'rt_rw',
                    render: function(data) {
                        return data && data !== '-/-' ? 'RT ' + data : '-';
                    }
                },
                { 
                    data: 'tanggal_kunjungan_formatted', 
                    name: 'tanggal_kunjungan',
                    render: function(data, type, row) {
                        if (!data || data === '-') return '<span class="text-muted">-</span>';
                        let html = '<span class="badge bg-info">' + data + '</span>';
                        if (row.status_kunjungan && row.status_kunjungan !== '-') {
                            html += '<br><small class="text-muted">Status: ' + row.status_kunjungan + '</small>';
                        }
                        return html;
                    }
                },
                { 
                    data: null,
                    name: 'pemeriksaan',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let html = '<div class="pemeriksaan-detail">';
                        
                        // TTV
                        if (row.pemeriksaan_ttv && row.pemeriksaan_ttv !== '-') {
                            html += '<div class="mb-1"><small class="text-primary">' +
                                '<i class="fas fa-heartbeat me-1"></i><strong>TTV:</strong> ' +
                                row.pemeriksaan_ttv + '</small></div>';
                        }
                        
                        // ADL
                        if (row.pemeriksaan_adl && row.pemeriksaan_adl !== '-') {
                            html += '<div class="mb-1"><small class="text-success">' +
                                '<i class="fas fa-check-circle me-1"></i><strong>AKS:</strong> ' +
                                row.pemeriksaan_adl + '</small></div>';
                        }
                        
                        // Health Form
                        if (row.pemeriksaan_health_form && row.pemeriksaan_health_form !== '-') {
                            html += '<div class="mb-1"><small class="text-warning">' +
                                '<i class="fas fa-file-medical me-1"></i><strong>Health Form:</strong> ' +
                                row.pemeriksaan_health_form + '</small></div>';
                        }
                        
                        if (row.pemeriksaan_ttv === '-' && row.pemeriksaan_adl === '-' && row.pemeriksaan_health_form === '-') {
                            html += '<span class="text-muted">Tidak ada data pemeriksaan</span>';
                        }
                        
                        html += '</div>';
                        return html;
                    }
                }
            ],
            order: [[3, 'asc']], // Order by nama pasien
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json",
                emptyTable: "Tidak ada data ditemukan",
                processing: "Memproses data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok ditemukan"
            },
            dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            drawCallback: function() {
                // Adjust column width after draw
                table.columns.adjust();
            }
        });

        // Reload table when filter button clicked
        $('#btnFilter').on('click', function() {
            table.ajax.reload();
        });

        // Auto reload when group_by or wilayah_id changes
        $('#group_by, #wilayah_id').on('change', function() {
            table.ajax.reload();
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
