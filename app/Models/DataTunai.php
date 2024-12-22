<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTunai extends Model
{
    use HasFactory;

    protected $table = 'datatunai';

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
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }

    public function dataPenagihan()
    {
        return $this->belongsTo(DataPenagihan::class, 'npwpd', 'npwpd');
    }

    public function laporanTunai()
{
    return $this->hasOne(LaporanTunai::class, 'npwpd', 'npwpd')
                ->where('periode', $this->periode);
}

// LaporanTransfer.php
public function dataTunai()
{
    return $this->belongsTo(DataTunai::class, 'npwpd', 'npwpd');
}
}