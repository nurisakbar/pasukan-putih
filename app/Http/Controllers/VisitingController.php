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

        $skriningAdl = SkriningAdl::create([
            'visiting_id' => $visiting->id,
            'pasien_id' => $pasien->id,
            'pemeriksa_id' => auth()->id(),
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

    /**
     * Show the SPA dashboard for the specified visiting.
     */
    public function dashboard($id)
    {
        $visiting = Visiting::with(['pasien', 'ttvs', 'healthForms', 'skriningAdl'])->findOrFail($id);
        
        // Ensure all related records exist
        if (!$visiting->ttvs->count()) {
            Ttv::create([
                'kunjungan_id' => $visiting->id,
            ]);
            $visiting->load('ttvs');
        }

        if (!$visiting->healthForms) {
            HealthForm::create([
                'visiting_id' => $visiting->id,
                'user_id' => auth()->id(),
            ]);
            $visiting->load('healthForms');
        }

        if (!$visiting->skriningAdl) {
            SkriningAdl::create([
                'visiting_id' => $visiting->id,
                'pasien_id' => $visiting->pasien_id,
                'pemeriksa_id' => auth()->id(),
            ]);
            $visiting->load('skriningAdl');
        }

        // Add screenings data for health form
        $screenings = [
            ["id" => "obesity", "label" => "Skrining Obesitas"],
            ["id" => "hypertension", "label" => "Skrining Hipertensi"],
            ["id" => "diabetes", "label" => "Skrining Diabetes Melitus"],
            ["id" => "stroke", "label" => "Skrining Faktor Risiko Stroke"],
            ["id" => "heart_disease", "label" => "Skrining Faktor Risiko Penyakit Jantung"],
            ["id" => "breast_cancer", "label" => "Skrining Kanker Payudara"],
            ["id" => "cervical_cancer", "label" => "Skrining Kanker Leher Rahim"],
            ["id" => "lung_cancer", "label" => "Skrining Kanker Paru"],
            ["id" => "colorectal_cancer", "label" => "Skrining Kanker Kolorektal"],
            ["id" => "mental_health", "label" => "Skrining Kesehatan Jiwa"],
            ["id" => "ppok", "label" => "Skrining Penyakit Paru Obstruktif Kronis (PPOK)"],
            ["id" => "tbc", "label" => "Skrining TBC"],
            ["id" => "vision", "label" => "Skrining Indera Penglihatan/Mata"],
            ["id" => "hearing", "label" => "Skrining Indera Pendengaran"],
            ["id" => "fitness", "label" => "Skrining Kebugaran"],
            ["id" => "dental", "label" => "Skrining Kesehatan Gigi dan Mulut"],
            ["id" => "elderly", "label" => "Skrining Lansia Sederhana (SKILAS)"]
        ];

        return view('visitings.dashboard', compact('visiting', 'screenings'));
    }

    /**
     * Show the skrining ADL form for the specified visiting.
     */
    public function skriningAdl($id)
    {
        $visiting = Visiting::with(['pasien', 'skriningAdl'])->findOrFail($id);
        
        // If skrining ADL doesn't exist, create it
        if (!$visiting->skriningAdl) {
            $skriningAdl = SkriningAdl::create([
                'visiting_id' => $visiting->id,
                'pasien_id' => $visiting->pasien_id,
                'pemeriksa_id' => auth()->id(),
            ]);
            $visiting->load('skriningAdl');
        }

        return view('visitings.skrining-adl', compact('visiting'));
    }

    /**
     * Store skrining ADL data for the specified visiting.
     */
    public function storeSkriningAdl(Request $request, $id)
    {
        $visiting = Visiting::findOrFail($id);
        
        $request->validate([
            'bab_control' => 'nullable|integer|min:0|max:2',
            'bak_control' => 'nullable|integer|min:0|max:2',
            'eating' => 'nullable|integer|min:0|max:2',
            'stairs' => 'nullable|integer|min:0|max:2',
            'bathing' => 'nullable|integer|min:0|max:2',
            'transfer' => 'nullable|integer|min:0|max:2',
            'walking' => 'nullable|integer|min:0|max:2',
            'dressing' => 'nullable|integer|min:0|max:2',
            'grooming' => 'nullable|integer|min:0|max:2',
            'toilet_use' => 'nullable|integer|min:0|max:2',
            'butuh_orang' => 'nullable|string',
            'pendamping_tetap' => 'nullable|string',
            'sasaran_home_service' => 'nullable|string',
        ]);

        // Calculate total score
        $totalScore = 0;
        $fields = ['bab_control', 'bak_control', 'eating', 'stairs', 'bathing', 'transfer', 'walking', 'dressing', 'grooming', 'toilet_use'];
        foreach ($fields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $totalScore += (int)$request->input($field);
            }
        }

        $data = $request->all();
        $data['total_score'] = $totalScore;

        // Update or create skrining ADL
        $skriningAdl = $visiting->skriningAdl;
        if ($skriningAdl) {
            $skriningAdl->update($data);
        } else {
            $data['visiting_id'] = $visiting->id;
            $data['pasien_id'] = $visiting->pasien_id;
            $data['pemeriksa_id'] = auth()->id();
            $skriningAdl = SkriningAdl::create($data);
        }

        return redirect()->route('visitings.index')
            ->with('success', 'Skrining ADL berhasil disimpan.');
    }

    /**
     * Update skrining ADL data for the specified visiting.
     */
    public function updateSkriningAdl(Request $request, $id)
    {
        return $this->storeSkriningAdl($request, $id);
    }

    /**
     * Store Skrining ADL data via AJAX for SPA.
     */
    public function storeSkriningAdlAjax(Request $request, $id)
    {
        $visiting = Visiting::findOrFail($id);
        
        $request->validate([
            'bab_control' => 'nullable|integer|min:0|max:2',
            'bak_control' => 'nullable|integer|min:0|max:2',
            'eating' => 'nullable|integer|min:0|max:2',
            'stairs' => 'nullable|integer|min:0|max:2',
            'bathing' => 'nullable|integer|min:0|max:2',
            'transfer' => 'nullable|integer|min:0|max:2',
            'walking' => 'nullable|integer|min:0|max:2',
            'dressing' => 'nullable|integer|min:0|max:2',
            'grooming' => 'nullable|integer|min:0|max:2',
            'toilet_use' => 'nullable|integer|min:0|max:2',
            'butuh_orang' => 'nullable|string',
            'pendamping_tetap' => 'nullable|string',
            'sasaran_home_service' => 'nullable|string',
        ]);

        // Calculate total score
        $totalScore = 0;
        $fields = ['bab_control', 'bak_control', 'eating', 'stairs', 'bathing', 'transfer', 'walking', 'dressing', 'grooming', 'toilet_use'];
        foreach ($fields as $field) {
            if ($request->has($field) && $request->input($field) !== null) {
                $totalScore += (int)$request->input($field);
            }
        }

        $data = $request->all();
        $data['total_score'] = $totalScore;

        // Update or create skrining ADL
        $skriningAdl = $visiting->skriningAdl;
        if ($skriningAdl) {
            $skriningAdl->update($data);
        } else {
            $data['visiting_id'] = $visiting->id;
            $data['pasien_id'] = $visiting->pasien_id;
            $data['pemeriksa_id'] = auth()->id();
            $skriningAdl = SkriningAdl::create($data);
        }

        return response()->json(['success' => true, 'message' => 'Skrining ADL berhasil disimpan']);
    }

    /**
     * Store TTV data via AJAX for SPA.
     */
    public function storeTtv(Request $request, $id)
    {
        $visiting = Visiting::findOrFail($id);
        
        $request->validate([
            'blood_pressure' => 'nullable|string|max:20',
            'pulse' => 'nullable|integer|min:30|max:200',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'oxygen_saturation' => 'nullable|integer|min:70|max:100',
            'weight' => 'nullable|numeric|min:10|max:300',
            'height' => 'nullable|numeric|min:50|max:250',
            'bmi' => 'nullable|numeric|min:10|max:100',
            'bmi_category' => 'nullable|string|max:50',
        ]);

        $ttv = $visiting->ttvs->first();
        if ($ttv) {
            $ttv->update($request->all());
        } else {
            Ttv::create([
                'kunjungan_id' => $visiting->id,
                ...$request->all()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'TTV berhasil disimpan']);
    }

    /**
     * Store Health Form data via AJAX for SPA.
     */
    public function storeHealthForm(Request $request, $id)
    {
        $visiting = Visiting::findOrFail($id);
        
        $request->validate([
            'no_disease' => 'nullable|boolean',
            'diseases' => 'nullable|array',
            'cancer_type' => 'nullable|string|max:50',
            'lung_disease_type' => 'nullable|string|max:50',
            'skor_aks' => 'nullable|string|in:mandiri,ketergantungan_ringan,ketergantungan_sedang,ketergantungan_berat,ketergantungan_total',
            // Screening fields
            'screening_obesity' => 'nullable|integer|in:0,1',
            'screening_hypertension' => 'nullable|integer|in:0,1',
            'screening_diabetes' => 'nullable|integer|in:0,1',
            'screening_stroke' => 'nullable|integer|in:0,1',
            'screening_heart_disease' => 'nullable|integer|in:0,1',
            'screening_breast_cancer' => 'nullable|integer|in:0,1',
            'screening_cervical_cancer' => 'nullable|integer|in:0,1',
            'screening_lung_cancer' => 'nullable|integer|in:0,1',
            'screening_colorectal_cancer' => 'nullable|integer|in:0,1',
            'screening_mental_health' => 'nullable|integer|in:0,1',
            'screening_ppok' => 'nullable|integer|in:0,1',
            'screening_tbc' => 'nullable|integer|in:0,1',
            'screening_vision' => 'nullable|integer|in:0,1',
            'screening_hearing' => 'nullable|integer|in:0,1',
            'screening_fitness' => 'nullable|integer|in:0,1',
            'screening_dental' => 'nullable|integer|in:0,1',
            'screening_elderly' => 'nullable|integer|in:0,1',
            // Additional fields
            'caregiver_availability' => 'nullable|string|in:selalu,kadang,tidak',
            'non_medical_issues_status' => 'nullable|integer|in:0,1',
            'non_medical_issues_text' => 'nullable|string|max:1000',
            'gangguan_komunikasi' => 'nullable|integer|in:0,1',
            'kesulitan_makan' => 'nullable|integer|in:0,1',
            'gangguan_fungsi_kardiorespirasi' => 'nullable|integer|in:0,1',
            'gangguan_fungsi_berkemih' => 'nullable|integer|in:0,1',
            'gangguan_mobilisasi' => 'nullable|integer|in:0,1',
            'gangguan_partisipasi' => 'nullable|integer|in:0,1',
            'perawatan' => 'nullable|string|max:1000',
            'perawatan_hygiene' => 'nullable|integer|in:0,1',
            'perawatan_skin_care' => 'nullable|integer|in:0,1',
            'perawatan_environment' => 'nullable|integer|in:0,1',
            'perawatan_welfare' => 'nullable|integer|in:0,1',
            'perawatan_sunlight' => 'nullable|integer|in:0,1',
            'perawatan_communication' => 'nullable|integer|in:0,1',
            'perawatan_recreation' => 'nullable|integer|in:0,1',
            'perawatan_penamtauan_obat' => 'nullable|integer|in:0,1',
            'perawatan_ibadah' => 'nullable|integer|in:0,1',
            'perawatan_membantu_warga' => 'nullable|integer|in:0,1',
            'perawatan_monitoring_gizi' => 'nullable|integer|in:0,1',
            'perawatan_membantu_bak_bab' => 'nullable|integer|in:0,1',
            'perawatan_menangani_gangguan' => 'nullable|integer|in:0,1',
            'perawatan_pengelolaan_stres' => 'nullable|integer|in:0,1',
            'keluaran' => 'nullable|integer|in:1,2,3',
            'keterangan' => 'nullable|string|max:500',
            'pembinaan' => 'nullable|string|in:ya,tidak',
            'kemandirian' => 'nullable|array',
            'catatan_keperawatan' => 'nullable|string|max:1000',
            'kunjungan_lanjutan' => 'nullable|string|in:ya,tidak',
            'permasalahan_lanjutan' => 'nullable|string|max:1000',
            'tanggal_kunjungan' => 'nullable|date',
            'henti_layanan' => 'nullable|string|in:kenaikan_nilai_aks,meninggal,menolak,pindah_domisili',
        ]);

        $data = $request->all();
        
        // Handle diseases array
        if ($request->has('diseases')) {
            if (is_array($request->diseases)) {
                $data['diseases'] = json_encode($request->diseases);
            } else {
                $data['diseases'] = json_encode([]);
            }
        } else {
            $data['diseases'] = json_encode([]);
        }

        // Handle kemandirian array
        if ($request->has('kemandirian')) {
            if (is_array($request->kemandirian)) {
                $data['kemandirian'] = json_encode($request->kemandirian);
            } else {
                $data['kemandirian'] = json_encode([]);
            }
        } else {
            $data['kemandirian'] = json_encode([]);
        }

        // Process screening status fields
        $screenings = [
            'obesity', 'hypertension', 'diabetes', 'stroke', 'heart_disease',
            'breast_cancer', 'cervical_cancer', 'lung_cancer', 'colorectal_cancer',
            'mental_health', 'ppok', 'tbc', 'vision', 'hearing', 'fitness',
            'dental', 'elderly'
        ];

        foreach ($screenings as $screening) {
            $screeningField = "screening_{$screening}";
            $statusField = "{$screening}_status";
            
            if ($request->has($screeningField)) {
                $data[$screeningField] = $request->input($screeningField) == 1 ? 1 : 0;
            } else {
                $data[$screeningField] = null;
            }
            if ($request->has($statusField)) {
                $data[$statusField] = $request->input($statusField);
            } else {
                $data[$statusField] = null;
            }
        }

        // Process gangguan fungsional fields
        $gangguans = [
            'gangguan_komunikasi', 'kesulitan_makan', 'gangguan_fungsi_kardiorespirasi',
            'gangguan_fungsi_berkemih', 'gangguan_mobilisasi', 'gangguan_partisipasi'
        ];

        foreach ($gangguans as $gangguan) {
            if ($request->has($gangguan)) {
                $data[$gangguan] = $request->input($gangguan) == 1 ? 1 : 0;
            } else {
                $data[$gangguan] = null;
            }
        }

        // Process perawatan umum fields
        $perawatanUmum = [
            'hygiene', 'skin_care', 'environment', 'welfare', 'sunlight',
            'communication', 'recreation', 'penamtauan_obat', 'ibadah'
        ];

        foreach ($perawatanUmum as $perawatan) {
            $field = "perawatan_{$perawatan}";
            if ($request->has($field)) {
                $data[$field] = $request->input($field) == 1 ? 1 : 0;
            } else {
                $data[$field] = null;
            }
        }

        // Process perawatan khusus fields
        $perawatanKhusus = [
            'membantu_warga', 'monitoring_gizi', 'membantu_bak_bab',
            'menangani_gangguan', 'pengelolaan_stres'
        ];

        foreach ($perawatanKhusus as $perawatan) {
            $field = "perawatan_{$perawatan}";
            if ($request->has($field)) {
                $data[$field] = $request->input($field) == 1 ? 1 : 0;
            } else {
                $data[$field] = null;
            }
        }

        // Log the data being processed
        \Log::info('Health Form Data:', $data);
        
        $healthForm = $visiting->healthForms;
        if ($healthForm) {
            $healthForm->update($data);
            \Log::info('Health Form Updated:', ['id' => $healthForm->id, 'data' => $data]);
        } else {
            $newHealthForm = HealthForm::create([
                'visiting_id' => $visiting->id,
                'user_id' => auth()->id(),
                ...$data
            ]);
            \Log::info('Health Form Created:', ['id' => $newHealthForm->id, 'data' => $data]);
        }

        return response()->json(['success' => true, 'message' => 'Form Kesehatan berhasil disimpan']);
    }
}