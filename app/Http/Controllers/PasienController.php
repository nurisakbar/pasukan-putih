<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasienRequest;
use App\Models\Pasien;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\KondisiRumah;
use App\Models\PhbsRumahTangga;
use App\Models\PemeliharaanKesehatanKeluarga;
use App\Models\PengkajianIndividu;
use App\Models\SirkulasiCairan;
use App\Models\Perkemihan;
use App\Models\Pencernaan;
use App\Models\Muskuloskeletal;
use App\Models\Neurosensori;

class PasienController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        $query = Pasien::latest(); 

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }
    
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->input('tanggal'));
        }
    
        $pasiens = $query->paginate(10);
        $nik = '54469587526';
        $pasien_nik = Pasien::where('nik', $nik)->get();
        
        return view('pasiens.index', compact('pasiens', 'pasien_nik'));
    }

    public function create(): \Illuminate\Contracts\View\View
    {
        $provinces = Province::all();
        return view('pasiens.create', compact('provinces'));
    }

    public function store(PasienRequest $request): \Illuminate\Http\RedirectResponse
    {
        // dd($request->all());
        $pasien = Pasien::create($request->validated());
        // dd($pasien);
        return redirect()->route('pasiens.index')->with('success', 'Created successfully');
    }

    public function show(Pasien $pasien): \Illuminate\Contracts\View\View
    {
        return view('pasiens.show', compact('pasien'));
    }

    public function edit(Pasien $pasien): \Illuminate\Contracts\View\View
    {
        $patient = $pasien;
        return view('pasiens.edit', compact('patient'));
    }

    public function update(PasienRequest $request, Pasien $pasien): \Illuminate\Http\RedirectResponse
    {
        $pasien->update($request->validated());
        return redirect()->route('pasiens.index')->with('success', 'Updated successfully');
    }

    public function destroy(Pasien $pasien): \Illuminate\Http\RedirectResponse
    {
        $pasien->delete();
        return redirect()->route('pasiens.index')->with('success', 'Deleted successfully');
    }

    public function autofill(Request $request)  
    {
        $search = $request->get('term');
        $field = $request->get('field'); 
        
        
        if (!in_array($field, ['name', 'nik'])) {
            return response()->json([]);
        }
        
        $pasiens = Pasien::where($field, 'LIKE', '%' . $search . '%')
                        ->limit(10)
                        ->get();
                        
        return response()->json($pasiens);
    }

    public function getPasienByNik(Request $request)
    {
        $nik = $request->input('nik');  // Mengambil NIK dari request

        $pasien = Pasien::where('nik', $nik)->first();

        if ($pasien) {
            return response()->json([
                'message' => 'Pasien ditemukan',
                'data' => $pasien
            ], 200);
        }

        return response()->json(['message' => 'Pasien tidak ditemukan'], 404);
    }

    public function createAsuhanKeluarga($id): \Illuminate\Contracts\View\View
    {
        $pasienId = $id;
        $kondisiRumah = KondisiRumah::where('pasien_id', $pasienId)->first();
        $PhbsRumahTangga = PhbsRumahTangga::where('pasien_id', $pasienId)->first();
        $pemeliharaanKesehatanKeluarga = PemeliharaanKesehatanKeluarga::where('pasien_id', $pasienId)->first();
        $pengkajianIndividu = PengkajianIndividu::where('pasien_id', $pasienId)->first();
        $sirkulasiCairan = SirkulasiCairan::where('pasien_id', $pasienId)->first();
        $perkemihan = Perkemihan::where('pasien_id', $pasienId)->first();
        $pencernaan = Pencernaan::where('pasien_id', $pasienId)->first();
        $muskuloskeletal = Muskuloskeletal::where('pasien_id', $pasienId)->first();
        $neurosensori = Neurosensori::where('pasien_id', $pasienId)->first();

        return view('kunjungans.form-pencatatan', compact(
            'pasienId', 
            'kondisiRumah', 
            'PhbsRumahTangga',
            'pemeliharaanKesehatanKeluarga',
            'pengkajianIndividu',
            'sirkulasiCairan',
            'perkemihan',
            'pencernaan',
            'muskuloskeletal',
            'neurosensori'
        ));
    }
}
