<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', config('app.name')); ?> — Secure Portal</title>

    
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #006B3C;      /* UAE Green */
            --primary-dark: #004d2b;
            --primary-light: #e8f5ee;
            --accent: #C8973A;       /* Gold */
            --accent-light: #fdf6e7;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --warning: #d97706;
            --warning-light: #fffbeb;
            --success: #059669;
            --success-light: #ecfdf5;
            --info: #0284c7;
            --info-light: #e0f2fe;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -2px rgba(0,0,0,.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,.1), 0 8px 10px -6px rgba(0,0,0,.1);
            --radius: 10px;
            --radius-sm: 6px;
            --radius-lg: 16px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            min-height: 100vh;
            font-size: 15px;
            line-height: 1.6;
        }

        a { color: var(--primary); text-decoration: none; }
        a:hover { text-decoration: underline; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px 22px; border-radius: var(--radius-sm); font-size: 15px;
            font-weight: 600; cursor: pointer; border: none; transition: all .2s;
            letter-spacing: .01em;
        }
        .btn-primary {
            background: var(--primary); color: var(--white);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: var(--shadow); }
        .btn-primary:active { transform: translateY(0); }
        .btn-outline {
            background: transparent; color: var(--primary);
            border: 2px solid var(--primary);
        }
        .btn-outline:hover { background: var(--primary-light); }
        .btn-block { width: 100%; }
        .btn:disabled { opacity: .6; cursor: not-allowed; transform: none !important; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 500; margin-bottom: 7px; color: var(--gray-700); font-size: 14px; }
        .form-control {
            width: 100%; padding: 11px 14px; border: 1.5px solid var(--gray-300);
            border-radius: var(--radius-sm); font-size: 15px; color: var(--gray-800);
            background: var(--white); transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0,107,60,.12);
        }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { color: var(--danger); font-size: 13px; margin-top: 5px; }

        .alert {
            padding: 13px 16px; border-radius: var(--radius-sm); font-size: 14px;
            margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px;
        }
        .alert-success { background: var(--success-light); color: #065f46; border-left: 4px solid var(--success); }
        .alert-danger  { background: var(--danger-light);  color: #991b1b; border-left: 4px solid var(--danger); }
        .alert-warning { background: var(--warning-light); color: #92400e; border-left: 4px solid var(--warning); }
        .alert-info    { background: var(--info-light);    color: #075985; border-left: 4px solid var(--info); }

        .badge {
            display: inline-flex; align-items: center; padding: 3px 10px;
            border-radius: 99px; font-size: 12px; font-weight: 600; letter-spacing: .03em;
        }
        .badge-success { background: var(--success-light); color: var(--success); }
        .badge-danger  { background: var(--danger-light);  color: var(--danger); }
        .badge-warning { background: var(--warning-light); color: var(--warning); }
        .badge-info    { background: var(--info-light);    color: var(--info); }
        .badge-gray    { background: var(--gray-100);      color: var(--gray-600); }

        /* Navigation */
        .navbar {
            background: var(--white); border-bottom: 1px solid var(--gray-200);
            padding: 0 24px; height: 64px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 100;
            box-shadow: var(--shadow-sm);
        }
        .navbar-brand {
            display: flex; align-items: center; gap: 12px; font-weight: 700;
            font-size: 17px; color: var(--gray-900); text-decoration: none;
        }
        .navbar-brand .logo-icon {
            width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 16px;
        }
        .navbar-nav { display: flex; align-items: center; gap: 8px; }
        .nav-user {
            display: flex; align-items: center; gap: 10px; padding: 6px 12px;
            border-radius: var(--radius-sm); background: var(--gray-50); font-size: 14px;
        }
        .nav-user .avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--primary); color: white; display: flex;
            align-items: center; justify-content: center; font-size: 12px; font-weight: 700;
        }

        /* Page layout */
        .page-container { max-width: 1200px; margin: 0 auto; padding: 32px 24px; }
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 24px; font-weight: 700; color: var(--gray-900); }
        .page-subtitle { color: var(--gray-500); margin-top: 4px; }

        /* Cards */
        .card {
            background: var(--white); border-radius: var(--radius);
            border: 1px solid var(--gray-200); overflow: hidden;
        }
        .card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--gray-100);
            font-weight: 600; font-size: 15px; color: var(--gray-800);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-body { padding: 20px; }

        /* Table */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .05em; color: var(--gray-500);
            background: var(--gray-50); border-bottom: 1px solid var(--gray-200);
        }
        tbody td {
            padding: 12px 14px; border-bottom: 1px solid var(--gray-100);
            font-size: 14px; color: var(--gray-700);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--gray-50); }

        /* Utility */
        .text-muted { color: var(--gray-500); }
        .text-sm    { font-size: 13px; }
        .mt-1 { margin-top: 6px; }
        .mt-2 { margin-top: 12px; }
        .mt-3 { margin-top: 20px; }
        .mb-0 { margin-bottom: 0; }

        /* Spinner */
        .spinner {
            width: 18px; height: 18px; border: 2px solid rgba(255,255,255,.3);
            border-top-color: white; border-radius: 50%;
            animation: spin .7s linear infinite; display: none;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Session timeout warning */
        #timeout-warning {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            background: var(--warning-light); border: 1px solid var(--warning);
            border-radius: var(--radius); padding: 16px 20px; max-width: 320px;
            box-shadow: var(--shadow-xl); display: none;
        }
        #timeout-warning h4 { font-size: 14px; font-weight: 600; color: #92400e; margin-bottom: 8px; }
        #timeout-warning p  { font-size: 13px; color: #92400e; margin-bottom: 12px; }
        #timeout-warning .countdown { font-size: 28px; font-weight: 700; color: var(--warning); }

        @media (max-width: 640px) {
            .page-container { padding: 16px; }
            .navbar { padding: 0 16px; }
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<?php if(auth()->guard()->check()): ?>
<nav class="navbar">
        <a href="<?php echo e(route('dashboard')); ?>" class="navbar-brand">  <img src="<?php echo e(asset('storage/axis-logo.png')); ?>" alt="Secure" title="Secure Chat" width="" height="60">
    </a>
    <div class="navbar-nav">
        <div class="nav-user">
            <div class="avatar"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
            <span><?php echo e(auth()->user()->name); ?></span>
            <span class="badge badge-info"><?php echo e(ucfirst(auth()->user()->role)); ?></span>
        </div>
        <form method="POST" action="<?php echo e(route('auth.logout')); ?>" style="margin:0">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline" style="padding:7px 14px;font-size:13px;">
                🔒 Logout
            </button>
        </form>
    </div>
</nav>


<div id="timeout-warning">
    <h4>⚠️ Session Expiring Soon</h4>
    <p>Your session will expire due to inactivity in:</p>
    <div class="countdown" id="countdown-timer">2:00</div>
    <button onclick="resetTimer()" class="btn btn-primary btn-block mt-2" style="margin-top:12px;">
        Stay Logged In
    </button>
</div>
<?php endif; ?>

<main>
    <?php echo $__env->yieldContent('content'); ?>
</main>

<script>
// ── Inactivity timeout warning ─────────────────────────────────────────────
<?php if(auth()->guard()->check()): ?>
(function() {
    const TIMEOUT_MS    = <?php echo e(config('magic_link.inactivity_timeout_minutes', 15) * 60 * 1000); ?>;
    const WARNING_MS    = 2 * 60 * 1000; // Show warning 2 min before
    const WARN_AFTER    = TIMEOUT_MS - WARNING_MS;
    const warning       = document.getElementById('timeout-warning');
    const countdownEl   = document.getElementById('countdown-timer');

    let warningTimer, countdownInterval, expiryTime;

    function formatTime(ms) {
        const s = Math.ceil(ms / 1000);
        return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
    }

    function startCountdown() {
        expiryTime = Date.now() + WARNING_MS;
        warning.style.display = 'block';
        countdownInterval = setInterval(function() {
            const remaining = expiryTime - Date.now();
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                window.location.href = '<?php echo e(route('auth.logout')); ?>';
                return;
            }
            countdownEl.textContent = formatTime(remaining);
        }, 1000);
    }

    function resetTimer() {
        clearTimeout(warningTimer);
        clearInterval(countdownInterval);
        warning.style.display = 'none';
        warningTimer = setTimeout(startCountdown, WARN_AFTER);
        // Ping server to reset session
        fetch('<?php echo e(route('dashboard')); ?>', { method: 'HEAD', credentials: 'same-origin' });
    }

    window.resetTimer = resetTimer;

    // Watch for activity
    ['mousemove','keydown','click','scroll','touchstart'].forEach(function(event) {
        document.addEventListener(event, resetTimer, { passive: true });
    });

    resetTimer(); // Start
})();
<?php endif; ?>

// ── Spinner on form submit ──────────────────────────────────────────────────
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function() {
        var btn = form.querySelector('[type=submit]');
        var spinner = form.querySelector('.spinner');
        if (btn) btn.disabled = true;
        if (spinner) spinner.style.display = 'block';
    });
});
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/sangharshsulke/Axis Data/communication-dash/resources/views/layouts/app.blade.php ENDPATH**/ ?>