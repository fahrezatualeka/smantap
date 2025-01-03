<?php

namespace App\Exports;

use App\Models\LaporanPelunasan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanPelunasanExport implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        // Ambil data pelunasan dari database
        $laporanPelunasan = LaporanPelunasan::with(['jenisPajak', 'kategoriPajak'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Menambahkan nomor urut untuk setiap data
        return $laporanPelunasan->map(function ($pelunasan, $index) {
            return [
                $index + 1,  // Menambahkan nomor urut (dimulai dari 1)
                $pelunasan->nama_pajak,
                $pelunasan->alamat,
                $pelunasan->npwpd,
                $pelunasan->jenisPajak->jenispajak ?? '-',
                $pelunasan->kategoriPajak->kategoripajak ?? '-',
                $pelunasan->nomor_telepon,
                $pelunasan->pembagian_zonasi,
                $pelunasan->jumlah_penagihan,
                $pelunasan->periode,
                // $pelunasan->jumlah_pembayaran,
                $pelunasan->tanggal_pembayaran,
                $pelunasan->tempat_pembayaran,
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
            'Kategori Pajak',
            'Nomor Telepon',
            'Pembagian Zonasi',
            'Jumlah Penagihan',
            'Periode',
            // 'Jumlah Pembayaran',
            'Tanggal Pembayaran',
            'Tempat Pembayaran',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // Menambahkan judul besar di atas header tabel (baris pertama)
                $sheet->mergeCells('A1:L1'); // Menggabungkan sel A1 hingga K1
                $sheet->setCellValue('A1', 'Laporan Pelunasan Pajak Pemerintah Kota Ambon'); // Menetapkan judul

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
                $sheet->getStyle('A2:L2')->applyFromArray([
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
                $laporanPelunasan = $this->collection();  // Ambil data yang sudah dimapping

                // Menambahkan data ke dalam sheet dimulai dari baris ketiga
                $row = 3; // Baris pertama untuk data dimulai di baris 3
                foreach ($laporanPelunasan as $pelunasan) {
                    foreach ($pelunasan as $key => $value) {
                        $sheet->setCellValueByColumnAndRow($key + 1, $row, $value);
                    }
                    $row++;
                }

                // Menambahkan style untuk data (baris ketiga dan seterusnya)
                $sheet->getStyle("A3:L" . ($row - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Mengatur lebar kolom secara otomatis
                foreach (range('A', 'L') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}