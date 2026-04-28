<?php $__env->startSection('title', 'Customer Inbox'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.inbox-wrap{display:grid;grid-template-columns:1fr;gap:16px;margin-top:20px}
@media(min-width:1024px){.inbox-wrap{grid-template-columns:320px 1fr}}

.sessions-list{
  background:#fff;border-radius:12px;border:1px solid #e5e7eb;
  overflow:hidden;height:calc(100vh - 180px);
  display:flex;flex-direction:column;
}
.sessions-header{
  padding:14px 16px;border-bottom:1px solid #f3f4f6;
  display:flex;align-items:center;justify-content:space-between;
}
.sessions-header h3{font-size:15px;font-weight:700;color:#111827}
.sessions-search{padding:10px 16px;border-bottom:1px solid #f3f4f6}
.sessions-search input{
  width:100%;padding:8px 12px;font-size:13px;
  border:1px solid #e5e7eb;border-radius:8px;outline:none;
  transition:all .15s;font-family:inherit;
}
.sessions-search input:focus{border-color:#c4a366;box-shadow:0 0 0 3px rgba(0,107,60,.1)}

.sessions-scroll{flex:1;overflow-y:auto}
.sessions-scroll::-webkit-scrollbar{width:4px}
.sessions-scroll::-webkit-scrollbar-thumb{background:#d1d5db;border-radius:2px}

.session-card{
  display:flex;gap:12px;padding:12px 16px;cursor:pointer;
  border-bottom:1px solid #f9fafb;transition:background .12s;
  color:inherit;text-decoration:none;
}
.session-card:hover{background:#f9fafb}
.session-card.has-unread{background:#e8f5ee}

.sess-av{
  width:44px;height:44px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-weight:700;font-size:16px;color:#fff;flex-shrink:0;
  background:linear-gradient(135deg,#006B3C,#00994d);
}

.sess-body{flex:1;min-width:0}
.sess-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
.sess-name{font-size:14px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sess-time{font-size:11px;color:#9ca3af;flex-shrink:0}
.sess-preview{font-size:12px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:3px}
.sess-footer{display:flex;align-items:center;justify-content:space-between;margin-top:4px}
.sess-cid{font-size:10px;color:#9ca3af;background:#f3f4f6;padding:1px 6px;border-radius:4px}

.unread-dot{
  width:18px;height:18px;border-radius:50%;background:#c4a366;
  color:#fff;font-size:10px;font-weight:700;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}

/* Right side — generate link card */
.quick-panel{
  background:#fff;border-radius:12px;border:1px solid #e5e7eb;
  padding:24px;
}
.quick-panel h3{font-size:16px;font-weight:700;color:#111827;margin-bottom:6px}
.quick-panel p{font-size:13px;color:#6b7280;line-height:1.6;margin-bottom:16px}

.stats-row{
  display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px;
}
.stat-box{
  padding:14px;border-radius:10px;
  text-align:center;border:1px solid #e5e7eb;
}
.stat-box.green{background:#ecfdf5;border-color:#a7f3d0}
.stat-box.blue{background:#eff6ff;border-color:#bfdbfe}
.stat-box.amber{background:#fffbeb;border-color:#fde68a}
.stat-value{font-size:22px;font-weight:800;color:#111827;line-height:1}
.stat-label{font-size:11px;color:#6b7280;margin-top:4px;letter-spacing:.02em;text-transform:uppercase;font-weight:600}

.link-template{
  background:#f9fafb;border:1.5px dashed #d1d5db;border-radius:10px;
  padding:14px;font-family:monospace;font-size:12px;color:#374151;
  word-break:break-all;margin:12px 0 16px;line-height:1.6;
}
.link-template code{background:#fef3c7;padding:0 6px;border-radius:4px}

.empty-inbox{
  text-align:center;padding:80px 20px;color:#9ca3af;
}
.empty-inbox .icon{font-size:56px;margin-bottom:14px;opacity:.6}
.empty-inbox h3{font-size:17px;font-weight:600;color:#4b5563;margin-bottom:8px}
.empty-inbox p{font-size:14px;line-height:1.7;max-width:380px;margin:0 auto}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-container">
  <div class="page-header">
    <h1 class="page-title">💬 Customer Inbox</h1>
    <p class="page-subtitle">Your active conversations with customers</p>
  </div>

  <?php
    $totalSessions  = $sessions->total();
    $activeSessions = $sessions->where('status', 'active')->count();
    $totalUnread    = $sessions->sum('unread_count');
  ?>

  <div class="inbox-wrap">

    
    <div class="sessions-list">
      <div class="sessions-header">
        <h3>Conversations</h3>
        <?php if($totalUnread > 0): ?>
          <span class="badge badge-success"><?php echo e($totalUnread); ?> unread</span>
        <?php endif; ?>
      </div>

      <div class="sessions-search">
        <input type="text" id="search-input" placeholder="Search conversations..." oninput="filterSessions(this.value)">
      </div>

      <div class="sessions-scroll" id="sessions-scroll">
        <?php $__empty_1 = true; $__currentLoopData = $sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <a href="<?php echo e(route('direct-chat.agent.session', $session->id)); ?>"
             class="session-card <?php echo e($session->unread_count > 0 ? 'has-unread' : ''); ?>"
             data-search="<?php echo e(strtolower($session->customer->name . ' ' . $session->customer_ref)); ?>">

            <div class="sess-av"><?php echo e(strtoupper(substr($session->customer->name, 0, 1))); ?></div>

            <div class="sess-body">
              <div class="sess-top">
                <div class="sess-name"><?php echo e($session->customer->name); ?></div>
                <div class="sess-time">
                  <?php echo e($session->last_activity_at?->diffForHumans(null, true) ?? $session->created_at->diffForHumans(null, true)); ?>

                </div>
              </div>
              <div class="sess-preview">
                <?php if($session->last_msg): ?>
                  <?php if($session->last_msg->sender_id === $session->agent_id): ?>
                    <span style="color:#9ca3af">You: </span>
                  <?php endif; ?>
                  <?php if($session->last_msg->type === 'attachment'): ?>
                    📎 Attachment
                  <?php else: ?>
                    <?php echo e(Str::limit($session->last_msg->body, 40)); ?>

                  <?php endif; ?>
                <?php else: ?>
                  <em>No messages yet</em>
                <?php endif; ?>
              </div>
              <div class="sess-footer">
                <?php if($session->unread_count > 0): ?>
                  <span class="unread-dot"><?php echo e($session->unread_count > 9 ? '9+' : $session->unread_count); ?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="empty-inbox">
            <div class="icon">💬</div>
            <h3>No conversations yet</h3>
            <p>Share your chat link with customers to start receiving messages.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    
    <div>
      
      <div class="stats-row">
        <div class="stat-box green">
          <div class="stat-value"><?php echo e($activeSessions); ?></div>
          <div class="stat-label">Active</div>
        </div>
        <div class="stat-box blue">
          <div class="stat-value"><?php echo e($totalSessions); ?></div>
          <div class="stat-label">Total</div>
        </div>
        <div class="stat-box amber">
          <div class="stat-value"><?php echo e($totalUnread); ?></div>
          <div class="stat-label">Unread</div>
        </div>
      </div>

      
      <div class="quick-panel">
        <h3>🔗 Your Customer Chat Link</h3>
        <p>Share this URL with customers via email, SMS, or WhatsApp. Replace <code>CUSTOMER_ID</code> with the customer's actual ID:</p>

        <div class="link-template" id="link-template">
          <?php echo e(url('/c/' . auth()->id())); ?>?cid=<code>CUSTOMER_ID</code>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap">
          <button class="btn btn-primary" onclick="copyBaseLink()">📋 Copy Template</button>
          <button class="btn btn-outline" onclick="openCustomLinkModal()">🎯 Generate for Customer</button>
        </div>

        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f3f4f6">
          <p style="font-size:12px;color:#9ca3af;line-height:1.7">
            💡 <strong style="color:#374151">How it works:</strong><br>
            1. Customer clicks the link — no login needed<br>
            2. A secure session is created automatically<br>
            3. You see their message here in real time<br>
            4. Each customer gets a unique resumable session
          </p>
        </div>
      </div>
    </div>

  </div>

  <?php if($sessions->hasPages()): ?>
  <div style="margin-top:20px"><?php echo e($sessions->links()); ?></div>
  <?php endif; ?>
</div>


<div class="modal-backdrop" id="custom-link-modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:16px;" onclick="closeModal()">
  <div class="modal" onclick="event.stopPropagation()" style="background:#fff;border-radius:16px;width:100%;max-width:420px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.2)">
    <div style="padding:18px 22px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center">
      <h3 style="font-size:16px;font-weight:700">🎯 Generate Custom Link</h3>
      <button onclick="closeModal()" style="width:32px;height:32px;border-radius:8px;background:#f3f4f6;border:none;cursor:pointer;font-size:14px">✕</button>
    </div>
    <div style="padding:20px 22px">
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Customer ID</label>
        <input type="text" id="custom-cid" placeholder="e.g. UAE-CUST-001" oninput="updateCustomLink()"
          style="width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit">
      </div>
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Customer Name (optional)</label>
        <input type="text" id="custom-name" placeholder="John Doe" oninput="updateCustomLink()"
          style="width:100%;padding:9px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;font-family:inherit">
      </div>
      <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:12px;font-family:monospace;font-size:12px;color:#374151;word-break:break-all;line-height:1.5" id="custom-preview">
        <?php echo e(url('/c/' . auth()->id())); ?>?cid=...
      </div>
    </div>
    <div style="padding:14px 22px;border-top:1px solid #f3f4f6;display:flex;gap:10px;justify-content:flex-end">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="copyCustomLink()">📋 Copy Link</button>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const BASE_LINK = '<?php echo e(url("/c/" . auth()->id())); ?>';

function filterSessions(q) {
  q = q.trim().toLowerCase();
  document.querySelectorAll('.session-card').forEach(el => {
    const match = el.dataset.search.includes(q);
    el.style.display = match ? '' : 'none';
  });
}

function copyBaseLink() {
  const text = BASE_LINK + '?cid=CUSTOMER_ID';
  navigator.clipboard.writeText(text).then(() => flashToast('Template copied!'));
}

function openCustomLinkModal() {
  document.getElementById('custom-link-modal').style.display = 'flex';
  document.getElementById('custom-cid').focus();
}
function closeModal() {
  document.getElementById('custom-link-modal').style.display = 'none';
}

function updateCustomLink() {
  const cid  = document.getElementById('custom-cid').value.trim();
  const name = document.getElementById('custom-name').value.trim();
  let url = BASE_LINK + '?cid=' + encodeURIComponent(cid || '...');
  if (name) url += '&name=' + encodeURIComponent(name);
  document.getElementById('custom-preview').textContent = url;
}

function copyCustomLink() {
  const cid = document.getElementById('custom-cid').value.trim();
  if (!cid) { alert('Enter a customer ID first'); return; }
  const text = document.getElementById('custom-preview').textContent;
  navigator.clipboard.writeText(text).then(() => {
    flashToast('Link copied!');
    closeModal();
  });
}

function flashToast(msg) {
  const t = document.createElement('div');
  t.textContent = msg;
  Object.assign(t.style, {
    position:'fixed',bottom:'24px',left:'50%',transform:'translateX(-50%)',
    background:'#065f46',color:'#fff',padding:'10px 22px',borderRadius:'20px',
    fontSize:'13px',zIndex:9999,boxShadow:'0 4px 12px rgba(0,0,0,.2)',
  });
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
}

// Auto-refresh every 15 seconds
setInterval(() => {
  if (!document.hidden && !document.querySelector('.modal-backdrop[style*="flex"]')) {
    const currentScroll = document.getElementById('sessions-scroll').scrollTop;
    // Only reload if we're on page 1 to avoid jumping
    const url = new URL(window.location);
    if (!url.searchParams.has('page') || url.searchParams.get('page') === '1') {
      // Soft refresh: reload but keep scroll
      fetch(window.location.href)
        .then(r => r.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const newScroll = doc.getElementById('sessions-scroll');
          if (newScroll) {
            document.getElementById('sessions-scroll').innerHTML = newScroll.innerHTML;
            document.getElementById('sessions-scroll').scrollTop = currentScroll;
          }
        }).catch(() => {});
    }
  }
}, 15000);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sangharshsulke/Axis Data/communication-dash/resources/views/chat/agent-inbox.blade.php ENDPATH**/ ?>