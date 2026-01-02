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
        'visiting_id',
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

    protected $casts = [
        'bab_control' => 'integer',
        'bak_control' => 'integer',
        'eating' => 'integer',
        'stairs' => 'integer',
        'bathing' => 'integer',
        'transfer' => 'integer',
        'walking' => 'integer',
        'dressing' => 'integer',
        'grooming' => 'integer',
        'toilet_use' => 'integer',
        'total_score' => 'integer',
    ];

    public function visiting()
    {
        return $this->belongsTo(Visiting::class, 'visiting_id');
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
