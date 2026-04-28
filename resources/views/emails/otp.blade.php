<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Security Verification Code</title>
<style>
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #fffbeb; margin: 0; padding: 20px; }
  .container { max-width: 480px; margin: 0 auto; }
  .card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
  .header { background: linear-gradient(135deg, #d97706, #b45309); padding: 28px; text-align: center; color: #fff; }
  .header h1 { font-size: 18px; margin: 0; }
  .body { padding: 32px; text-align: center; }
  .otp-display {
    font-size: 48px; font-weight: 800; letter-spacing: 12px;
    color: #111827; margin: 24px 0; font-family: 'Courier New', monospace;
    background: #f3f4f6; border-radius: 10px; padding: 20px;
    border: 2px dashed #d1d5db;
  }
  .body p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 12px; }
  .footer { background: #fef3c7; border-top: 1px solid #fde68a; padding: 16px 32px; text-align: center; font-size: 12px; color: #92400e; }
</style>
</head>
<body>
<div class="container">
  <div class="card">
    <div class="header">
      <div style="font-size:36px;margin-bottom:10px">🛡️</div>
      <h1>Security Verification Code</h1>
      <p style="opacity:.85;font-size:13px;margin:4px 0 0">{{ config('app.name') }}</p>
    </div>
    <div class="body">
      <p>Hello <strong>{{ $user->name }}</strong>,</p>
      <p>
        A login attempt was detected from an unusual location or device.
        Enter this code to verify your identity:
      </p>
      <div class="otp-display">{{ $otp }}</div>
      <p><strong>⏱ This code expires in 5 minutes.</strong></p>
      <p>🔒 Do not share this code with anyone.</p>
      <p style="color:#dc2626;font-weight:600;margin-top:16px;">
        If you did not attempt to log in, please contact support immediately.
      </p>
    </div>
    <div class="footer">
      Regulated by <strong>Central Bank of UAE (CBUAE)</strong> ·
      This is an automated security email — do not reply.
    </div>
  </div>
</div>
</body>
</html>
