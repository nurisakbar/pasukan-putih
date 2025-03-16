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
            'pulse' => 'nullable|integer|min:0|max:300',
            'respiration' => 'nullable|integer|min:0|max:100',
            'temperature' => 'nullable|numeric|between:30,45',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'knee_height' => 'nullable|numeric|min:0|max:100',
            'sitting_height' => 'nullable|numeric|min:0|max:200',
            'arm_span' => 'nullable|numeric|min:0|max:300',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $examination = Ttv::create($request->all());

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
            'blood_pressure' => 'nullable|string|max:10',
            'pulse' => 'nullable|integer|min:0|max:300',
            'respiration' => 'nullable|integer|min:0|max:100',
            'temperature' => 'nullable|numeric|between:30,45',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'weight' => 'nullable|numeric|min:0|max:500',
            'height' => 'nullable|numeric|min:0|max:300',
            'knee_height' => 'nullable|numeric|min:0|max:100',
            'sitting_height' => 'nullable|numeric|min:0|max:200',
            'arm_span' => 'nullable|numeric|min:0|max:300',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ttv = Ttv::findOrFail($id);
        $ttv->update($request->all());

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
            'weight' => 'required|numeric|min:0|max:500',
            'height' => 'required|numeric|min:0|max:300',
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