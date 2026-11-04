<?php

namespace App\Exports;

use App\Support\SalesReport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport
{
    private const MONEY = '"Rp" #,##0';

    private const STATUS = [
        'paid'      => 'Antre',
        'cooking'   => 'Dimasak',
        'done'      => 'Selesai',
        'cancelled' => 'Batal',
    ];

    private const METHOD = [
        'cash'  => 'Tunai',
        'qris'  => 'QRIS',
        'debit' => 'Debit',
    ];

    public function __construct(private SalesReport $report)
    {
    }

    public function build(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        $this->summarySheet($spreadsheet->getActiveSheet());
        $this->dailySheet($spreadsheet->createSheet());
        $this->menuSheet($spreadsheet->createSheet());
        $this->transactionSheet($spreadsheet->createSheet());

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function summarySheet(Worksheet $sheet): void
    {
        $summary = $this->report->summary();

        $sheet->setTitle('Ringkasan');
        $sheet->setCellValue('A1', 'Laporan Pendapatan - Dapur Babeh');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->fromArray([
            ['Periode', $this->report->from->format('d/m/Y') . ' - ' . $this->report->to->format('d/m/Y')],
            ['Total Pendapatan', $summary['revenue']],
            ['Jumlah Transaksi', $summary['count']],
            ['Rata-rata per Transaksi', $summary['average']],
            ['Dibuat pada', now()->format('d/m/Y H:i')],
        ], null, 'A3');

        $sheet->getStyle('A3:A7')->getFont()->setBold(true);
        $sheet->getStyle('B4')->getNumberFormat()->setFormatCode(self::MONEY);
        $sheet->getStyle('B6')->getNumberFormat()->setFormatCode(self::MONEY);
        $sheet->getStyle('B3:B7')->getAlignment()->setHorizontal('left');

        // Rincian per metode pembayaran
        $sheet->setCellValue('A9', 'Pendapatan per Metode');
        $sheet->getStyle('A9')->getFont()->setBold(true);

        $row = 10;
        foreach ($this->report->byMethod() as $m) {
            $sheet->setCellValue("A{$row}", self::METHOD[$m->payment_method] ?? $m->payment_method);
            $sheet->setCellValue("B{$row}", (int) $m->revenue);
            $sheet->setCellValue("C{$row}", (int) $m->trx . ' transaksi');
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode(self::MONEY);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal('left');
            $row++;
        }

        $this->autosize($sheet, ['A', 'B', 'C']);
    }

    private function dailySheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Per Hari');
        $sheet->fromArray(['Tanggal', 'Jumlah Transaksi', 'Pendapatan'], null, 'A1');
        $this->header($sheet, 'A1:C1');

        $rows = $this->report->daily()->sortBy('day')->values()->map(fn ($r) => [
            \Illuminate\Support\Carbon::parse($r->day)->format('d/m/Y'),
            (int) $r->trx,
            (int) $r->revenue,
        ])->all();

        if ($rows) {
            $sheet->fromArray($rows, null, 'A2');
            $last = count($rows) + 1;

            $sheet->setCellValue('A' . ($last + 1), 'TOTAL');
            $sheet->setCellValue('B' . ($last + 1), "=SUM(B2:B{$last})");
            $sheet->setCellValue('C' . ($last + 1), "=SUM(C2:C{$last})");
            $sheet->getStyle('A' . ($last + 1) . ':C' . ($last + 1))->getFont()->setBold(true);
            $sheet->getStyle('C2:C' . ($last + 1))->getNumberFormat()->setFormatCode(self::MONEY);
        }

        $this->autosize($sheet, ['A', 'B', 'C']);
    }

    private function menuSheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Menu Terjual');
        $sheet->fromArray(['Menu', 'Jumlah Terjual', 'Nilai Penjualan'], null, 'A1');
        $this->header($sheet, 'A1:C1');

        $rows = $this->report->menus()->map(fn ($m) => [
            $m->name,
            (int) $m->qty,
            (int) $m->revenue,
        ])->all();

        if ($rows) {
            $sheet->fromArray($rows, null, 'A2');
            $sheet->getStyle('C2:C' . (count($rows) + 1))->getNumberFormat()->setFormatCode(self::MONEY);
        }

        $this->autosize($sheet, ['A', 'B', 'C']);
    }

    private function transactionSheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Transaksi');
        $sheet->fromArray(
            ['Invoice', 'Waktu', 'Kasir', 'Meja', 'Metode', 'Status', 'Total', 'Tunai Diterima', 'Kembalian', 'Referensi'],
            null,
            'A1'
        );
        $this->header($sheet, 'A1:J1');

        $rows = $this->report->transactionsQuery()
            ->with(['cashier', 'table'])
            ->orderBy('created_at')
            ->get()
            ->map(fn ($t) => [
                $t->invoice_number,
                $t->created_at->format('d/m/Y H:i'),
                $t->cashier->name,
                $t->table?->table_number ?? 'Takeaway',
                self::METHOD[$t->payment_method] ?? $t->payment_method,
                self::STATUS[$t->status] ?? $t->status,
                $t->total_amount,
                $t->cash_received,
                $t->change_amount,
                $t->payment_reference,
            ])->all();

        if ($rows) {
            $sheet->fromArray($rows, null, 'A2');
            $sheet->getStyle('G2:I' . (count($rows) + 1))->getNumberFormat()->setFormatCode(self::MONEY);
        }

        $this->autosize($sheet, ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J']);
    }

    private function header(Worksheet $sheet, string $range): void
    {
        $style = $sheet->getStyle($range);
        $style->getFont()->setBold(true);
        $style->getFont()->getColor()->setRGB('FFFFFF');
        $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EA580C');
    }

    private function autosize(Worksheet $sheet, array $columns): void
    {
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}