<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = AuditLog::with('user')
            ->when($request->action,    fn ($q) => $q->where('action', $request->action))
            ->when($request->status,    fn ($q) => $q->where('status', $request->status))
            ->when($request->email,     fn ($q) => $q->where('email', 'like', '%' . $request->email . '%'))
            ->when($request->ip,        fn ($q) => $q->where('ip_address', $request->ip))
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(50);

        $actions = AuditLog::distinct()->pluck('action');

        return view('admin.audit-logs', compact('logs', 'actions'));
    }
}
