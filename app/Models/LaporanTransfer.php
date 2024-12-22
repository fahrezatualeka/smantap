<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanTransfer extends Model
{
    use HasFactory;

    protected $table = 'laporantransfer';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'jenis_pajak_id',
        'telepon',
        'zona',
        // 'tagihan',
        'periode',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'jumlah_pembayaran',
        'buktipembayaran',
        'buktisspd',
        // 'buktivisit',
        'keterangan',
        'pengirim',
        'konfirmasi',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }

    public function dataPenagihan()
    {
        return $this->belongsTo(DataPenagihan::class, 'npwpd', 'npwpd');
    }

    // DataTransfer.php
public function laporanTransfer()
{
    return $this->hasOne(LaporanTransfer::class, 'npwpd', 'npwpd')
                ->where('periode', $this->periode);
}

// LaporanTransfer.php
public function dataTransfer()
{
    return $this->belongsTo(DataTransfer::class, 'npwpd', 'npwpd');
}
}