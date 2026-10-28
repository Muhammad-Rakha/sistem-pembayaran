<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_transaksi')->unique()->constrained('transaksi_payment_gateway', 'id_transaksi');
            $table->foreignId('id_tagihan')->unique()->constrained('tagihan_spp', 'id_tagihan'); // BR-11
            $table->dateTime('tanggal_pembayaran'); // actual payment date (BR-09)
            $table->unsignedInteger('nominal_pembayaran');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
