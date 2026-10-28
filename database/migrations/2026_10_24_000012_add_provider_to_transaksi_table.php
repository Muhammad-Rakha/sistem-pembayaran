<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional columns on transaksi_payment_gateway (§13.12):
 * provider and order_id for cross-referencing with permintaan_pembayaran.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi_payment_gateway', function (Blueprint $table) {
            $table->string('provider', 20)->nullable()->after('id_transaksi')
                ->comment('midtrans | simulator');
            $table->string('order_id', 100)->nullable()->after('provider')
                ->comment('FK ke permintaan_pembayaran.order_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi_payment_gateway', function (Blueprint $table) {
            $table->dropColumn(['provider', 'order_id']);
        });
    }
};
