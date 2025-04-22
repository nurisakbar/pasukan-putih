<?php
namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummaryHentiLayananExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        return \DB::table('pasiens as p')
            ->join('villages as vil', 'vil.id', '=', 'p.village_id')
            ->join('districts as d', 'd.id', '=', 'vil.district_id')
            ->join('regencies as r', 'r.id', '=', 'd.regency_id')
    
            // Left join untuk health_form berdasarkan visiting terakhir
            ->leftJoin('visitings as v', 'v.pasien_id', '=', 'p.id')
            ->leftJoin('health_forms as hf', 'hf.visiting_id', '=', 'v.id')
    
            // Query untuk menghitung jumlah sasaran dan total henti layanan
            ->select(
                'r.name as `KABUPATEN/KOTA`',
                'd.name as `KECAMATAN`',
                'vil.name as `KELURAHAN`',
                \DB::raw('COUNT(p.id) as `JUMLAH SASARAN`'),
                \DB::raw('COUNT(CASE WHEN hf.henti_layanan = "kenaikan_aks" THEN 1 END) as `TOTAL HENTI LAYANAN - KENAIKAN AKS`'),
                \DB::raw('COUNT(CASE WHEN hf.henti_layanan = "meninggal" THEN 1 END) as `TOTAL HENTI LAYANAN - MENINGGAL`'),
                \DB::raw('COUNT(CASE WHEN hf.henti_layanan = "menolak" THEN 1 END) as `TOTAL HENTI LAYANAN - MENOLAK`'),
                \DB::raw('COUNT(CASE WHEN hf.henti_layanan = "pindah_domisili" THEN 1 END) as `TOTAL HENTI LAYANAN - PINDAH DOMISILI`')
            )
    
            // Grouping berdasarkan KABUPATEN/KOTA, KECAMATAN, KELURAHAN
            ->groupBy('r.name', 'd.name', 'vil.name')
    
            // Optional, bisa ditambahkan filter jika diperlukan
            ->when($this->bulan, function ($query) {
                return $query->whereMonth('v.tanggal', $this->bulan);
            })
            ->when($this->tanggalAwal, function ($query) {
                return $query->whereDate('v.tanggal', '>=', $this->tanggalAwal);
            })
            ->when($this->tanggalAkhir, function ($query) {
                return $query->whereDate('v.tanggal', '<=', $this->tanggalAkhir);
            })
            ->when($this->search, function ($query) {
                return $query->where('p.nik', 'like', '%' . $this->search . '%')
                                ->orWhere('p.name', 'like', '%' . $this->search . '%')
                                ->orWhere('p.alamat', 'like', '%' . $this->search . '%')
                                ->orWhere('p.jenis_ktp', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'KABUPATEN/KOTA', 'KECAMATAN', 'KELURAHAN', 'JUMLAH SASARAN', 
            'TOTAL HENTI LAYANAN - KENAIKAN AKS', 'TOTAL HENTI LAYANAN - MENINGGAL', 
            'TOTAL HENTI LAYANAN - MENOLAK', 'TOTAL HENTI LAYANAN - PINDAH DOMISILI'
        ];
    }
}
