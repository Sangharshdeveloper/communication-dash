@extends('layouts.app')
@section('title', 'Customer Inbox')

@push('styles')
<style>
/* ─── Reset & Base ─────────────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

:root {
  --green:       #00875A;
  --green-mid:   #00a862;
  --green-dark:  #005c3d;
  --green-bg:    #e8f5ee;
  --green-ring:  rgba(0,135,90,.15);
  --gold:        #c4a366;
  --gold-bg:     #fdf6ec;
  --surface:     #ffffff;
  --surface2:    #f7f8fa;
  --surface3:    #f0f2f5;
  --border:      #e8eaed;
  --border2:     #d1d5db;
  --text1:       #0d1117;
  --text2:       #4b5563;
  --text3:       #9ca3af;
  --blue:        #3b82f6;
  --radius:      10px;
  --shadow-sm:   0 1px 3px rgba(0,0,0,.07), 0 1px 2px rgba(0,0,0,.04);
  --shadow-md:   0 4px 12px rgba(0,0,0,.08);
  --font:        'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ─── Shell ────────────────────────────────────────────────────────────────── */
.inbox-shell {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 0;
  height: calc(100vh - 148px);
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-md);
  margin-top: 18px;
  background: var(--surface);
}

@media (max-width: 900px) {
  .inbox-shell { grid-template-columns: 1fr; }
  .inbox-right-col { display: none; }
}

/* ─── Session List (Left) ───────────────────────────────────────────────────── */
.sessions-col {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border-right: 1px solid var(--border);
  background: var(--surface);
}

.sessions-hdr {
  padding: 16px 18px 14px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
  background: var(--surface);
}

.sessions-hdr h3 {
  font-size: 15px;
  font-weight: 700;
  color: var(--text1);
  letter-spacing: -.01em;
}

.unread-pill {
  background: var(--green);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 20px;
  letter-spacing: .02em;
}

.sessions-search {
  padding: 10px 14px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.sessions-search input {
  width: 100%;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 8px 12px 8px 34px;
  font-size: 13px;
  font-family: var(--font);
  color: var(--text1);
  outline: none;
  transition: border-color .15s, box-shadow .15s;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: 10px center;
}

.sessions-search input:focus {
  border-color: var(--green);
  box-shadow: 0 0 0 3px var(--green-ring);
  background-color: var(--surface);
}

.sessions-scroll {
  flex: 1;
  overflow-y: auto;
}

.sessions-scroll::-webkit-scrollbar { width: 3px; }
.sessions-scroll::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }

/* ─── Session Card ──────────────────────────────────────────────────────────── */
.session-card {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  cursor: pointer;
  border-bottom: 1px solid var(--border);
  border-left: 3px solid transparent;
  transition: background .12s, border-color .12s;
  color: inherit;
  text-decoration: none;
  position: relative;
}

.session-card:hover {
  background: var(--surface2);
}

.session-card.has-unread {
  background: #f0faf5;
  border-left-color: var(--green);
}

.sess-av {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 16px;
  color: #fff;
  flex-shrink: 0;
  background: linear-gradient(135deg, var(--green), var(--green-mid));
}

.sess-body { flex: 1; min-width: 0; }

.sess-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
}

.sess-name {
  font-size: 13.5px;
  font-weight: 600;
  color: var(--text1);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  letter-spacing: -.01em;
}

.sess-time {
  font-size: 11px;
  color: var(--text3);
  flex-shrink: 0;
}

.has-unread .sess-time {
  color: var(--green);
  font-weight: 600;
}

.sess-preview {
  font-size: 12px;
  color: var(--text3);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 3px;
  line-height: 1.4;
}

.sess-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 4px;
}

.sess-ref {
  font-size: 10px;
  color: var(--text3);
  background: var(--surface3);
  padding: 1px 6px;
  border-radius: 4px;
  border: 1px solid var(--border);
  font-weight: 500;
}

.unread-dot {
  width: 19px;
  height: 19px;
  border-radius: 50%;
  background: var(--green);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ─── Right Column ──────────────────────────────────────────────────────────── */
.inbox-right-col {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: var(--surface2);
}

/* ─── Stats Row ─────────────────────────────────────────────────────────────── */
.stats-bar {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
  background: var(--surface);
}

.stat-box {
  padding: 16px 12px;
  text-align: center;
  border-right: 1px solid var(--border);
}
.stat-box:last-child { border-right: none; }

.stat-value {
  font-size: 24px;
  font-weight: 800;
  line-height: 1;
  letter-spacing: -.02em;
}
.stat-value.green { color: var(--green); }
.stat-value.blue  { color: var(--blue); }
.stat-value.gold  { color: var(--gold); }

.stat-label {
  font-size: 10px;
  color: var(--text3);
  margin-top: 4px;
  text-transform: uppercase;
  letter-spacing: .06em;
  font-weight: 600;
}

/* ─── Welcome / Link Panel ──────────────────────────────────────────────────── */
.welcome-panel {
  flex: 1;
  overflow-y: auto;
  padding: 28px 24px;
}

.welcome-panel::-webkit-scrollbar { width: 3px; }
.welcome-panel::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }

.panel-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 22px;
  margin-bottom: 16px;
}

.panel-card h3 {
  font-size: 15px;
  font-weight: 700;
  color: var(--text1);
  margin-bottom: 6px;
  letter-spacing: -.01em;
}

.panel-card p {
  font-size: 13px;
  color: var(--text2);
  line-height: 1.65;
  margin-bottom: 16px;
}

.link-template {
  background: var(--surface2);
  border: 1.5px dashed var(--border2);
  border-radius: 8px;
  padding: 13px 14px;
  font-family: 'SFMono-Regular', 'Consolas', monospace;
  font-size: 12px;
  color: var(--text2);
  word-break: break-all;
  margin: 10px 0 18px;
  line-height: 1.65;
}

.link-template code {
  background: #fef3c7;
  color: #92400e;
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 11px;
  border: 1px solid #fde68a;
}

.btn-row { display: flex; gap: 8px; flex-wrap: wrap; }

.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  font-family: var(--font);
  cursor: pointer;
  border: none;
  transition: all .15s;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, var(--green), var(--green-mid));
  color: #fff;
  box-shadow: 0 2px 6px rgba(0,135,90,.3);
}
.btn-primary:hover { box-shadow: 0 4px 10px rgba(0,135,90,.4); transform: translateY(-1px); }
.btn-primary:active { transform: translateY(0); }

.btn-outline {
  background: var(--surface);
  color: var(--text2);
  border: 1px solid var(--border2);
}
.btn-outline:hover { background: var(--surface2); border-color: var(--green); color: var(--green-dark); }

.how-it-works {
  margin-top: 20px;
  padding-top: 18px;
  border-top: 1px solid var(--border);
}

.how-it-works p {
  font-size: 12px;
  color: var(--text3);
  line-height: 1.7;
  margin: 0;
}

.step-list {
  list-style: none;
  padding: 0;
  margin: 8px 0 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.step-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  font-size: 12px;
  color: var(--text2);
}

.step-num {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: var(--green-bg);
  color: var(--green-dark);
  font-size: 10px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}

/* ─── Empty Inbox ───────────────────────────────────────────────────────────── */
.empty-inbox {
  text-align: center;
  padding: 60px 24px;
  color: var(--text3);
}

.empty-inbox .empty-icon {
  font-size: 48px;
  margin-bottom: 14px;
  opacity: .6;
  display: block;
}

.empty-inbox h3 {
  font-size: 16px;
  font-weight: 600;
  color: var(--text2);
  margin-bottom: 8px;
}

.empty-inbox p {
  font-size: 13px;
  line-height: 1.65;
  max-width: 300px;
  margin: 0 auto;
}

/* ─── Modal ─────────────────────────────────────────────────────────────────── */
.modal-overlay {
  display: none;
  position: fixed;
  inset: 0;
  z-index: 1000;
  background: rgba(0,0,0,.45);
  align-items: center;
  justify-content: center;
  padding: 16px;
  backdrop-filter: blur(2px);
}

.modal-overlay.open { display: flex; }

.modal-box {
  background: var(--surface);
  border-radius: 16px;
  width: 100%;
  max-width: 440px;
  overflow: hidden;
  box-shadow: 0 24px 48px rgba(0,0,0,.2);
  animation: modalIn .2s ease;
}

@keyframes modalIn {
  from { opacity: 0; transform: scale(.96) translateY(8px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}

.modal-hdr {
  padding: 18px 22px;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-hdr h3 {
  font-size: 15px;
  font-weight: 700;
  color: var(--text1);
}

.modal-close {
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: var(--surface2);
  border: none;
  cursor: pointer;
  font-size: 14px;
  color: var(--text2);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background .12s;
}
.modal-close:hover { background: var(--surface3); }

.modal-body { padding: 20px 22px; }

.field-label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  color: var(--text2);
  text-transform: uppercase;
  letter-spacing: .05em;
  margin-bottom: 6px;
}

.modal-input {
  width: 100%;
  padding: 9px 12px;
  border: 1.5px solid var(--border);
  border-radius: 8px;
  font-size: 13.5px;
  font-family: var(--font);
  color: var(--text1);
  outline: none;
  transition: border-color .15s, box-shadow .15s;
  margin-bottom: 14px;
}
.modal-input:focus {
  border-color: var(--green);
  box-shadow: 0 0 0 3px var(--green-ring);
}

.modal-preview {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 11px 13px;
  font-family: 'SFMono-Regular', 'Consolas', monospace;
  font-size: 11.5px;
  color: var(--text2);
  word-break: break-all;
  line-height: 1.55;
  margin-top: 4px;
}

.modal-footer {
  padding: 14px 22px;
  border-top: 1px solid var(--border);
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

/* ─── Toast ─────────────────────────────────────────────────────────────────── */
.toast {
  position: fixed;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%) translateY(12px);
  background: #111827;
  color: #fff;
  padding: 10px 22px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  z-index: 9999;
  box-shadow: 0 6px 20px rgba(0,0,0,.25);
  opacity: 0;
  transition: all .25s cubic-bezier(.34,1.56,.64,1);
  white-space: nowrap;
  pointer-events: none;
}
.toast.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}
</style>
@endpush

@section('content')
<div class="page-container">
  <div class="page-header">
    <h1 class="page-title">💬 Customer Inbox</h1>
    <p class="page-subtitle">Manage your active conversations</p>
  </div>

  @php
    $totalSessions  = $sessions->total();
    $activeSessions = $sessions->where('status', 'active')->count();
    $totalUnread    = $sessions->sum('unread_count');
  @endphp

  <div class="inbox-shell">

    {{-- ══════════════════ LEFT: Session List ══════════════════ --}}
    <div class="sessions-col">

      <div class="sessions-hdr">
        <h3>Conversations</h3>
        @if($totalUnread > 0)
          <span class="unread-pill">{{ $totalUnread }} unread</span>
        @endif
      </div>

      <div class="sessions-search">
        <input type="text" id="search-input" placeholder="Search conversations…"
               oninput="filterSessions(this.value)">
      </div>

      <div class="sessions-scroll" id="sessions-scroll">
        @forelse($sessions as $session)
          <a href="{{ route('direct-chat.agent.session', $session->id) }}"
             class="session-card {{ $session->unread_count > 0 ? 'has-unread' : '' }}"
             data-search="{{ strtolower($session->customer->name . ' ' . $session->customer_ref) }}">

            <div class="sess-av">{{ strtoupper(substr($session->customer->name, 0, 1)) }}</div>

            <div class="sess-body">
              <div class="sess-top">
                <span class="sess-name">{{ $session->customer->name }}</span>
                <span class="sess-time">
                  {{ $session->last_activity_at?->diffForHumans(null, true) ?? $session->created_at->diffForHumans(null, true) }}
                </span>
              </div>

              <div class="sess-preview">
                @if($session->last_msg)
                  @if($session->last_msg->sender_id === $session->agent_id)
                    <span style="color:var(--text3)">You: </span>
                  @endif
                  @if($session->last_msg->type === 'attachment')
                    📎 Attachment
                  @else
                    {{ Str::limit($session->last_msg->body, 42) }}
                  @endif
                @else
                  <em>No messages yet</em>
                @endif
              </div>

              <div class="sess-footer">
                @if($session->customer_ref)
                  <span class="sess-ref">{{ $session->customer_ref }}</span>
                @else
                  <span></span>
                @endif
                @if($session->unread_count > 0)
                  <span class="unread-dot">{{ $session->unread_count > 9 ? '9+' : $session->unread_count }}</span>
                @endif
              </div>
            </div>
          </a>
        @empty
          <div class="empty-inbox">
            <span class="empty-icon">💬</span>
            <h3>No conversations yet</h3>
            <p>Share your chat link with customers to start receiving messages.</p>
          </div>
        @endforelse
      </div>

    </div>

    {{-- ══════════════════ RIGHT: Stats + Link Panel ══════════════════ --}}
    <div class="inbox-right-col">

      {{-- Stats Bar --}}
      <div class="stats-bar">
        <div class="stat-box">
          <div class="stat-value green">{{ $activeSessions }}</div>
          <div class="stat-label">Active</div>
        </div>
        <div class="stat-box">
          <div class="stat-value blue">{{ $totalSessions }}</div>
          <div class="stat-label">Total</div>
        </div>
        <div class="stat-box">
          <div class="stat-value gold">{{ $totalUnread }}</div>
          <div class="stat-label">Unread</div>
        </div>
      </div>

      {{-- Welcome / Link Panel --}}
      <div class="welcome-panel">

        <div class="panel-card">
          <h3>🔗 Your Customer Chat Link</h3>
          <p>Share this URL with customers via email, SMS, or WhatsApp. Replace <code>CUSTOMER_ID</code> with the customer's actual ID.</p>

          <div class="link-template" id="link-template">
            {{ url('/c/' . auth()->id()) }}?cid=<code>CUSTOMER_ID</code>
          </div>

          <div class="btn-row">
            <button class="btn btn-primary" onclick="copyBaseLink()">📋 Copy Template</button>
            <button class="btn btn-outline" onclick="openCustomLinkModal()">🎯 Generate for Customer</button>
          </div>

          <div class="how-it-works">
            <p style="font-size:11px;color:var(--text3);font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">How it works</p>
            <ul class="step-list">
              <li>
                <span class="step-num">1</span>
                <span>Customer clicks the link — no login needed</span>
              </li>
              <li>
                <span class="step-num">2</span>
                <span>A secure session is created automatically</span>
              </li>
              <li>
                <span class="step-num">3</span>
                <span>Messages appear here in real time</span>
              </li>
              <li>
                <span class="step-num">4</span>
                <span>Each customer gets a unique resumable session</span>
              </li>
            </ul>
          </div>
        </div>

      </div>
    </div>

  </div>

  @if($sessions->hasPages())
    <div style="margin-top:20px">{{ $sessions->links() }}</div>
  @endif
</div>

{{-- ══════════════════ Custom Link Modal ══════════════════ --}}
<div class="modal-overlay" id="custom-link-modal" onclick="closeModal()">
  <div class="modal-box" onclick="event.stopPropagation()">
    <div class="modal-hdr">
      <h3>🎯 Generate Custom Link</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <label class="field-label">Customer ID</label>
      <input type="text" id="custom-cid" class="modal-input"
             placeholder="e.g. UAE-CUST-001" oninput="updateCustomLink()">

      <label class="field-label">Customer Name <span style="color:var(--text3);font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
      <input type="text" id="custom-name" class="modal-input"
             placeholder="e.g. Ahmed Al-Farsi" oninput="updateCustomLink()">

      <label class="field-label" style="margin-bottom:4px">Generated Link</label>
      <div class="modal-preview" id="custom-preview">{{ url('/c/' . auth()->id()) }}?cid=...</div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="copyCustomLink()">📋 Copy Link</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>
@endsection

@push('scripts')
<script>
const BASE_LINK = '{{ url("/c/" . auth()->id()) }}';

function filterSessions(q) {
  q = q.trim().toLowerCase();
  document.querySelectorAll('.session-card').forEach(el => {
    el.style.display = el.dataset.search.includes(q) ? '' : 'none';
  });
}

function copyBaseLink() {
  navigator.clipboard.writeText(BASE_LINK + '?cid=CUSTOMER_ID').then(() => showToast('Template copied!'));
}

function openCustomLinkModal() {
  document.getElementById('custom-link-modal').classList.add('open');
  document.getElementById('custom-cid').focus();
}

function closeModal() {
  document.getElementById('custom-link-modal').classList.remove('open');
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
  navigator.clipboard.writeText(document.getElementById('custom-preview').textContent).then(() => {
    showToast('Link copied!');
    closeModal();
  });
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
}

// Auto-refresh every 15 s (page 1 only, preserves scroll)
setInterval(() => {
  if (document.hidden) return;
  if (document.getElementById('custom-link-modal').classList.contains('open')) return;
  const url = new URL(window.location);
  if (url.searchParams.has('page') && url.searchParams.get('page') !== '1') return;
  const scroll = document.getElementById('sessions-scroll').scrollTop;
  fetch(window.location.href)
    .then(r => r.text())
    .then(html => {
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const newList = doc.getElementById('sessions-scroll');
      if (newList) {
        document.getElementById('sessions-scroll').innerHTML = newList.innerHTML;
        document.getElementById('sessions-scroll').scrollTop = scroll;
      }
    }).catch(() => {});
}, 15000);
</script>
@endpush