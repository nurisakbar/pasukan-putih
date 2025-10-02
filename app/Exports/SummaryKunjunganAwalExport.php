<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SummaryKunjunganAwalExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $tanggalMulai;
    protected $tanggalSelesai;
    protected $search;
    protected $bulan;

    public function __construct($tanggalMulai, $tanggalSelesai, $search = null, $bulan = null)
    {
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalSelesai = $tanggalSelesai;
        $this->search = $search;
        $this->bulan = $bulan;
    }

    public function collection()
    {
        $query = DB::table('visitings')
            ->select(
                'regencies.name as regency_name',
                'districts.name as district_name',
                'villages.name as village_name',
                DB::raw('COUNT(DISTINCT visitings.id) as jumlah_sasaran'),
                DB::raw("COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Awal' THEN visitings.id END) as jumlah_kunjungan_awal"),
                DB::raw("COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Awal' AND health_forms.skor_aks NOT IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END) as jumlah_bukan_sasaran"),
                DB::raw("COUNT(DISTINCT CASE WHEN visitings.status = 'Kunjungan Awal' AND health_forms.skor_aks IN ('ketergantungan_berat', 'ketergantungan_total') THEN visitings.id END) as jumlah_sasaran_setelah_kunjungan")
            )
            ->join('pasiens', 'visitings.pasien_id', '=', 'pasiens.id')
            ->join('villages', 'pasiens.village_id', '=', 'villages.id')
            ->join('districts', 'villages.district_id', '=', 'districts.id')
            ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->join('users', 'visitings.user_id', '=', 'users.id')
            ->leftJoin('health_forms', 'visitings.id', '=', 'health_forms.visiting_id');

        // Filter tanggal
        if ($this->tanggalMulai && $this->tanggalSelesai) {
            $query->whereBetween('visitings.tanggal', [$this->tanggalMulai, $this->tanggalSelesai]);
        }

        // Filter user
        if (Auth::user()->role === 'perawat' || Auth::user()->role === 'operator') {
            $query->where('visitings.user_id', Auth::id());
        }

        // Filter search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('regencies.name', 'like', '%' . $this->search . '%')
                    ->orWhere('districts.name', 'like', '%' . $this->search . '%')
                    ->orWhere('villages.name', 'like', '%' . $this->search . '%');
            });
        }

        $results = $query
            ->groupBy('regencies.name', 'districts.name', 'villages.name')
            ->orderBy('regencies.name')
            ->orderBy('districts.name')
            ->orderBy('villages.name')
            ->get();

        // Ubah null ke 0
        return $results->map(function ($item) {
            return [
                'regency_name' => $item->regency_name,
                'district_name' => $item->district_name,
                'village_name' => $item->village_name,
                'jumlah_sasaran' => (int) ($item->jumlah_sasaran ?? 0),
                'jumlah_kunjungan_awal' => (int) ($item->jumlah_kunjungan_awal ?? 0),
                'jumlah_bukan_sasaran' => (int) ($item->jumlah_bukan_sasaran ?? 0),
                'jumlah_sasaran_setelah_kunjungan' => (int) ($item->jumlah_sasaran_setelah_kunjungan ?? 0),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kabupaten/Kota',
            'Kecamatan',
            'Kelurahan',
            'Jumlah Sasaran',
            'Jumlah Kunjungan Awal',
            'Jumlah Bukan Sasaran Setelah Kunjungan Awal',
            'Jumlah Sasaran Setelah Kunjungan Awal',
        ];
    }
}
