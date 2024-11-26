<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;

// class DataZonasi extends Model
// {
//     use HasFactory;

//     protected $table = 'datazonasi';

//     protected $fillable = [
//         'nama_pajak',
//         'alamat',
//         'npwpd',
//         'nomor_telepon',
//         'jenis_pajak_id',
//         'kategori_pajak_id',
//         // 'tanggal_tagihan',
//         'jumlah_piutang',
//         'pembagian_zonasi',
//     ];

//     public $timestamps = true;

//     // Relasi ke DataPenagihan
//     public function dataPenagihan()
//     {
//         return $this->hasMany(DataPenagihan::class, 'kode_zonasi', 'kode_zonasi');
//     }

//     // Relasi ke LaporanPenagihan
//     public function laporanPenagihan()
//     {
//         return $this->hasMany(LaporanPenagihan::class, 'data_zonasi_id', 'id');
//     }

//     // Relasi ke JenisPajak
//     public function jenisPajak()
//     {
//         return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
//     }

//     // Relasi ke KategoriPajak
//     public function kategoriPajak()
//     {
//         return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id');
//     }
// }

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataZonasi extends Model
{
    use HasFactory;

    protected $table = 'datazonasi';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'nomor_telepon',
        'jenis_pajak_id',
        'kategori_pajak_id',
        'jumlah_piutang',
        'pembagian_zonasi',
    ];

    public $timestamps = true;

    // Relasi ke DataWajibPajak
    public function dataWajibPajak()
    {
        return $this->belongsTo(DataWajibPajak::class, 'npwpd', 'npwpd');
    }

        // Relasi ke DataPenagihan
    public function dataPenagihan()
    {
        return $this->hasMany(DataPenagihan::class, 'kode_zonasi', 'kode_zonasi');
    }

    // Relasi ke LaporanPenagihan
    public function laporanPenagihan()
    {
        return $this->hasMany(LaporanPenagihan::class, 'data_zonasi_id', 'id');
    }

    // Relasi ke JenisPajak
    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }

    // Relasi ke KategoriPajak
    public function kategoriPajak()
    {
        return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id');
    }
}
