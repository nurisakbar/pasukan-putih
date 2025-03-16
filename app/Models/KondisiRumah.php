<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//uuid
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KondisiRumah extends Model
{
    use HasUuids;

    protected $table = 'kondisi_rumahs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'pasien_id',
        'ventilasi',
        'pencahayaan',
        'saluran_limbah',
        'sumber_air',
        'jamban',
        'tempat_sampah',
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
