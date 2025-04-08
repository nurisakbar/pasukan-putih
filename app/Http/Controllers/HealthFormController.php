<?php

namespace App\Http\Controllers;

use App\Models\HealthForm;
use App\Models\Visiting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HealthFormController extends Controller
{
    /**
     * Display the health form.
     *
     * @return \Illuminate\View\View
     */
    public function create(Visiting $visiting)
    {
        return view('visitings.form-kesehatan', compact('visiting'));
    }


    /**
     * Store a newly created health form in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the basic form data
        $validator = Validator::make($request->all(), [
            'cancer_type' => 'nullable|required_if:diseases,cancer',
            'lung_disease_type' => 'nullable|required_if:diseases,lung_disease',
            'kunjungan_lanjutan' => 'nullable|string',
            'permasalahan_lanjutan' => 'nullable|required_if:kunjungan_lanjutan,ya|string',
            'tanggal_kunjungan' => 'nullable|required_if:kunjungan_lanjutan,ya|date',
            'pembinaan' => 'nullable|string',
            'perawatan' => 'nullable|string',
            'keluaran' => 'nullable|integer|between:1,3',
            'keterangan' => 'nullable|string',
            'skor_aks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Start building the health form data
        $formData = [
            'user_id' => Auth::id(),
            'visiting_id' => $request->input('visiting_id'),
            'no_disease' => $request->has('no_disease'),
            'cancer_type' => $request->input('cancer_type'),
            'lung_disease_type' => $request->input('lung_disease_type'),
            'diseases' => $request->input('diseases', []),
            'skor_aks' => $request->input('skor_aks'),
            'perawatan' => $request->input('perawatan'),
            'keluaran' => $request->input('keluaran'),
            'keterangan' => $request->input('keterangan'),
            'pembinaan' => $request->input('pembinaan'),
            'kemandirian' => $request->input('kemandirian', []),
            'kunjungan_lanjutan' => $request->input('kunjungan_lanjutan'),
            'permasalahan_lanjutan' => $request->input('permasalahan_lanjutan'),
            'tanggal_kunjungan' => $request->input('tanggal_kunjungan'),
        ];

        // Process Screening Fields
        $screenings = [
            'obesity', 'hypertension', 'diabetes', 'stroke', 'heart_disease',
            'breast_cancer', 'cervical_cancer', 'lung_cancer', 'colorectal_cancer',
            'mental_health', 'ppok', 'tbc', 'vision', 'hearing', 'fitness',
            'dental', 'elderly'
        ];

        foreach ($screenings as $screening) {
            $screeningField = "screening_{$screening}";
            $statusField = "{$screening}_status";
            
            $formData[$screeningField] = $request->input($screeningField) == 1;
            $formData[$statusField] = $request->input($statusField);
        }

        // Process Gangguan Fungsional Fields
        $gangguans = [
            'gangguan_komunikasi', 'kesulitan_makan', 'gangguan_fungsi_kardiorespirasi',
            'gangguan_fungsi_berkemih', 'gangguan_mobilisasi', 'gangguan_partisipasi'
        ];

        foreach ($gangguans as $gangguan) {
            $formData[$gangguan] = $request->has($gangguan) && $request->input($gangguan) == 1;
        }

        // Process Perawatan Umum Fields
        $perawatanUmum = [
            'hygiene', 'skin_care', 'environment', 'welfare', 'sunlight',
            'communication', 'recreation', 'penamtauan_obat', 'ibadah'
        ];

        foreach ($perawatanUmum as $perawatan) {
            $field = "perawatan_{$perawatan}";
            $formData[$field] = $request->has($field) && $request->input($field) == 1;
        }

        // Process Perawatan Khusus Fields
        $perawatanKhusus = [
            'membantu_warga', 'monitoring_gizi', 'membantu_bak_bab',
            'menangani_gangguan', 'pengelolaan_stres'
        ];

        foreach ($perawatanKhusus as $perawatan) {
            $field = "perawatan_{$perawatan}";
            $formData[$field] = $request->has($field) && $request->input($field) == 1;
        }

        // Create and save the health form
        $healthForm = new HealthForm($formData);
        $healthForm->save();

        return redirect()->route('visitings.index')
            ->with('success', 'Form berhasil disimpan!');
    }

    /**
     * Display the specified health form.
     *
     * @param  \App\Models\HealthForm  $healthForm
     * @return \Illuminate\View\View
     */
    public function show(HealthForm $healthForm)
    {
        $this->authorize('view', $healthForm);
        
        return view('health-form.show', compact('healthForm'));
    }

    /**
     * Show the form for editing the specified health form.
     *
     * @param  \App\Models\HealthForm  $healthForm
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $healthForm = HealthForm::where('visiting_id', $id)->first();
    
        $diseases = [
               'diabetes' => 'Diabetes',
                'hypertension' => 'Hipertensi',
                'heart_disease' => 'Penyakit Jantung',       
                'cancer' => 'Kanker',
                'lung_disease' => 'Penyakit Paru',
            ];

        return view('visitings.edit-form-kesehatan', compact('healthForm'));
    }

    /**
     * Update the specified health form in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HealthForm  $healthForm
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        
        // Validate the basic form data (similar to store method)
        $validator = Validator::make($request->all(), [
            'cancer_type' => 'nullable|required_if:diseases,cancer',
            'lung_disease_type' => 'nullable|required_if:diseases,lung_disease',
            'kunjungan_lanjutan' => 'nullable|string',
            'permasalahan_lanjutan' => 'nullable|required_if:kunjungan_lanjutan,ya|string',
            'tanggal_kunjungan' => 'nullable|required_if:kunjungan_lanjutan,ya|date',
            'pembinaan' => 'nullable|string',
            'perawatan' => 'nullable|string',
            'keluaran' => 'nullable|integer|between:1,3',
            'keterangan' => 'nullable|string',
            'skor_aks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $healthForm = HealthForm::findOrFail($id);

        // Update form data (similar to store method but for updating)
        $formData = [
            'no_disease' => $request->has('no_disease'),
            'cancer_type' => $request->input('cancer_type'),
            'lung_disease_type' => $request->input('lung_disease_type'),
            'diseases' => $request->input('diseases', []),
            'skor_aks' => $request->input('skor_aks'),
            'perawatan' => $request->input('perawatan'),
            'keluaran' => $request->input('keluaran'),
            'keterangan' => $request->input('keterangan'),
            'pembinaan' => $request->input('pembinaan'),
            'kemandirian' => $request->input('kemandirian', []),
            'kunjungan_lanjutan' => $request->input('kunjungan_lanjutan'),
            'permasalahan_lanjutan' => $request->input('permasalahan_lanjutan'),
            'tanggal_kunjungan' => $request->input('tanggal_kunjungan'),
        ];

        // Process Screening Fields
        $screenings = [
            'obesity', 'hypertension', 'diabetes', 'stroke', 'heart_disease',
            'breast_cancer', 'cervical_cancer', 'lung_cancer', 'colorectal_cancer',
            'mental_health', 'ppok', 'tbc', 'vision', 'hearing', 'fitness',
            'dental', 'elderly'
        ];

        foreach ($screenings as $screening) {
            $screeningField = "screening_{$screening}";
            $statusField = "{$screening}_status";
            
            $formData[$screeningField] = $request->has($screeningField) && $request->input($screeningField) == 1;
            $formData[$statusField] = $request->input($statusField);
        }

        // Process Gangguan Fungsional Fields
        $gangguans = [
            'gangguan_komunikasi', 'kesulitan_makan', 'gangguan_fungsi_kardiorespirasi',
            'gangguan_fungsi_berkemih', 'gangguan_mobilisasi', 'gangguan_partisipasi'
        ];

        foreach ($gangguans as $gangguan) {
            $formData[$gangguan] = $request->has($gangguan) && $request->input($gangguan) == 1;
        }

        // Process Perawatan Umum Fields
        $perawatanUmum = [
            'hygiene', 'skin_care', 'environment', 'welfare', 'sunlight',
            'communication', 'recreation', 'penamtauan_obat', 'ibadah'
        ];

        foreach ($perawatanUmum as $perawatan) {
            $field = "perawatan_{$perawatan}";
            $formData[$field] = $request->has($field) && $request->input($field) == 1;
        }

        // Process Perawatan Khusus Fields
        $perawatanKhusus = [
            'membantu_warga', 'monitoring_gizi', 'membantu_bak_bab',
            'menangani_gangguan', 'pengelolaan_stres'
        ];

        foreach ($perawatanKhusus as $perawatan) {
            $field = "perawatan_{$perawatan}";
            $formData[$field] = $request->has($field) && $request->input($field) == 1;
        }

        // Update the health form
        $healthForm->update($formData);

        return redirect()->back()
            ->with('success', 'Form berhasil diperbarui!');
    }

    /**
     * Remove the specified health form from storage.
     *
     * @param  \App\Models\HealthForm  $healthForm
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(HealthForm $healthForm)
    {
        $this->authorize('delete', $healthForm);
        
        $healthForm->delete();
        
        return redirect()->route('health-form.index')
            ->with('success', 'Form berhasil dihapus!');
    }

    /**
     * Display a listing of the health forms.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $healthForms = HealthForm::where('user_id', Auth::id())->paginate(10);
        
        return view('health-form.index', compact('healthForms'));
    }
}