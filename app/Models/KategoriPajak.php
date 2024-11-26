<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriPajak extends Model
{
    use HasFactory;

    protected $table = 'kategoripajak';

    protected $fillable = ['jenis_pajak_id', 'kategoripajak'];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }

    public function dataWajibPajak()
    {
        return $this->hasMany(DataWajibPajak::class, 'kategori_pajak_id');
    }  
    
}