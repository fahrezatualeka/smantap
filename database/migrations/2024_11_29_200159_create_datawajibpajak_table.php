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
        Schema::create('datawajibpajak', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak');
            $table->string('alamat');
            $table->string('npwpd');
            $table->unsignedBigInteger('jenis_pajak_id');
            $table->unsignedBigInteger('kategori_pajak_id');
            // $table->integer('nomor_telepon');
            $table->string('nomor_telepon');
            $table->integer('pembagian_zonasi');
            // $table->boolean('is_readonly')->default(false);
            // $table->string('jumlah_piutang')->nullable();
            // $table->string('status_lunas')->nullable()->default('Belum Lunas');
            $table->timestamps();

            // Menambahkan foreign key
            $table->foreign('jenis_pajak_id')->references('id')->on('jenispajak')->onDelete('cascade');
            $table->foreign('kategori_pajak_id')->references('id')->on('kategoripajak')->onDelete('cascade');

            // Menambahkan index pada foreign key untuk performa
            $table->index('jenis_pajak_id');
            $table->index('kategori_pajak_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('datawajibpajak', function (Blueprint $table) {
            $table->dropColumn('is_readonly');
        });
    }
};