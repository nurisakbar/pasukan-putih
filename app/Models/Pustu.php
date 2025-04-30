<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pustu extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'pustus';
    protected $primaryKey = 'id';

    protected $fillable=['nama_pustu','village_id','id','district_id'];

    public function villages()
    {
        return $this->belongsTo(Village::class, 'village_id', 'id');
    }

    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
}
