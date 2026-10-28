<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_payment_gateway', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('transaction_id')->unique(); // BR-12
            $table->string('virtual_account')->index();
            $table->foreignId('id_siswa')->nullable()->constrained('siswa', 'id_siswa');
            $table->date('tanggal_transaksi');
            $table->time('waktu_transaksi');
            $table->unsignedInteger('nominal');
            $table->string('status_transaksi');
            $table->string('payment_channel')->nullable();
            $table->string('reference_number')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_payment_gateway');
    }
};
