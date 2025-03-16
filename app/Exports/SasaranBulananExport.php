<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Kunjungan;
use App\Models\SkriningAdl;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SasaranBulananExport implements FromArray, WithHeadings
{

    protected $bulan;
    protected $tanggalAwal;
    protected $tanggalAkhir;
    protected $search;

    public function __construct($bulan = null, $tanggalAwal = null, $tanggalAkhir = null, $search = null)
    {
        $this->bulan = $bulan;
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
        $this->search = $search;
    }

    public function array(): array
    {
        $query = SkriningAdl::with('kunjungan', 'kunjungan.pasien', 'pemeriksa');

        // Filter berdasarkan bulan kunjungan
        if ($this->bulan) {
            $query->whereMonth('kunjungans.tanggal', $this->bulan);
        }

        // Filter berdasarkan rentang tanggal kunjungan
        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $query->whereBetween('kunjungans.tanggal', [
                Carbon::parse($this->tanggalAwal)->startOfDay(),
                Carbon::parse($this->tanggalAkhir)->endOfDay(),
            ]);
        }

        // Filter pencarian berdasarkan nama atau NIK pasien
        if ($this->search) {
            $query->whereHas('kunjungan.pasien', function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%")
                    ->orWhere('nik', 'LIKE', "%{$this->search}%");
            });
        }

        // Sorting berdasarkan Nama dan Alamat
        $query->join('kunjungans', 'skrining_adl.kunjungan_id', '=', 'kunjungans.id')
            ->join('pasiens', 'kunjungans.pasien_id', '=', 'pasiens.id')
            ->orderBy('pasiens.name', 'asc')
            ->orderBy('pasiens.regency_id', 'asc')
            ->orderBy('pasiens.district_id', 'asc')
            ->orderBy('pasiens.village_id', 'asc');

        $data = $query->select('skrining_adl.*')->get();

        return $data->map(function ($skrining, $index) {
            $tanggal_lahir = $skrining->kunjungan->pasien->tanggal_lahir ?? null;
            $umur = $tanggal_lahir ? Carbon::parse($tanggal_lahir)->age : '';

            return [
                'No' => $index + 1,
                'KABUPATEN/KOTA' => $skrining->kunjungan->pasien->regency_id ?? '',
                'KECAMATAN' => $skrining->kunjungan->pasien->district_id ?? '',
                'KELURAHAN' => $skrining->kunjungan->pasien->village_id ?? '',
                'NIK' => $skrining->kunjungan->pasien->nik ?? '',
                'NAMA' => $skrining->kunjungan->pasien->name ?? '',
                'JENIS KTP' => $skrining->kunjungan->pasien->jenis_ktp ?? '',
                'UMUR' => $umur,
                'TANGGAL KUNJUNGAN' => $skrining->kunjungan->tanggal ?? '',
                'MEMBUTUHKAN BANTUAN UNTUK KEGIATAN SEHARI-HARI' => $skrining->buth_orang == 1 ? 'YA' : 'TIDAK',
                'SKOR AKS' => $skrining->total_score ?? '',
                'MEMILIKI PENDAMPING DALAM MELAKUKAN AKTIVITAS SEHARI-HARI' => $skrining->pendamping_tetap == 1 ? 'YA' : 'TIDAK',
                'SASARAN' => $skrining->sasaran_home_service == 1 ? 'YA' : 'TIDAK',
            ];
        })->toArray();
    }



    public function headings(): array
    {
        return [
            'NO', 
            'KABUPATEN/KOTA', 
            'KECAMATAN', 
            'KELURAHAN', 
            'NIK', 
            'NAMA', 
            'JENIS KTP', 
            'UMUR', 
            'MEMBUTUHKAN BANTUAN UNTUK KEGIATAN SEHARI-HARI', 
            'SKOR AKS', 
            'MEMILIKI PENDAMPING DALAM MELAKUKAN AKTIVITAS SEHARI-HARI',
            'SASARAN'
        ];
    }

}
