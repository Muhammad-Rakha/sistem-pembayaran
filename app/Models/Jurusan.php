<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'jurusan';
    protected $primaryKey = 'id_jurusan';
    protected $guarded = [];

    public function kelas() { return $this->hasMany(Kelas::class, 'id_jurusan', 'id_jurusan'); }
}
