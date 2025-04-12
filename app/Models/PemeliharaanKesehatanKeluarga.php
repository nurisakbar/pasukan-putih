<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//uuid
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PemeliharaanKesehatanKeluarga extends Model
{
    use HasUuids;

    protected $table = 'pemeliharaan_kesehatan_keluargas';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pasien_id',
        'perhatian_keluarga',
        'mengetahui_masalah_kesehatan',
        'penyebab_masalah_kesehatan',
        'akibat_masalah_kesehatan',
        'keyakinan_keluarga',
        'upaya_peningkatan_kesehatan',
        'upaya_peningkatan_kesehatan_deskripsi',
        'merawat_anggota_keluarga',
        'kebutuhan_pengobatan',
        'melakukan_pencegahan_masalah',
        'mendukung_kesehatan',
        'memanfaatkan_sumber',
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
