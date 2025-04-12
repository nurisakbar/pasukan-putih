<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
<<<<<<< Updated upstream
    use HasFactory, Notifiable, HasUuids, HasApiTokens;
=======
    use HasFactory;
    use Notifiable;
    use HasUuids;
    use HasApiTokens;
    use SoftDeletes;
>>>>>>> Stashed changes

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    protected $primaryKey = 'id';
<<<<<<< Updated upstream
    protected $keyType = 'string';  // UUID adalah string
=======
    protected $keyType = 'string';
>>>>>>> Stashed changes
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
<<<<<<< Updated upstream
        'parent_id'
=======
        'no_wa',
        'keterangan',
        'status_pegawai',
        'pustu_id'
>>>>>>> Stashed changes
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

<<<<<<< Updated upstream
=======
    protected $dates = ['deleted_at'];

>>>>>>> Stashed changes
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = Str::uuid()->toString();
            }
        });
    }

    public function pustu()
    {
        return $this->belongsTo(Pustu::class, 'pustu_id');
    }

    // Relasi ke user bawahan (anaknya)
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function kunjungan()
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function skriningAdl()
    {
        return $this->hasMany(SkriningAdl::class);
    }
}
