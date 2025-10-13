<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\KunjunganRequest;
use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\User;
use App\Models\Ttv;
use App\Models\Pasien;
use App\Models\SkriningAdl;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KunjunganExport;
use App\Models\Province;
use Carbon\Carbon;

class KunjunganController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        $user = auth()->user();
        $query = Kunjungan::query()->latest();

        if ($user->role === 'perawat' || $user->role === 'operator') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'caregiver') {
            $query->where('user_id', $user->id);
        } else {
            $childUserIds = $this->getAllChildUserIds($user->id, $user->role);
            $query->whereIn('user_id', $childUserIds);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $tanggalAwal = $request->filled('tanggal_awal') 
            ? Carbon::parse($request->input('tanggal_awal'))->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalAkhir = $request->filled('tanggal_akhir') 
            ? Carbon::parse($request->input('tanggal_akhir'))->endOfDay()
            : Carbon::today()->endOfDay();

        // Terapkan filter tanggal
        $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        $kunjungans = $query->paginate(10);

        return view('kunjungans.index', compact('kunjungans'));
    }


    public function create(): \Illuminate\Contracts\View\View
    {

        $users = User::all();
        $pasiens = Pasien::all();
        $provinces = Province::all();
        
        return view('kunjungans.form-rencana-kunjungan-awal', compact('users', 'pasiens', 'provinces'));
    }

    public function store(KunjunganRequest $request): \Illuminate\Http\RedirectResponse
    {
        $pasien = Pasien::find($request->pasien_id);

        if (!$pasien) {
            return redirect()->route('kunjungans.create')->with('error', 'Pasien tidak ditemukan.');
        }

        // Cek apakah pasien sudah memiliki kunjungan di tanggal yang sama
        $existingVisit = Kunjungan::where('pasien_id', $pasien->id)
                        ->whereDate('tanggal', $request->tanggal)
                        ->first();

        if ($existingVisit) {
            return redirect()->route('kunjungans.create')->with('error', 'Pasien sudah memiliki kunjungan pada hari yang sama.');
        }

        // Reset semua opsi henti layanan ke false (0)
        $hentiLayanan = [
            'henti_layanan_kenaikan_aks' => false,
            'henti_layanan_meninggal' => false,
            'henti_layanan_menolak' => false,
            'henti_layanan_pindah_domisili' => false,
        ];

        $selectedHentiLayanan = $request->input('henti_layanan');

        if (array_key_exists($selectedHentiLayanan, $hentiLayanan)) {
            $hentiLayanan[$selectedHentiLayanan] = true;
        }


        $kunjungan = Kunjungan::create(array_merge([
            'tanggal' => $request->tanggal,
            'pasien_id' => $pasien->id,
            'user_id' => $request->user_id,
            'hasil_periksa' => $request->hasil_periksa ?? null,
            'status' => 'belum',
            'jenis' => $request->jenis ?? null,
            'skor_aks_data_sasaran' => $request->skor_aks_data_sasaran ?? null,
            'skor_aks' => $request->skor_aks ?? 0,
            'lanjut_kunjungan' => $request->lanjut_kunjungan ?? 0,
            'rencana_kunjungan_lanjutan' => $request->rencana_kunjungan_lanjutan ?? null,
            'rujukan' => $request->rujukan ?? 0,
            'konversi_data_ke_sasaran_kunjungan_lanjutan' => $request->konversi_data_ke_sasaran_kunjungan_lanjutan ?? false,
        ], $hentiLayanan));

        $ttv = Ttv::create([
            'kunjungan_id' => $kunjungan->id,
        ]);

        return redirect()->route('kunjungans.index')
        ->with('success', 'Created successfully');

    }


    public function show(Kunjungan $kunjungan): \Illuminate\Contracts\View\View
    {
        return view('kunjungans.show', compact('kunjungan'));
    }

    public function edit(Kunjungan $kunjungan): \Illuminate\Contracts\View\View
    {
        $kunjungan = Kunjungan::with('pasien')->where('id', $kunjungan->id)->latest()->first();
        return view('kunjungans.edit', compact('kunjungan'));
    }

    public function update(KunjunganRequest $request, Kunjungan $kunjungan): \Illuminate\Http\RedirectResponse
    {
        $existingVisit = Kunjungan::where('pasien_id', $request->pasien_id)
                          ->whereDate('tanggal', $request->tanggal)
                          ->where('id', '<>', $kunjungan->id) 
                          ->first();

        if ($existingVisit) {
            return redirect()->route('kunjungans.edit', $kunjungan->id)->with('error', 'Pasien sudah memiliki kunjungan pada hari yang sama.');
        }

        $kunjungan->update($request->validated());
        return redirect()->route('kunjungans.index')->with('success', 'Updated successfully');
    }

    public function destroy(Kunjungan $kunjungan): \Illuminate\Http\RedirectResponse
    {
        $kunjungan->delete();
        return redirect()->route('kunjungans.index')->with('success', 'Deleted successfully');
    }

    public function rencanaKunjunganAwal()
    {
        return view('kunjungans.form-rencana-kunjungan-awal');
    }

    public function skriningAdl($id)
    {

        $kunjungan = Kunjungan::with('pasien')->where('id', $id)->latest()->first();
        $skriningAdl = SkriningAdl::where('kunjungan_id', $kunjungan->id)->first();
        // dd($skriningAdl);
        if (!$kunjungan) {
            return redirect()->route('kunjungans.index')->with('error', 'Kunjungan not found');
        }
        if ($skriningAdl) {
            return view('kunjungans.form-skrining-adl', compact('kunjungan', 'skriningAdl'));
        }
       return view('kunjungans.form-skrining-adl', compact('kunjungan', 'skriningAdl'));
    }

    public function storeSkriningAdl(Request $request, $id)
    {
        // Validasi input
        // dd($request->all());
        $request->validate([
            'bab_control'  => 'required|integer',
            'bak_control'  => 'required|integer',
            'eating'      => 'required|integer',
            'stairs'       => 'required|integer',
            'bathing'      => 'required|integer',
            'transfer'     => 'required|integer',
            'walking'     => 'required|integer',
            'dressing'     => 'required|integer',
            'grooming'     => 'required|integer',
            'toilet_use'   => 'required|integer',
            'pendamping_tetap' => 'required|integer',
            'butuh_orang'      => 'required|integer',

        ]);

        $pasien = Pasien::find($request->pasien_id);
        $sasaran_home_service = ($pasien->jenis_ktp == 'DKI' && $request->butuh_orang == 1 && $request->pendamping_tetap == 1 && $request->total_score < 9) ? 1 : 0;

        // dd($sasaran_home_service);

        SkriningAdl::create([
            'kunjungan_id' => $id,
            'pemeriksa_id' => auth()->user()->id,
            'pendamping_tetap' => $request->pendamping_tetap,
            'butuh_orang'      => $request->butuh_orang,
            'pasien_id'    => $request->pasien_id,
            'bab_control'  => $request->bab_control,
            'bak_control'  => $request->bak_control,
            'eating'      => $request->eating,
            'stairs'       => $request->stairs,
            'bathing'      => $request->bathing,
            'transfer'     => $request->transfer,
            'walking'     => $request->walking,
            'dressing'     => $request->dressing,
            'grooming'     => $request->grooming,
            'toilet_use'   => $request->toilet_use,
            'total_score'  => $request->total_score,
            'sasaran_home_service' => $sasaran_home_service
        ]);

        return redirect()->route('kunjungans.index')->with('success', 'Data Skrining ADL berhasil disimpan!');
    }

    public function updateSkriningAdl(Request $request, $id)
        {
        $request->validate([
            'pendamping_tetap' => 'required|integer',
            'butuh_orang'      => 'required|integer',
            'bab_control'  => 'required|integer',
            'bak_control'  => 'required|integer',
            'eating'      => 'required|integer',
            'stairs'       => 'required|integer',
            'bathing'      => 'required|integer',
            'transfer'     => 'required|integer',
            'walking'     => 'required|integer',    
            'dressing'     => 'required|integer',
            'grooming'     => 'required|integer',
            'toilet_use'   => 'required|integer',
        ]);

        $skrining = SkriningAdl::findOrFail($id);
        $pasien = $skrining->kunjungan->pasien ?? null;;
        $total_score = $request->bab_control 
        + $request->bak_control 
        + $request->eating 
        + $request->stairs 
        + $request->bathing 
        + $request->transfer 
        + $request->walking 
        + $request->dressing 
        + $request->grooming 
        + $request->toilet_use;
        $sasaran_home_service = ($pasien && $pasien->jenis_ktp == 'DKI' && 
        $request->butuh_orang == 1 && 
        $request->pendamping_tetap == 1 && 
        $total_score < 9) ? 1 : 0;


        SkriningAdl::where('id', $id)->update([
            'pendamping_tetap' => $request->pendamping_tetap,
            'butuh_orang'      => $request->butuh_orang,
            'bab_control'  => $request->bab_control,
            'bak_control'  => $request->bak_control,
            'eating'      => $request->eating,
            'stairs'       => $request->stairs,
            'bathing'      => $request->bathing,
            'transfer'     => $request->transfer,
            'walking'     => $request->walking,     
            'dressing'     => $request->dressing,
            'grooming'     => $request->grooming,   
            'toilet_use'   => $request->toilet_use,
            'total_score'  => $request->total_score,
            'sasaran_home_service' => $sasaran_home_service
        ]); 

        return redirect()->route('kunjungans.index')->with('success', 'Data Skrining ADL berhasil diupdate!');
    }

    public function exportKunjungan() 
    {
        return Excel::download(new KunjunganExport, 'Kunjunagan.xlsx');
    }

    private function getAllChildUserIds($userId, $role)
    {
        if ($role === 'superadmin') {
            return User::pluck('id')->toArray();
        }

        $allChildren = collect();
        
        $directChildren = User::where('pustu_id', $userId)->pluck('id');
        
        foreach ($directChildren as $childId) {
            $allChildren->push($childId);
            $allChildren = $allChildren->merge($this->getAllChildUserIds($childId, $role));
        }

        return $allChildren->toArray();
    }

    public function editKunjunganFromPasiens($id)
    {
        $kunjungan = Kunjungan::findOrFail($id);
        return view('pasiens.edit-from-pasien', compact('kunjungan'));
    }

    public function updateKunjunganFromPasiens(Request $request, $id)
    {
        $kunjungan = Kunjungan::findOrFail($id);
        $kunjungan->update($request->all());
        return redirect()->route('kunjungans.index')->with('success', 'Data Kunjungan berhasil diupdate!');
    }
}


        
    

