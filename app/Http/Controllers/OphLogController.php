<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OphLog;
use App\Models\Ttv;
use App\Models\Visiting;
use Carbon\Carbon;

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


        $tanggal = Carbon::parse($data['date'])->toDateString();

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            return response()->json([
                'message' => 'Invalid date format. Use YYYY-MM-DD.'
            ], 400);
        }
        try {
            $tanggal = Carbon::parse($tanggal)->toDateString();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Invalid date value.'
            ], 400);
        }

        $kunjunganIds = Visiting::where('tanggal',$tanggal)
        ->whereHas('pasien', function ($query) use ($nik) {
            $query->where('nik', $nik);
        })->pluck('id');

        if ($kunjunganIds->isEmpty()) {
            return response()->json([
                'message' => 'No kunjungan found for the given NIK'
            ], 404);
        }

        $examination = Ttv::where('kunjungan_id', $kunjunganIds)->first();

        //return $examination;

        if (!$examination) {
            return response()->json([
                'message' => 'No TTV found for the given kunjungan ID'
            ], 404);
        }

        foreach ($data['examinations'] as $examinationData) {
            $examinationName = strtoupper($examinationData['examination_name']);

            switch ($examinationName) {
                case 'SYSTOLE_MMHG':
                    $systole = $examinationData['result'];
                    break;
                case 'SLACK_MMHG':
                    $slack = $examinationData['result'];
                    break;
                case 'TEMPERATURE':
                    $examination->temperature = $examinationData['result'];
                    break;
                case 'BLOOD_PRESSURE':
                    if ($systole !== null && $slack !== null) {
                        $examination->blood_pressure = $systole . '/' . $slack;
                    }
                    break;
                case 'BMI':
                    $examination->bmi = $examinationData['result'];
                    break;
                case 'BMI_CATEGORY':
                    $examination->bmi_category = $examinationData['result'];
                    break;
                case 'HEIGHT':
                    $examination->height = $examinationData['result'];
                    break;
                case 'WEIGHT':
                    $examination->weight = $examinationData['result'];
                    break;
                case 'PULSE_RATE':
                    $examination->pulse = $examinationData['result'];
                    break;
                case 'BLOOD_OXYGEN':
                    $examination->oxygen_saturation = $examinationData['result'];
                    break;
                case 'BLOOD_SUGAR':
                    $examination->blood_sugar = $examinationData['result'];
                    break;
                case 'URIC_ACID':
                    $examination->uric_acid = $examinationData['result'];
                    break;
                case 'TCHO':
                    $examination->tcho = $examinationData['result'];
                    break;
                case 'TRIGLYCERIDE':
                    $examination->triglyceride = $examinationData['result'];
                    break;
                case 'HIGH_DENSITY_PROTEIN':
                    $examination->high_density_protein = $examinationData['result'];
                    break;
                case 'LOW_DENSITY_PROTEIN':
                    $examination->low_density_protein = $examinationData['result'];
                    break;
                case 'HEMOGLOBIN':
                    $examination->hemoglobin = $examinationData['result'];
                    break;
                case 'JAUNDICE':
                    $examination->jaundice = $examinationData['result'];
                    break;
                case 'W_WAIST':
                    $examination->w_waist = $examinationData['result'];
                    break;
                case 'W_BUST':
                    $examination->w_bust = $examinationData['result'];
                    break;
                case 'W_HIP':
                    $examination->w_hip = $examinationData['result'];
                    break;
                case 'FETAL_HEART':
                    $examination->fetal_heart = $examinationData['result'];
                    break;
                case 'ECG':
                    $examination->ecg = $examinationData['result'];
                    break;
                case 'ULTRASONIC':
                    $examination->ultrasound = $examinationData['result'];
                    break;
                case 'WHITE_CORPUSCLE':
                    $examination->white_corpuscle = $examinationData['result'];
                    break;
                case 'RED_CORPUSCLE':
                    $examination->red_corpuscle = $examinationData['result'];
                    break;
                case 'NITROUS_ACID':
                    $examination->nitrous_acid = $examinationData['result'];
                    break;
                case 'KETONE_BODY':
                    $examination->ketone_body = $examinationData['result'];
                    break;
                case 'UROBILINOGEN':
                    $examination->urobilinogen = $examinationData['result'];
                    break;
                case 'BILIRUBIN':
                    $examination->bilirubin = $examinationData['result'];
                    break;
                case 'PROTEIN':
                    $examination->protein = $examinationData['result'];
                    break;
                case 'GLUCOSE':
                    $examination->glucose = $examinationData['result'];
                    break;
                case 'PH':
                    $examination->ph = $examinationData['result'];
                    break;
                case 'VITAMIN_C':
                    $examination->vitamin_c = $examinationData['result'];
                    break;
                case 'CREATININE':
                    $examination->creatinine = $examinationData['result'];
                    break;
                case 'PROPORTION':
                    $examination->proportion = $examinationData['result'];
                    break;
                case 'ALBUMIN':
                    $examination->albumin = $examinationData['result'];
                    break;
                case 'CA':
                    $examination->calcium = $examinationData['result'];
                    break;
                default:
                    \Log::warning("Unknown examination type: $examinationName");
                    break;
            }
        }

        $examination->save();

        $logData = [
            'nik' => $nik,
            'kunjungan_id' => $kunjunganIds,
            'ttv' => $examination->toArray(),
            'examinations' => $data['examinations'],
        ];

        OphLog::create([
            'data' => serialize($logData),
        ]);

        return response()->json([
            'message' => 'Examination updated successfully',
            'ttv' => $examination,
            'log' => $logData
        ]);
    }

    public function logs(Request $request)
    {
        $serializedData = serialize($request->all());

        $log = OphLog::create([
            'data' => $serializedData,
        ]);

        return response()->json([
            'message' => 'Log saved successfully',
            'log' => $log
        ]);
    }
}
