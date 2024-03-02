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
        Schema::create('harian', function (Blueprint $table) {
            $table->id('id_harian');
            $table->unsignedBigInteger('id_penetasan');
            $table->dateTime('waktu_harian');
            $table->integer('menetas');
            $table->float('suhu_harian');
            $table->float('kelembaban_harian');
            $table->longText('deskripsi');
            $table->string('bukti_harian', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_penetasan')->references('id_penetasan')->on('penetasan');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harian');
    }
};
