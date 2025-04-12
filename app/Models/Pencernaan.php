<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pencernaan extends Model
{
    use HasUuids;

    protected $table = 'pencernaans';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'pasien_id',
        'mual',
        'muntah',
        'kembung',
        'nafsu_makan',
        'sulit_menelan',
        'disfagia',
        'bau_napas',
        'kerusakan_gigi',
        'distensi_abdomen',
        'bising_usus',
        'konstipasi',
        'diare',
        'hemoroid',
        'stomatitis',
        'warna_stomatitis',
        'massa_abdomen',
        'obat_pencahar',
        'konsistensi',
        'diet_khusus',
        'kebiasaan_makan',
        'alergi_makanan',
        'alat_bantu',
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
