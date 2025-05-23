<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pasien extends Model
{
    use HasUuids;
    use SoftDeletes;

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
        'user_id',
        'rt',
        'rw',
        'pustu_id'
    ];

    protected $dates = ['deleted_at'];

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

    public function visiting()
    {
        return $this->hasMany(Visiting::class);
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function pustu()
    {
        return $this->belongsTo(Pustu::class, 'pustu_id', 'id');
    }
}
