<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataWajibPajak extends Model
{
    protected $table = 'datawajibpajak';

    protected $fillable = [
        'nama_pajak',
        'alamat',
        'npwpd',
        'jenis_pajak_id',
        // 'kategori_pajak_id',
        'telepon',
        'zona',
        // 'status_lunas',
    ];

    public function jenisPajak()
    {
        return $this->belongsTo(JenisPajak::class, 'jenis_pajak_id');
    }
    public function dataPiutang()
    {
        return $this->hasMany(DataPiutang::class, 'npwpd', 'npwpd');
    }
    
    
}