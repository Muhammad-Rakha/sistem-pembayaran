<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifSpp extends Model
{
    protected $table = 'tarif_spp';
    protected $primaryKey = 'id_tarif';
    protected $guarded = [];

    public function tahunAjaran() { return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran', 'id_tahun_ajaran'); }
    public function tagihan()     { return $this->hasMany(TagihanSpp::class, 'id_tarif', 'id_tarif'); }
}
