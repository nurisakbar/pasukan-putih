<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OphLog;
use App\Models\Ttv;
use App\Models\Kunjungan;

class OphLogController extends Controller
{
    public function index()
    {
        $logs = OphLog::all();
        $logs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'data' => unserialize($log->data), 
                'created_at' => $log->created_at,
            ];
        });

        return response()->json([
            'message' => 'Data retrieved successfully',
            'logs' => $logs
        ]);
    }

    public function store(Request $request)
    {
        
        $data = $request->json()->all();

        $nik = $data['nik'];

        $kunjunganIds = Kunjungan::whereHas('pasien', function ($query) use ($nik) {
            $query->where('nik', $nik);
        })->pluck('id');

        if ($kunjunganIds->isEmpty()) {
            return response()->json([
                'message' => 'No kunjungan found for the given NIK'
            ], 404);
        }

        $ttv = Ttv::where('kunjungan_id', $kunjunganIds)->first();
 
        if (!$ttv) {
            $ttv = Ttv::create([
                'kunjungan_id' => $kunjunganIds->first() 
            ]);
        }

        foreach ($data['examinations'] as $examination) {
            $examinationName = strtoupper($examination['examination_name']);  
        
            switch ($examinationName) {
                case 'BODY TEMPERATURE':
                    $ttv->temperature = $examination['result'];
                    break;
                case 'BLOOD PRESSURE':
                    $ttv->blood_pressure = $examination['result'];
                    break;
                case 'BMI':
                    $ttv->bmi = $examination['result'];
                    break;
                case 'PULSE':
                    $ttv->pulse = $examination['result'];
                    break;
                case 'RESPIRATION':
                    $ttv->respiration = $examination['result'];
                    break;
                case 'OXYGEN SATURATION':
                    $ttv->oxygen_saturation = $examination['result'];
                    break;
                case 'WEIGHT':
                    $ttv->weight = $examination['result'];
                    break;
                case 'HEIGHT':
                    $ttv->height = $examination['result'];
                    break;
                case 'KNEE HEIGHT':
                    $ttv->knee_height = $examination['result'];
                    break;
                case 'SITTING HEIGHT':
                    $ttv->sitting_height = $examination['result'];
                    break;
                case 'ARM SPAN':
                    $ttv->arm_span = $examination['result'];
                    break;
                case 'BMI CATEGORY':
                    $ttv->bmi_category = $examination['result'];
                    break;
            }
        }

        $ttv->save();

        return response()->json([
            'message' => 'TTV updated successfully',
            'ttv' => $ttv
        ]);
    }
}
