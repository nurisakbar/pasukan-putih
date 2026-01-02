<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Ttv extends Model
{
    use HasUuids;

    protected $table = 'ttvs';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kunjungan_id',
        'temperature',
        'blood_pressure',
        'bmi',
        'respiration',
        'bmi_category',
        'height',
        'weight',
        'pulse',
        'oxygen_saturation',
        'blood_sugar',
        'uric_acid',
        'tcho',
        'triglyceride',
        'high_density_protein',
        'low_density_protein',
        'hemoglobin',
        'jaundice',
        'w_waist',
        'w_bust',
        'w_hip',
        'fetal_heart',
        'ecg',
        'ultrasound',
        'white_corpuscle',
        'red_corpuscle',
        'nitrous_acid',
        'ketone_body',
        'urobilinogen',
        'bilirubin',
        'protein',
        'glucose',
        'ph',
        'vitamin_c',
        'creatinine',
        'proportion',
        'albumin',
        'calcium',
    ];

    protected $casts = [
        'pulse' => 'integer',
        'fetal_heart' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($examination) {
            if ($examination->weight && $examination->height) {
                $heightInMeters = $examination->height / 100;
                $examination->bmi = $examination->weight / ($heightInMeters * $heightInMeters);
                
                if ($examination->bmi < 17) {
                    $examination->bmi_category = 'Kurus';
                } elseif ($examination->bmi <= 18.4) {
                    $examination->bmi_category = 'Kurus';
                } elseif ($examination->bmi <= 25) {
                    $examination->bmi_category = 'Normal';
                } else {
                    $examination->bmi_category = 'Gemuk';
                }
            }
        });
    }

    public function visiting()
    {
        return $this->belongsTo(Visiting::class, 'kunjungan_id');
    }
}
