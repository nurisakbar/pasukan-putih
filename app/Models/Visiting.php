<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Visiting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'visitings';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';
    
    protected $fillable = [
        'pasien_id',
        'user_id',
        'tanggal',
        'status',
        'berat_badan',
        'tinggi_badan',
        'imt',
        'selesai'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'berat_badan' => 'decimal:2',
        'tinggi_badan' => 'decimal:2',
        'imt' => 'decimal:2'
    ];

    /**
     * Get the pasien that owns the kunjungan.
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    /**
     * Get the user that created the kunjungan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function healthForms()
    {
        return $this->hasOne(HealthForm::class, 'visiting_id');
    }

    public function ttvs()
    {
        return $this->hasMany(Ttv::class, 'kunjungan_id');
    }

    public function skriningAdl()
    {
        return $this->hasOne(SkriningAdl::class, 'visiting_id');
    }
}