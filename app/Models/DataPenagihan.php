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
        'jenis_pajak_id', // Harus menggunakan jenis_pajak_id
        'kategori_pajak_id', // Harus menggunakan kategori_pajak_id
        'nomor_telepon', // Harus menggunakan kategori_pajak_id
        'pembagian_zonasi',
        'jumlah_penagihan',
        'periode',
        'buktipembayaran',
        'buktivisit',
        // 'tempat_pembayaran',
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

    public function petugasPenagihan()
    {
        return $this->belongsTo(User::class, 'pembagian_zonasi', 'pembagian_zonasi');
    }

    public function laporanPelunasan()
{
    return $this->belongsTo(LaporanPelunasan::class);
}

    
}