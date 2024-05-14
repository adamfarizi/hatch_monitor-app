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
        Schema::create('infertil', function (Blueprint $table) {
            $table->id('id_infertil');
            $table->unsignedBigInteger('id_harian');
            $table->dateTime('waktu_infertil');
            $table->string('nomor_telur', 255)->nullable();
            $table->integer('jumlah_infertil');
            $table->string('bukti_infertil', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_harian')->references('id_harian')->on('harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infertil');
    }
};
