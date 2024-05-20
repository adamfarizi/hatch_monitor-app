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
        Schema::create('penetasan', function (Blueprint $table) {
            $table->id('id_penetasan');
            $table->unsignedBigInteger('id_peternak');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->dateTime('batas_scan');
            $table->integer('jumlah_telur');
            $table->integer('prediksi_menetas')->default(0);
            $table->integer('total_menetas')->default(0);
            $table->float('rata_rata_suhu')->default(0);
            $table->float('rata_rata_kelembaban')->default(0);
            $table->timestamps();

            $table->foreign('id_peternak')->references('id_peternak')->on('peternak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penetasan');
    }
};
