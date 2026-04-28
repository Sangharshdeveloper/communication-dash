@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="page-container">

    <div class="page-header">
        <h1 class="page-title">📋 Audit Logs</h1>
        <p class="page-subtitle">CBUAE compliance audit trail — all actions logged with IP and timestamp</p>
    </div>

    {{-- Filters --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
                <div style="flex:1;min-width:160px;">
                    <label class="form-label">Action</label>
                    <select name="action" class="form-control">
                        <option value="">All Actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="flex:1;min-width:140px;">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="success" @selected(request('status') === 'success')>Success</option>
                        <option value="failure" @selected(request('status') === 'failure')>Failure</option>
                        <option value="warning" @selected(request('status') === 'warning')>Warning</option>
                    </select>
                </div>
                <div style="flex:2;min-width:180px;">
                    <label class="form-label">Email</label>
                    <input type="text" name="email" class="form-control"
                        placeholder="user@example.ae" value="{{ request('email') }}">
                </div>
                <div style="flex:1;min-width:140px;">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div style="flex:1;min-width:140px;">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.audit-logs') }}" class="btn btn-outline" style="margin-left:8px;">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Showing {{ $logs->total() }} records
            <span class="text-sm text-muted">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>User / Email</th>
                        <th>IP Address</th>
                        <th>Description</th>
                        <th>Timestamp (GST)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                    <tr>
                        <td class="text-muted text-sm">{{ $log->id }}</td>
                        <td>
                            <span style="font-family:monospace;font-size:12px;
                                background:var(--gray-100);padding:2px 7px;border-radius:4px;">
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
                        <td class="text-sm">
                            @if ($log->user)
                                <div style="font-weight:500">{{ $log->user->name }}</div>
                            @endif
                            <div class="text-muted">{{ $log->email }}</div>
                        </td>
                        <td class="text-sm text-muted">{{ $log->ip_address }}</td>
                        <td class="text-sm text-muted" style="max-width:240px;">
                            {{ Str::limit($log->description, 80) }}
                        </td>
                        <td class="text-sm text-muted" style="white-space:nowrap;">
                            {{ $log->created_at->setTimezone('Asia/Dubai')->format('d M Y H:i:s') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:32px;color:var(--gray-400);">
                            No audit logs found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($logs->hasPages())
        <div style="padding:16px 20px;border-top:1px solid var(--gray-100);">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
