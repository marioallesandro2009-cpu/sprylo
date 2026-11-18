<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FinancialReport;
use App\Services\FinancialReportExporter;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FinancialReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = $this->report($request);

        return view('admin.reports.finance', [
            'report' => $report,
            'orders' => $report->query()->paginate(25)->withQueryString(),
        ]);
    }

    public function download(Request $request, FinancialReportExporter $exporter, string $format): BinaryFileResponse
    {
        $report = $this->report($request);

        $path = match ($format) {
            'xlsx' => $exporter->excel($report),
            'pdf' => $exporter->pdf($report),
        };

        return response()->download($path, $report->filename($format))->deleteFileAfterSend();
    }

    private function report(Request $request): FinancialReport
    {
        $data = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ], [
            'from.date_format' => 'Tanggal awal tidak valid.',
            'to.date_format' => 'Tanggal akhir tidak valid.',
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $today = CarbonImmutable::now(FinancialReport::TIMEZONE);
        $report = FinancialReport::between(
            $data['from'] ?? $today->startOfMonth()->toDateString(),
            $data['to'] ?? $today->toDateString(),
        );

        if ($report->from->gt($report->to)) {
            throw ValidationException::withMessages(['to' => 'Tanggal akhir tidak boleh sebelum tanggal awal.']);
        }

        if ($report->from->diffInDays($report->to) > FinancialReport::MAX_DAYS) {
            throw ValidationException::withMessages(['to' => 'Satu laporan paling panjang '.FinancialReport::MAX_DAYS.' hari.']);
        }

        return $report;
    }
}
