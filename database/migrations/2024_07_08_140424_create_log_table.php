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
        Schema::create('log', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_penetasan');
            $table->dateTime('waktu_log');
            $table->integer('infertil_rendah')->default(0)->nullable();
            $table->integer('infertil_sedang')->default(0)->nullable();
            $table->integer('infertil_tinggi')->default(0)->nullable();
            $table->integer('fertil_rendah')->default(0)->nullable();
            $table->integer('fertil_sedang')->default(0)->nullable();
            $table->integer('fertil_tinggi')->default(0)->nullable();
            $table->integer('unknown')->default(0)->nullable();
            $table->string('bukti_log', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_penetasan')->references('id_penetasan')->on('penetasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log');
    }
};
