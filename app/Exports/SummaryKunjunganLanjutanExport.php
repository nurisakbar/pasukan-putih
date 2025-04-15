<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummaryKunjunganLanjutanExport implements FromCollection, WithHeadings, ShouldAutoSize
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

    public function collection()
    {
        $query = DB::table('visitings')
            ->select(
                'regencies.name as kabupaten_kota',
                'districts.name as kecamatan',
                'villages.name as kelurahan',
                DB::raw('COALESCE(COUNT(DISTINCT visitings.id), 0) as jumlah_sasaran'),
                DB::raw("COALESCE(COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Awal' AND health_forms.skor_aks IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END), 0) as jumlah_total_warga_sasaran_setelah_kunjungan_awal"),
                DB::raw("COALESCE(COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Lanjutan' THEN visitings.id END), 0) as jumlah_warga_yang_mendapat_kunjungan_lanjutan"),
                DB::raw("COALESCE(COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Lanjutan' AND health_forms.skor_aks NOT IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END), 0) as jumlah_bukan_warga_sasaran_setelah_kunjungan_lanjutan"),
                DB::raw("COALESCE(COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Lanjutan' AND health_forms.skor_aks IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END), 0) as jumlah_total_warga_sasaran_setelah_kunjungan_lanjutan")
            )        
            ->join('pasiens', 'visitings.pasien_id', '=', 'pasiens.id')
            ->join('villages', 'pasiens.village_id', '=', 'villages.id')
            ->join('districts', 'villages.district_id', '=', 'districts.id')
            ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->leftJoin('health_forms', 'visitings.id', '=', 'health_forms.visiting_id');
            
        // Filter berdasarkan bulan
        if ($this->bulan) {
            $query->whereMonth('visitings.tanggal', $this->bulan);
        }

        // Filter berdasarkan rentang tanggal
        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $query->whereBetween('visitings.tanggal', [$this->tanggalAwal, $this->tanggalAkhir]);
        }

        if (\Auth::user()->role === 'perawat') {
            $query->where('visitings.user_id', \Auth::id());
        }

        // Filter berdasarkan pencarian (misalnya nama atau NIK)
        if ($this->search) {
            $query->where(function ($subquery) {
                $subquery->where('pasiens.name', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('pasiens.nik', 'LIKE', '%' . $this->search . '%');
            });
        }

        // Group dan order
        return $query->groupBy('regencies.name', 'districts.name', 'villages.name')
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name')
            ->get();
    }


    public function headings(): array
    {
        return [
            'KABUPATEN/KOTA',
            'KECAMATAN',
            'KELURAHAN',
            'JUMLAH SASARAN',
            'JUMLAH TOTAL WARGA SASARAN SETELAH KUNJUNGAN AWAL',
            'JUMLAH WARGA YANG MENDAPAT KUNJUNGAN LANJUTAN',
            'JUMLAH BUKAN WARGA SASARAN SETELAH KUNJUNGAN LANJUTAN',
            'JUMLAH TOTAL WARGA SASARAN SETELAH KUNJUNGAN LANJUTAN',
        ];
    }
}
