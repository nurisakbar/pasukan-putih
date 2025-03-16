<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Perkemihan extends Model
{
    use HasUuids;
    protected $table = 'perkemihans';
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pasien_id',
        'pola_bak',
        'volume',
        'hematuri',
        'poliuria',
        'oliguria',
        'disuria',
        'inkontinensia',
        'retensi',
        'nyeri_bak',
        'kemampuan_bak',
        'alat_bantu_bak',
        'obat_bak',
        'kemampuan_bab',
        'alat_bantu_bab',
        'obat_bab',
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
