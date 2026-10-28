<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $guarded = [];

    protected $casts = ['tanggal_pembayaran' => 'datetime'];

    public function transaksi() { return $this->belongsTo(TransaksiPaymentGateway::class, 'id_transaksi', 'id_transaksi'); }
    public function tagihan()   { return $this->belongsTo(TagihanSpp::class, 'id_tagihan', 'id_tagihan'); }
}
