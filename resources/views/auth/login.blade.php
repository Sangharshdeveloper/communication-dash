@extends('layouts.app')

@section('title', 'Secure Login')

@push('styles')
<style>
    body { background: linear-gradient(135deg, #f0fdf4 0%, #e8f5ee 50%, #f0f9ff 100%); }

    .auth-wrapper {
        min-height: 100vh; display: flex; align-items: center;
        justify-content: center; padding: 24px;
    }
    .auth-card {
        background: white; border-radius: 20px; box-shadow: var(--shadow-xl);
        width: 100%; max-width: 440px; overflow: hidden;
        border: 1px solid rgba(0,107,60,.1);
    }
    .auth-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 36px 36px 28px; text-align: center; color: white;
        position: relative; overflow: hidden;
    }
    .auth-header::before {
        content: ''; position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .auth-logo {
        width: 64px; height: 64px; background: rgba(255,255,255,.15);
        border-radius: 16px; display: flex; align-items: center;
        justify-content: center; margin: 0 auto 16px; font-size: 30px;
        backdrop-filter: blur(10px);
    }
    .auth-header h1 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
    .auth-header p  { font-size: 14px; opacity: .85; }

    .auth-body { padding: 32px 36px 36px; }

    .security-badge {
        display: flex; align-items: center; gap: 8px; padding: 10px 14px;
        background: var(--primary-light); border-radius: var(--radius-sm);
        margin-bottom: 24px; font-size: 13px; color: var(--primary);
        border: 1px solid rgba(0,107,60,.15);
    }

    .divider {
        text-align: center; margin: 20px 0; position: relative;
        font-size: 13px; color: var(--gray-400);
    }
    .divider::before {
        content: ''; position: absolute; top: 50%; left: 0; right: 0;
        height: 1px; background: var(--gray-200);
    }
    .divider span { background: white; padding: 0 12px; position: relative; }

    .cbuae-footer {
        text-align: center; padding: 16px; border-top: 1px solid var(--gray-100);
        font-size: 12px; color: var(--gray-400);
        background: var(--gray-50);
    }
    .cbuae-footer strong { color: var(--gray-600); }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <div class="auth-logo">🏛️</div>
            <h1>{{ config('app.name') }}</h1>
            <p>AXIS INSURANCE</p>
        </div>

        <div class="auth-body">

            {{-- Alerts --}}
            @if (session('success'))
                <div class="alert alert-success">
                    <span>✅</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning">
                    <span>⚠️</span>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif

            @if ($errors->has('link'))
                <div class="alert alert-danger">
                    <span>🔗</span>
                    <div>
                        {{ $errors->first('link') }}
                        <a href="{{ route('login') }}" style="display:block;margin-top:6px;font-weight:600;">
                            Request a new magic link →
                        </a>
                    </div>
                </div>
            @endif

            @if ($errors->any() && !$errors->has('link'))
                <div class="alert alert-danger">
                    <span>❌</span>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            {{-- Security badge --}}
            <div class="security-badge">
                <span>🔐</span>
                <span>Passwordless secure login — we'll email you a one-time link</span>
            </div>

            {{-- Login form --}}
            @unless (session('success'))
            <form method="POST" action="{{ route('auth.magic.request') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="you@company.ae"
                        autocomplete="email"
                        autofocus
                        required
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <div class="spinner"></div>
                    <span>Send Secure Login Link</span>
                    <span>→</span>
                </button>
            </form>
            @endunless

            <div class="divider"><span>How it works</span></div>

            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach ([
                    ['1', 'Enter your registered email address above'],
                    ['2', 'Check your inbox for a secure login link'],
                    ['3', 'Click the link to access your account instantly'],
                ] as [$step, $text])
                <div style="display:flex;align-items:center;gap:12px;font-size:13px;color:var(--gray-600)">
                    <div style="width:26px;height:26px;border-radius:50%;background:var(--primary-light);
                        color:var(--primary);display:flex;align-items:center;justify-content:center;
                        font-weight:700;font-size:12px;flex-shrink:0">{{ $step }}</div>
                    {{ $text }}
                </div>
                @endforeach
            </div>

        </div>

        <div class="cbuae-footer">
            Regulated by <strong>Central Bank of UAE (CBUAE)</strong> ·
            <strong>256-bit TLS</strong> encrypted ·
            Data stored in <strong>UAE</strong>
        </div>

    </div>
</div>
@endsection
