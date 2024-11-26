<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function getPlainPasswordAttribute()
    {
    // Hanya contoh sederhana untuk mengambil password tanpa hashing
    // Ini tidak disarankan dalam penggunaan produksi
    return $this->attributes['password'];
    }
    
     protected $fillable = [
        'nama',
        'username',
        'password',
        'nomor_telepon',
        'alamat',
        'role',
        'pembagian_zonasi',
    ];    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function dataPiutang()
{
    return $this->hasMany(DataPiutang::class, 'pembagian_zonasi', 'pembagian_zonasi');
}

}