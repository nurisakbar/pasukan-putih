<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pustu extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['nama_pustu','village_id','id','village'];
}
