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
        Schema::create('pembayaran_freelances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelance_id')->constrained('freelances')->onDelete('cascade');
            $table->foreignId('akun_detail_id')->constrained('akun_details')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->decimal('total_upah', 15, 2)->default(0);
            $table->decimal('total_lembur', 15, 2)->default(0);
            $table->decimal('total_pembayaran', 15, 2)->default(0);
            $table->decimal('potongan_kasbon', 15, 2)->default(0);
            $table->decimal('total_keluar', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_freelances');
    }
};
