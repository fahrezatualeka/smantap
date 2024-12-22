<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPenutupan extends Model
{
    use HasFactory;

    protected $table = 'laporanpenutupan';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'jenis_pajak_id',
        'telepon',
        'zona',
        // 'tagihan',
        'periode',
        'tanggal_kunjungan',
        'metode_pembayaran',
        // 'jumlah_pembayaran',
        // 'buktipembayaran',
        // 'buktisspd',
        // 'buktivisit',
        'buktipenutupan',
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

    public function laporanPenutupan()
{
    return $this->hasOne(LaporanPenutupan::class, 'npwpd', 'npwpd')
                ->where('periode', $this->periode);
}

// LaporanTransfer.php
public function dataPenutupan()
{
    return $this->belongsTo(DataPenutupan::class, 'npwpd', 'npwpd');
}
}