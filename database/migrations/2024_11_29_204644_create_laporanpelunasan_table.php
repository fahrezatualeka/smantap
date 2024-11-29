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
        Schema::create('laporanpelunasan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->string('alamat');
            $table->string('npwpd');
            $table->unsignedBigInteger('jenis_pajak_id')->nullable();
            $table->unsignedBigInteger('kategori_pajak_id')->nullable();
            $table->string('nomor_telepon');
            $table->integer('pembagian_zonasi');
            $table->decimal('jumlah_penagihan', 15, 2);
            $table->string('periode');
            // $table->decimal('jumlah_pembayaran', 15, 2);
            $table->date('tanggal_pembayaran');
            $table->string('buktipembayaran')->nullable();
            $table->string('buktivisit')->nullable();
            // $table->enum('tempat_pembayaran', ['Admin', 'Petugas Penagihan']);
            // $table->string('tempat_pembayaran')->nullable();
            $table->enum('tempat_pembayaran', ['Admin', 'Petugas Penagihan'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporanpelunasan');
    }
};