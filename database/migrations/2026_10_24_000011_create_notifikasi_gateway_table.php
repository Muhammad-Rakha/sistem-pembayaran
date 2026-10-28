<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Append-only notification log (BR-17, FR-30, §13.12).
 * Setiap notifikasi yang masuk (valid, duplikat, ditolak) dicatat di sini.
 * TIDAK ADA UPDATE pada tabel ini — hanya INSERT.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_gateway', function (Blueprint $table) {
            $table->id('id_notifikasi');

            $table->string('provider', 20)->comment('midtrans | simulator');

            // Referensi order/attempt di sisi kita
            $table->string('order_id', 100)->nullable()->index();

            // Transaction ID dari provider (BR-12)
            $table->string('transaction_id', 100)->nullable()->index();

            // Apakah signature/HMAC valid?
            $table->boolean('signature_valid')->default(false);

            // Status yang diterima dari provider (as-is, sebelum mapping)
            $table->string('status_provider', 50)->nullable()
                ->comment('settlement | pending | expire | PAID | FAILED | dll.');

            // Seluruh payload mentah
            $table->json('payload')->nullable();

            // Kode hasil pemrosesan
            $table->string('hasil_proses', 50)->nullable()
                ->comment('processed | duplicate | rejected-signature | rejected-amount | rejected-unknown-va | dll.');

            $table->string('pesan_proses')->nullable()->comment('Deskripsi hasil proses');

            // FK ke transaksi yang dibuat (nullable — mungkin tidak ada jika ditolak)
            $table->unsignedBigInteger('id_transaksi')->nullable();
            $table->foreign('id_transaksi')->references('id_transaksi')->on('transaksi_payment_gateway')->onDelete('set null');

            // Tidak ada updated_at — tabel ini append-only
            $table->timestamp('created_at')->useCurrent();

            $table->index(['provider', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_gateway');
    }
};
