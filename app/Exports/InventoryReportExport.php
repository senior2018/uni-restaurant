<?php

namespace App\Exports;

use App\Models\Meal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventoryReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['popular_meals'] ?? []);
    }

    public function headings(): array
    {
        return [
            'Meal Name',
            'Category',
            'Price',
            'Availability Status',
            'Order Count',
            'Total Revenue'
        ];
    }

    public function map($row): array
    {
        return [
            $row['name'] ?? '',
            $row['category'] ?? 'Uncategorized',
            '$' . number_format($row['price'] ?? 0, 2),
            $row['is_available'] ? 'Available' : 'Unavailable',
            $row['order_count'] ?? 0,
            '$' . number_format($row['revenue'] ?? 0, 2),
        ];
    }

    public function title(): string
    {
        return 'Inventory Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D5A27']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add restaurant branding header
                $sheet->insertNewRowBefore(1, 4);

                // Restaurant name and logo area
                $sheet->setCellValue('A1', 'OUR RESTAURANT');
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Report title
                $sheet->setCellValue('A2', 'INVENTORY & AVAILABILITY REPORT');
                $sheet->mergeCells('A2:F2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Report type
                $sheet->setCellValue('A3', 'CURRENT INVENTORY STATUS');
                $sheet->mergeCells('A3:F3');
                $sheet->getStyle('A3')->getFont()->setSize(12);
                $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Generated date
                $sheet->setCellValue('A4', 'Generated on: ' . now()->format('F j, Y \a\t g:i A'));
                $sheet->mergeCells('A4:F4');
                $sheet->getStyle('A4')->getFont()->setSize(10);
                $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Summary section
                $summaryRow = 6;
                $sheet->setCellValue('A' . $summaryRow, 'INVENTORY SUMMARY');
                $sheet->mergeCells('A' . $summaryRow . ':F' . $summaryRow);
                $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $summaryRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E8');

                // Summary data
                $summary = $this->data['summary'] ?? [];
                $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Meals: ' . ($summary['total_meals'] ?? 0));
                $sheet->setCellValue('A' . ($summaryRow + 2), 'Available Meals: ' . ($summary['available_meals'] ?? 0));
                $sheet->setCellValue('A' . ($summaryRow + 3), 'Unavailable Meals: ' . ($summary['unavailable_meals'] ?? 0));
                $sheet->setCellValue('A' . ($summaryRow + 4), 'Availability Rate: ' . ($summary['availability_percentage'] ?? 0) . '%');

                // Category breakdown section
                $categoryRow = $summaryRow + 6;
                $sheet->setCellValue('A' . $categoryRow, 'CATEGORY BREAKDOWN');
                $sheet->mergeCells('A' . $categoryRow . ':F' . $categoryRow);
                $sheet->getStyle('A' . $categoryRow)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $categoryRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E8');

                // Category data
                $categoryBreakdown = $this->data['category_breakdown'] ?? [];
                $currentRow = $categoryRow + 1;
                foreach ($categoryBreakdown as $category) {
                    $sheet->setCellValue('A' . $currentRow, $category['category'] . ': ' . $category['available_meals'] . '/' . $category['total_meals'] . ' available');
                    $currentRow++;
                }

                // Adjust column widths
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);

                // Add borders to data area
                $dataStartRow = $currentRow + 2;
                $dataEndRow = $dataStartRow + count($this->data['popular_meals'] ?? []);
                $sheet->getStyle('A' . $dataStartRow . ':F' . $dataEndRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
