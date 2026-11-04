<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Support\SalesReport;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $report = $this->report($request);

        return view('admin.dashboard', [
            'from'         => $report->from,
            'to'           => $report->to,
            'summary'      => $report->summary(),
            'methods'      => $report->byMethod(),
            'daily'        => $report->daily(),
            'topMenus'     => $report->menus(10),
            'transactions' => $report->transactionsQuery()
                ->with(['cashier', 'table'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function export(Request $request)
    {
        $report      = $this->report($request);
        $spreadsheet = (new SalesReportExport($report))->build();

        $filename = sprintf(
            'laporan-pendapatan_%s_sd_%s.xlsx',
            $report->from->toDateString(),
            $report->to->toDateString()
        );

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function report(Request $request): SalesReport
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to'   => ['nullable', 'date', 'after_or_equal:from'],
        ], [
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        return SalesReport::make($request->input('from'), $request->input('to'));
    }
}