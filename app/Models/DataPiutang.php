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
        // 'nomor_telepon',
        'jenis_pajak_id',
        // 'kategori_pajak_id',
        'telepon',
        'zona',
        'periode',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id'); // Sesuaikan dengan nama kolom relasi
    }
    public function datapiutang()
    {
        return $this->belongsTo(DataPiutang::class, 'npwpd', 'npwpd');
    }

    public function dataWajibPajak()
    {
        return $this->belongsTo(DataWajibPajak::class, 'npwpd', 'npwpd');
    }
    
    
}