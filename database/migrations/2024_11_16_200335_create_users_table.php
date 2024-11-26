<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('nomor_telepon');
            $table->string('alamat');
            $table->enum('role', ['admin','petugas_penagihan', 'pimpinan']);
            $table->integer('pembagian_zonasi')->nullable(); // Ubah ke integer dan tambahkan nullable
            $table->rememberToken();
            $table->timestamps();
        });
    }    

    public function down()
    {
        Schema::dropIfExists('users');
    }
}