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
        Schema::create('monitor', function (Blueprint $table) {
            $table->id('id_monitor');
            $table->unsignedBigInteger('id_penetasan')->nullable();
            $table->dateTime('waktu_monitor');
            $table->float('suhu_monitor');
            $table->float('kelembaban_monitor');
            $table->timestamps();

            $table->foreign('id_penetasan')->references('id_penetasan')->on('penetasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor');
    }
};
