<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PengkajianIndividu extends Model
{

    use HasUuids;

    protected $table = 'pengkajian_individus';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'pasien_id',
        'kesadaran',
        'gcs',
        'sistole',
        'diastole',
        'pernapasan',
        'suhu',
        'nadi',
        'takikardi',
        'bradikardia',
        'tubuhHangat',
        'menggigil',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }
}
