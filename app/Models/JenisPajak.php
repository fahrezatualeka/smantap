<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPajak extends Model
{
    use HasFactory;

    protected $table = 'jenispajak';

    protected $fillable = ['jenispajak'];

    // Relasi ke KategoriPajak
    public function kategoriPajak()
    {
        return $this->hasMany(KategoriPajak::class);
    }
    public function dataWajibPajak()
    {
        return $this->hasMany(DataWajibPajak::class, 'jenis_pajak_id');
    }

    public function dataZonasi()
{
    return $this->hasMany(DataZonasi::class);
}

    
    
    
}