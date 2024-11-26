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
        // 'nomor_telepon',
        'jenis_pajak_id',
        'kategori_pajak_id',
        // 'status_lunas',
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
        return $this->hasMany(DataPenetapan::class);
    }
    


    // public function dataZonasi()
    // {
    //     return $this->hasMany(DataZonasi::class, 'npwpd', 'npwpd');
    // }

    // public function dataPelunasan()
    // {
    //     return $this->hasMany(DataPelunasan::class, 'npwpd', 'npwpd');
    // }

    // public function setNomorTeleponAttribute($value)
    // {
    //     $this->attributes['nomor_telepon'] = preg_replace('/\D/', '', $value);
    // }

    // // Event untuk update/soft delete yang mempengaruhi DataZonasi dan DataPelunasan
    // public static function boot()
    // {
    //     parent::boot();

    //     static::updated(function ($dataWajibPajak) {
    //         // Update DataZonasi terkait
    //         $dataWajibPajak->dataZonasi()->update([
    //             'nama_pajak' => $dataWajibPajak->nama_pajak,
    //             'alamat' => $dataWajibPajak->alamat,
    //             'jenis_pajak_id' => $dataWajibPajak->jenis_pajak_id,
    //             'kategori_pajak_id' => $dataWajibPajak->kategori_pajak_id,
    //         ]);

    //         // Update DataPelunasan terkait
    //         $dataWajibPajak->dataPelunasan()->update([
    //             'nama_pajak' => $dataWajibPajak->nama_pajak,
    //             'alamat' => $dataWajibPajak->alamat,
    //             'jenis_pajak_id' => $dataWajibPajak->jenis_pajak_id,
    //             'kategori_pajak_id' => $dataWajibPajak->kategori_pajak_id,
    //         ]);
    //     });

    //     static::deleted(function ($dataWajibPajak) {
    //         // Hapus data terkait di DataZonasi dan DataPelunasan
    //         $dataWajibPajak->dataZonasi()->delete();
    //         $dataWajibPajak->dataPelunasan()->delete();
    //     });
    // }
}