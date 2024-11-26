<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPenagihan extends Model
{
    protected $table = 'laporanpenagihan'; // Menetapkan nama tabel yang benar

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'nomor_telepon',
        'jenis_pajak_id',
        'kategori_pajak_id',
        'tanggal_tagihan',
        'jumlah_piutang',
        'pembagian_zonasi',
        'uploadbuktivisit',
        'uploadbuktipembayaran',
        'tanggal_pembayaran',
        'status',
        // 'verifikasi',
    ];    

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id', 'id');
    }

    public function kategoriPajak()
    {
        return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id', 'id');
    }

    public function dataPenagihan()
{
    return $this->hasMany(DataPenagihan::class, 'laporan_penagihan_id');
}

}
