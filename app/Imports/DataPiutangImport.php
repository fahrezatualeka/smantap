<?php

namespace App\Imports;

use App\Models\DataPiutang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class DataPiutangImport implements ToModel, WithHeadingRow
{

    public function __construct($bulan)
    {
        $this->bulan = $bulan;
    }
    public function model(array $row)
    {
        // Validasi jika npwpd kosong
        if (empty($row['npwpd'])) {
            return null; // Lewati baris jika npwpd kosong
        }

        // Periksa apakah tanggal dalam bentuk angka dan konversikan
        $tanggal_tagihan = is_numeric($row['tanggal_tagihan'])
            ? Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_tagihan']))->format('Y-m-d')
            : Carbon::parse($row['tanggal_tagihan'])->format('Y-m-d');

        return new DataPiutang([
            'nama_pajak' => $row['nama_pajak'],
            'alamat' => $row['alamat'],
            'npwpd' => $row['npwpd'],
            'nomor_telepon' => $row['nomor_telepon'],
            'jenis_pajak_id' => $row['jenis_pajak_id'],
            'kategori_pajak_id' => $row['kategori_pajak_id'],
            'jumlah_piutang' => $row['jumlah_piutang'],
            'tanggal_tagihan' => $tanggal_tagihan,
            'bulan' => $this->bulan,
        ]);
    }
}