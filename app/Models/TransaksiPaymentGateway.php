<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPaymentGateway extends Model
{
    protected $table = 'transaksi_payment_gateway';
    protected $primaryKey = 'id_transaksi';
    protected $guarded = [];

    protected $casts = ['raw_response' => 'array'];

    public function siswa()        { return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa'); }
    public function pembayaran()   { return $this->hasOne(Pembayaran::class, 'id_transaksi', 'id_transaksi'); }
    public function permintaan()   { return $this->belongsTo(PermintaanPembayaran::class, 'order_id', 'order_id'); }
    public function notifikasi()   { return $this->hasMany(NotifikasiGateway::class, 'id_transaksi', 'id_transaksi'); }
}
