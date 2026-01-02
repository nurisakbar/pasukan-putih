<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HealthForm extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'health_forms';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'visiting_id',
        'no_disease',
        'diseases',
        'cancer_type',
        'lung_disease_type',
        'screening_obesity',
        'obesity_status',
        'screening_hypertension',
        'hypertension_status',
        'screening_diabetes',
        'diabetes_status',
        'screening_stroke',
        'stroke_status',
        'screening_heart_disease',
        'heart_disease_status',
        'screening_breast_cancer',
        'breast_cancer_status',
        'screening_cervical_cancer',
        'cervical_cancer_status',
        'screening_lung_cancer',
        'lung_cancer_status',
        'screening_colorectal_cancer',
        'colorectal_cancer_status',
        'screening_mental_health',
        'mental_health_status',
        'screening_ppok',
        'ppok_status',
        'screening_tbc',
        'tbc_status',
        'screening_vision',
        'vision_status',
        'screening_hearing',
        'hearing_status',
        'screening_fitness',
        'fitness_status',
        'screening_dental',
        'dental_status',
        'screening_elderly',
        'elderly_status',
        'skor_aks',
        'gangguan_komunikasi',
        'kesulitan_makan',
        'gangguan_fungsi_kardiorespirasi',
        'gangguan_fungsi_berkemih',
        'gangguan_mobilisasi',
        'gangguan_partisipasi',
        // Perawatan Umum fields
        'perawatan_hygiene',
        'perawatan_skin_care',
        'perawatan_environment',
        'perawatan_welfare',
        'perawatan_sunlight',
        'perawatan_communication',
        'perawatan_recreation',
        'perawatan_penamtauan_obat',
        'perawatan_ibadah',
        // Perawatan Khusus fields
        'perawatan_membantu_warga',
        'perawatan_monitoring_gizi',
        'perawatan_membantu_bak_bab',
        'perawatan_menangani_gangguan',
        'perawatan_pengelolaan_stres',
        // Other fields
        'perawatan',
        'keluaran',
        'keterangan',
        'pembinaan',
        'kemandirian',
        'tingkat_kemandirian',
        'kunjungan_lanjutan',
        'dilakukan_oleh',
        'operator_id_lanjutan',
        'permasalahan_lanjutan',
        'tanggal_kunjungan',
        'catatan_keperawatan',
        'henti_layanan',
        'non_medical_issues_status',
        'non_medical_issues_text',
        'caregiver_availability',
        // SKILAS fields - Simple checkbox
        'skilas_kognitif',
        'skilas_mobilisasi',
        'skilas_malnutrisi_berat_badan',
        'skilas_malnutrisi_nafsu_makan',
        'skilas_malnutrisi_lila',
        'skilas_penglihatan',
        'skilas_penglihatan_keterangan',
        'skilas_pendengaran',
        'skilas_depresi_sedih',
        'skilas_depresi_minat',
        'skilas_rujukan',
        'skilas_rujukan_keterangan',
        'skilas_hasil_tindakan_keperawatan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'no_disease' => 'boolean',
        'diseases' => 'array',
        'screening_obesity' => 'boolean',
        'screening_hypertension' => 'boolean',
        'screening_diabetes' => 'boolean',
        'screening_stroke' => 'boolean',
        'screening_heart_disease' => 'boolean',
        'screening_breast_cancer' => 'boolean',
        'screening_cervical_cancer' => 'boolean',
        'screening_lung_cancer' => 'boolean',
        'screening_colorectal_cancer' => 'boolean',
        'screening_mental_health' => 'boolean',
        'screening_ppok' => 'boolean',
        'screening_tbc' => 'boolean',
        'screening_vision' => 'boolean',
        'screening_hearing' => 'boolean',
        'screening_fitness' => 'boolean',
        'screening_dental' => 'boolean',
        'screening_elderly' => 'boolean',
        'gangguan_komunikasi' => 'boolean',
        'kesulitan_makan' => 'boolean',
        'gangguan_fungsi_kardiorespirasi' => 'boolean',
        'gangguan_fungsi_berkemih' => 'boolean',
        'gangguan_mobilisasi' => 'boolean',
        'gangguan_partisipasi' => 'boolean',
        // Perawatan Umum casts
        'perawatan_hygiene' => 'boolean',
        'perawatan_skin_care' => 'boolean',
        'perawatan_environment' => 'boolean',
        'perawatan_welfare' => 'boolean',
        'perawatan_sunlight' => 'boolean',
        'perawatan_communication' => 'boolean',
        'perawatan_recreation' => 'boolean',
        'perawatan_penamtauan_obat' => 'boolean',
        'perawatan_ibadah' => 'boolean',
        // Perawatan Khusus casts
        'perawatan_membantu_warga' => 'boolean',
        'perawatan_monitoring_gizi' => 'boolean',
        'perawatan_membantu_bak_bab' => 'boolean',
        'perawatan_menangani_gangguan' => 'boolean',
        'perawatan_pengelolaan_stres' => 'boolean',
        // Other casts
        'keluaran' => 'integer',
        'kemandirian' => 'array',
        'dilakukan_oleh' => 'array',
        'tanggal_kunjungan' => 'date',
        // SKILAS casts - Simple checkbox
        'skilas_kognitif' => 'boolean',
        'skilas_mobilisasi' => 'boolean',
        'skilas_malnutrisi_berat_badan' => 'boolean',
        'skilas_malnutrisi_nafsu_makan' => 'boolean',
        'skilas_malnutrisi_lila' => 'boolean',
        'skilas_penglihatan' => 'boolean',
        'skilas_pendengaran' => 'boolean',
        'skilas_depresi_sedih' => 'boolean',
        'skilas_depresi_minat' => 'boolean',
        'skilas_rujukan' => 'boolean',
    ];

    /**
     * Get the user that owns the health form.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate and set the tingkat_kemandirian based on kemandirian array
     */
    public function calculateTingkatKemandirian()
    {
        if (!$this->kemandirian) {
            $this->tingkat_kemandirian = 'Belum Ditentukan';
            return;
        }

        // Handle both array and JSON string cases
        $kemandirian = $this->kemandirian;
        if (is_string($kemandirian)) {
            $kemandirian = json_decode($kemandirian, true);
        }
        
        if (!is_array($kemandirian)) {
            $this->tingkat_kemandirian = 'Belum Ditentukan';
            return;
        }

        $count = count($kemandirian);
        
        if ($count >= 7) {
            $this->tingkat_kemandirian = 'Keluarga IV';
        } elseif ($count === 6) {
            $this->tingkat_kemandirian = 'Keluarga III';
        } elseif ($count === 5) {
            $this->tingkat_kemandirian = 'Keluarga II';
        } elseif ($count >= 1 && $count <= 4) {
            $this->tingkat_kemandirian = 'Keluarga I';
        } else {
            $this->tingkat_kemandirian = 'Belum Ditentukan';
        }
    }

    /**
     * Get the general care items as an array
     */
    public function getGeneralCareItems()
    {
        $items = [];
        
        $careFields = [
            'perawatan_hygiene' => 'Pemeliharaan kebersihan diri',
            'perawatan_skin_care' => 'Pencegahan Masalah Kesehatan Kulit',
            'perawatan_environment' => 'Pemeliharaan Kebersihan dan Keamanan Lingkungan',
            'perawatan_welfare' => 'Mempertahankan Tingkat Kemandirian warga jakarta yang membutuhkan',
            'perawatan_sunlight' => 'Tercukupinya pajanan Sinar Matahari',
            'perawatan_communication' => 'Komunikasi dengan baik',
            'perawatan_recreation' => 'Motivasi untuk melaksanakan Kegiatan Rekreasi',
            'perawatan_penamtauan_obat' => 'Pemantauan Penggunaan Obat',
            'perawatan_ibadah' => 'Motivasi untuk Pelaksanaan Ibadah',
        ];
        
        foreach ($careFields as $field => $label) {
            if ($this->$field) {
                $items[] = $label;
            }
        }
        
        return $items;
    }
    
    /**
     * Get the specialized care items as an array
     */
    public function getSpecializedCareItems()
    {
        $items = [];
        
        $careFields = [
            'perawatan_membantu_warga' => 'Membantu warga jakarta yang membutuhkan yang Mengalami Gangguan Gerak',
            'perawatan_monitoring_gizi' => 'Monitoring dan Edukasi Pemenuhan Gizi yang baik',
            'perawatan_membantu_bak_bab' => 'Membantu Buang Air Kecil (BAK) dan Buang Air Besar (BAB)',
            'perawatan_menangani_gangguan' => 'Menangani Gangguan Perilaku dengan Pikun/Demensial',
            'perawatan_pengelolaan_stres' => 'Pengelolaan Stres',
        ];
        
        foreach ($careFields as $field => $label) {
            if ($this->$field) {
                $items[] = $label;
            }
        }
        
        return $items;
    }

    /**
     * Override the save method to calculate tingkat_kemandirian before saving
     */
    public function save(array $options = [])
    {
        $this->calculateTingkatKemandirian();
        return parent::save($options);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function visiting()
    {
        return $this->belongsTo(Visiting::class, 'visiting_id');
    }
}