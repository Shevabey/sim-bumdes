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
        Schema::create('region', function (Blueprint $table) {
            $table->string('id_region', 30)->primary();
            $table->enum('level', ['provinsi', 'kota', 'kecamatan', 'kelurahan']);
            $table->string('parent_id', 30)->nullable();
            $table->string('nama', 150);
            $table->boolean('is_koordinator')->default(false);
            $table->timestamps();

            $table->foreign('parent_id')->references('id_region')->on('region')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('region');
    }
};
