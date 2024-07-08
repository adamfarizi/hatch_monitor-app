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
        Schema::create('scan', function (Blueprint $table) {
            $table->id('id_scan');
            $table->unsignedBigInteger('id_harian');
            $table->dateTime('waktu_scan');
            $table->integer('infertil_rendah')->default(0)->nullable();
            $table->integer('infertil_sedang')->default(0)->nullable();
            $table->integer('infertil_tinggi')->default(0)->nullable();
            $table->integer('fertil_rendah')->default(0)->nullable();
            $table->integer('fertil_sedang')->default(0)->nullable();
            $table->integer('fertil_tinggi')->default(0)->nullable();
            $table->string('bukti_scan', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_harian')->references('id_harian')->on('harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan');
    }
};
