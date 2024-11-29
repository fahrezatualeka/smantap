<?php

namespace App\Imports;

use App\Models\DataWajibPajak;
use App\Models\DataPenetapan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DataPenetapanImport implements ToModel, WithHeadingRow
{
    protected $periode;

    // Daftar bulan dalam bahasa Indonesia
    private $months = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    public function __construct($periode)
    {
        $this->periode = $periode;
    }

    public function model(array $row)
    {
        try {
            // Cek jika Data Wajib Pajak ada berdasarkan NPWPD
            $wajibPajak = DataWajibPajak::where('npwpd', $row['npwpd'])->first();

            if ($wajibPajak) {
                Log::info('Data Wajib Pajak ditemukan', ['npwpd' => $wajibPajak->npwpd]);

                // Cek apakah data dengan NPWPD dan periode yang sama sudah ada
                $existingData = DataPenetapan::where('npwpd', $wajibPajak->npwpd)
                    ->where('periode', $this->periode)
                    ->first();

                if ($existingData) {
                    // Jika data sudah ada, lemparkan exception untuk ditangani di controller
                    throw new \Exception('Gagal! Data yang Anda upload sudah ada di dalam tabel.');
                }

                // Validasi jumlah_penagihan jika kosong atau tidak valid
                $jumlahPenagihan = isset($row['jumlah_penagihan']) && is_numeric($row['jumlah_penagihan']) 
                    ? $row['jumlah_penagihan'] 
                    : 0; // Menetapkan nilai default jika tidak valid

                // Cek dan ubah periode menjadi nama bulan dalam bahasa Indonesia
                $periodeParts = explode('-', $this->periode);

                if (count($periodeParts) < 2) {
                    throw new \Exception("Format periode tidak valid: " . $this->periode);
                }

                $bulanAngka = $periodeParts[1]; // Ambil bulan dalam format angka

                // Pastikan bulan ada dalam array months
                if (!isset($this->months[$bulanAngka])) {
                    throw new \Exception("Bulan tidak valid: " . $bulanAngka);
                }

                $bulanNama = $this->months[$bulanAngka]; // Ambil nama bulan dari daftar

                return new DataPenetapan([
                    'nama_pajak' => $wajibPajak->nama_pajak,
                    'alamat' => $wajibPajak->alamat,
                    'npwpd' => $wajibPajak->npwpd,
                    'jenis_pajak_id' => $wajibPajak->jenis_pajak_id,
                    'kategori_pajak_id' => $wajibPajak->kategori_pajak_id,
                    'nomor_telepon' => $wajibPajak->nomor_telepon,
                    'pembagian_zonasi' => $wajibPajak->pembagian_zonasi,
                    'jumlah_penagihan' => $jumlahPenagihan,
                    'periode' => "$bulanNama {$periodeParts[0]}", // Formatkan periode
                    'status' => 'Belum Bayar', // Status default jika baru
                ]);
            } else {
                // Log jika NPWPD tidak ditemukan
                Log::warning('NPWPD tidak ditemukan', ['npwpd' => $row['npwpd']]);
            }
        } catch (\Exception $e) {
            // Tangani kesalahan umum lainnya
            Log::error('Kesalahan saat impor data Penetapan', [
                'message' => $e->getMessage(),
                'row' => $row,
                'trace' => $e->getTraceAsString(),
            ]);
            // Lemparkan exception untuk ditangani di controller
            throw new \Exception($e->getMessage());
        }

        return null; // Abaikan baris jika terjadi kesalahan atau data tidak ditemukan
    }
}
