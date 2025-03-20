<?php

namespace App\Http\Controllers;

use App\Models\Ttv;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TtvController extends Controller
{
    /**
     * Show the form for creating a new health examination.
     *
     * @param  int  $kunjunganId
     * @return \Illuminate\Http\Response
     */
    public function create(Kunjungan $kunjungan)
    {
        return view('kunjungans.form-ttv', compact('kunjungan'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kunjungan_id' => 'required',
            'temperature' => 'nullable|numeric|between:30,45',
            'blood_pressure' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'bmi' => 'nullable|numeric|min:0',
            'bmi_category' => 'nullable|string',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'pulse' => 'nullable|integer|min:0|max:300',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'blood_sugar' => 'nullable|numeric|min:0',
            'uric_acid' => 'nullable|numeric|min:0',
            'tcho' => 'nullable|numeric|min:0',
            'triglyceride' => 'nullable|numeric|min:0',
            'high_density_protein' => 'nullable|numeric|min:0',
            'low_density_protein' => 'nullable|numeric|min:0',
            'hemoglobin' => 'nullable|numeric|min:0',
            'jaundice' => 'nullable|string',
            'w_waist' => 'nullable|numeric|min:0',
            'w_bust' => 'nullable|numeric|min:0',
            'w_hip' => 'nullable|numeric|min:0',
            'fetal_heart' => 'nullable|integer|min:0',
            'ecg' => 'nullable|string',
            'ultrasound' => 'nullable|string',
            'white_corpuscle' => 'nullable|numeric|min:0',
            'red_corpuscle' => 'nullable|numeric|min:0',
            'nitrous_acid' => 'nullable|string',
            'ketone_body' => 'nullable|string',
            'urobilinogen' => 'nullable|string',
            'bilirubin' => 'nullable|string',
            'protein' => 'nullable|string',
            'glucose' => 'nullable|string',
            'ph' => 'nullable|numeric|between:0,14',
            'vitamin_c' => 'nullable|string|min:0',
            'creatinine' => 'nullable|numeric|min:0',
            'proportion' => 'nullable|string',
            'albumin' => 'nullable|string|min:0',
            'calcium' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Calculate BMI if height and weight are provided
        $data = $request->all();
        if (!empty($data['weight']) && !empty($data['height'])) {
            $weight = $data['weight'];
            $height = $data['height'] / 100; // Convert to meters
            $bmi = $weight / ($height * $height);
            $data['bmi'] = round($bmi, 2);
            
            // Set BMI category
            if ($bmi < 17) {
                $data['bmi_category'] = 'Kurus';
            } elseif ($bmi <= 18.4) {
                $data['bmi_category'] = 'Kurus';
            } elseif ($bmi <= 25) {
                $data['bmi_category'] = 'Normal';
            } else {
                $data['bmi_category'] = 'Gemuk';
            }
        }

        $examination = Ttv::create($data);

        return redirect()->route('kunjungans.index')->with('success', 'Pemeriksaan kesehatan berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $examination = Ttv::with('patient')->findOrFail($id);
        return view('health-examinations.show', compact('examination'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ttv = Ttv::where('kunjungan_id', $id)->first();

        return view('kunjungans.form-edit-ttv', compact('ttv'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kunjungan_id' => 'required',
            'temperature' => 'nullable|numeric|between:30,45',
            'blood_pressure' => 'nullable|string|regex:/^\d{2,3}\/\d{2,3}$/',
            'bmi' => 'nullable|numeric|min:0',
            'bmi_category' => 'nullable|string',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'pulse' => 'nullable|integer|min:0|max:300',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'blood_sugar' => 'nullable|numeric|min:0',
            'uric_acid' => 'nullable|numeric|min:0',
            'tcho' => 'nullable|numeric|min:0',
            'triglyceride' => 'nullable|numeric|min:0',
            'high_density_protein' => 'nullable|numeric|min:0',
            'low_density_protein' => 'nullable|numeric|min:0',
            'hemoglobin' => 'nullable|numeric|min:0',
            'jaundice' => 'nullable|string',
            'w_waist' => 'nullable|numeric|min:0',
            'w_bust' => 'nullable|numeric|min:0',
            'w_hip' => 'nullable|numeric|min:0',
            'fetal_heart' => 'nullable|integer|min:0',
            'ecg' => 'nullable|string',
            'ultrasound' => 'nullable|string',
            'white_corpuscle' => 'nullable|numeric|min:0',
            'red_corpuscle' => 'nullable|numeric|min:0',
            'nitrous_acid' => 'nullable|string',
            'ketone_body' => 'nullable|string',
            'urobilinogen' => 'nullable|string',
            'bilirubin' => 'nullable|string',
            'protein' => 'nullable|string',
            'glucose' => 'nullable|string',
            'ph' => 'nullable|numeric|between:0,14',
            'vitamin_c' => 'nullable|string|min:0',
            'creatinine' => 'nullable|numeric|min:0',
            'proportion' => 'nullable|string',
            'albumin' => 'nullable|string|min:0',
            'calcium' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali input Anda.');
        }

        $ttv = Ttv::find($id);
        if (!$ttv) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
        $data = $request->only([
            'kunjungan_id', 'temperature', 'blood_pressure', 'bmi', 'bmi_category',
            'height', 'weight', 'pulse', 'oxygen_saturation', 'blood_sugar', 'uric_acid',
            'tcho', 'triglyceride', 'high_density_protein', 'low_density_protein',
            'hemoglobin', 'jaundice', 'w_waist', 'w_bust', 'w_hip', 'fetal_heart',
            'ecg', 'ultrasound', 'white_corpuscle', 'red_corpuscle', 'nitrous_acid',
            'ketone_body', 'urobilinogen', 'bilirubin', 'protein', 'glucose', 'ph',
            'vitamin_c', 'creatinine', 'proportion', 'albumin', 'calcium'
        ]);

        if (!empty($data['weight']) && !empty($data['height']) && $data['height'] > 0) {
            $weight = (float) $data['weight'];
            $height = (float) $data['height'] / 100; // Convert cm ke meter
            $bmi = $weight / ($height * $height);
            $data['bmi'] = round($bmi, 2);
            
            // Klasifikasi BMI
            if ($bmi < 17) {
                $data['bmi_category'] = 'Kurus';
            } elseif ($bmi <= 18.4) {
                $data['bmi_category'] = 'Kurus';
            } elseif ($bmi <= 25) {
                $data['bmi_category'] = 'Normal';
            } else {
                $data['bmi_category'] = 'Gemuk';
            }
        }

        // dd($data);
        $ttv->update($data);

        if ($ttv->update($data)) {
            return redirect()->route('kunjungans.index')->with('success', 'Pemeriksaan kesehatan berhasil diperbarui.');
        } else {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $examination = Ttv::findOrFail($id);
        $kunjunganId = $examination->kunjungan_id;
        $examination->delete();

        return redirect()->route('kunjungans.index')->with('success', 'Pemeriksaan kesehatan berhasil dihapus.');
    }

    /**
     * API endpoint to calculate BMI
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function calculateBmi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'weight' => 'required|string|min:0|max:500',
            'height' => 'required|string|min:0|max:300',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $weight = $request->input('weight');
        $height = $request->input('height') / 100; // Convert to meters
        $bmi = $weight / ($height * $height);
        
        $category = '';
        if ($bmi < 17) {
            $category = 'Kurus';
        } elseif ($bmi <= 18.4) {
            $category = 'Kurus';
        } elseif ($bmi <= 25) {
            $category = 'Normal';
        } else {
            $category = 'Gemuk';
        }

        return response()->json([
            'success' => true,
            'bmi' => round($bmi, 2),
            'category' => $category
        ]);
    }
}