<?php

namespace App\Exports;

use App\Models\Wp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;

class WpSheetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithCustomStartCell, WithEvents, WithColumnWidths
{
    protected $jenis;

    public function __construct($jenis)
    {
        $this->jenis = $jenis;
    }

    public function collection()
    {
        return Wp::where('jenis', $this->jenis)->get();
    }

    public function headings(): array
    {
        return [
            'NPWPD',
            'Nama',
            'Nama Pemilik',
            'Jenis Pajak',
            'Telepon',
            'Alamat Wajib Pajak',
            'Omset',
            'Tarif Pajak',
        ];
    }

    public function map($wp): array
    {
        return [
            $wp->npwpd,
            $wp->nama_pajak,
            $wp->nama_kelola,
            $wp->jenis,
            $wp->no_telepon,
            $wp->alamat,
            'Rp' . number_format($wp->omset, 0, ',', '.'),
            number_format($wp->pajak, 0, ',', '.') . '%',
        ];
    }

    public function title(): string
    {
        return ucwords(str_replace('_', ' ', $this->jenis));
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:H1');
                $event->sheet->setCellValue('A1', 'Pajak Pemerintah Kota Ambon');

                $event->sheet->mergeCells('A2:H2');
                $event->sheet->setCellValue('A2', 'Jenis Pajak ' . ucwords(str_replace('_', ' ', $this->jenis)));

                // Format Header
                $event->sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Format Table Header
                $event->sheet->getStyle('A4:H4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Format Table Body
                $event->sheet->getStyle('A5:H' . (5 + $this->collection()->count() - 1))->applyFromArray([
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Wrap Text in Columns
                $event->sheet->getStyle('A5:H' . (5 + $this->collection()->count() - 1))->getAlignment()->setWrapText(true);
            },
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // NPWPD
            'B' => 20, // Nama
            'C' => 25, // Nama Pemilik
            'D' => 20, // Jenis Pajak
            'E' => 15, // Telepon
            'F' => 30, // Alamat Wajib Pajak
            'G' => 20, // Omset
            'H' => 10, // Tarif Pajak
        ];
    }
}