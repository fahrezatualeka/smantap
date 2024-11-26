<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public function dataPiutang()
    {
        return $this->hasMany(DataPiutang::class, 'npwpd', 'npwpd');
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($penetapan) {
            // Periksa apakah status berubah menjadi "Sudah Bayar"
            if ($penetapan->isDirty('status') && $penetapan->status === 'Sudah Bayar') {
                // Hapus data terkait di tabel DataPiutang
                $deleted = \App\Models\DataPiutang::where('npwpd', $penetapan->npwpd)
                    ->where('periode', $penetapan->periode)
                    ->delete();

                if ($deleted) {
                    Log::info("Data Piutang berhasil dihapus untuk NPWPD: {$penetapan->npwpd} dan Periode: {$penetapan->periode}");
                } else {
                    Log::warning("Tidak ada Data Piutang yang ditemukan untuk NPWPD: {$penetapan->npwpd} dan Periode: {$penetapan->periode}");
                }
            }
        });
    }
}
