<?php

namespace App\Imports;

use App\Models\DataWajibPajak;
use App\Models\JenisPajak;
use App\Models\KategoriPajak;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Log;

class DataWajibPajakImport implements ToModel
{
    /**
     * Transform each row into a model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Debug untuk melihat row data
        // dd($row); // Pastikan ini dihapus setelah debugging selesai.

        // Validasi apakah npwpd ada atau tidak
        if (empty($row[2])) {
            Log::warning("NPWPD kosong pada baris: " . json_encode($row));
            return null;  // Melewatkan baris jika npwpd kosong
        }

        // Mencocokkan jenis pajak berdasarkan ID
        $jenisPajak = JenisPajak::find($row[4]);  // Pastikan kolom yang digunakan benar
        if (!$jenisPajak) {
            Log::warning("Jenis Pajak tidak ditemukan untuk ID: " . $row[4]);
            $jenisPajakId = null;
        } else {
            $jenisPajakId = $jenisPajak->id;
        }

        // Mencocokkan kategori pajak berdasarkan ID
        $kategoriPajak = KategoriPajak::find($row[5]);  // Pastikan kolom yang digunakan benar
        if (!$kategoriPajak) {
            Log::warning("Kategori Pajak tidak ditemukan untuk ID: " . $row[5]);
            $kategoriPajakId = null;
        } else {
            $kategoriPajakId = $kategoriPajak->id;
        }

        // Log untuk memeriksa nilai ID Jenis Pajak dan Kategori Pajak
        Log::info('Jenis Pajak ID: ' . $jenisPajakId);
        Log::info('Kategori Pajak ID: ' . $kategoriPajakId);

        // Jika JenisPajak atau KategoriPajak tidak ditemukan, Anda bisa menangani dengan cara lain
        if ($jenisPajakId === null || $kategoriPajakId === null) {
            Log::warning("Data tidak lengkap, baris dilewatkan: " . json_encode($row));
            return null;
        }

        // Proses data dan tambahkan nilai pembagian_zonasi jika ada
        $pembagian_zonasi = isset($row[6]) ? $row[6] : null; // Misalkan pembagian zonasi ada di kolom ke-7

        return new DataWajibPajak([
            'nama_pajak' => $row[0],
            'alamat' => $row[1],
            'npwpd' => $row[2],
            'nomor_telepon' => $row[3],
            'jenis_pajak_id' => $jenisPajakId,
            'kategori_pajak_id' => $kategoriPajakId,
            'pembagian_zonasi' => $pembagian_zonasi,
        ]);
    }
}