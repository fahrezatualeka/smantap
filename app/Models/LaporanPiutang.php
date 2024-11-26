<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPiutang extends Model
{
    use HasFactory;

    protected $table = 'laporanpiutang';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'jenis_pajak_id',
        'kategori_pajak_id',
        'jumlah_penagihan',
        'periode',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id'); // Sesuaikan dengan nama kolom relasi
    }
    
    public function kategoriPajak()
    {
        return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id'); // Sesuaikan dengan nama kolom relasi
    }
}
