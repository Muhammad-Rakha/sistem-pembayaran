<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembayaran extends Model
{
    protected $table      = 'permintaan_pembayaran';
    protected $primaryKey = 'id_permintaan';
    protected $guarded    = [];

    protected $casts = [
        'raw_request'      => 'array',
        'raw_response'     => 'array',
        'waktu_kedaluwarsa'=> 'datetime',
    ];

    public function tagihan()   { return $this->belongsTo(TagihanSpp::class, 'id_tagihan', 'id_tagihan'); }
    public function siswa()     { return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa'); }
    public function transaksi() { return $this->hasOne(TransaksiPaymentGateway::class, 'order_id', 'order_id'); }
}
