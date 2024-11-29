<?php

namespace App\Imports;

use App\Models\DataWajibPajak;
use App\Models\JenisPajak;
use App\Models\KategoriPajak;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Tambahkan ini
use Illuminate\Support\Facades\Log;

class DataWajibPajakImport implements ToModel, WithHeadingRow // Tambahkan WithHeadingRow
{
    /**
     * Transform each row into a model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        Log::info('Proses row setelah heading: ' . json_encode($row));

        // Validasi apakah npwpd ada atau tidak
        if (empty($row['npwpd'])) {
            Log::warning("NPWPD kosong pada baris: " . json_encode($row));
            return null;
        }

        // Validasi dan cari Jenis Pajak
        $jenisPajakId = null;
        if (!empty($row['jenis_pajak_id'])) {
            $jenisPajak = JenisPajak::find($row['jenis_pajak_id']);
            $jenisPajakId = $jenisPajak ? $jenisPajak->id : null;
            if (!$jenisPajakId) {
                Log::warning("Jenis Pajak tidak ditemukan untuk ID: {$row['jenis_pajak_id']}");
            }
        }

        // Validasi dan cari Kategori Pajak
        $kategoriPajakId = null;
        if (!empty($row['kategori_pajak_id'])) {
            $kategoriPajak = KategoriPajak::find($row['kategori_pajak_id']);
            $kategoriPajakId = $kategoriPajak ? $kategoriPajak->id : null;
            if (!$kategoriPajakId) {
                Log::warning("Kategori Pajak tidak ditemukan untuk ID: {$row['kategori_pajak_id']}");
            }
        }

        // Pastikan pembagian zonasi sesuai
        $pembagian_zonasi = $row['pembagian_zonasi'] ?? null;

        return new DataWajibPajak([
            'nama_pajak' => $row['nama_pajak'],
            'alamat' => $row['alamat'],
            'npwpd' => $row['npwpd'],
            'nomor_telepon' => $row['nomor_telepon'],
            'jenis_pajak_id' => $jenisPajakId,
            'kategori_pajak_id' => $kategoriPajakId,
            'pembagian_zonasi' => $pembagian_zonasi,
        ]);
    }
}