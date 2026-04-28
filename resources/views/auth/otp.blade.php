@extends('layouts.app')

@section('title', 'Verify Your Identity')

@push('styles')
<style>
    body { background: linear-gradient(135deg, #fffbeb 0%, #fdf6e7 100%); }
    .auth-wrapper {
        min-height: 100vh; display: flex; align-items: center;
        justify-content: center; padding: 24px;
    }
    .auth-card {
        background: white; border-radius: 20px; box-shadow: var(--shadow-xl);
        width: 100%; max-width: 420px; overflow: hidden;
    }
    .auth-header {
        background: linear-gradient(135deg, #d97706, #b45309);
        padding: 32px 36px 24px; text-align: center; color: white;
    }
    .auth-header .icon { font-size: 48px; margin-bottom: 12px; }
    .auth-header h1 { font-size: 20px; font-weight: 700; }
    .auth-header p  { font-size: 13px; opacity: .85; margin-top: 6px; }
    .auth-body { padding: 32px 36px; }

    .otp-inputs {
        display: flex; gap: 10px; justify-content: center; margin: 24px 0;
    }
    .otp-input {
        width: 52px; height: 60px; text-align: center; font-size: 24px; font-weight: 700;
        border: 2px solid var(--gray-300); border-radius: var(--radius-sm);
        color: var(--gray-900); transition: border-color .15s, box-shadow .15s;
    }
    .otp-input:focus {
        border-color: var(--warning); outline: none;
        box-shadow: 0 0 0 3px rgba(217,119,6,.15);
    }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="icon">🛡️</div>
            <h1>Identity Verification</h1>
            <p>Suspicious activity detected. Enter the 6-digit code sent to your email.</p>
        </div>
        <div class="auth-body">

            @if (session('info'))
                <div class="alert alert-warning">
                    <span>⚠️</span><div>{{ session('info') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <span>❌</span><div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.otp.verify') }}" id="otp-form">
                @csrf

                <p style="font-size:14px;color:var(--gray-600);text-align:center;margin-bottom:8px;">
                    Enter the 6-digit verification code
                </p>

                {{-- Visual OTP inputs (JS-assembled into hidden field) --}}
                <div class="otp-inputs">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="tel" maxlength="1" class="otp-input" pattern="[0-9]"
                               inputmode="numeric" autocomplete="one-time-code">
                    @endfor
                </div>

                <input type="hidden" name="otp" id="otp-hidden">

                <button type="submit" class="btn btn-primary btn-block" id="submit-btn" disabled>
                    <div class="spinner"></div>
                    <span>Verify Identity</span>
                </button>
            </form>

            <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--gray-500)">
                Didn't receive the code?
                <a href="{{ route('login') }}">Request a new magic link</a>
            </p>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const inputs = document.querySelectorAll('.otp-input');
const hidden = document.getElementById('otp-hidden');
const btn    = document.getElementById('submit-btn');

function assembleOtp() {
    const val = Array.from(inputs).map(i => i.value).join('');
    hidden.value = val;
    btn.disabled = val.length !== 6;
}

inputs.forEach(function(input, idx) {
    input.addEventListener('input', function(e) {
        const v = e.target.value.replace(/\D/g,'');
        e.target.value = v.slice(-1);
        if (v && idx < inputs.length - 1) inputs[idx+1].focus();
        assembleOtp();
    });
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !input.value && idx > 0) {
            inputs[idx-1].focus();
        }
    });
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'');
        pasted.split('').slice(0,6).forEach(function(char, i) {
            if (inputs[i]) inputs[i].value = char;
        });
        assembleOtp();
        const next = pasted.length < 6 ? inputs[pasted.length] : inputs[5];
        if (next) next.focus();
    });
});

inputs[0].focus();
</script>
@endpush
