<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_spp', function (Blueprint $table) {
            $table->id('id_tagihan');
            $table->foreignId('id_siswa')->constrained('siswa', 'id_siswa');
            $table->foreignId('id_tarif')->constrained('tarif_spp', 'id_tarif');
            $table->foreignId('id_tahun_ajaran')->constrained('tahun_ajaran', 'id_tahun_ajaran');
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedInteger('nominal');
            $table->date('tanggal_jatuh_tempo'); // last day of the month (BR-05)
            $table->string('status')->default('UNPAID'); // UNPAID | PAID | OVERDUE
            $table->timestamps();

            $table->unique(['id_siswa', 'bulan', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_spp');
    }
};
