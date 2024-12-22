<?php

namespace App\Imports;

use App\Models\DataWajibPajak;
use App\Models\DataPiutang;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataPiutangImport implements ToModel, WithHeadingRow
{
    protected $periode;

    public function __construct($periode)
    {
        $this->periode = $periode;
    }

    public function model(array $row)
    {
        // Log data yang diterima untuk memastikan data Excel dibaca dengan benar
        Log::info('Data yang diterima:', $row);
    
        $wajibPajak = DataWajibPajak::where('npwpd', $row['npwpd'])->first();
        
        if (!$wajibPajak) {
            Log::warning("NPWPD tidak ditemukan: {$row['npwpd']}");
            return null;
        }
    
        return new DataPiutang([
            'nama_pajak' => $wajibPajak->nama_pajak,
            'alamat' => $wajibPajak->alamat,
            'npwpd' => $wajibPajak->npwpd,
            'jenis_pajak_id' => $wajibPajak->jenis_pajak_id,
            'telepon' => $wajibPajak->telepon,
            'zona' => $wajibPajak->zona,
            'periode' => $this->periode,
            'status' => 'Belum Bayar',
        ]);
    }
}