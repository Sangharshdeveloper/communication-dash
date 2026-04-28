<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Secure Login Link</title>
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
  .container { max-width: 560px; margin: 0 auto; }
  .card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .header { background: linear-gradient(135deg, #006B3C, #004d2b); padding: 32px; text-align: center; color: #fff; }
  .header h1 { font-size: 20px; margin: 0 0 4px; }
  .header p { font-size: 13px; opacity: .8; margin: 0; }
  .logo { font-size: 40px; margin-bottom: 16px; }
  .body { padding: 32px; }
  .body p { color: #374151; line-height: 1.6; font-size: 15px; margin: 0 0 16px; }
  .btn-wrap { text-align: center; margin: 28px 0; }
  .btn {
    display: inline-block; background: #006B3C; color: #fff !important;
    text-decoration: none; padding: 14px 36px; border-radius: 8px;
    font-size: 16px; font-weight: 700; letter-spacing: .02em;
  }
  .meta-box {
    background: #f9fafb; border-radius: 8px; padding: 16px;
    border: 1px solid #e5e7eb; margin: 20px 0; font-size: 13px; color: #6b7280;
  }
  .meta-box strong { color: #374151; }
  .url-fallback {
    word-break: break-all; font-size: 12px; color: #6b7280;
    background: #f3f4f6; border-radius: 6px; padding: 10px 12px;
    border: 1px solid #e5e7eb; font-family: monospace; margin-top: 16px;
  }
  .warning { color: #dc2626; font-weight: 600; }
  .footer { background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 32px; text-align: center; font-size: 12px; color: #9ca3af; }
  .footer a { color: #6b7280; }
</style>
</head>
<body>
<div class="container">
  <div class="card">
    <div class="header">
      <div class="logo">🏛️</div>
      <h1>{{ config('app.name') }}</h1>
      <p>CBUAE Licensed Insurance Platform</p>
    </div>
    <div class="body">
      <p>Hello <strong>{{ $user->name }}</strong>,</p>
      <p>
        You requested a secure login link for your account.
        Click the button below to sign in instantly — no password required.
      </p>

      <div class="btn-wrap">
        <a href="{{ $magicUrl }}" class="btn">
          🔐 &nbsp; Sign In Securely
        </a>
      </div>

      <div class="meta-box">
        <p style="margin:0 0 8px;"><strong>⏱ This link expires in {{ $expiryMinutes }} minutes</strong></p>
        <p style="margin:0 0 4px;">🔒 One-time use only — it becomes invalid after clicking</p>
        <p style="margin:0;">📍 Requested at: {{ now()->setTimezone('Asia/Dubai')->format('D, d M Y H:i:s') }} GST</p>
      </div>

      <p>If the button doesn't work, copy and paste this URL into your browser:</p>
      <div class="url-fallback">{{ $magicUrl }}</div>

      <p class="warning" style="margin-top:20px;">
        ⚠️ If you did NOT request this link, please ignore this email and contact support immediately.
        Your account has not been accessed.
      </p>
    </div>
    <div class="footer">
      <p>This is an automated security email from <strong>{{ config('app.name') }}</strong></p>
      <p>Regulated by the <strong>Central Bank of UAE (CBUAE)</strong></p>
      <p>Do not reply to this email · <a href="{{ config('app.url') }}">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</a></p>
    </div>
  </div>
</div>
</body>
</html>
