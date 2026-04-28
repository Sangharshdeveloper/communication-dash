<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Role-based landing page.
     * - Admin      → User management
     * - Agent      → Customer inbox
     * - Auditor    → Audit logs
     * - Customer   → Dashboard (for profile/info)
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.users');

            case 'agent':
                return redirect()->route('direct-chat.agent.inbox');

            case 'auditor':
                return redirect()->route('admin.audit-logs');

            case 'customer':
            default:
                return view('dashboard.index', [
                    'user'      => $user,
                    'auditLogs' => $user->auditLogs()->latest()->limit(10)->get(),
                ]);
        }
    }
}