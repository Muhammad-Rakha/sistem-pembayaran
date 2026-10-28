<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanSpp extends Model
{
    protected $table = 'tagihan_spp';
    protected $primaryKey = 'id_tagihan';
    protected $guarded = [];

    protected $casts = ['tanggal_jatuh_tempo' => 'date'];

    public function siswa()      { return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa'); }
    public function tarif()      { return $this->belongsTo(TarifSpp::class, 'id_tarif', 'id_tarif'); }
    public function pembayaran() { return $this->hasOne(Pembayaran::class, 'id_tagihan', 'id_tagihan'); }
}
