<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kunjungan extends Model
{
    use HasUuids;

    protected $table = 'kunjungans';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tanggal',
        'pasien_id',
        'user_id',
        'hasil_periksa',
        'jenis',
        'status',
        'skor_aks_data_sasaran',
        'lanjut_kunjungan',
        'rencana_kunjungan_lanjutan',
        'henti_layanan',
        'rujukan',
        'konversi_data_ke_sasaran_kunjungan_lanjutan',
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

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'pasien_id'); 
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ttvs()
    {
        return $this->hasMany(Ttv::class);
    }

    public function skriningAdl()
    {
        return $this->hasOne(SkriningAdl::class, 'kunjungan_id');
    }
}
