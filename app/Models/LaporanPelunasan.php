<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class DataPelunasan extends Model
// {
//     use HasFactory;

//     protected $table = 'datapelunasan';

//     protected $fillable = [
//         'nama_pajak',
//         'alamat',
//         'npwpd',
//         'nomor_telepon',
//         'jenis_pajak_id',
//         'kategori_pajak_id',
//         'tanggal_pelunasan',
//     ];

//     // Model Pelunasan.php
// public function jenisPajak()
// {
//     return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id'); // Sesuaikan dengan nama kolom relasi
// }

// public function kategoriPajak()
// {
//     return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id'); // Sesuaikan dengan nama kolom relasi
// }

// }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPelunasan extends Model
{
    use HasFactory;

    protected $table = 'laporanpelunasan';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'nomor_telepon',
        'jenis_pajak_id',
        'kategori_pajak_id',
        'jumlah_penagihan',
        // 'jumlah_pembayaran',
        'tanggal_pembayaran',
        'buktipembayaran',
        'buktivisit',
        'tempat_pembayaran',
    ];

    // Relasi ke DataWajibPajak
    public function dataWajibPajak()
    {
        return $this->belongsTo(DataWajibPajak::class, 'npwpd', 'npwpd');
    }

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }
    
    public function kategoriPajak()
    {
        return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id');
    }
    

public function dataPenagihan()
{
    return $this->belongsTo(DataPenagihan::class, 'npwpd', 'npwpd');
}

}
