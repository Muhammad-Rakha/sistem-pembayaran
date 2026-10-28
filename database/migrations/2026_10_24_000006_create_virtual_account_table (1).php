<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('virtual_account', function (Blueprint $table) {
            $table->id('id_va');
            $table->foreignId('id_siswa')->unique()->constrained('siswa', 'id_siswa'); // BR-01
            $table->string('nomor_va')->unique();
            $table->string('prefix');
            $table->string('status')->default('aktif');
            $table->date('tanggal_dibuat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('virtual_account');
    }
};
