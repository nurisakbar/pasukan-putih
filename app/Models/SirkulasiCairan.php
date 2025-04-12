<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SirkulasiCairan extends Model
{
    use HasUuids;

    protected $table = 'sirkulasi_cairans';
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'pasien_id',
        'edema',
        'bunyi_jantung',
        'asites',
        'akral_dingin',
        'tanda_perdarahan',
        'tanda_anemia',
        'tanda_dehidrasi',
        'pusing',
        'kesemutan',
        'berkeringat',
        'rasa_haus',
        'pengisian_kapiler',
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
