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
        Schema::create('kas_bumdes', function (Blueprint $table) {
            $table->string('id_kas', 35)->primary();
            $table->string('id_bumdes', 30)->unique();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_bumdes')->references('id_bumdes')->on('bumdes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_bumdes');
    }
};
