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
        Schema::create('iuran_bumdes', function (Blueprint $table) {
            $table->string('id_iuran', 40)->primary();
            $table->string('id_bumdes', 30);
            $table->string('bulan_tahun', 7); // format YYYY-MM
            $table->decimal('jumlah', 15, 2)->default(50000);
            $table->enum('status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->enum('sumber_dana', ['kas', 'luar_kas'])->nullable();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->string('diverifikasi_oleh', 30)->nullable(); // admin BUMDes koordinator
            $table->timestamps();

            $table->foreign('id_bumdes')->references('id_bumdes')->on('bumdes');
            $table->foreign('diverifikasi_oleh')->references('id_akun')->on('akun')->nullOnDelete();
            $table->unique(['id_bumdes', 'bulan_tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iuran_bumdes');
    }
};
