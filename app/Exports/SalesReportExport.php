<?php

namespace App\Exports;

use App\Support\SalesReport;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport
{
    private const MONEY = '"Rp" #,##0';
    private const COUNT = '#,##0';

    // Warna tema (sama dengan website)
    private const GREEN  = '0F5F46';
    private const ORANGE = 'F58220';
    private const CREAM  = 'F7F3EA';
    private const HAIR   = 'E8E1D2';
    private const MUTED  = '6B7280';

       private const STATUS = [
        'paid'      => 'Antre',
        'cooking'   => 'Dimasak',
        'ready'     => 'Siap Diantar',
        'served'    => 'Sudah Diantar',
        'done'      => 'Selesai',
        'cancelled' => 'Batal',
    ];
    
    // Warna tulisan kolom Status
    private const STATUS_COLOR = [
        'Antre'   => 'B45309',
        'Dimasak' => '1D4ED8',
        'Selesai' => '0B4A37',
        'Batal'   => 'B91C1C',
    ];

    private const METHOD = [
        'cash'  => 'Tunai',
        'qris'  => 'QRIS',
        'debit' => 'Debit',
    ];

    private const LEFT   = Alignment::HORIZONTAL_LEFT;
    private const RIGHT  = Alignment::HORIZONTAL_RIGHT;
    private const CENTER = Alignment::HORIZONTAL_CENTER;

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

    /* ------------------------------------------------------------------ */
    /*  Sheet 1: Ringkasan                                                  */
    /* ------------------------------------------------------------------ */
    private function summarySheet(Worksheet $sheet): void
    {
        $summary = $this->report->summary();

        $sheet->setTitle('Ringkasan');
        $this->basics($sheet, self::GREEN);

        // Judul + keterangan
        $sheet->setCellValue('A1', 'Laporan Pendapatan - Dapur Babeh');
        $sheet->setCellValue('A2', 'Periode: ' . $this->report->from->format('d/m/Y') . ' - ' . $this->report->to->format('d/m/Y'));
        $sheet->setCellValue('A3', 'Dibuat pada: ' . now()->format('d/m/Y H:i'));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18)->getColor()->setRGB(self::GREEN);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10)->getColor()->setRGB(self::MUTED);
        $sheet->getStyle('A3:C3')->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::ORANGE);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Tiga kartu angka utama
        $sheet->fromArray([['TOTAL PENDAPATAN', 'JUMLAH TRANSAKSI', 'RATA-RATA PER TRANSAKSI']], null, 'A5');
        $sheet->fromArray([[$summary['revenue'], $summary['count'], $summary['average']]], null, 'A6');

        $sheet->getStyle('A5:C6')->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::CREAM]],
            'alignment' => ['horizontal' => self::LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 1],
        ]);
        $sheet->getStyle('A5:C5')->getFont()->setSize(9)->setBold(true)->getColor()->setRGB(self::MUTED);
        $sheet->getStyle('A6:C6')->getFont()->setSize(18)->setBold(true)->getColor()->setRGB(self::GREEN);
        $sheet->getStyle('A6')->getNumberFormat()->setFormatCode(self::MONEY);
        $sheet->getStyle('B6')->getNumberFormat()->setFormatCode(self::COUNT);
        $sheet->getStyle('C6')->getNumberFormat()->setFormatCode(self::MONEY);
        $sheet->getStyle('A5:B6')->getBorders()->getRight()
            ->setBorderStyle(Border::BORDER_THICK)->getColor()->setRGB('FFFFFF');
        $sheet->getRowDimension(5)->setRowHeight(24);
        $sheet->getRowDimension(6)->setRowHeight(40);

        // Rincian per metode pembayaran
        $sheet->setCellValue('A8', 'Pendapatan per Metode');
        $sheet->getStyle('A8')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB(self::GREEN);
        $sheet->getRowDimension(8)->setRowHeight(26);

        $sheet->fromArray(['Metode', 'Pendapatan', 'Jumlah Transaksi'], null, 'A9');
        $this->header($sheet, 'A9:C9');

        $row = 10;
        foreach ($this->report->byMethod() as $m) {
            $sheet->setCellValue("A{$row}", self::METHOD[$m->payment_method] ?? $m->payment_method);
            $sheet->setCellValue("B{$row}", (int) $m->revenue);
            $sheet->setCellValue("C{$row}", (int) $m->trx);
            $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode(self::MONEY);
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('#,##0" transaksi"');
            $row++;
        }

        if ($row === 10) {
            $sheet->setCellValue('A10', 'Belum ada transaksi pada periode ini');
            $sheet->getStyle('A10')->getFont()->setItalic(true)->getColor()->setRGB(self::MUTED);
            $sheet->getStyle('A10')->getAlignment()->setIndent(1);
        } else {
            $this->body($sheet, 10, $row - 1, 'C');
            $this->align($sheet, 'A9:A' . ($row - 1), self::LEFT);
            $this->align($sheet, 'B9:C' . ($row - 1), self::RIGHT);
        }

        $this->widths($sheet, ['A' => 30, 'B' => 30, 'C' => 30]);
        $this->page($sheet, false);
    }

    /* ------------------------------------------------------------------ */
    /*  Sheet 2: Per Hari                                                   */
    /* ------------------------------------------------------------------ */
    private function dailySheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Per Hari');
        $this->basics($sheet, self::ORANGE);

        $sheet->fromArray(['Tanggal', 'Jumlah Transaksi', 'Pendapatan'], null, 'A1');
        $this->header($sheet, 'A1:C1');

        $rows = $this->report->daily()->sortBy('day')->values()->map(fn ($r) => [
            Carbon::parse($r->day)->format('d/m/Y'),
            (int) $r->trx,
            (int) $r->revenue,
        ])->all();

        $last = 1;

        if ($rows) {
            $sheet->fromArray($rows, null, 'A2');
            $last  = count($rows) + 1;
            $total = $last + 1;

            $sheet->setCellValue("A{$total}", 'TOTAL');
            $sheet->setCellValue("B{$total}", "=SUM(B2:B{$last})");
            $sheet->setCellValue("C{$total}", "=SUM(C2:C{$last})");

            $this->body($sheet, 2, $total, 'C');
            $this->align($sheet, "A1:A{$total}", self::LEFT);
            $this->align($sheet, "B1:C{$total}", self::RIGHT);

            $sheet->getStyle("B2:B{$total}")->getNumberFormat()->setFormatCode(self::COUNT);
            $sheet->getStyle("C2:C{$total}")->getNumberFormat()->setFormatCode(self::MONEY);

            $sheet->getStyle("A{$total}:C{$total}")->applyFromArray([
                'font'    => ['bold' => true, 'color' => ['rgb' => self::GREEN]],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::GREEN]]],
            ]);
        }

        $sheet->freezePane('A2');
        $this->widths($sheet, ['A' => 18, 'B' => 22, 'C' => 24]);
        $this->page($sheet, true);
    }

    /* ------------------------------------------------------------------ */
    /*  Sheet 3: Menu Terjual                                               */
    /* ------------------------------------------------------------------ */
    private function menuSheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Menu Terjual');
        $this->basics($sheet, self::ORANGE);

        $sheet->fromArray(['Menu', 'Jumlah Terjual', 'Nilai Penjualan'], null, 'A1');
        $this->header($sheet, 'A1:C1');

        $rows = $this->report->menus()->map(fn ($m) => [
            $m->name,
            (int) $m->qty,
            (int) $m->revenue,
        ])->all();

        $nameWidth = 32;

        if ($rows) {
            $sheet->fromArray($rows, null, 'A2');
            $last = count($rows) + 1;

            $this->body($sheet, 2, $last, 'C');
            $this->align($sheet, "A1:A{$last}", self::LEFT);
            $this->align($sheet, "B1:C{$last}", self::RIGHT);

            $sheet->getStyle("B2:B{$last}")->getNumberFormat()->setFormatCode(self::COUNT);
            $sheet->getStyle("C2:C{$last}")->getNumberFormat()->setFormatCode(self::MONEY);

            $longest   = max(array_map(fn ($r) => mb_strlen((string) $r[0]), $rows));
            $nameWidth = max(32, min(55, $longest + 5));
        }

        $sheet->freezePane('A2');
        $this->widths($sheet, ['A' => $nameWidth, 'B' => 20, 'C' => 24]);
        $this->page($sheet, true);
    }

    /* ------------------------------------------------------------------ */
    /*  Sheet 4: Transaksi                                                  */
    /* ------------------------------------------------------------------ */
    private function transactionSheet(Worksheet $sheet): void
    {
        $sheet->setTitle('Transaksi');
        $this->basics($sheet, self::ORANGE);

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
            $last = count($rows) + 1;

            $this->body($sheet, 2, $last, 'J');
            $this->align($sheet, "A1:A{$last}", self::LEFT);
            $this->align($sheet, "B1:B{$last}", self::CENTER);
            $this->align($sheet, "C1:C{$last}", self::LEFT);
            $this->align($sheet, "D1:F{$last}", self::CENTER);
            $this->align($sheet, "G1:I{$last}", self::RIGHT);
            $this->align($sheet, "J1:J{$last}", self::LEFT);

            $sheet->getStyle("G2:I{$last}")->getNumberFormat()->setFormatCode(self::MONEY);

            // Warna tulisan Status
            foreach ($rows as $i => $r) {
                $color = self::STATUS_COLOR[$r[5]] ?? null;

                if ($color) {
                    $sheet->getStyle('F' . ($i + 2))->getFont()->setBold(true)->getColor()->setRGB($color);
                }
            }

            $sheet->setAutoFilter("A1:J{$last}");
        }

        $sheet->freezePane('A2');
        $this->widths($sheet, [
            'A' => 22, 'B' => 18, 'C' => 20, 'D' => 12, 'E' => 12,
            'F' => 12, 'G' => 18, 'H' => 18, 'I' => 16, 'J' => 24,
        ]);
        $this->page($sheet, true);
    }

    /* ------------------------------------------------------------------ */
    /*  Helper tampilan                                                     */
    /* ------------------------------------------------------------------ */

    // Dasar tiap sheet: tanpa garis kisi, baris lega, tab berwarna
    private function basics(Worksheet $sheet, string $tabColor): void
    {
        $sheet->setShowGridlines(false);
        $sheet->getTabColor()->setRGB($tabColor);
        $sheet->getDefaultRowDimension()->setRowHeight(21);
    }

    // Baris judul kolom: latar krem, tulisan hijau tebal, garis hijau di bawah
    private function header(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => self::GREEN]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::CREAM]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true, 'horizontal' => self::LEFT, 'indent' => 1],
            'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::GREEN]]],
        ]);

        $firstRow = (int) preg_replace('/\D/', '', explode(':', $range)[0]);
        $sheet->getRowDimension($firstRow)->setRowHeight(28);
    }

    // Isi tabel: hanya garis tipis pemisah antar baris
    private function body(Worksheet $sheet, int $from, int $to, string $lastCol): void
    {
        if ($to < $from) {
            return;
        }

        $line = ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::HAIR]];

        $sheet->getStyle("A{$from}:{$lastCol}{$to}")->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['horizontal' => $line, 'bottom' => $line],
        ]);
    }

    // Rata kiri/kanan/tengah (indent kiri-kanan agar teks tidak menempel garis)
    private function align(Worksheet $sheet, string $range, string $horizontal): void
    {
        $alignment = $sheet->getStyle($range)->getAlignment();
        $alignment->setHorizontal($horizontal);

        if ($horizontal !== self::CENTER) {
            $alignment->setIndent(1);
        }
    }

    private function widths(Worksheet $sheet, array $widths): void
    {
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
    }

    // Pengaturan cetak: A4, muat satu lebar halaman, judul kolom diulang
    private function page(Worksheet $sheet, bool $landscape): void
    {
        $setup = $sheet->getPageSetup();
        $setup->setOrientation($landscape ? PageSetup::ORIENTATION_LANDSCAPE : PageSetup::ORIENTATION_PORTRAIT);
        $setup->setPaperSize(PageSetup::PAPERSIZE_A4);
        $setup->setFitToPage(true);
        $setup->setFitToWidth(1);
        $setup->setFitToHeight(0);

        if ($landscape) {
            $setup->setRowsToRepeatAtTopByStartAndEnd(1, 1);
        }
    }
}