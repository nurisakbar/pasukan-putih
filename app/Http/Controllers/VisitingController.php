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
use App\Exports\VisitingExport;
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
            ->leftJoin('users as operators', 'operators.id', '=', 'visitings.operator_id')
            ->whereNull('pasiens.deleted_at')
            ->select(
                'visitings.*',
                'pasiens.name as pasien_name',
                'pasiens.alamat as pasien_alamat',
                'pasiens.rt as pasien_rt',
                'pasiens.rw as pasien_rw',
                'villages.name as village_name',
                'districts.name as district_name',
                'regencies.name as regency_name',
                'operators.name as operator_name'
            );
    
        // Filter berdasarkan role
        if ($user->role === 'perawat' || $user->role === 'operator') {
            if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $user->pustu->district_id;

                // Ambil semua pasien dari district ini
                $pasienIds = DB::table('pasiens')
                    ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                    ->where('villages.district_id', $districtId)
                    ->pluck('pasiens.id');

                $query->whereIn('visitings.pasien_id', $pasienIds);
            } else {
                // Perawat/Operator non-puskesmas: kunjungan yang dibuat oleh user ATAU ditugaskan ke user sebagai operator
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
            $item->operator = $item->operator_name ? (object) [
                'name' => $item->operator_name
            ] : null;
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
            return redirect()->route('visitings.create')->with('error', 'Pasien tidak ditemukan.');
        }

        // Cek apakah pasien sudah memiliki kunjungan di tanggal yang sama
        $existingVisit = Visiting::where('pasien_id', $pasien->id)
                        ->whereDate('tanggal', $request->tanggal)
                        ->first();

        if ($existingVisit) {
            return redirect()->route('visitings.create')->with('error', 'Pasien sudah memiliki kunjungan pada hari yang sama.');
        }

        // Validasi untuk kunjungan awal
        if ($request->status === 'Kunjungan Awal') {
            // Cek apakah pasien sudah pernah melakukan kunjungan awal
            $hasInitialVisit = Visiting::where('pasien_id', $pasien->id)
                ->where('status', 'Kunjungan Awal')
                ->exists();

            if ($hasInitialVisit) {
                return redirect()->route('visitings.create')->with('error', 'Pasien sudah pernah melakukan kunjungan awal. Tidak dapat melakukan kunjungan awal lagi. Gunakan kunjungan lanjutan.');
            }
        }

        // Validasi untuk kunjungan lanjutan
        if ($request->status === 'Kunjungan Lanjutan') {
            // Cek apakah pasien sudah pernah melakukan kunjungan awal
            $hasInitialVisit = Visiting::where('pasien_id', $pasien->id)
                ->where('status', 'Kunjungan Awal')
                ->exists();

            if (!$hasInitialVisit) {
                return redirect()->route('visitings.create')->with('error', 'Pasien belum pernah melakukan kunjungan awal. Tidak dapat melakukan kunjungan lanjutan.');
            }

            // Cek apakah pasien sudah henti layanan
            $hasStoppedService = HealthForm::whereHas('visiting', function ($q) use ($pasien) {
                $q->where('pasien_id', $pasien->id);
            })->whereNotNull('henti_layanan')->exists();

            if ($hasStoppedService) {
                return redirect()->route('visitings.create')->with('error', 'Pasien sudah henti layanan. Tidak dapat melakukan kunjungan lanjutan.');
            }
        }

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'status' => 'required|in:Kunjungan Awal,Kunjungan Lanjutan',
            'nik' => 'required|string',
            'operator_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('visitings.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Check if pasien exists, create if not
        $pasien = Pasien::where('id', $request->pasien_id)->first();

        // Create visiting
        $visiting = Visiting::create([
            'pasien_id' => $pasien->id,
            'user_id' => auth()->id(),
            'operator_id' => $request->operator_id,
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
        $visiting->load('operator');
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
            'operator_id' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('visitings.edit', $visiting->id)
                ->withErrors($validator)
                ->withInput();
        }

        $visiting->update([
            'tanggal' => $request->tanggal,
            'status' => $request->status,
            'operator_id' => $request->operator_id,
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

        // Build visit history for the same patient with ADL total scores
        $visitHistory = Visiting::with(['skriningAdl'])
            ->where('pasien_id', $visiting->pasien_id)
            ->where('id', '!=', $visiting->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('visitings.dashboard', compact('visiting', 'screenings', 'visitHistory'));
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
        
        try {
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
                Ttv::create(array_merge(
                    ['kunjungan_id' => $visiting->id],
                    $request->all()
                ));
            }

            return response()->json(['success' => true, 'message' => 'TTV berhasil disimpan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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
            // SKILAS fields - Simple checkbox (Ya/Tidak)
            'skilas_kognitif' => 'nullable|integer|in:0,1',
            'skilas_mobilisasi' => 'nullable|integer|in:0,1',
            'skilas_malnutrisi_berat_badan' => 'nullable|integer|in:0,1',
            'skilas_malnutrisi_nafsu_makan' => 'nullable|integer|in:0,1',
            'skilas_malnutrisi_lila' => 'nullable|integer|in:0,1',
            'skilas_penglihatan' => 'nullable|integer|in:0,1',
            'skilas_penglihatan_keterangan' => 'nullable|string|max:1000',
            'skilas_pendengaran' => 'nullable|integer|in:0,1',
            'skilas_depresi_sedih' => 'nullable|integer|in:0,1',
            'skilas_depresi_minat' => 'nullable|integer|in:0,1',
            'skilas_rujukan' => 'nullable|integer|in:0,1',
            'skilas_rujukan_keterangan' => 'nullable|string|max:1000',
            'skilas_hasil_tindakan_keperawatan' => 'nullable|string|max:2000',
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
            'dilakukan_oleh' => 'nullable|array',
            'dilakukan_oleh.*' => 'nullable|string|in:perawat,petugas_layanan_kesehatan',
            'operator_id_lanjutan' => 'nullable|exists:users,id',
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

        // Handle dilakukan_oleh array
        if ($request->has('dilakukan_oleh')) {
            if (is_array($request->dilakukan_oleh)) {
                $data['dilakukan_oleh'] = $request->dilakukan_oleh;
            } else {
                $data['dilakukan_oleh'] = [];
            }
        } else {
            $data['dilakukan_oleh'] = [];
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

        // Process SKILAS fields - Simple checkboxes
        $skilasFields = [
            'skilas_kognitif',
            'skilas_mobilisasi',
            'skilas_malnutrisi_berat_badan',
            'skilas_malnutrisi_nafsu_makan',
            'skilas_malnutrisi_lila',
            'skilas_penglihatan',
            'skilas_pendengaran',
            'skilas_depresi_sedih',
            'skilas_depresi_minat',
        ];

        foreach ($skilasFields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->input($field) == 1 ? 1 : 0;
            } else {
                $data[$field] = null;
            }
        }
        
        // Process SKILAS keterangan fields
        if ($request->has('skilas_penglihatan_keterangan')) {
            $data['skilas_penglihatan_keterangan'] = $request->input('skilas_penglihatan_keterangan');
        } else {
            $data['skilas_penglihatan_keterangan'] = null;
        }
        
        // Process SKILAS rujukan
        if ($request->has('skilas_rujukan')) {
            $data['skilas_rujukan'] = $request->input('skilas_rujukan') == 1 ? 1 : 0;
        } else {
            $data['skilas_rujukan'] = null;
        }
        
        if ($request->has('skilas_rujukan_keterangan')) {
            $data['skilas_rujukan_keterangan'] = $request->input('skilas_rujukan_keterangan');
        } else {
            $data['skilas_rujukan_keterangan'] = null;
        }
        
        // Process SKILAS hasil tindakan keperawatan
        if ($request->has('skilas_hasil_tindakan_keperawatan')) {
            $data['skilas_hasil_tindakan_keperawatan'] = $request->input('skilas_hasil_tindakan_keperawatan');
        } else {
            $data['skilas_hasil_tindakan_keperawatan'] = null;
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
            $healthForm = $newHealthForm;
            \Log::info('Health Form Created:', ['id' => $newHealthForm->id, 'data' => $data]);
        }

        // Auto-create visiting jika kunjungan lanjutan = "ya"
        $newVisitingCreated = false;
        if ($request->kunjungan_lanjutan === 'ya' && $request->tanggal_kunjungan) {
            $newVisitingCreated = $this->autoCreateFollowUpVisiting($visiting, $healthForm, $request);
        }

        $message = 'Form Kesehatan berhasil disimpan';
        if ($newVisitingCreated) {
            $message .= ' dan Kunjungan Lanjutan berhasil dibuat untuk tanggal ' . 
                        \Carbon\Carbon::parse($request->tanggal_kunjungan)->format('d/m/Y');
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Auto-create visiting untuk kunjungan lanjutan
     */
    private function autoCreateFollowUpVisiting($currentVisiting, $healthForm, $request)
    {
        try {
            // Cek apakah sudah ada visiting dengan tanggal yang sama
            $existingVisiting = Visiting::where('pasien_id', $currentVisiting->pasien_id)
                ->whereDate('tanggal', $request->tanggal_kunjungan)
                ->first();

            if ($existingVisiting) {
                \Log::info('Visiting sudah ada untuk tanggal ini:', [
                    'pasien_id' => $currentVisiting->pasien_id,
                    'tanggal' => $request->tanggal_kunjungan
                ]);
                return false;
            }

            // Tentukan user_id dan operator_id berdasarkan dilakukan_oleh
            $userId = auth()->id();
            $operatorId = null;
            
            $dilakukanOleh = $request->dilakukan_oleh ?? [];
            
            // Logic penentuan operator_id:
            // 1. Jika hanya perawat → user_id = auth()->id(), operator_id = null (perawat handle sendiri)
            // 2. Jika hanya petugas → user_id = auth()->id(), operator_id = dari dropdown (operator yang dipilih)
            // 3. Jika kedua checkbox dipilih → user_id = auth()->id(), operator_id = dari dropdown (kolaborasi perawat & operator)
            
            if (is_array($dilakukanOleh)) {
                $perawatDipilih = in_array('perawat', $dilakukanOleh);
                $petugasDipilih = in_array('petugas_layanan_kesehatan', $dilakukanOleh);
                
                if ($petugasDipilih) {
                    // Jika petugas dipilih (baik sendiri atau dengan perawat) → gunakan operator_id_lanjutan dari dropdown
                    $operatorId = $request->operator_id_lanjutan ?? null;
                }
                // Jika hanya perawat dipilih, operator_id tetap null
            }

            // Create visiting baru
            $newVisiting = Visiting::create([
                'pasien_id' => $currentVisiting->pasien_id,
                'user_id' => $userId,
                'operator_id' => $operatorId,
                'tanggal' => $request->tanggal_kunjungan,
                'status' => 'Kunjungan Lanjutan',
            ]);

            // Create related records untuk visiting baru
            Ttv::create([
                'kunjungan_id' => $newVisiting->id,
            ]);

            HealthForm::create([
                'visiting_id' => $newVisiting->id,
                'user_id' => auth()->id(),
            ]);

            SkriningAdl::create([
                'visiting_id' => $newVisiting->id,
                'pasien_id' => $currentVisiting->pasien_id,
                'pemeriksa_id' => auth()->id(),
            ]);

            \Log::info('Auto-created follow-up visiting:', [
                'new_visiting_id' => $newVisiting->id,
                'tanggal' => $newVisiting->tanggal,
                'status' => $newVisiting->status
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error auto-creating follow-up visiting:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Return all scheduled follow-up dates (health_forms.tanggal_kunjungan) for a pasien.
     */
    public function getScheduledDates($pasienId)
    {
        // Dates from health forms related to visitings of this pasien
        $dates = HealthForm::query()
            ->select('tanggal_kunjungan')
            ->whereNotNull('tanggal_kunjungan')
            ->whereHas('visiting', function ($q) use ($pasienId) {
                $q->where('pasien_id', $pasienId);
            })
            ->orderBy('tanggal_kunjungan', 'asc')
            ->pluck('tanggal_kunjungan')
            ->map(function ($date) {
                return optional($date)->format('Y-m-d');
            })
            ->filter()
            ->values();

        return response()->json(['dates' => $dates]);
    }

    /**
     * Return count of scheduled patients per date (untuk kalender)
     */
    public function getScheduledCounts(Request $request)
    {
        $user = auth()->user();
        
        // Ambil tanggal awal dan akhir (default 6 bulan ke depan)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->addMonths(6)->endOfMonth()->format('Y-m-d'));

        // Query untuk menghitung jumlah visiting per tanggal
        $query = Visiting::whereBetween('tanggal', [$startDate, $endDate]);
        
        // Filter berdasarkan role user (sama seperti di index)
        if ($user->role === 'perawat' || $user->role === 'operator') {
            if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $user->pustu->district_id;
                $pasienIds = DB::table('pasiens')
                    ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                    ->where('villages.district_id', $districtId)
                    ->pluck('pasiens.id');
                $query->whereIn('pasien_id', $pasienIds);
            } else {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('operator_id', $user->id);
                });
            }
        } elseif ($user->role === 'sudinkes') {
            $pasienIds = DB::table('pasiens')
                ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
                ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->where('regencies.id', $user->regency_id)
                ->pluck('pasiens.id');
            $query->whereIn('pasien_id', $pasienIds);
        }

        // Group by tanggal dan hitung jumlahnya
        $counts = $query->select(
                DB::raw('DATE(tanggal) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        return response()->json($counts);
    }

    /**
     * Return detailed list of scheduled patients for a specific date
     */
    public function getScheduledPatients(Request $request)
    {
        $user = auth()->user();
        $date = $request->input('date');
        
        if (!$date) {
            return response()->json(['success' => false, 'message' => 'Tanggal tidak valid']);
        }

        // Query untuk mengambil detail pasien yang terjadwal di tanggal tertentu
        $query = Visiting::with(['pasien', 'user'])
            ->whereDate('tanggal', $date);
        
        // Filter berdasarkan role user (sama seperti di index)
        if ($user->role === 'perawat' || $user->role === 'operator') {
            if ($user->pustu && $user->pustu->jenis_faskes === 'puskesmas') {
                $districtId = $user->pustu->district_id;
                $pasienIds = DB::table('pasiens')
                    ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                    ->where('villages.district_id', $districtId)
                    ->pluck('pasiens.id');
                $query->whereIn('pasien_id', $pasienIds);
            } else {
                $query->where(function($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhere('operator_id', $user->id);
                });
            }
        } elseif ($user->role === 'sudinkes') {
            $pasienIds = DB::table('pasiens')
                ->leftJoin('villages', 'pasiens.village_id', '=', 'villages.id')
                ->leftJoin('districts', 'villages.district_id', '=', 'districts.id')
                ->leftJoin('regencies', 'districts.regency_id', '=', 'regencies.id')
                ->where('regencies.id', $user->regency_id)
                ->pluck('pasiens.id');
            $query->whereIn('pasien_id', $pasienIds);
        }

        $visitings = $query->orderBy('created_at', 'asc')->get();

        // Format data untuk response
        $patients = $visitings->map(function ($visiting) {
            return [
                'id' => $visiting->pasien->id,
                'name' => $visiting->pasien->name,
                'nik' => $visiting->pasien->nik,
                'alamat' => $visiting->pasien->alamat,
                'status' => $visiting->status,
                'created_at' => $visiting->created_at->format('H:i'),
                'diperiksa_oleh' => $visiting->user ? $visiting->user->name : 'Tidak diketahui',
            ];
        });

        return response()->json([
            'success' => true,
            'patients' => $patients,
            'count' => $patients->count()
        ]);
    }

    /**
     * Export visiting data to Excel
     */
    public function export(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'tanggal_awal' => $request->input('tanggal_awal'),
            'tanggal_akhir' => $request->input('tanggal_akhir'),
        ];

        $filename = 'kunjungan_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new VisitingExport($filters), $filename);
    }
}