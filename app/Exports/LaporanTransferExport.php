<?php

namespace App\Exports;

use App\Models\LaporanTransfer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanTransferExport implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
            // Ambil data yang akan ditampilkan di PDF
            $laporanTransfer = LaporanTransfer::with(['jenisPajak'])
            ->where('metode_pembayaran', 'Transfer')
    ->orderBy('created_at', 'desc')
    ->get();
        
        // Mapping data sesuai dengan urutan tabel, dan menambahkan kolom 'No' sebagai nomor urut
        return $laporanTransfer->map(function ($transfer, $index) {
            return [
                $index + 1,                        // Kolom No (nomor urut)
                $transfer->nama_pajak,             // Nama Pajak
                $transfer->alamat,                 // Alamat
                $transfer->npwpd,                  // NPWPD
                $transfer->jenisPajak->jenispajak ?? '-',  // Jenis Pajak
                $transfer->telepon,          // Nomor Telepon
                $transfer->zona,       // Pembagian Zonasi
                $transfer->periode,                // Periode
                \Carbon\Carbon::parse($transfer->tanggal_pembayaran)->locale('id')->isoFormat('D MMMM YYYY'),
                'Rp ' . number_format($transfer->jumlah_pembayaran, 0, ',', '.'),
                $transfer->keterangan ?? 'Tidak ada',                // Periode
                $transfer->pengirim,                // Periode
            ];
        });
    }

    public function headings(): array
    {
        // Header kolom yang akan ditampilkan sesuai urutan tabel
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
            'Periode',
            'Tanggal Pembayaran',
            'Jumlah Pembayaran',
            'Keterangan',
            'Pengirim',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
    
                // Menambahkan judul besar di atas header tabel (baris pertama)
                $sheet->mergeCells('A1:L1'); // Menggabungkan sel A1 hingga J1
                $sheet->setCellValue('A1', 'Laporan Transfer BAPENDA Kota Ambon'); // Menetapkan judul
    
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
                $laporanTransfer = $this->collection();  // Ambil data yang sudah dimapping
    
                // Menambahkan data ke dalam sheet dimulai dari baris ketiga
                $row = 3; // Baris pertama untuk data dimulai di baris 3
                foreach ($laporanTransfer as $transfer) {
                    foreach ($transfer as $key => $value) {
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