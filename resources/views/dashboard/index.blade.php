@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
        gap: 16px; margin-bottom: 28px;
    }
    .stat-card {
        background: white; border-radius: var(--radius); padding: 20px;
        border: 1px solid var(--gray-200); display: flex; align-items: center; gap: 16px;
    }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px; display: flex;
        align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
    }
    .stat-icon.green  { background: var(--success-light); }
    .stat-icon.blue   { background: var(--info-light); }
    .stat-icon.gold   { background: var(--accent-light); }
    .stat-icon.red    { background: var(--danger-light); }
    .stat-value { font-size: 26px; font-weight: 700; color: var(--gray-900); line-height: 1.1; }
    .stat-label { font-size: 13px; color: var(--gray-500); margin-top: 2px; }

    .grid-2 { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }
    @media(max-width:900px){ .grid-2 { grid-template-columns: 1fr; } }

    .security-status {
        display: flex; flex-direction: column; gap: 12px;
    }
    .security-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 0; border-bottom: 1px solid var(--gray-100); font-size: 14px;
    }
    .security-item:last-child { border-bottom: none; }
    .security-item .label { color: var(--gray-600); }
    .security-item .value { font-weight: 600; color: var(--gray-900); }
</style>
@endpush

@section('content')
<div class="page-container">

    <div class="page-header">
        <h1 class="page-title">Welcome, {{ $user->name }} 👋</h1>
        <p class="page-subtitle">
            Logged in securely ·
            Role: <strong>{{ ucfirst($user->role) }}</strong> ·
            Last login: {{ $user->last_login_at?->diffForHumans() ?? 'First login' }}
        </p>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div>
                <div class="stat-value">Secure</div>
                <div class="stat-label">Session Status</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">🔐</div>
            <div>
                <div class="stat-value">Magic Link</div>
                <div class="stat-label">Auth Method</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon gold">🕐</div>
            <div>
                <div class="stat-value">{{ config('magic_link.inactivity_timeout_minutes') }}m</div>
                <div class="stat-label">Session Timeout</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">🇦🇪</div>
            <div>
                <div class="stat-value">UAE</div>
                <div class="stat-label">Data Residency</div>
            </div>
        </div>
    </div>

    <div class="grid-2">

        {{-- Recent Audit Logs --}}
        <div class="card">
            <div class="card-header">
                📋 Recent Activity
                @if ($user->isAdmin())
                    <a href="{{ route('admin.audit-logs') }}" style="font-size:13px;font-weight:400">
                        View all →
                    </a>
                @endif
            </div>
            <div class="table-container">
                @if ($auditLogs->isEmpty())
                    <div style="padding:32px;text-align:center;color:var(--gray-400);">
                        No activity recorded yet.
                    </div>
                @else
                <table>
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($auditLogs as $log)
                        <tr>
                            <td>
                                <span style="font-family:monospace;font-size:12px;background:var(--gray-100);
                                    padding:2px 7px;border-radius:4px;">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                @if ($log->status === 'success')
                                    <span class="badge badge-success">✓ Success</span>
                                @elseif ($log->status === 'failure')
                                    <span class="badge badge-danger">✗ Failed</span>
                                @else
                                    <span class="badge badge-warning">⚠ Warning</span>
                                @endif
                            </td>
                            <td class="text-muted text-sm">{{ $log->ip_address }}</td>
                            <td class="text-muted text-sm">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        {{-- Security Status --}}
        <div class="card">
            <div class="card-header">🛡️ Security Status</div>
            <div class="card-body">
                <div class="security-status">
                    <div class="security-item">
                        <span class="label">HTTPS Enforced</span>
                        <span class="value" style="color:var(--success)">✅ Yes</span>
                    </div>
                    <div class="security-item">
                        <span class="label">Session Encrypted</span>
                        <span class="value" style="color:var(--success)">✅ Yes</span>
                    </div>
                    <div class="security-item">
                        <span class="label">Auth Method</span>
                        <span class="value">Passwordless</span>
                    </div>
                    <div class="security-item">
                        <span class="label">Token Storage</span>
                        <span class="value">SHA-256 Hash Only</span>
                    </div>
                    <div class="security-item">
                        <span class="label">Auto Logout</span>
                        <span class="value">{{ config('magic_link.inactivity_timeout_minutes') }} min</span>
                    </div>
                    <div class="security-item">
                        <span class="label">Rate Limiting</span>
                        <span class="value" style="color:var(--success)">✅ Active</span>
                    </div>
                    <div class="security-item">
                        <span class="label">Audit Logging</span>
                        <span class="value" style="color:var(--success)">✅ Active</span>
                    </div>
                    <div class="security-item">
                        <span class="label">CBUAE Compliant</span>
                        <span class="value" style="color:var(--success)">✅ Yes</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Admin links --}}
    @if ($user->isAdmin())
    <div class="card" style="margin-top:20px;">
        <div class="card-header">⚙️ Administration</div>
        <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('admin.audit-logs') }}" class="btn btn-primary">
                📋 Full Audit Logs
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-outline">
                👥 Manage Users
            </a>
        </div>
    </div>
    @endif

</div>
@endsection
