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
        Schema::create('kas_mutasi', function (Blueprint $table) {
            $table->bigIncrements('id_mutasi');
            $table->string('id_kas', 35);
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->decimal('jumlah', 15, 2);
            $table->enum('sumber', ['referral', 'iuran', 'lainnya']);
            $table->string('keterangan', 255)->nullable();
            $table->dateTime('tanggal');
            $table->dateTime('created_at')->nullable();

            $table->foreign('id_kas')->references('id_kas')->on('kas_bumdes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_mutasi');
    }
};
