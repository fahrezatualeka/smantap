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
        // Migration datazonasi
        Schema::create('datazonasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->string('alamat');
            $table->string('npwpd')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->string('jenis_pajak_id');
            $table->string('kategori_pajak_id');
            // $table->string('tanggal_tagihan');
            $table->string('jumlah_piutang')->nullable();
            $table->integer('pembagian_zonasi')->nullable();
            $table->string('bulan')->nullable();
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
    */
    public function down(): void
    {
        Schema::dropIfExists('datazonasi');
    }
};