<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Muskuloskeletal extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pasien_id',
        'kontraktur', 
        'fraktur', 
        'nyeri_otot_tulang', 
        'drop_foot_lokasi', 
        'tremor', 
        'malaise_fatigue',
        'atrofi', 
        'kekuatan_otot', 
        'postur_tidak_normal', 
        'alat_bantu', 
        'nyeri', 
        'tonus_otot',
        'ekstremitas_atas', 
        'berdiri', 
        'berjalan'
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
