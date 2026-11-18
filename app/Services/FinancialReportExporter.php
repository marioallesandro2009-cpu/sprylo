<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/** Turns a FinancialReport into a file on disk and returns its path. The caller deletes it after sending. */
class FinancialReportExporter
{
    public const HEADERS = ['No', 'Order #', 'Customer', 'Contact', 'Items', 'Total Amount', 'Status', 'Date & Time'];

    public const HEADER_COLOUR = 'D1FAE5';

    private const MONEY = '"Rp" #,##0';

    public function excel(FinancialReport $report): string
    {
        $book = new Spreadsheet;
        $book->getProperties()->setTitle('Laporan Keuangan '.$report->periodLabel())->setCreator($report->siteName());

        $this->ordersSheet($book->getActiveSheet(), $report);

        $path = $this->tempPath();
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();

        return $path;
    }

    public function pdf(FinancialReport $report): string
    {
        $path = $this->tempPath();

        Pdf::loadView('admin.reports.finance-pdf', ['report' => $report, 'headers' => self::HEADERS, 'headerColour' => self::HEADER_COLOUR])
            ->setPaper('a4', 'landscape')
            ->setOption(['defaultFont' => 'DejaVu Sans', 'isRemoteEnabled' => false])
            ->save($path);

        return $path;
    }

    /** One sheet, one line per paid order. Text always goes in as text: a customer called "=1+1" must stay a name. */
    private function ordersSheet(Worksheet $sheet, FinancialReport $report): void
    {
        $sheet->setTitle('Laporan Keuangan');

        foreach (self::HEADERS as $i => $header) {
            $this->text($sheet, $i + 1, 1, $header);
        }

        $row = 1;
        foreach ($report->orders() as $order) {
            $row++;
            $cells = [
                $row - 1, $order->order_number, $order->name, $order->email, (int) $order->items_sum_qty, (float) $order->total,
                $report->statusLabel($order->status), $report->placedAt($order),
            ];
            foreach ($cells as $i => $value) {
                $this->cell($sheet, $i + 1, $row, $value);
            }
            $sheet->getStyle([6, $row])->getNumberFormat()->setFormatCode(self::MONEY);
        }

        $lastColumn = count(self::HEADERS);
        $sheet->getStyle([1, 1, $lastColumn, $row])->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $header = $sheet->getStyle([1, 1, $lastColumn, 1]);
        $header->getFont()->setBold(true);
        $header->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $header->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::HEADER_COLOUR);

        $this->fitColumns($sheet);
        $sheet->setSelectedCell('A1'); // otherwise Excel opens with the last styled range highlighted
    }

    private function cell(Worksheet $sheet, int $column, int $row, mixed $value): void
    {
        if ($value instanceof DateTimeInterface) {
            $sheet->getCell([$column, $row])->setValue(ExcelDate::dateTimeToExcel($value));
            $sheet->getStyle([$column, $row])->getNumberFormat()->setFormatCode('dd mmm yyyy hh:mm');

            return;
        }

        if (is_int($value) || is_float($value)) {
            $sheet->getCell([$column, $row])->setValue($value);

            return;
        }

        $this->text($sheet, $column, $row, (string) $value);
    }

    private function text(Worksheet $sheet, int $column, int $row, string $value): void
    {
        $sheet->getCell([$column, $row])->setValueExplicit($value, DataType::TYPE_STRING);
    }

    private function fitColumns(Worksheet $sheet): void
    {
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
    }

    private function tempPath(): string
    {
        return tempnam(sys_get_temp_dir(), 'report');
    }
}
