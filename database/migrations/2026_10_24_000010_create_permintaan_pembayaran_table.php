<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel payment attempts (FR-31, §13.12).
 * Setiap Generate Payment mencatat satu baris di sini sebelum webhook diterima.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permintaan_pembayaran', function (Blueprint $table) {
            $table->id('id_permintaan');

            $table->string('provider', 20)->comment('midtrans | simulator');

            // order_id unik per provider — digunakan untuk idempotency
            $table->string('order_id', 100)->unique()->comment('Order ID yang dikirim ke provider (unik)');

            $table->foreignId('id_tagihan')
                ->constrained('tagihan_spp', 'id_tagihan')
                ->onDelete('cascade');

            $table->unsignedBigInteger('id_siswa');
            $table->foreign('id_siswa')->references('id_siswa')->on('siswa')->onDelete('cascade');

            $table->string('bank', 20)->nullable()->comment('BCA, BNI, BRI, PERMATA, dll.');

            // VA yang diterbitkan oleh provider (beda dari school VA — BR-15)
            $table->string('nomor_va_provider', 50)->nullable()->comment('VA yang diterbitkan provider (bukan school VA)');

            $table->unsignedInteger('nominal');

            $table->string('status', 20)->default('waiting')
                ->comment('waiting | paid | expired | cancelled | failed');

            // provider_transaction_id diisi saat webhook settlement diterima
            $table->string('provider_transaction_id', 100)->nullable();

            $table->dateTime('waktu_kedaluwarsa')->nullable()->comment('Waktu kedaluwarsa VA dari provider');

            $table->json('raw_request')->nullable()->comment('Payload yang dikirim ke provider');
            $table->json('raw_response')->nullable()->comment('Response dari provider saat create transaction');

            $table->timestamps();

            $table->index(['id_tagihan', 'provider', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permintaan_pembayaran');
    }
};
