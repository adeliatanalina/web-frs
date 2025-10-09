<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = ['user_id','matkul_id','kelas_id'];

    public function matkul() { return $this->belongsTo(Matkul::class); }
    public function kelas()  { return $this->belongsTo(Kelas::class); }
}
