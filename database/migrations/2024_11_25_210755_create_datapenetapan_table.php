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
        Schema::create('datapenetapan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->string('alamat');
            // $table->string('npwpd')->unique();
            $table->string('npwpd');
            $table->unsignedBigInteger('jenis_pajak_id')->nullable();
            $table->unsignedBigInteger('kategori_pajak_id')->nullable();
            $table->decimal('jumlah_penagihan', 15, 2);
            $table->string('periode')->nullable();
            // $table->string('status')->nullable()->default('Belum Bayar');
            $table->enum('status', ['Belum Bayar', 'Sudah Bayar'])->default('Belum Bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datapenetapan');
    }
};