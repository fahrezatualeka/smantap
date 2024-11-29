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
        Schema::create('laporanpiutang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->string('alamat');
            $table->string('npwpd')->unique();
            $table->unsignedBigInteger('jenis_pajak_id');
            $table->unsignedBigInteger('kategori_pajak_id');
            $table->string('nomor_telepon');
            $table->integer('pembagian_zonasi');
            $table->decimal('jumlah_penagihan', 15, 2);
            $table->string('periode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporanpiutang');
    }
};