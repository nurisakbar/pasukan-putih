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
        'blood_pressure',
        'pulse',
        'respiration',
        'temperature',
        'oxygen_saturation',
        'weight',
        'height',
        'knee_height',
        'sitting_height',
        'arm_span',
        'bmi',
        'bmi_category',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'pulse' => 'integer',
        'respiration' => 'integer',
        'temperature' => 'decimal:1',
        'oxygen_saturation' => 'integer',
        'weight' => 'decimal:2',
        'height' => 'decimal:1',
        'knee_height' => 'decimal:1',
        'sitting_height' => 'decimal:1',
        'arm_span' => 'decimal:1',
        'bmi' => 'decimal:2',
    ];

    /**
     * Calculate BMI and category before saving
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($examination) {
            // Calculate BMI if weight and height are available
            if ($examination->weight && $examination->height) {
                $heightInMeters = $examination->height / 100;
                $examination->bmi = $examination->weight / ($heightInMeters * $heightInMeters);
<<<<<<< Updated upstream
                
                // Determine BMI category
=======

>>>>>>> Stashed changes
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

    /**
     * Get the patient that owns the health examination.
     */
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }

}
