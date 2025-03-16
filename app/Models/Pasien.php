<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pasien extends Model
{
    use HasUuids;

    protected $table = 'pasiens';
    protected $primaryKey = 'id';
    protected $keyType = 'uuid';

    protected $fillable = [
        'name',
        'nik',
        'alamat',
        'jenis_kelamin',
        'jenis_ktp',
        'tanggal_lahir',
        'village_id',
        'district_id',
        'regency_id',
        'province_id'
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

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class);
    }


}
