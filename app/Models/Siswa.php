<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';
    protected $guarded = [];

    public function kelas()          { return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas'); }
    public function tahunAjaran()    { return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran'); }
    public function user()           { return $this->belongsTo(User::class, 'id_user'); }
    public function virtualAccount() { return $this->hasOne(VirtualAccount::class, 'id_siswa', 'id_siswa'); }
    public function tagihan()        { return $this->hasMany(TagihanSpp::class, 'id_siswa', 'id_siswa'); }
}
