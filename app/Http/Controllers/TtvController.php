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
            'kunjungan_id' => 'required|exists:kunjungans,id',
            'blood_pressure' => 'nullable|string|max:10',
            'pulse' => 'nullable|string|min:0|max:300',
            // 'respiration' => 'nullable|string|min:0|max:100',
            'temperature' => 'nullable|string|between:30,45',
            'oxygen_saturation' => 'nullable|string|min:0|max:100',
            'weight' => 'nullable|string|min:0|max:500',
            'height' => 'nullable|string|min:0|max:300',
            'blood_sugar' => 'nullable|string|min:0',
            'uric_acid' => 'nullable|string|min:0',
            'tcho' => 'nullable|string|min:0',
            'triglyceride' => 'nullable|string|min:0',
            'high_density_protein' => 'nullable|string|min:0',
            'low_density_protein' => 'nullable|string|min:0',
            'hemoglobin' => 'nullable|string|min:0',
            'jaundice' => 'nullable|string',
            'w_waist' => 'nullable|string|min:0',
            'w_bust' => 'nullable|string|min:0',
            'w_hip' => 'nullable|string|min:0',
            'fetal_heart' => 'nullable|string|min:0',
            'ecg' => 'nullable|string',
            'ultrasound' => 'nullable|string',
            'white_corpuscle' => 'nullable|string|min:0',
            'red_corpuscle' => 'nullable|string|min:0',
            'nitrous_acid' => 'nullable|string',
            'ketone_body' => 'nullable|string',
            'urobilinogen' => 'nullable|string',
            'bilirubin' => 'nullable|string',
            'protein' => 'nullable|string',
            'glucose' => 'nullable|string',
            'ph' => 'nullable|string|between:0,14',
            'vitamin_c' => 'nullable|string|min:0',
            'creatinine' => 'nullable|string|min:0',
            'proportion' => 'nullable|string|min:0',
            'albumin' => 'nullable|string|min:0',
            'calcium' => 'nullable|string|min:0',
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'blood_pressure' => 'nullable|string|max:10',
            'pulse' => 'nullable|string|min:0|max:300',
            'temperature' => 'nullable|string|between:30,45',
            'oxygen_saturation' => 'nullable|string|min:0|max:100',
            'weight' => 'nullable|string|min:0|max:500',
            'height' => 'nullable|string|min:0|max:300',
            'blood_sugar' => 'nullable|string|min:0',
            'uric_acid' => 'nullable|string|min:0',
            'tcho' => 'nullable|string|min:0',
            'triglyceride' => 'nullable|string|min:0',
            'high_density_protein' => 'nullable|string|min:0',
            'low_density_protein' => 'nullable|string|min:0',
            'hemoglobin' => 'nullable|string|min:0',
            'jaundice' => 'nullable|string',
            'w_waist' => 'nullable|string|min:0',
            'w_bust' => 'nullable|string|min:0',
            'w_hip' => 'nullable|string|min:0',
            'fetal_heart' => 'nullable|string|min:0',
            'ecg' => 'nullable|string',
            'ultrasound' => 'nullable|string',
            'white_corpuscle' => 'nullable|string|min:0',
            'red_corpuscle' => 'nullable|string|min:0',
            'nitrous_acid' => 'nullable|string',
            'ketone_body' => 'nullable|string',
            'urobilinogen' => 'nullable|string',
            'bilirubin' => 'nullable|string',
            'protein' => 'nullable|string',
            'glucose' => 'nullable|string',
            'ph' => 'nullable|string|between:0,14',
            'vitamin_c' => 'nullable|string|min:0',
            'creatinine' => 'nullable|string|min:0',
            'proportion' => 'nullable|string|min:0',
            'albumin' => 'nullable|string|min:0',
            'calcium' => 'nullable|string|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ttv = Ttv::findOrFail($id);
        $data = $request->all();

        // Calculate BMI if height and weight are provided
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

        $ttv->update($data);

        return redirect()->route('kunjungans.index')->with('success', 'Pemeriksaan kesehatan berhasil diperbarui.');
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