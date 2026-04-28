<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — Axis Communication Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  min-height:100vh;
  background:#000000;
  display:flex;align-items:center;justify-content:center;
  padding:20px;
  position:relative;overflow:hidden;
}
body::before{
  content:'';position:absolute;inset:0;
  background:
   radial-gradient(ellipse 80% 60% at 20% 20%, rgba(196,163,102,.25) 0%, transparent 60%),
    radial-gradient(ellipse 60% 80% at 80% 80%, rgba(196,163,102,.2) 0%, transparent 60%);
}
body::after{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),
    linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);
  background-size:40px 40px;
}

.card{
  position:relative;z-index:1;
  width:100%;max-width:420px;
  background:rgba(255,255,255,.04);
  border:1px solid rgba(255,255,255,.1);
  border-radius:20px;
  backdrop-filter:blur(20px);
  overflow:hidden;
  box-shadow:0 25px 50px rgba(234, 224, 224, 0.5);
}
.card-top{height:4px;background:linear-gradient(90deg,#c4a366,#d6b77a,#a8894f);}
.card-body{padding:36px 36px 32px}

.logo-wrap{
  justify-content: center;display:flex;align-items:center;gap:12px;margin-bottom:28px}
.logo-icon{
  width:48px;height:48px;border-radius:12px;
  background:linear-gradient(135deg,#c4a366,#d6b77a);
  display:flex;align-items:center;justify-content:center;
  font-size:22px;flex-shrink:0;
  box-shadow:0 4px 12px rgba(0,107,60,.4);
}
.logo-text h1{font-size:17px;font-weight:800;color:#fff;letter-spacing:-.02em}
.logo-text p{font-size:11px;color:rgba(255,255,255,.45);margin-top:2px;letter-spacing:.03em;text-transform:uppercase}

/* Tab switcher */
.tabs{
  display:flex;background:rgba(255,255,255,.06);border-radius:10px;
  padding:3px;margin-bottom:28px;gap:3px;
}
.tab{
  flex:1;padding:9px;border-radius:7px;font-size:13px;font-weight:600;
  color:rgba(255,255,255,.45);cursor:pointer;text-align:center;
  transition:all .2s;border:none;background:transparent;
}
.tab.active{
  background:linear-gradient(135deg,#c4a366,#d6b77a);
  color:#fff;box-shadow:0 2px 8px rgba(195, 213, 205, 0.4);
}
.tab:not(.active):hover{color:rgba(255,255,255,.7);background:rgba(255,255,255,.06)}

/* Panels */
.panel{display:none}
.panel.active{display:block}

.form-heading{margin-bottom:22px}
.form-heading h2{font-size:20px;font-weight:800;color:#fff;letter-spacing:-.02em}
.form-heading p{font-size:13px;color:rgba(255,255,255,.45);margin-top:4px}

.role-hints{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:22px}
.role-badge{font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;letter-spacing:.02em}
.role-admin  {background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.2)}
.role-agent  {background:rgba(59,130,246,.15);color:#93c5fd;border:1px solid rgba(59,130,246,.2)}
.role-auditor{background:rgba(245,158,11,.15);color:#fcd34d;border:1px solid rgba(245,158,11,.2)}

.field{margin-bottom:16px}
.field label{
  display:block;font-size:12px;font-weight:600;
  color:rgba(255,255,255,.5);text-transform:uppercase;
  letter-spacing:.06em;margin-bottom:8px;
}
.input-wrap{position:relative}
.input-wrap .icon{
  position:absolute;left:14px;top:50%;transform:translateY(-50%);
  font-size:16px;pointer-events:none;
}
.input-wrap input{
  width:100%;padding:12px 14px 12px 42px;
  background:rgba(255,255,255,.06);
  border:1.5px solid rgba(255,255,255,.1);
  border-radius:10px;font-size:14px;color:#fff;
  font-family:inherit;transition:all .2s;
}
.input-wrap input::placeholder{color:rgba(255,255,255,.25)}
.input-wrap input:focus{
  outline:none;border-color:rgba(0,153,77,.6);
  background:rgba(255,255,255,.08);
  box-shadow:0 0 0 3px rgba(0,107,60,.15);
}
.input-wrap.has-error input{border-color:rgba(239,68,68,.5)}
.error-msg{font-size:12px;color:#fca5a5;margin-top:5px}

.pwd-toggle{
  position:absolute;right:12px;top:50%;transform:translateY(-50%);
  background:none;border:none;cursor:pointer;
  color:rgba(255,255,255,.35);font-size:16px;padding:4px;
  transition:color .15s;line-height:1;
}
.pwd-toggle:hover{color:rgba(255,255,255,.7)}

.remember{display:flex;align-items:center;gap:8px;margin-bottom:20px}
.remember input[type=checkbox]{width:16px;height:16px;accent-color:#006B3C;cursor:pointer}
.remember label{font-size:13px;color:rgba(255,255,255,.5);cursor:pointer}

.submit-btn{
  width:100%;padding:13px;border-radius:10px;font-size:15px;font-weight:700;
  border:none;cursor:pointer;transition:all .2s;letter-spacing:.01em;
  display:flex;align-items:center;justify-content:center;gap:8px;color:#fff;
}
.btn-staff{
  background:linear-gradient(135deg,#c4a366,#d6b77a);
  box-shadow:0 4px 15px rgba(0,107,60,.4);
}
.btn-staff:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(0,107,60,.5)}
.btn-customer{
  background:linear-gradient(135deg,#1d4ed8,#2563eb);
  box-shadow:0 4px 15px rgba(29,78,216,.4);
}
.btn-customer:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(29,78,216,.5)}
.submit-btn:active{transform:translateY(0)!important}
.submit-btn:disabled{opacity:.5;transform:none!important;cursor:not-allowed}

.btn-spinner{
  width:16px;height:16px;
  border:2px solid rgba(255,255,255,.3);border-top-color:#fff;
  border-radius:50%;animation:spin .7s linear infinite;display:none;
}
@keyframes spin{to{transform:rotate(360deg)}}

.alert{
  padding:11px 14px;border-radius:8px;font-size:13px;
  margin-bottom:18px;display:flex;align-items:flex-start;gap:8px;
}
.alert-error  {background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.2);color:#fca5a5}
.alert-success{background:rgba(0,153,77,.1);border:1px solid rgba(0,153,77,.2);color:#6ee7b7}
.alert-info   {background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#93c5fd}

.divider{
  display:flex;align-items:center;gap:10px;
  margin:18px 0;color:rgba(255,255,255,.2);font-size:12px;
}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.08)}

.switch-link{
  width:100%;padding:11px;border-radius:10px;
  border:1.5px solid rgba(255,255,255,.1);
  color:rgba(255,255,255,.5);font-size:13px;font-weight:500;
  background:transparent;cursor:pointer;transition:all .2s;
  font-family:inherit;
}
.switch-link:hover{border-color:rgba(255,255,255,.25);color:rgba(255,255,255,.8);background:rgba(255,255,255,.04)}

/* How it works steps */
.steps{display:flex;flex-direction:column;gap:10px;margin-bottom:22px}
.step{display:flex;align-items:center;gap:12px;font-size:13px;color:rgba(255,255,255,.55)}
.step-num{
  width:24px;height:24px;border-radius:50%;flex-shrink:0;
  background:rgba(29,78,216,.3);border:1px solid rgba(59,130,246,.3);
  display:flex;align-items:center;justify-content:center;
  font-size:11px;font-weight:700;color:#93c5fd;
}

.card-footer{
  padding:14px 36px 20px;border-top:1px solid rgba(255,255,255,.06);
  text-align:center;font-size:11px;color:rgba(255,255,255,.25);letter-spacing:.02em;
}

@media(max-width:480px){
  .card-body{padding:24px 20px 20px}
  .card-footer{padding:12px 20px 16px}
}
</style>
</head>
<body>
<div class="card">
  <div class="card-top"></div>
  <div class="card-body">

    <div class="logo-wrap">
          <img src="<?php echo e(asset('storage/axis-logo.png')); ?>" alt="Secure" title="Secure Chat" width="" height="70">
    </div>

    <div class="tabs">
      <button class="tab active" id="tab-staff" onclick="switchTab('staff')">🔐 Staff Login</button>
      <button class="tab" id="tab-customer" onclick="switchTab('customer')">✉️ Customer Access</button>
    </div>

    
    <div class="panel active" id="panel-staff">

      <div class="form-heading">
        <h2>Welcome back</h2>
        <p>Sign in with your staff credentials</p>
      </div>

      <div class="role-hints">
        <span class="role-badge role-admin">Admin</span>
        <span class="role-badge role-agent">Agent</span>
      </div>

      <?php if($errors->hasAny(['email','password']) && old('_form') === 'staff'): ?>
        <div class="alert alert-error">
          <span>⚠️</span><div><?php echo e($errors->first()); ?></div>
        </div>
      <?php endif; ?>

      <?php if(session('success')): ?>
        <div class="alert alert-success">
          <span>✅</span><div><?php echo e(session('success')); ?></div>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('staff.login')); ?>" id="staff-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_form" value="staff">

        <div class="field">
          <label>Email Address</label>
          <div class="input-wrap <?php echo e($errors->has('email') && old('_form') === 'staff' ? 'has-error' : ''); ?>">
            <span class="icon">✉️</span>
            <input type="email" name="email"
              value="<?php echo e(old('_form') === 'staff' ? old('email') : ''); ?>"
              placeholder="you@insurance.ae"
              autocomplete="email" required>
          </div>
          <?php if($errors->has('email') && old('_form') === 'staff'): ?>
            <div class="error-msg"><?php echo e($errors->first('email')); ?></div>
          <?php endif; ?>
        </div>

        <div class="field">
          <label>Password</label>
          <div class="input-wrap <?php echo e($errors->has('password') && old('_form') === 'staff' ? 'has-error' : ''); ?>">
            <span class="icon">🔑</span>
            <input type="password" name="password" id="pwd-field"
              placeholder="Enter your password"
              autocomplete="current-password" required>
            <button type="button" class="pwd-toggle" onclick="togglePwd()">👁️</button>
          </div>
          <?php if($errors->has('password') && old('_form') === 'staff'): ?>
            <div class="error-msg"><?php echo e($errors->first('password')); ?></div>
          <?php endif; ?>
        </div>

        <div class="remember">
          <input type="checkbox" id="remember" name="remember">
          <label for="remember">Keep me signed in for 30 days</label>
        </div>

        <button type="submit" class="submit-btn btn-staff" id="staff-submit">
          <div class="btn-spinner" id="staff-spinner"></div>
          <span id="staff-btn-text">Sign In →</span>
        </button>
      </form>

      <div class="divider">not a staff member?</div>

      <button class="switch-link" onclick="switchTab('customer')">
        ✉️ Customer? Request a magic link →
      </button>
    </div>

    
    <div class="panel" id="panel-customer">

      <div class="form-heading">
        <h2>Customer Access</h2>
        <p>We'll email you a secure one-time login link — no password needed</p>
      </div>

      <div class="steps">
        <div class="step"><div class="step-num">1</div>Enter your registered email below</div>
        <div class="step"><div class="step-num">2</div>Check your inbox for the magic link</div>
        <div class="step"><div class="step-num">3</div>Click the link to log in instantly</div>
      </div>

      <?php if($errors->has('email') && old('_form') === 'customer'): ?>
        <div class="alert alert-error">
          <span>⚠️</span><div><?php echo e($errors->first('email')); ?></div>
        </div>
      <?php endif; ?>

      <?php if(session('magic_sent')): ?>
        <div class="alert alert-success">
          <span>✅</span><div><?php echo e(session('magic_sent')); ?></div>
        </div>
      <?php endif; ?>

      <?php if($errors->has('link')): ?>
        <div class="alert alert-error">
          <span>🔗</span>
          <div>
            <?php echo e($errors->first('link')); ?>

            <br><small style="opacity:.7">Request a new link below.</small>
          </div>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('auth.magic.request')); ?>" id="customer-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="_form" value="customer">

        <div class="field">
          <label>Your Email Address</label>
          <div class="input-wrap">
            <span class="icon">✉️</span>
            <input type="email" name="email"
              value="<?php echo e(old('_form') === 'customer' ? old('email') : ''); ?>"
              placeholder="your@email.com"
              autocomplete="email" required>
          </div>
        </div>

        <button type="submit" class="submit-btn btn-customer" id="customer-submit">
          <div class="btn-spinner" id="customer-spinner"></div>
          <span id="customer-btn-text">Send Secure Login Link →</span>
        </button>
      </form>

      <div class="divider">are you staff?</div>

      <button class="switch-link" onclick="switchTab('staff')">
        🔐 Staff member? Sign in with password →
      </button>
    </div>

  </div>
  <div class="card-footer">
    🔒 256-bit TLS · CBUAE Regulated · UAE Data Residency
  </div>
</div>

<script>
// ── Tab switcher ─────────────────────────────────────────────────────────────
function switchTab(name) {
  ['staff','customer'].forEach(function(t) {
    document.getElementById('tab-' + t).classList.toggle('active', t === name);
    document.getElementById('panel-' + t).classList.toggle('active', t === name);
  });
  // Focus the email input of the active panel
  setTimeout(function() {
    var panel = document.getElementById('panel-' + name);
    var input = panel ? panel.querySelector('input[type=email]') : null;
    if (input) input.focus();
  }, 50);
}

// ── Password toggle ──────────────────────────────────────────────────────────
function togglePwd() {
  var f = document.getElementById('pwd-field');
  var b = document.querySelector('.pwd-toggle');
  if (f.type === 'password') { f.type = 'text'; b.textContent = '🙈'; }
  else { f.type = 'password'; b.textContent = '👁️'; }
}

// ── Spinner on submit ────────────────────────────────────────────────────────
document.getElementById('staff-form').addEventListener('submit', function() {
  document.getElementById('staff-spinner').style.display = 'block';
  document.getElementById('staff-btn-text').textContent = 'Signing in…';
  document.getElementById('staff-submit').disabled = true;
});

document.getElementById('customer-form').addEventListener('submit', function() {
  document.getElementById('customer-spinner').style.display = 'block';
  document.getElementById('customer-btn-text').textContent = 'Sending link…';
  document.getElementById('customer-submit').disabled = true;
});

// ── Auto-switch to correct panel based on server state ───────────────────────
(function() {
  var form = '<?php echo e(old("_form")); ?>';
  var hasMagicSent  = <?php echo e(session('magic_sent') ? 'true' : 'false'); ?>;
  var hasLinkError  = <?php echo e($errors->has('link') ? 'true' : 'false'); ?>;

  if (form === 'customer' || hasMagicSent || hasLinkError) {
    switchTab('customer');
  }
  // Default: staff tab (already active in HTML)
})();
</script>
</body>
</html><?php /**PATH /Users/sangharshsulke/Axis Data/communication-dash/resources/views/auth/staff-login.blade.php ENDPATH**/ ?>