<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PhbsRumahTangga extends Model
{
    use HasUuids;

    protected $table = 'phbs_rumah_tanggas';

    protected $primaryKey = 'id';

    protected $fillable = [
        'ibu_nifas',
        'ada_bayi',
        'ada_balita',
        'air_bersih',
        'mencuci_tangan',
        'buang_sampah',
        'menjaga_lingkungan_rumah',
        'konsumsi_lauk',
        'gunakan_jamban',
        'jentik_dirumah',
        'makan_buah_sayur',
        'aktivitas_fisik',
        'merokok_dalam_rumah',
        'pasien_id'
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
