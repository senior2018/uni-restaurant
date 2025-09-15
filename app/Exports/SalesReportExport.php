<?php

namespace App\Exports;

use App\Models\Order;
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

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithEvents
{
    protected $data;
    protected $period;
    protected $year;

    public function __construct($data, $period, $year = null)
    {
        $this->data = $data;
        $this->period = $period;
        $this->year = $year;
    }

    public function collection()
    {
        return collect($this->data['daily_breakdown'] ?? []);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Total Orders',
            'Total Revenue',
            'Average Order Value',
            'Top Selling Meal',
            'Orders Count'
        ];
    }

    public function map($row): array
    {
        return [
            $row['date'] ?? '',
            $row['total_orders'] ?? 0,
            number_format($row['total_revenue'] ?? 0, 0) . ' TZS',
            number_format($row['average_order_value'] ?? 0, 0) . ' TZS',
            $row['top_meal'] ?? 'N/A',
            $row['total_orders'] ?? 0,
        ];
    }

    public function title(): string
    {
        return 'Sales Report - ' . ucfirst($this->period);
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
                $sheet->setCellValue('A2', 'SALES REPORT');
                $sheet->mergeCells('A2:F2');
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Period information
                $periodText = strtoupper($this->period) . ' REPORT';
                if ($this->year) {
                    $periodText .= ' - ' . $this->year;
                }
                $sheet->setCellValue('A3', $periodText);
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
                $sheet->setCellValue('A' . $summaryRow, 'SUMMARY');
                $sheet->mergeCells('A' . $summaryRow . ':F' . $summaryRow);
                $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A' . $summaryRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E8F5E8');

                // Summary data
                $summary = $this->data['summary'] ?? [];
                $sheet->setCellValue('A' . ($summaryRow + 1), 'Total Revenue: ' . number_format($summary['total_revenue'] ?? 0, 0) . ' TZS');
                $sheet->setCellValue('A' . ($summaryRow + 2), 'Total Orders: ' . ($summary['total_orders'] ?? 0));
                $sheet->setCellValue('A' . ($summaryRow + 3), 'Average Order Value: ' . number_format($summary['average_order_value'] ?? 0, 0) . ' TZS');
                $sheet->setCellValue('A' . ($summaryRow + 4), 'Total Items Sold: ' . ($summary['total_items_sold'] ?? 0));
                $sheet->setCellValue('A' . ($summaryRow + 5), 'Cancelled Orders: ' . ($summary['cancelled_orders'] ?? 0));
                $sheet->setCellValue('A' . ($summaryRow + 6), 'Refunded Amount: ' . number_format($summary['refunded_amount'] ?? 0, 0) . ' TZS');

                // Adjust column widths
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(15);

                // Add borders to data area
                $dataStartRow = $summaryRow + 5;
                $dataEndRow = $dataStartRow + count($this->data['daily_breakdown'] ?? []);
                $sheet->getStyle('A' . $dataStartRow . ':F' . $dataEndRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
