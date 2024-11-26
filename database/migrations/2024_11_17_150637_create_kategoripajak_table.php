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
        Schema::create('kategoripajak', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_pajak_id');  // Perubahan nama kolom
            $table->string('kategoripajak');
            $table->timestamps();

            // Menambahkan foreign key
            $table->foreign('jenis_pajak_id')->references('id')->on('jenispajak')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoripajak');
    }
};