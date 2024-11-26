<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPiutang extends Model
{
    protected $table = 'datapiutang';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'jenis_pajak_id',
        'kategori_pajak_id',
        'jumlah_penagihan',
        'periode',
        'pembagian_zonasi',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }
    
    public function kategoriPajak()
    {
        return $this->belongsTo(KategoriPajak::class, 'kategori_pajak_id');
    }

    public function dataPenetapan()
    {
        return $this->belongsTo(DataPenetapan::class, 'npwpd', 'npwpd');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'pembagian_zonasi', 'pembagian_zonasi');
    }
    

}
