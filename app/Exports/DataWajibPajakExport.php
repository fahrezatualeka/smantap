<?php

namespace App\Exports;

use App\Models\DataWajibPajak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class DataWajibPajakExport implements FromCollection, WithHeadings, WithEvents
{

    public function collection()
{
    // Ambil data penetapan dari database
    $dataWajibPajak = DataWajibPajak::with(['jenisPajak'])
    // ->orderBy('created_at', 'desc')
    ->get();

    // Pastikan data berjumlah 6 dan mapping data
    return $dataWajibPajak->map(function ($datawajibpajak, $index) {
        return [
            $index + 1,  // Menambahkan nomor urut (dimulai dari 1)
            $datawajibpajak->nama_pajak,
            $datawajibpajak->alamat,
            $datawajibpajak->npwpd,
            $datawajibpajak->jenisPajak->jenispajak ?? '-',
            // $datawajibpajak->kategoriPajak->kategoripajak ?? '-',
            $datawajibpajak->telepon,
            $datawajibpajak->zona,
            // $datawajibpajak->jumlah_penagihan,
            // $datawajibpajak->periode,
        ];
    });
}

public function headings(): array
{
    // Header kolom yang akan ditampilkan
    return [
        'No',
        'Nama Pajak',
        'Alamat',
        'NPWPD',
        'Jenis Pajak',
        // 'Kategori Pajak',
        'Telepon',
        'Zona',
        // 'Jumlah Penagihan',
        // 'Periode',
    ];
}

public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet;

            // Menambahkan judul besar di atas header tabel (baris pertama)
            $sheet->mergeCells('A1:G1'); // Menggabungkan sel A1 hingga E1
            $sheet->setCellValue('A1', 'Data Wajib Pajak BAPENDA Kota Ambon'); // Menetapkan judul

            // Menambahkan style pada judul
            $sheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'size' => 16,
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Mengatur tinggi baris pertama (judul) agar terlihat lebih baik
            $sheet->getRowDimension(1)->setRowHeight(20); // Baris untuk judul

            // Menambahkan header kolom pada baris kedua
            $headers = $this->headings();
            foreach ($headers as $key => $value) {
                $sheet->setCellValueByColumnAndRow($key + 1, 2, $value); // Menambahkan heading pada baris 2
            }

            // Menambahkan style untuk header kolom (baris kedua)
            $sheet->getStyle('A2:G2')->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Mengatur tinggi baris kedua (header) agar terlihat lebih baik
            $sheet->getRowDimension(2)->setRowHeight(20); // Baris untuk header kolom

            // Menambahkan data pada baris ketiga dan seterusnya
            $dataWajibPajak = $this->collection();  // Ambil data yang sudah dimapping

            // Menambahkan data ke dalam sheet dimulai dari baris ketiga
            $row = 3; // Baris pertama untuk data dimulai di baris 3
            foreach ($dataWajibPajak as $datawajibpajak) {
                foreach ($datawajibpajak as $key => $value) {
                    $sheet->setCellValueByColumnAndRow($key + 1, $row, $value);
                }
                $row++;
            }

            // Menambahkan style untuk data (baris ketiga dan seterusnya)
            $sheet->getStyle("A3:G" . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Mengatur lebar kolom secara otomatis
            foreach (range('A', 'G') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
        },
    ];
}


}