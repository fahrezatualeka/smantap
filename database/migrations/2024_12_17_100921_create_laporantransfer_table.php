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
        Schema::create('laporantransfer', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->string('alamat');
            $table->string('npwpd')->unique();
            $table->unsignedBigInteger('jenis_pajak_id')->nullable();
            // $table->unsignedBigInteger('kategori_pajak_id')->nullable();
            $table->string('telepon');
            $table->integer('zona');
            // $table->decimal('tagihan', 15, 2);
            $table->string('periode');
            // $table->decimal('jumlah_pembayaran', 15, 2);
            $table->date('tanggal_pembayaran');
            $table->string('metode_pembayaran');
            $table->string('jumlah_pembayaran')->nullable();
            $table->string('buktipembayaran')->nullable();
            $table->string('buktisspd')->nullable();
            // $table->string('buktivisit')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('pengirim', 255)->nullable();
            $table->enum('konfirmasi', ['Belum kirim', 'Sudah kirim'])->default('Belum kirim');
            // $table->enum('tempat_pembayaran', ['Admin', 'Petugas Penagihan'])->nullable();
            // $table->string('tempat_pembayaran', 255)->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporantransfer');
    }
};