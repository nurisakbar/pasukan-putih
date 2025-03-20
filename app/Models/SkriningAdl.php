<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SkriningAdl extends Model
{
    use HasUuids;

    protected $table = 'skrining_adl';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'kunjungan_id',
        'pasien_id',
        'bab_control',
        'bak_control',
        'eating',
        'stairs',
        'bathing',
        'transfer',
        'walking',
        'dressing',
        'grooming',
        'toilet_use',
        'total_score',
        'butuh_orang',
        'pendamping_tetap',
        'sasaran_home_service',
        'pemeriksa_id',
    ];

    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function pemeriksa()
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }
}
