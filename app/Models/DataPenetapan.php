<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPenetapan extends Model
{
    use HasFactory;

    protected $table = 'datapenetapan';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'jenis_pajak_id',
        'kategori_pajak_id',
        'jumlah_penagihan',
        'periode',
        'status', 
        // 'jumlah_pembayaran', 
        // 'tanggal_pembayaran', 
        // 'bukti_visit', 
        // 'bukti_pembayaran'
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }

    public function kategoriPajak()
    {
        return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id');
    }

    public function wajibPajak()
    {
        return $this->belongsTo(DataWajibPajak::class, 'npwpd', 'npwpd');
    }

    public function piutang()
    {
        return $this->hasOne(DataPiutang::class, 'npwpd', 'npwpd');
    }
    
       
    
}