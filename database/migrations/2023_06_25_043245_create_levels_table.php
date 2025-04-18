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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->integer('gaji_pokok')->nullable();
            $table->integer('komunikasi')->nullable();
            $table->integer('transportasi')->nullable();
            $table->integer('kehadiran')->nullable();
            $table->integer('lama_kerja')->nullable();
            $table->integer('harga_lembur')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
