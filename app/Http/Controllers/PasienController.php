<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasienRequest;
use App\Models\Pasien;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\KondisiRumah;
use App\Models\PhbsRumahTangga;
use App\Models\PemeliharaanKesehatanKeluarga;
use App\Models\PengkajianIndividu;
use App\Models\SirkulasiCairan;
use App\Models\Perkemihan;
use App\Models\Pencernaan;
use App\Models\Muskuloskeletal;
use App\Models\Neurosensori;
use App\Models\Kunjungan;
use App\Models\Visiting;
use App\Imports\PasienImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{
    public function index(Request $request): \Illuminate\Contracts\View\View
    {
        $parentId = auth()->user()->parent_id;

        $query = Pasien::latest();

        if (!is_null($parentId)) {
            $query->where('parent_id', $parentId);
        }

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


        return view('pasiens.index', compact('pasiens'));
    }

    public function create(): \Illuminate\Contracts\View\View
    {
        $provinces = Province::all();
        $parentId = auth()->user()->parent_id;
        return view('pasiens.create', compact('provinces', 'parentId'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'nik' => 'string|max:255|unique:pasiens,nik',
            'alamat' => 'string|max:255',
            'jenis_kelamin' => 'string|max:255',
            'jenis_ktp' => 'string|max:255',
            'tanggal_lahir' => 'date',
            'village_id' => 'string|max:255',
            'district_id' => 'string|max:255',
            'regency_id' => 'string|max:255',
            'province_id' => 'string|max:255',
            'no_wa' => 'string|max:255',
            'keterangan' => 'string|max:255',
            'rt' => 'string|max:255',
            'rw' => 'string|max:255',
        ]);

        $parentId = auth()->user()->parent_id;

        $pasien = Pasien::create([
            ...$validated,
            'parent_id' => $parentId,
        ]);
        return redirect()->route('pasiens.index')->with('success', 'Created successfully');
    }

    public function show(Pasien $pasien): \Illuminate\Contracts\View\View
    {
        $kunjungan = Visiting::with(['pasien', 'user', 'healthForms'] )->where('pasien_id', $pasien->id)->get();
        return view('pasiens.show', compact('pasien', 'kunjungan'));
    }

    public function edit(Pasien $pasien): \Illuminate\Contracts\View\View
    {
        $provinces = Province::all();
        $regencies = Regency::all();
        $districts = District::all();
        $villages = Village::all();
        $pasien = $pasien;
        return view('pasiens.edit', compact('pasien', 'provinces', 'regencies', 'districts', 'villages'));
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
        $search = $request->input('q');

        $pasiens = Pasien::with(['village', 'district', 'regency'])
            ->where(function ($query) use ($search) {
                $query->where('nik', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();



        return response()->json(
            $pasiens->map(function ($pasien) {
                return [
                    'id' => $pasien->id,
                    'text' => "{$pasien->name} ({$pasien->nik}) - {$pasien->alamat}," . "{$pasien->village_id}",
                    'fullData' => [
                        'id' => $pasien->id,
                        'name' => $pasien->name,
                        'nik' => $pasien->nik,
                        'alamat' => $pasien->alamat,
                        'rt' => $pasien->rt,
                        'rw' => $pasien->rw,
                        'village_id' => $pasien->village_id,
                        'district_id' => $pasien->district_id,
                        'regency_id' => $pasien->regency_id,
                    ]
                ];
            })
        );
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

    public function importPasien(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new PasienImport, $request->file('file'));

        return back()->with('success', 'Data pasien berhasil diimport!');
    }

    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/template_pasiens.xlsx');

        if (file_exists($filePath)) {
            return Response::download($filePath);
        }

        // Jika file tidak ditemukan
        return redirect()->back()->with('error', 'Template tidak ditemukan.');
    }

    public function searchVillage(Request $request)
    {
        $q = $request->input('q');

        $results = DB::table('villages')
            ->join('districts', 'villages.district_id', '=', 'districts.id')
            ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->join('provinces', 'regencies.province_id', '=', 'provinces.id')
            ->select(
                'villages.id as village_id', 'villages.name as village_name',
                'districts.id as district_id', 'districts.name as district_name',
                'regencies.id as regency_id', 'regencies.name as regency_name',
                'provinces.id as province_id', 'provinces.name as province_name'
            )
            ->where('villages.name', 'LIKE', '%' . $q . '%')
            ->limit(20)
            ->get();
        return response()->json($results);
    }

}
