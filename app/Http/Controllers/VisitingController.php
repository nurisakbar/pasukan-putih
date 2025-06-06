<?php

namespace App\Http\Controllers;

use App\Models\Visiting;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Ttv;
use App\Models\SkriningAdl;
use App\Models\HealthForm;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KunjunganExport;
use App\Models\Province;
use Carbon\Carbon;
use DB;

class VisitingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
    
        $query = DB::table('visitings')
            ->leftJoin('pasiens', 'pasiens.id', '=', 'visitings.pasien_id')
            ->leftJoin('villages', 'villages.id', '=', 'pasiens.village_id')
            ->leftJoin('districts', 'districts.id', '=', 'villages.district_id')
            ->leftJoin('regencies', 'regencies.id', '=', 'districts.regency_id')
            ->leftJoin('users', 'users.id', '=', 'visitings.user_id')
            ->whereNull('pasiens.deleted_at')
            ->select(
                'visitings.*',
                'pasiens.name as pasien_name',
                'pasiens.alamat as pasien_alamat',
                'pasiens.rt as pasien_rt',
                'pasiens.rw as pasien_rw',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name'
            );
    
        // Filter berdasarkan role
        if ($user->role === 'perawat') {
            if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $user->pustu->district_id;

                // Ambil semua pasien dari district ini
                $pasienIds = DB::table('pasiens')
                    ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                    ->where('villages.district_id', $districtId)
                    ->pluck('pasiens.id');

                $query->whereIn('visitings.pasien_id', $pasienIds);
            } else {
                // Perawat non-puskesmas: hanya kunjungan milik dia sendiri
                $query->where('visitings.user_id', $user->id);
            }
        } elseif ($user->role === 'sudinkes') {
            $pasienIds = DB::table('pasiens')
            ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
            ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
            ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
            ->where('regencies.id', $user->regency_id)
            ->pluck('pasiens.id');
        
            $query->whereIn('visitings.pasien_id', $pasienIds);
        } else {
            // For other roles (like superadmin) no additional filter
        }
    
        // Filter pencarian nama / nik pasien
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('pasiens.name', 'like', "%$search%")
                  ->orWhere('pasiens.nik', 'like', "%$search%");
            });
        }
    
        // Filter tanggal
        $tanggalAwal = $request->filled('tanggal_awal') 
            ? Carbon::parse($request->input('tanggal_awal'))->startOfDay()
            : Carbon::today()->startOfDay();
    
        $tanggalAkhir = $request->filled('tanggal_akhir') 
            ? Carbon::parse($request->input('tanggal_akhir'))->endOfDay()
            : Carbon::today()->endOfDay();
    
        $query->whereBetween('visitings.tanggal', [$tanggalAwal, $tanggalAkhir]);
        
        // Ambil semua data untuk DataTables
        $visitingsRaw = $query->orderBy('visitings.created_at', 'desc')->get();
    
        // Map ke objek mirip model agar view tidak error
        $visitings = $visitingsRaw->map(function ($item) {
            $item = (object) $item;
            $item->pasien = (object) [
                'name' => $item->pasien_name ?? 'Data tidak tersedia',
                'alamat' => $item->pasien_alamat ?? 'Alamat tidak tersedia',
                'rt' => $item->pasien_rt ?? '-',
                'rw' => $item->pasien_rw ?? '-',
                'village' => (object) [
                    'name' => $item->village_name ?? 'Data tidak tersedia',
                    'district' => (object) [
                        'name' => $item->district_name ?? 'Data tidak tersedia',
                        'regency' => (object) [
                            'name' => $item->regency_name ?? 'Data tidak tersedia',
                        ]
                    ]
                ]
            ];
            return $item;
        });
    
        // Debug information
        $totalRecords = $visitings->count();
    
        return view('visitings.index', compact('visitings', 'totalRecords'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('visitings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pasien = Pasien::find($request->pasien_id);

        if (!$pasien) {
            return redirect()->back()->with('error', 'Pasien tidak ditemukan.');
        }

        // Cek apakah pasien sudah memiliki kunjungan di tanggal yang sama
        $existingVisit = Visiting::where('pasien_id', $pasien->id)
                        ->whereDate('tanggal', $request->tanggal)
                        ->first();

        if ($existingVisit) {
            return redirect()->back()->with('error', 'Pasien sudah memiliki kunjungan pada hari yang sama.');
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'status' => 'required|in:Kunjungan Awal,Kunjungan Lanjutan',
            'nik' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if pasien exists, create if not
        $pasien = Pasien::where('id', $request->pasien_id)->first();

        // Create visiting
        $visiting = Visiting::create([
            'pasien_id' => $pasien->id,
            'user_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ]);

        $update_pustu_id = Pasien::where('id', $request->pasien_id)
        ->where('user_id', '=', '-')
        ->whereNull('pustu_id') 
        ->update([
            'pustu_id' => auth()->user()->pustu_id,
            'user_id' => auth()->user()->id
        ]);

        $ttv = Ttv::create([
            'kunjungan_id' => $visiting->id,
        ]);

        $healthForm = HealthForm::create([
            'visiting_id' => $visiting->id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('visitings.index')
        ->with('success', 'Kunjungan berhasil ditambahkan.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Visiting $visiting)
    {
        return view('visitings.show', compact('visiting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visiting $visiting)
    {
        return view('visitings.edit', compact('visiting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Visiting $visiting)
    {
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'status' => 'required|in:Kunjungan Awal,Kunjungan Lanjutan',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $visiting->update([
            'tanggal' => $request->tanggal,
            'status' => $request->status,
        ]);

        return redirect()->route('visitings.index')
            ->with('success', 'Kunjungan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visiting $visiting)
    {
        $visiting->delete();

        return redirect()->route('visitings.index')
            ->with('success', 'Kunjungan berhasil dihapus.');
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
        $visiting = Visiting::findOrFail($id);
        return view('visitings.edit-form-pasien', compact('visiting'));
    }
}