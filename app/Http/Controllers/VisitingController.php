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

class VisitingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Visiting::query()->latest();

        // Filter berdasarkan role user
        if (in_array($user->role, ['perawat', 'caregiver'])) {
            $query->where('user_id', $user->id);
        } else {
            $childUserIds = $this->getAllChildUserIds($user->id, $user->role); // pastikan method ini ada
            $query->whereIn('user_id', $childUserIds);
        }

        // Filter pencarian berdasarkan pasien (nama atau NIK)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        // Filter tanggal
        $tanggalAwal = $request->filled('tanggal_awal') 
            ? Carbon::parse($request->input('tanggal_awal'))->startOfDay()
            : Carbon::today()->startOfDay();

        $tanggalAkhir = $request->filled('tanggal_akhir') 
            ? Carbon::parse($request->input('tanggal_akhir'))->endOfDay()
            : Carbon::today()->endOfDay();

        $query->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir]);

        // Ambil hasil paginasi
        $visitings = $query->paginate(10);

        return view('visitings.index', compact('visitings'));
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
            'name' => 'required|string|max:255',
            'alamat' => 'required|string',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'imt' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if pasien exists, create if not
        $pasien = Pasien::where('nik', $request->nik)->first();
        
        if (!$pasien) {
            $pasien = Pasien::create([
                'nik' => $request->nik,
                'name' => $request->name,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'kabupaten' => $request->kabupaten,
                'village_id' => $request->kelurahan,
                'district_id' => $request->kecamatan,
                'regency_id' => $request->kabupaten,
            ]);
        }

        // Create visiting
        $visiting = Visiting::create([
            'pasien_id' => $pasien->id,
            'user_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'imt' => $request->imt,
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
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'imt' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $visiting->update([
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'imt' => $request->imt,
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
        
        $directChildren = User::where('parent_id', $userId)->pluck('id');
        
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