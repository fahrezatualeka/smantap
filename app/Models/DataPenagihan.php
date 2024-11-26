<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPenagihan extends Model
{
    use HasFactory;

    protected $table = 'datapenagihan';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'nomor_telepon',
        'jenis_pajak_id', // Harus menggunakan jenis_pajak_id
        'kategori_pajak_id', // Harus menggunakan kategori_pajak_id
        'jumlah_penagihan',
        'periode',
        'pembagian_zonasi',
        'buktipembayaran',
        'buktivisit',
        'status',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id', 'id');
    }
    
    public function kategoriPajak()
    {
        return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id', 'id');
    }

    public function dataPiutang()
    {
        return $this->belongsTo(DataPiutang::class, 'npwpd', 'npwpd');
    }

    public function laporanPelunasan()
{
    return $this->belongsTo(LaporanPelunasan::class);
}

    
}