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
        Schema::create('laporanpenagihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->string('alamat');
            $table->string('npwpd')->nullable();
            $table->string('nomor_telepon')->nullable();
            $table->unsignedBigInteger('jenis_pajak_id');
            $table->unsignedBigInteger('kategori_pajak_id');
            // $table->date('tanggal_tagihan');
            $table->bigInteger('jumlah_piutang');
            $table->integer('pembagian_zonasi');
            $table->string('uploadbuktivisit')->nullable();
            $table->string('uploadbuktipembayaran')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporanpenagihan');
    }
};
