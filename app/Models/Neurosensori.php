<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Neurosensori extends Model
{
    use HasUuids;

    protected $table = 'neurosensoris';
    protected $primaryKey = 'id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pasien_id',
        'buram',
        'tidak_bisa_melihat',
        'alat_bantu_penglihatan',
        'visus',
        'kesemutan',
        'kebas',
        'kurang_jelas',
        'tuli',
        'tinnitus',
        'refleks_patologi',
        'disorientasi',
        'parese',
        'alat_bantu_saraf',
        'halusinasi',
        'disatria',
        'amnesia',
        'kekuatan_otot',
        'postur_tidak_normal',
        'nyeri',
        'sifat',
        'frekuensi',
        'lama',
        'mampu',
        'terganggu',
        'memar',
        'laserasi',
        'ulserasi',
        'pus',
        'bulae_lepuh',
        'perdarahan_bawah',
        'krusta',
        'perubahan_warna',
        'luka_bakar_kulit',
        'decubitus_grade',
        'decubitus_lokasi',
        'susah_tidur',
        'waktu_tidur',
        'bantuan_obat',
        'cemas',
        'marah',
        'denial',
        'takut',
        'putus_asa',
        'depresi',
        'rendah_diri',
        'menarik_diri',
        'agresif',
        'perilaku_kekerasan',
        'tidak_mau_melihat_bagian_tubuh_yang_rusak',
        'respon_pasca_trauma',
        'interaksi_keluarga',
        'berkomunikasi',
        'kegiatan_sosial',
        'gigi_dan_mulut_kotor',
        'kulit_kotor',
        'hidung_kotor',
        'telinga_kotor',
        'mata_kotor',
        'perial_genial_kotor',
        'kuku_kotor',
        'rambut_kepala_kotor',
        'mandi',
        'berpakaian',
        'menyisir_rambut',
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
