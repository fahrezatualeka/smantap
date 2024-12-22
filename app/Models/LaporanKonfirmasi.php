<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKonfirmasi extends Model
{
    use HasFactory;

    protected $table = 'laporankonfirmasi';

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
        'buktivisit',
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

    public function laporanKonfirmasi()
{
    return $this->hasOne(LaporanKonfirmasi::class, 'npwpd', 'npwpd')
                ->where('periode', $this->periode);
}

// LaporanTransfer.php
public function dataKonfirmasi()
{
    return $this->belongsTo(DataKonfirmasi::class, 'npwpd', 'npwpd');
}
}