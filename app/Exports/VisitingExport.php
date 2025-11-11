<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class VisitingExport implements FromArray, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function array(): array
    {
        $user = Auth::user();
        
        // Build query sama seperti di index
        $query = DB::table('visitings')
            ->leftJoin('pasiens', 'pasiens.id', '=', 'visitings.pasien_id')
            ->leftJoin('villages', 'villages.id', '=', 'pasiens.village_id')
            ->leftJoin('districts', 'districts.id', '=', 'villages.district_id')
            ->leftJoin('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->leftJoin('skrining_adl', 'skrining_adl.visiting_id', '=', 'visitings.id')
            ->whereNull('pasiens.deleted_at')
            ->select(
                'visitings.id',
                'visitings.pasien_id',
                'visitings.tanggal',
                'visitings.status',
                'pasiens.name as pasien_name',
                'pasiens.nik as pasien_nik',
                'pasiens.jenis_kelamin as pasien_jenis_kelamin',
                'pasiens.alamat as pasien_alamat',
                'skrining_adl.total_score as aks_score'
            );

        // Filter berdasarkan role
        if ($user->role === 'perawat' || $user->role === 'operator') {
            if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $user->pustu->district_id;
                $pasienIds = DB::table('pasiens')
                    ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                    ->where('villages.district_id', $districtId)
                    ->pluck('pasiens.id');
                $query->whereIn('visitings.pasien_id', $pasienIds);
            } else {
                $query->where(function($q) use ($user) {
                    $q->where('visitings.user_id', $user->id)
                      ->orWhere('visitings.operator_id', $user->id);
                });
            }
        } elseif ($user->role === 'sudinkes') {
            $pasienIds = DB::table('pasiens')
                ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
                ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->where('regencies.id', $user->regency_id)
                ->pluck('pasiens.id');
            $query->whereIn('visitings.pasien_id', $pasienIds);
        }

        // Filter pencarian nama / nik pasien
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('pasiens.name', 'like', "%$search%")
                  ->orWhere('pasiens.nik', 'like', "%$search%");
            });
        }

        // Filter tanggal
        $tanggalAwal = !empty($this->filters['tanggal_awal']) 
            ? Carbon::parse($this->filters['tanggal_awal'])->startOfDay()
            : Carbon::today()->startOfDay();
        
        $tanggalAkhir = !empty($this->filters['tanggal_akhir']) 
            ? Carbon::parse($this->filters['tanggal_akhir'])->endOfDay()
            : Carbon::today()->endOfDay();
        
        $query->whereBetween('visitings.tanggal', [$tanggalAwal, $tanggalAkhir]);

        // Ambil semua data
        $visitings = $query->orderBy('pasiens.name', 'asc')
            ->orderBy('pasiens.alamat', 'asc')
            ->orderBy('visitings.tanggal', 'asc')
            ->get();

        // Group by pasien dan ambil nilai AKS
        $groupedData = [];
        foreach ($visitings as $visiting) {
            $pasienId = $visiting->pasien_id;
            
            if (!isset($groupedData[$pasienId])) {
                $groupedData[$pasienId] = [
                    'pasien_id' => $pasienId,
                    'name' => $visiting->pasien_name ?? 'Tidak Ada',
                    'nik' => $visiting->pasien_nik ?? 'Tidak Ada',
                    'jenis_kelamin' => $visiting->pasien_jenis_kelamin ?? 'Tidak Ada',
                    'alamat' => $visiting->pasien_alamat ?? 'Tidak Ada',
                    'tanggal_kunjungan' => $visiting->tanggal ?? null,
                    'aks_awal' => null,
                    'aks_lanjutan_1' => null,
                    'aks_lanjutan_2' => null,
                    'aks_lanjutan_3' => null,
                    'total_kunjungan' => 0,
                    'visitings' => []
                ];
            }

            // Simpan visiting untuk diolah
            $groupedData[$pasienId]['visitings'][] = [
                'status' => $visiting->status,
                'tanggal' => $visiting->tanggal,
                'aks_score' => $visiting->aks_score
            ];
            $groupedData[$pasienId]['total_kunjungan']++;
        }

        // Proses setiap pasien untuk mendapatkan nilai AKS
        $result = [];
        
        // Sort grouped data by name then address
        uasort($groupedData, function($a, $b) {
            $nameCompare = strcmp($a['name'], $b['name']);
            if ($nameCompare !== 0) {
                return $nameCompare;
            }
            return strcmp($a['alamat'], $b['alamat']);
        });
        
        $no = 1;
        foreach ($groupedData as $pasienId => $data) {
            // Sort visitings by tanggal
            usort($data['visitings'], function($a, $b) {
                $dateA = $a['tanggal'] ? strtotime($a['tanggal']) : 0;
                $dateB = $b['tanggal'] ? strtotime($b['tanggal']) : 0;
                return $dateA - $dateB;
            });

            $aksAwal = null;
            $aksLanjutan1 = null;
            $aksLanjutan2 = null;
            $aksLanjutan3 = null;
            $tanggalKunjungan = null;
            $lanjutanCount = 0;

            foreach ($data['visitings'] as $visiting) {
                if ($visiting['status'] === 'Kunjungan Awal') {
                    $aksAwal = $visiting['aks_score'] ?? null;
                    // Set tanggal kunjungan dari kunjungan awal
                    if (!$tanggalKunjungan && $visiting['tanggal']) {
                        $tanggalKunjungan = $visiting['tanggal'];
                    }
                } elseif ($visiting['status'] === 'Kunjungan Lanjutan') {
                    $lanjutanCount++;
                    if ($lanjutanCount === 1) {
                        $aksLanjutan1 = $visiting['aks_score'] ?? null;
                    } elseif ($lanjutanCount === 2) {
                        $aksLanjutan2 = $visiting['aks_score'] ?? null;
                    } elseif ($lanjutanCount === 3) {
                        $aksLanjutan3 = $visiting['aks_score'] ?? null;
                    }
                }
            }

            // Jika tidak ada tanggal kunjungan dari kunjungan awal, ambil tanggal pertama
            if (!$tanggalKunjungan && !empty($data['visitings'])) {
                $tanggalKunjungan = $data['visitings'][0]['tanggal'];
            }

            // Format tanggal
            $tanggalFormatted = '-';
            if ($tanggalKunjungan) {
                try {
                    $tanggalFormatted = Carbon::parse($tanggalKunjungan)->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalFormatted = $tanggalKunjungan;
                }
            }

            $result[] = [
                'NO' => $no++,
                'NAMA' => $data['name'],
                'NIK' => '`' . $data['nik'] . '`', // Format sebagai text untuk NIK
                'JENIS KELAMIN' => $data['jenis_kelamin'],
                'ALAMAT' => $data['alamat'],
                'TANGGAL KUNJUNGAN' => $tanggalFormatted,
                'NILAI AKS KUNJUNGAN AWAL' => $aksAwal !== null ? $aksAwal : '-',
                'NILAI AKS KUNJUNGAN LANJUTAN 1' => $aksLanjutan1 !== null ? $aksLanjutan1 : '-',
                'NILAI AKS KUNJUNGAN LANJUTAN 2' => $aksLanjutan2 !== null ? $aksLanjutan2 : '-',
                'NILAI AKS KUNJUNGAN LANJUTAN 3' => $aksLanjutan3 !== null ? $aksLanjutan3 : '-',
                'TOTAL KUNJUNGAN' => $data['total_kunjungan']
            ];
        }

        return $result;
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA',
            'NIK',
            'JENIS KELAMIN',
            'ALAMAT',
            'TANGGAL KUNJUNGAN',
            'NILAI AKS KUNJUNGAN AWAL',
            'NILAI AKS KUNJUNGAN LANJUTAN 1',
            'NILAI AKS KUNJUNGAN LANJUTAN 2',
            'NILAI AKS KUNJUNGAN LANJUTAN 3',
            'TOTAL KUNJUNGAN'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT, // Format kolom NIK (kolom C) sebagai teks
        ];
    }
}

