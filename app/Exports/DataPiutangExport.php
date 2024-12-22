<?php

namespace App\Exports;

use App\Models\DataPiutang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class DataPiutangExport implements FromCollection, WithHeadings, WithEvents
{
    public function collection()
    {
        // Ambil data penetapan dari database
        $dataPiutang = DataPiutang::with(['jenisPajak'])
        ->orderBy('created_at', 'desc')

        // ->orderByRaw("
        //     CASE 
        //         WHEN status = 'Belum Bayar' THEN 0
        //         WHEN status = 'Sudah Bayar' THEN 1
        //     END ASC
        // ")
        ->get();
        
        // Mapping data sesuai dengan urutan tabel, dan menambahkan kolom 'No' sebagai nomor urut
        return $dataPiutang->map(function ($piutang, $index) {
            return [
                $index + 1,                        // Kolom No (nomor urut)
                $piutang->nama_pajak,             // Nama Pajak
                $piutang->alamat,                 // Alamat
                $piutang->npwpd,                  // NPWPD
                $piutang->jenisPajak->jenispajak ?? '-',  // Jenis Pajak
                $piutang->telepon,          // Nomor Telepon
                $piutang->zona,       // Pembagian Zonasi
                $piutang->periode,                // Periode
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
            // 'Status',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
    
                // Menambahkan judul besar di atas header tabel (baris pertama)
                $sheet->mergeCells('A1:H1'); // Menggabungkan sel A1 hingga J1
                $sheet->setCellValue('A1', 'Data Piutang BAPENDA Kota Ambon'); // Menetapkan judul
    
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
                $sheet->getStyle('A2:H2')->applyFromArray([
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
                $dataPiutang = $this->collection();  // Ambil data yang sudah dimapping
    
                // Menambahkan data ke dalam sheet dimulai dari baris ketiga
                $row = 3; // Baris pertama untuk data dimulai di baris 3
                foreach ($dataPiutang as $piutang) {
                    foreach ($piutang as $key => $value) {
                        $sheet->setCellValueByColumnAndRow($key + 1, $row, $value);
                    }
                    $row++;
                }
    
                // Menambahkan style untuk data (baris ketiga dan seterusnya)
                $sheet->getStyle("A3:H" . ($row - 1))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
    
                // Mengatur lebar kolom secara otomatis
                foreach (range('A', 'H') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}