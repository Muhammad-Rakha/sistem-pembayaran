<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id_tahun_ajaran';
    protected $guarded = [];

    public function kelas() { return $this->hasMany(Kelas::class, 'id_tahun_ajaran', 'id_tahun_ajaran'); }
    public function siswa() { return $this->hasMany(Siswa::class, 'id_tahun_ajaran', 'id_tahun_ajaran'); }
    public function tarif() { return $this->hasMany(TarifSpp::class, 'id_tahun_ajaran', 'id_tahun_ajaran'); }
}
