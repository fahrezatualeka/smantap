<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// DataPenagihan Migration
public function up(): void
{
    Schema::create('datapenagihan', function (Blueprint $table) {
        $table->id();
        $table->string('nama_pajak');
        $table->string('alamat');
        $table->string('npwpd')->unique();            

        $table->unsignedBigInteger('jenis_pajak_id');
        // $table->unsignedBigInteger('kategori_pajak_id');
        $table->string('telepon');
        $table->integer('zona');
        // $table->bigInteger('tagihan');
        $table->string('periode');
        $table->enum('metode_pembayaran', ['Transfer', 'Tunai', 'Konfirmasi', 'Penutupan'])->nullable();
        $table->decimal('jumlah_pembayaran', 15, 2)->nullable();
        $table->string('buktipembayaran')->nullable();
        $table->string('buktisspd')->nullable();
        $table->string('buktivisit')->nullable();
        $table->string('buktipenutupan')->nullable();
        $table->string('keterangan')->nullable();
        // $table->string('status');
        // $table->string('status')->default('Belum Bayar');
        // $table->string('status')->default('Belum Bayar')->nullable();
        $table->enum('status', ['Belum Bayar', 'Sudah Bayar'])->default('Belum Bayar');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datapenagihan');
    }
};
