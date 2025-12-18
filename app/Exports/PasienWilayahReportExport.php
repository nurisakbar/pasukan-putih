<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class PasienWilayahReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $groupedData;
    protected $groupBy;

    public function __construct($groupedData, $groupBy = 'district')
    {
        $this->groupedData = $groupedData;
        $this->groupBy = $groupBy;
    }

    public function collection()
    {
        $flatData = collect();
        
        foreach ($this->groupedData as $wilayahKey => $wilayahData) {
            foreach ($wilayahData['pasien'] as $pasien) {
                $flatData->push(array_merge($pasien, [
                    'wilayah_name' => $wilayahData['wilayah_name'],
                    'group_by' => $this->groupBy
                ]));
            }
        }
        
        return $flatData;
    }

    public function headings(): array
    {
        $groupLabels = [
            'village' => 'Kelurahan/Desa',
            'district' => 'Kecamatan',
            'regency' => 'Kabupaten/Kota',
            'province' => 'Provinsi'
        ];
        
        $groupLabel = $groupLabels[$this->groupBy] ?? 'Wilayah';
        
        return [
            'No',
            $groupLabel,
            'NIK',
            'Nama Pasien',
            'Alamat',
            'RT',
            'RW',
            'Kelurahan',
            'Kecamatan',
            'Kabupaten/Kota',
            'Provinsi',
            'Tanggal Kunjungan Terakhir',
            'Status Kunjungan',
            'TD (Tekanan Darah)',
            'Nadi',
            'Suhu',
            'BMI',
            'Kategori BMI',
            'Skor ADL',
            'Butuh Orang',
            'Pendamping Tetap',
            'Sasaran Home Service',
            'Skor AKS',
            'Tingkat Kemandirian',
            'Henti Layanan',
            'Kunjungan Lanjutan',
            'Diagnosis Penyakit',
            'Nama Penginput'
        ];
    }

    public function map($row): array
    {
        static $counter = 0;
        $counter++;

        // Convert to array if object
        $data = is_object($row) ? (array) $row : $row;

        // TTV Data
        $pemeriksaan = $data['pemeriksaan'] ?? [];
        $ttv = $pemeriksaan['ttv'] ?? null;
        $td = ($ttv && isset($ttv['blood_pressure'])) ? $ttv['blood_pressure'] : '-';
        $nadi = ($ttv && isset($ttv['pulse'])) ? $ttv['pulse'] : '-';
        $suhu = ($ttv && isset($ttv['temperature'])) ? $ttv['temperature'] : '-';
        $bmi = ($ttv && isset($ttv['bmi']) && $ttv['bmi']) ? number_format($ttv['bmi'], 2) : '-';
        $bmiCategory = ($ttv && isset($ttv['bmi_category'])) ? $ttv['bmi_category'] : '-';

        // Skrining ADL Data
        $adl = $pemeriksaan['skrining_adl'] ?? null;
        $skorAdl = ($adl && isset($adl['total_score'])) ? $adl['total_score'] : '-';
        $butuhOrang = ($adl && isset($adl['butuh_orang']) && $adl['butuh_orang']) ? 'Ya' : 'Tidak';
        $pendampingTetap = ($adl && isset($adl['pendamping_tetap']) && $adl['pendamping_tetap']) ? 'Ya' : 'Tidak';
        $sasaranHomeService = ($adl && isset($adl['sasaran_home_service']) && $adl['sasaran_home_service']) ? 'Ya' : 'Tidak';

        // Health Form Data
        $healthForm = $pemeriksaan['health_form'] ?? null;
        $skorAks = ($healthForm && isset($healthForm['skor_aks'])) ? $healthForm['skor_aks'] : '-';
        $tingkatKemandirian = ($healthForm && isset($healthForm['tingkat_kemandirian'])) ? $healthForm['tingkat_kemandirian'] : '-';
        $hentiLayanan = ($healthForm && isset($healthForm['henti_layanan'])) ? $healthForm['henti_layanan'] : '-';
        $kunjunganLanjutan = ($healthForm && isset($healthForm['kunjungan_lanjutan'])) ? $healthForm['kunjungan_lanjutan'] : '-';

        // Kunjungan Data
        $kunjungan = $data['kunjungan'] ?? [];
        $tanggalKunjungan = (isset($kunjungan['tanggal']) && $kunjungan['tanggal']) 
            ? Carbon::parse($kunjungan['tanggal'])->format('d/m/Y') 
            : '-';
        $statusKunjungan = isset($kunjungan['status']) ? $kunjungan['status'] : '-';

        // Diagnosis dan Nama Penginput
        $diagnosisPenyakit = $data['diagnosis_penyakit'] ?? '-';
        $namaPenginput = $data['nama_penginput'] ?? '-';

        return [
            $counter,
            $data['wilayah_name'] ?? '-',
            "'" . ($data['nik'] ?? ''), // Prefix dengan single quote agar Excel treat sebagai text
            $data['nama_pasien'] ?? '-',
            $data['alamat'] ?? '-',
            $data['rt'] ?? '-',
            $data['rw'] ?? '-',
            $data['village_name'] ?? '-',
            $data['district_name'] ?? '-',
            $data['regency_name'] ?? '-',
            $data['province_name'] ?? '-',
            $tanggalKunjungan,
            $statusKunjungan,
            $td,
            $nadi,
            $suhu,
            $bmi,
            $bmiCategory,
            $skorAdl,
            $butuhOrang,
            $pendampingTetap,
            $sasaranHomeService,
            $skorAks,
            $tingkatKemandirian,
            $hentiLayanan,
            $kunjunganLanjutan,
            $diagnosisPenyakit,
            $namaPenginput
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true], 'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // Wilayah
            'C' => 20,  // NIK
            'D' => 25,  // Nama Pasien
            'E' => 30,  // Alamat
            'F' => 8,   // RT
            'G' => 8,   // RW
            'H' => 20,  // Kelurahan
            'I' => 20,  // Kecamatan
            'J' => 20,  // Kabupaten/Kota
            'K' => 20,  // Provinsi
            'L' => 20,  // Tanggal Kunjungan Terakhir
            'M' => 20,  // Status Kunjungan
            'N' => 15,  // TD
            'O' => 10,  // Nadi
            'P' => 10,  // Suhu
            'Q' => 10,  // BMI
            'R' => 15,  // Kategori BMI
            'S' => 12,  // Skor ADL
            'T' => 15,  // Butuh Orang
            'U' => 18,  // Pendamping Tetap
            'V' => 20,  // Sasaran Home Service
            'W' => 12,  // Skor AKS
            'X' => 20,  // Tingkat Kemandirian
            'Y' => 15,  // Henti Layanan
            'Z' => 20,  // Kunjungan Lanjutan
            'AA' => 40, // Diagnosis Penyakit
            'AB' => 25, // Nama Penginput
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Freeze first row
                $sheet->freezePane('A2');
                
                // Auto filter
                $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
            },
        ];
    }
}
