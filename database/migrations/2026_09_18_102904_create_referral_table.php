<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral', function (Blueprint $table) {
            $table->string('id_referral', 40)->primary();
            $table->string('id_bumdes_pengaju', 30);
            $table->string('id_bumdes_penerima', 30)->nullable(); // nullable sampai di-redeem
            $table->string('kode_unik', 10)->unique();
            $table->dateTime('tanggal_generate');
            $table->dateTime('tanggal_expired'); // = tanggal_generate + 5 hari
            $table->enum('status', ['aktif', 'terpakai', 'kedaluwarsa', 'pending', 'cair', 'gagal'])
                ->default('aktif');
            $table->dateTime('tanggal_redeem')->nullable();
            $table->dateTime('batas_verifikasi')->nullable(); // = tanggal_redeem + 15 hari
            $table->dateTime('tanggal_cair')->nullable();
            $table->timestamps();

            $table->foreign('id_bumdes_pengaju')->references('id_bumdes')->on('bumdes');
            $table->foreign('id_bumdes_penerima')->references('id_bumdes')->on('bumdes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral');
    }
};
