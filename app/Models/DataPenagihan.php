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
        // 'kategori_pajak_id', // Harus menggunakan kategori_pajak_id
        'telepon', // Harus menggunakan kategori_pajak_id
        'zona',
        // 'tagihan',
        'periode',
        'metode_pembayaran',
        'jumlah_pembayaran',
        'buktipembayaran',
        'buktisspd',
        'buktivisit',
        'buktipenutupan',
        'keterangan',
        // 'tempat_pembayaran',
        'status',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }
    

    public function dataPiutang()
    {
        return $this->belongsTo(DataPiutang::class, 'npwpd', 'npwpd');
    }

    public function petugasPenagihan()
    {
        return $this->belongsTo(User::class, 'zona', 'zona');
    }

    public function laporanPelunasan()
    {
        return $this->belongsTo(LaporanPelunasan::class);
    }

    public function dataPelunasan()
    {
        return $this->hasMany(DataPelunasan::class, 'npwpd', 'npwpd');
    }

    
}