<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Discount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Read-only audit trail for super admins: who signed in, and who created, changed or deleted what.
 * There is deliberately no way to edit or delete entries from here.
 */
class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = $this->filtered($request)
            ->latest('created_at')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        $admins = User::whereIn('role', ['super_admin', 'admin'])
            ->orderByRaw("role = 'super_admin' desc")
            ->orderBy('name')
            ->get();

        $changeActions = ['created', 'updated', 'deleted'];

        // One grouped query each, keyed by user id, instead of a query per admin card.
        $lastLogin = AuditLog::where('action', 'login')->selectRaw('user_id, max(created_at) as at')->groupBy('user_id')->pluck('at', 'user_id');
        $lastChange = AuditLog::whereIn('action', $changeActions)->selectRaw('user_id, max(created_at) as at')->groupBy('user_id')->pluck('at', 'user_id');
        $weekChanges = AuditLog::whereIn('action', $changeActions)->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('user_id, count(*) as total')->groupBy('user_id')->pluck('total', 'user_id');
        $failedToday = AuditLog::where('action', 'login_failed')->where('created_at', '>=', now()->subDay())->count();

        $types = AuditLog::whereNotNull('subject_type')->distinct()->orderBy('subject_type')->pluck('subject_type');

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'admins' => $admins,
            'lastLogin' => $lastLogin->map(fn ($at) => $this->shopTime($at)),
            'lastChange' => $lastChange->map(fn ($at) => $this->shopTime($at)),
            'weekChanges' => $weekChanges,
            'failedToday' => $failedToday,
            'types' => $types,
            'actions' => AuditLog::ACTIONS,
            'tz' => Discount::DISPLAY_TZ,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = $this->filtered($request)->latest('created_at')->latest('id');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['time (WIB)', 'admin', 'email', 'role', 'action', 'type', 'item', 'description', 'ip']);

            // Capped so an unfiltered export can't exhaust memory or time.
            $query->limit(10000)->cursor()->each(function (AuditLog $log) use ($out) {
                fputcsv($out, [
                    $this->shopTime($log->created_at)->format('Y-m-d H:i:s'),
                    $log->user_name, $log->user_email, $log->user_role,
                    $log->action_label, $log->subject_type, $log->subject_label,
                    $log->description, $log->ip_address,
                ]);
            });

            fclose($out);
        }, 'audit-log-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function filtered(Request $request): Builder
    {
        $tz = Discount::DISPLAY_TZ;

        return AuditLog::query()
            ->when($request->filled('admin'), fn ($q) => $q->where('user_id', (int) $request->input('admin')))
            ->when($request->filled('action') && isset(AuditLog::ACTIONS[$request->input('action')]), fn ($q) => $q->where('action', $request->input('action')))
            ->when($request->filled('type'), fn ($q) => $q->where('subject_type', $request->input('type')))
            ->when($this->validDate($request->input('from')), fn ($q) => $q->where('created_at', '>=', Carbon::parse($request->input('from'), $tz)->startOfDay()->utc()))
            ->when($this->validDate($request->input('to')), fn ($q) => $q->where('created_at', '<=', Carbon::parse($request->input('to'), $tz)->endOfDay()->utc()))
            ->when(trim((string) $request->input('q')) !== '', function ($q) use ($request) {
                $term = '%'.addcslashes(trim($request->input('q')), '%_').'%';
                $q->where(fn ($w) => $w->where('description', 'like', $term)
                    ->orWhere('subject_label', 'like', $term)
                    ->orWhere('user_email', 'like', $term)
                    ->orWhere('user_name', 'like', $term)
                    ->orWhere('ip_address', 'like', $term));
            });
    }

    private function validDate(mixed $value): bool
    {
        return is_string($value) && (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
    }

    /** Rows are stored in UTC; admins read them in shop time. */
    private function shopTime(mixed $value): Carbon
    {
        return Carbon::parse($value, config('app.timezone'))->timezone(Discount::DISPLAY_TZ);
    }
}
