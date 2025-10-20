@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Debug Log - Skrining ILP</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Instruksi:</strong>
                        <ol>
                            <li>Buka halaman visiting yang memiliki data Skrining ILP</li>
                            <li>Periksa log file di <code>storage/logs/laravel.log</code></li>
                            <li>Cari log dengan keyword "SKRINING ILP DEBUG"</li>
                        </ol>
                    </div>
                    
                    <h5>Log Terbaru:</h5>
                    <pre style="background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;">
@php
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        $logs = file_get_contents($logFile);
        $lines = explode("\n", $logs);
        $skriningLogs = array_filter($lines, function($line) {
            return strpos($line, 'SKRINING ILP DEBUG') !== false || 
                   strpos($line, 'Screening ') !== false || 
                   strpos($line, 'Status dropdown') !== false;
        });
        
        $recentLogs = array_slice($skriningLogs, -50); // Ambil 50 log terbaru
        echo implode("\n", $recentLogs);
    } else {
        echo "Log file tidak ditemukan.";
    }
@endphp
                    </pre>
                    
                    <div class="mt-3">
                        <a href="{{ route('visitings.index') }}" class="btn btn-primary">Kembali ke Daftar Visiting</a>
                        <button onclick="location.reload()" class="btn btn-secondary">Refresh Log</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
