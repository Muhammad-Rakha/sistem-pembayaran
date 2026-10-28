<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Append-only notification log (BR-17).
 * This model intentionally has no updated_at column.
 */
class NotifikasiGateway extends Model
{
    protected $table      = 'notifikasi_gateway';
    protected $primaryKey = 'id_notifikasi';
    protected $guarded    = [];

    // Append-only: no update_at
    const UPDATED_AT = null;

    protected $casts = [
        'payload'         => 'array',
        'signature_valid' => 'boolean',
        'created_at'      => 'datetime',
    ];

    public function transaksi() { return $this->belongsTo(TransaksiPaymentGateway::class, 'id_transaksi', 'id_transaksi'); }
}
