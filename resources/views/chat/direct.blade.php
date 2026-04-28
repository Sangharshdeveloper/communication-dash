@extends('layouts.app')
@section('title', 'Chat with ' . $agent->name)

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@endpush

@push('styles')
<style>
:root {
  --brand:         #c4a366;
  --brand-dark:    #a88849;
  --brand-deep:    #7a6230;
  --brand-light:   #e8d4a8;
  --brand-pale:    #f7f0e0;
  --brand-glow:    rgba(196,163,102,0.18);

  --white:         #ffffff;
  --surface:       #f8f9fa;
  --surface-2:     #f0f2f5;
  --border:        rgba(0,0,0,0.06);
  --border-strong: rgba(0,0,0,0.10);

  --text:          #111827;
  --text-2:        #374151;
  --text-muted:    #9ca3af;
  --text-faint:    #d1d5db;

  --bubble-out-bg: #e9dfc8;
  --bubble-out-text: #1a1410;
  --bubble-in-bg:  #ffffff;
  --bubble-in-text:#111827;

  --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-sm: 0 1px 4px rgba(0,0,0,0.08), 0 0 1px rgba(0,0,0,0.04);
  --shadow-md: 0 4px 16px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04);
  --shadow-lg: 0 8px 32px rgba(0,0,0,0.10), 0 2px 8px rgba(0,0,0,0.05);
  --shadow-gold: 0 4px 20px rgba(196,163,102,0.25);

  --radius-sm: 8px;
  --radius-md: 14px;
  --radius-lg: 20px;
  --radius-xl: 24px;
  --radius-full: 9999px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
  background: var(--surface-2);
  color: var(--text);
  -webkit-font-smoothing: antialiased;
}

/* ── Layout shell ───────────────────────────────────────────────────── */
.chat-shell {
  display: flex;
  height: 100vh;
  height: 100dvh;
  max-width: 820px;
  margin: 0 auto;
  background: var(--white);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  position: relative;
}

.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}

/* ── Header ─────────────────────────────────────────────────────────── */
.chat-hdr {
  flex-shrink: 0;
  background: var(--white);
  border-bottom: 1px solid var(--border);
  padding: 14px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 1px 0 var(--border), var(--shadow-xs);
  z-index: 10;
  position: relative;
}

.hdr-av {
  width: 44px;
  height: 44px;
  border-radius: var(--radius-full);
  background: linear-gradient(135deg, var(--brand), var(--brand-dark));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 17px;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
  box-shadow: var(--shadow-gold);
  letter-spacing: -0.5px;
}

.hdr-info { flex: 1; min-width: 0; }

.hdr-name {
  font-size: 15px;
  font-weight: 700;
  color: var(--text);
  line-height: 1.2;
  letter-spacing: -0.2px;
}

.hdr-status {
  font-size: 12px;
  color: var(--text-muted);
  display: flex;
  align-items: center;
  gap: 5px;
  margin-top: 3px;
  font-weight: 500;
}

.status-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 0 2.5px rgba(34,197,94,0.22);
  animation: pulse-green 2.5s ease-in-out infinite;
  flex-shrink: 0;
}

@keyframes pulse-green {
  0%, 100% { box-shadow: 0 0 0 2.5px rgba(34,197,94,0.22); }
  50%       { box-shadow: 0 0 0 4px rgba(34,197,94,0.12); }
}

.hdr-badge {
  background: var(--brand-pale);
  color: var(--brand-dark);
  font-size: 11px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: var(--radius-full);
  border: 1px solid var(--brand-light);
  letter-spacing: 0.02em;
  white-space: nowrap;
}

/* ── Chat background ─────────────────────────────────────────────────── */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 20px 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 2px;
  position: relative;
  scroll-behavior: smooth;
  overscroll-behavior: contain;

  /* Professional subtle dot-grid background */
  background-color: #f0ece4;
  background-image:
    radial-gradient(circle, rgba(196,163,102,0.20) 1px, transparent 1px);
  background-size: 22px 22px;
}

/* Scrollbar */
.chat-messages::-webkit-scrollbar { width: 4px; }
.chat-messages::-webkit-scrollbar-track { background: transparent; }
.chat-messages::-webkit-scrollbar-thumb {
  background: rgba(196,163,102,0.35);
  border-radius: 2px;
}

/* ── History loader / end ────────────────────────────────────────────── */
.history-loader {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px;
  color: var(--text-muted);
  font-size: 12.5px;
  font-weight: 500;
}
.history-loader.hidden { display: none; }

.spin {
  width: 15px;
  height: 15px;
  border: 2px solid var(--brand-light);
  border-top-color: var(--brand);
  border-radius: 50%;
  animation: spin 0.65s linear infinite;
  flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

.history-end {
  align-self: center;
  background: rgba(255,255,255,0.75);
  backdrop-filter: blur(6px);
  color: var(--text-muted);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 5px 14px;
  border-radius: var(--radius-full);
  margin: 8px 0;
  border: 1px solid var(--border-strong);
}

/* ── Date separator ──────────────────────────────────────────────────── */
.date-sep {
  align-self: center;
  background: rgba(255,255,255,0.80);
  backdrop-filter: blur(8px);
  color: var(--text-2);
  font-size: 12px;
  font-weight: 600;
  padding: 5px 14px;
  border-radius: var(--radius-full);
  margin: 12px 0 8px;
  box-shadow: var(--shadow-sm);
  border: 1px solid rgba(255,255,255,0.9);
  letter-spacing: 0.01em;
}

/* ── Messages ────────────────────────────────────────────────────────── */
.msg {
  max-width: 72%;
  display: flex;
  flex-direction: column;
  gap: 3px;
  animation: msg-in 0.18s ease-out both;
}
@keyframes msg-in {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: translateY(0); }
}

.msg.mine   { align-self: flex-end;   align-items: flex-end; }
.msg.theirs { align-self: flex-start; align-items: flex-start; }
.msg.system { align-self: center; max-width: 82%; }

/* Bubble */
.bubble {
  padding: 9px 13px;
  border-radius: 16px;
  font-size: 14.5px;
  line-height: 1.45;
  word-wrap: break-word;
  word-break: break-word;
  position: relative;
  transition: box-shadow 0.15s;
}

.msg.mine .bubble {
  background: var(--bubble-out-bg);
  color: var(--bubble-out-text);
  border-bottom-right-radius: 4px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.10), 0 0 0 1px rgba(196,163,102,0.15);
}

.msg.theirs .bubble {
  background: var(--bubble-in-bg);
  color: var(--bubble-in-text);
  border-bottom-left-radius: 4px;
  box-shadow: var(--shadow-sm);
}

.msg.system .bubble {
  background: rgba(255,255,255,0.82);
  backdrop-filter: blur(8px);
  color: var(--brand-deep);
  font-size: 13px;
  text-align: center;
  font-style: italic;
  font-weight: 500;
  border-radius: var(--radius-md);
  border: 1px solid var(--brand-light);
  box-shadow: var(--shadow-xs);
  padding: 8px 16px;
}

/* Bubble tail using pseudo-element */
.msg.mine .bubble::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: -7px;
  width: 12px;
  height: 12px;
  background: var(--bubble-out-bg);
  clip-path: polygon(0 0, 0 100%, 100% 100%);
}

.msg.theirs .bubble::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: -7px;
  width: 12px;
  height: 12px;
  background: var(--bubble-in-bg);
  clip-path: polygon(100% 0, 0 100%, 100% 100%);
}

.msg.system .bubble::after { display: none; }

/* Meta */
.msg-meta {
  font-size: 11px;
  color: var(--text-muted);
  padding: 0 5px;
  display: flex;
  align-items: center;
  gap: 4px;
  font-variant-numeric: tabular-nums;
}
.msg.mine .msg-meta { flex-direction: row-reverse; }

.tick { font-size: 12px; color: var(--brand); }
.tick.read { color: #3b82f6; }

/* Attachments */
.att-img {
  max-width: 210px;
  border-radius: 10px;
  margin-top: 6px;
  display: block;
  cursor: pointer;
  transition: opacity 0.15s;
}
.att-img:hover { opacity: 0.92; }

.att {
  display: flex;
  align-items: center;
  gap: 9px;
  padding: 8px 10px;
  margin-top: 6px;
  background: rgba(0,0,0,0.05);
  border-radius: var(--radius-sm);
  text-decoration: none;
  color: inherit;
  font-size: 13px;
  transition: background 0.15s;
  border: 1px solid rgba(0,0,0,0.07);
}
.att:hover { background: rgba(0,0,0,0.09); }
.att .ico  { font-size: 20px; flex-shrink: 0; }
.att .name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
.att .sz   { font-size: 11px; color: var(--text-muted); white-space: nowrap; }

/* ── Composer ────────────────────────────────────────────────────────── */
.composer-wrap {
  flex-shrink: 0;
  background: var(--white);
  border-top: 1px solid var(--border);
  box-shadow: 0 -2px 12px rgba(0,0,0,0.04);
}

.att-preview {
  display: none;
  padding: 8px 16px 0;
  gap: 6px;
  flex-wrap: wrap;
}
.att-preview.active { display: flex; }

.att-chip {
  background: var(--brand-pale);
  border: 1px solid var(--brand-light);
  padding: 5px 10px;
  border-radius: var(--radius-full);
  font-size: 12px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--brand-deep);
  max-width: 200px;
}
.att-chip .name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.att-chip .x {
  cursor: pointer;
  color: var(--brand-dark);
  font-weight: 700;
  font-size: 13px;
  line-height: 1;
  padding: 0 2px;
  flex-shrink: 0;
  opacity: 0.7;
  transition: opacity 0.15s;
}
.att-chip .x:hover { opacity: 1; }

.composer {
  padding: 12px 14px;
  display: flex;
  align-items: flex-end;
  gap: 8px;
}

.composer textarea {
  flex: 1;
  resize: none;
  border: 1.5px solid var(--border-strong);
  border-radius: var(--radius-lg);
  padding: 11px 16px;
  font-size: 14.5px;
  font-family: inherit;
  line-height: 1.4;
  max-height: 130px;
  min-height: 46px;
  background: var(--surface);
  color: var(--text);
  outline: none;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.composer textarea:focus {
  border-color: var(--brand);
  background: var(--white);
  box-shadow: 0 0 0 3px var(--brand-glow);
}
.composer textarea::placeholder { color: var(--text-faint); }

.icon-btn {
  width: 44px;
  height: 44px;
  border-radius: var(--radius-full);
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 19px;
  transition: transform 0.12s, background 0.15s, box-shadow 0.15s;
  flex-shrink: 0;
  -webkit-tap-highlight-color: transparent;
}
.icon-btn:active { transform: scale(0.90); }

.attach-btn {
  background: var(--surface-2);
  color: var(--brand-dark);
  border: 1.5px solid var(--border-strong);
}
.attach-btn:hover {
  background: var(--brand-pale);
  border-color: var(--brand-light);
}

.send-btn {
  background: linear-gradient(135deg, var(--brand), var(--brand-dark));
  color: #fff;
  box-shadow: 0 3px 10px rgba(196,163,102,0.40);
}
.send-btn:hover {
  background: linear-gradient(135deg, var(--brand-dark), var(--brand-deep));
  box-shadow: 0 4px 14px rgba(196,163,102,0.50);
  transform: translateY(-1px);
}
.send-btn:active { transform: scale(0.90) translateY(0); }
.send-btn:disabled {
  background: var(--text-faint);
  box-shadow: none;
  cursor: not-allowed;
  transform: none;
}

input[type="file"] { display: none; }

/* ── Scroll-to-bottom FAB ────────────────────────────────────────────── */
.scroll-fab {
  position: absolute;
  bottom: 80px;
  right: 16px;
  width: 42px;
  height: 42px;
  border-radius: var(--radius-full);
  background: var(--white);
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border-strong);
  display: none;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 17px;
  color: var(--brand-dark);
  transition: transform 0.15s, box-shadow 0.15s;
  z-index: 20;
}
.scroll-fab:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}
.scroll-fab.visible { display: flex; }

.unread-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #ef4444;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  border-radius: var(--radius-full);
  padding: 2px 6px;
  min-width: 18px;
  text-align: center;
  border: 2px solid var(--white);
  line-height: 1.4;
}

/* ── Toast ───────────────────────────────────────────────────────────── */
.toast {
  position: fixed;
  bottom: 96px;
  left: 50%;
  transform: translateX(-50%) translateY(8px);
  background: #1f2937;
  color: #f9fafb;
  padding: 10px 20px;
  border-radius: var(--radius-full);
  font-size: 13px;
  font-weight: 500;
  z-index: 9999;
  box-shadow: var(--shadow-lg);
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s, transform 0.2s;
  white-space: nowrap;
}
.toast.visible {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
  pointer-events: auto;
}

/* ── Responsive ──────────────────────────────────────────────────────── */
@media (max-width: 600px) {
  .chat-shell { box-shadow: none; }
  .msg { max-width: 82%; }
  .bubble { font-size: 15px; }
  .chat-hdr { padding: 12px 14px; }
  .hdr-badge { display: none; }
}

.msg.theirs .msg-sender-label {
  align-self: flex-start;
}

.msg-sender-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--brand-dark);
    padding: 0 4px 2px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.sender-agent-tag {
    font-size: 9px;
    background: var(--brand-pale);
    color: var(--brand-deep);
    border: 1px solid var(--brand-light);
    padding: 1px 5px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 700;
}

/* ── Consecutive message grouping ───────────────────────────────────── */
.msg + .msg.mine  { margin-top: 1px; }
.msg + .msg.theirs { margin-top: 1px; }
.msg + .msg.mine .bubble::after  { display: none; }
.msg + .msg.theirs .bubble::after { display: none; }

/* Show tail only on the last in a group */
.msg.mine:not(:has(+ .msg.mine)) .bubble::after  { display: block; }
.msg.theirs:not(:has(+ .msg.theirs)) .bubble::after { display: block; }
</style>
@endpush

@section('content')
<div class="chat-shell">
  <div class="chat-main">

    {{-- Header --}}
    <header class="chat-hdr">
      <div class="hdr-av">{{ strtoupper(substr($agent->name, 0, 1)) }}</div>
      <div class="hdr-info">
        <div class="hdr-name">{{ $agent->name }}</div>
        <div class="hdr-status">
          <span class="status-dot"></span>
          <span>Online</span>
        </div>
      </div>
      <div class="hdr-badge">🔒 Secure Chat</div>
    </header>

    {{-- Messages --}}
    <div class="chat-messages" id="messages">
      <div class="history-loader hidden" id="history-loader">
        <span class="spin"></span>
        Loading older messages…
      </div>
      <div class="history-end" id="history-end" style="display:none">
        — Beginning of conversation —
      </div>

      @php $lastDate = null; @endphp
      @foreach($messages as $m)
        @if($m['date'] !== $lastDate)
          <div class="date-sep">
            {{ \Carbon\Carbon::parse($m['date'])->format('D, M j, Y') }}
          </div>
          @php $lastDate = $m['date']; @endphp
        @endif
        @include('chat.partials.message', ['m' => $m])
      @endforeach
    </div>

    {{-- Scroll FAB --}}
    <button class="scroll-fab" id="scroll-fab" type="button" aria-label="Scroll to latest">
      ↓
      <span class="unread-badge" id="unread-count" style="display:none">0</span>
    </button>

    {{-- Composer --}}
    <div class="composer-wrap">
      <div class="att-preview" id="att-preview"></div>
      <div class="composer">
        <button class="icon-btn attach-btn" type="button" id="attach-btn" title="Attach file">
          📎
        </button>
        <input type="file" id="file-input" multiple
               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
        <textarea id="msg-input" placeholder="Type a message…" rows="1"
                  aria-label="Message input"></textarea>
        <button class="icon-btn send-btn" type="button" id="send-btn" title="Send message">
          ➤
        </button>
      </div>
    </div>

  </div>{{-- /.chat-main --}}
</div>{{-- /.chat-shell --}}

<div class="toast" id="toast" role="alert" aria-live="assertive"></div>
@endsection

@push('scripts')
<script>

(function () {
  'use strict';

  // ── State ────────────────────────────────────────────────────────────
  const SESSION_TOKEN = @json($session->session_token);
  const ROUTES = {
    send:    @json(route('direct-chat.send')),
    poll:    @json(route('direct-chat.poll')),
    history: @json(route('direct-chat.history')),
  };
  const CSRF = @json(csrf_token());
 

  let oldestLoadedId   = @json($oldestLoadedId ?? null);
  let hasMoreHistory   = @json($hasMoreHistory ?? false);
  let lastMessageId    = {{ !empty($messages) ? max(array_column($messages, 'id')) : 0 }};
  let isLoadingHistory = false;
  let unreadCount      = 0;
  let isNearBottom     = true;
  const pendingFiles   = [];

  // ── DOM refs ─────────────────────────────────────────────────────────
  const messagesEl  = document.getElementById('messages');
  const loaderEl    = document.getElementById('history-loader');
  const endEl       = document.getElementById('history-end');
  const scrollFab   = document.getElementById('scroll-fab');
  const msgInput    = document.getElementById('msg-input');
  const sendBtn     = document.getElementById('send-btn');
  const attachBtn   = document.getElementById('attach-btn');
  const fileInput   = document.getElementById('file-input');
  const unreadEl    = document.getElementById('unread-count');
  const previewEl   = document.getElementById('att-preview');
  const toastEl     = document.getElementById('toast');

  // ── Init ─────────────────────────────────────────────────────────────
  function init() {
    scrollToBottom(false);
    if (!hasMoreHistory) endEl.style.display = 'block';

    messagesEl.addEventListener('scroll', onScroll, { passive: true });
    scrollFab.addEventListener('click', () => scrollToBottom(true));
    sendBtn.addEventListener('click', sendMessage);
    attachBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', onFilesPicked);
    msgInput.addEventListener('input', autoGrow);
    msgInput.addEventListener('keydown', e => {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    setInterval(pollMessages, 4000);
  }
  function playMsgSound(mine = false) {
  if (!_audioCtx) return;
  // Resume context if suspended (browser autoplay policy)
  if (_audioCtx.state === 'suspended') _audioCtx.resume();

  const osc  = _audioCtx.createOscillator();
  const gain = _audioCtx.createGain();
  osc.connect(gain);
  gain.connect(_audioCtx.destination);

  if (mine) {
    // Sent sound: short high beep
    osc.frequency.setValueAtTime(880, _audioCtx.currentTime);
    gain.gain.setValueAtTime(0.12, _audioCtx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.0001, _audioCtx.currentTime + 0.12);
    osc.start(_audioCtx.currentTime);
    osc.stop(_audioCtx.currentTime + 0.12);
  } else {
    // Received sound: WhatsApp-like two-tone
    osc.frequency.setValueAtTime(700, _audioCtx.currentTime);
    osc.frequency.setValueAtTime(900, _audioCtx.currentTime + 0.08);
    gain.gain.setValueAtTime(0.18, _audioCtx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.0001, _audioCtx.currentTime + 0.22);
    osc.start(_audioCtx.currentTime);
    osc.stop(_audioCtx.currentTime + 0.22);
  }
}

  // ── Scroll ───────────────────────────────────────────────────────────
  function onScroll() {
    if (messagesEl.scrollTop < 120 && hasMoreHistory && !isLoadingHistory) loadHistory();
    const dist = messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight;
    isNearBottom = dist < 150;
    scrollFab.classList.toggle('visible', !isNearBottom);
    if (isNearBottom) { unreadCount = 0; unreadEl.style.display = 'none'; }
  }

  function scrollToBottom(smooth) {
    messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
    isNearBottom = true;
    unreadCount  = 0;
    unreadEl.style.display = 'none';
    scrollFab.classList.remove('visible');
  }

  // ── Load history ─────────────────────────────────────────────────────
  async function loadHistory() {
    if (isLoadingHistory || !hasMoreHistory || !oldestLoadedId) return;
    isLoadingHistory = true;
    loaderEl.classList.remove('hidden');

    const prevHeight = messagesEl.scrollHeight;
    const prevTop    = messagesEl.scrollTop;

    try {
      const url = new URL(ROUTES.history, window.location.origin);
      url.searchParams.set('session_token', SESSION_TOKEN);
      url.searchParams.set('before_id', oldestLoadedId);

      const res = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) throw new Error('Failed');
      const data = await res.json();

      const frag = document.createDocumentFragment();
      let lastDate = null;
      data.messages.forEach(m => {
        if (m.date !== lastDate) {
          const sep = document.createElement('div');
          sep.className = 'date-sep';
          sep.textContent = formatDateLabel(m.date);
          frag.appendChild(sep);
          lastDate = m.date;
        }
        frag.appendChild(buildMessageEl(m));
      });

      let anchor = endEl.nextSibling;
      while (anchor && anchor.nodeType !== 1) anchor = anchor.nextSibling;
      messagesEl.insertBefore(frag, anchor);

      if (data.oldest_loaded_id) oldestLoadedId = data.oldest_loaded_id;
      hasMoreHistory = data.has_more;
      if (!hasMoreHistory) endEl.style.display = 'block';
      messagesEl.scrollTop = messagesEl.scrollHeight - prevHeight + prevTop;

    } catch (e) {
      console.error('History load failed', e);
      showToast('Failed to load older messages');
    } finally {
      loaderEl.classList.add('hidden');
      isLoadingHistory = false;
    }
  }

  // ── Send ─────────────────────────────────────────────────────────────
  function onFilesPicked(e) {
    for (const f of e.target.files) pendingFiles.push(f);
    renderAttPreview();
    e.target.value = '';
  }

  function renderAttPreview() {
    if (!pendingFiles.length) {
      previewEl.classList.remove('active');
      previewEl.innerHTML = '';
      return;
    }
    previewEl.classList.add('active');
    previewEl.innerHTML = '';
    pendingFiles.forEach((f, i) => {
      const chip = document.createElement('div');
      chip.className = 'att-chip';
      chip.innerHTML = `<span>📎</span><span class="name">${escapeHtml(f.name)}</span><span class="x" data-i="${i}">✕</span>`;
      chip.querySelector('.x').addEventListener('click', () => {
        pendingFiles.splice(Number(chip.querySelector('.x').dataset.i), 1);
        renderAttPreview();
      });
      previewEl.appendChild(chip);
    });
  }

  async function sendMessage() {
    const body = msgInput.value.trim();
    if (!body && !pendingFiles.length) return;
    sendBtn.disabled = true;

    const fd = new FormData();
    fd.append('session_token', SESSION_TOKEN);
    fd.append('body', body);
    pendingFiles.forEach(f => fd.append('attachments[]', f));

    try {
      const res = await fetch(ROUTES.send, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
      });

      if (!res.ok) { showToast(`Send failed (${res.status})`); return; }

      const msg = await res.json();
      appendMessage(msg);
      msgInput.value = '';
      autoGrow();
      pendingFiles.length = 0;
      renderAttPreview();
      scrollToBottom(true);
    //   playMsgSound(true);

    } catch (e) {
      console.error('Network error:', e);
      showToast('Network error — please retry');
    } finally {
      sendBtn.disabled = false;
    }
  }

  // ── Poll ─────────────────────────────────────────────────────────────
  async function pollMessages() {
    if (document.hidden) return;
    try {
      const url = new URL(ROUTES.poll, window.location.origin);
      url.searchParams.set('session_token', SESSION_TOKEN);
      url.searchParams.set('after_id', lastMessageId);

      const res = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) return;
      const data = await res.json();

      for (const m of data.messages) {
        appendMessage(m);
        if (!isNearBottom && !m.mine) {
          unreadCount++;
          unreadEl.textContent = unreadCount;
          unreadEl.style.display = 'inline-block';
        }
      }
    //   if (!m.mine) playMsgSound(false);

      if (data.messages.length && isNearBottom) scrollToBottom(true);
    } catch { /* silent */ }
  }

  // ── Helpers ───────────────────────────────────────────────────────────
  function appendMessage(m) {
    const allMsgs = messagesEl.querySelectorAll('.msg');
    const lastMsg  = allMsgs[allMsgs.length - 1];
    if (m.date !== (lastMsg && lastMsg.dataset.date)) {
      const sep = document.createElement('div');
      sep.className = 'date-sep';
      sep.textContent = formatDateLabel(m.date);
      messagesEl.appendChild(sep);
    }
    messagesEl.appendChild(buildMessageEl(m));
    if (m.id > lastMessageId) lastMessageId = m.id;
  }

//   function buildMessageEl(m) {
//     const row = document.createElement('div');
//     const side = m.type === 'system' ? 'system' : (m.mine ? 'mine' : 'theirs');
//     row.className = `msg ${side}`;
//     row.dataset.date = m.date;
//     row.dataset.id   = m.id;

//     const atts = (m.attachments || []).map(a => {
//       if (a.is_image) {
//         return `<a href="${a.url}" target="_blank" rel="noopener"><img class="att-img" src="${a.url}" alt="${escapeHtml(a.name)}" loading="lazy"></a>`;
//       }
//       return `<a class="att" href="${a.url}" target="_blank" rel="noopener">
//         <span class="ico">📄</span>
//         <span class="name">${escapeHtml(a.name)}</span>
//         <span class="sz">${escapeHtml(String(a.size))}</span>
//       </a>`;
//     }).join('');

//     const bodyHtml = m.body ? escapeHtml(m.body).replace(/\n/g, '<br>') : '';
//     const tick = m.mine
//       ? `<span class="tick ${m.is_read ? 'read' : ''}">${m.is_read ? '✓✓' : '✓'}</span>`
//       : '';

//     row.innerHTML = `
//       <div class="bubble">${bodyHtml}${atts}</div>
//       <div class="msg-meta">${escapeHtml(m.time)}${tick ? ' · ' + tick : ''}</div>
//     `;
//     return row;
//   }
function buildMessageEl(m) {
    const row  = document.createElement('div');
    const side = m.type === 'system' ? 'system' : (m.mine ? 'mine' : 'theirs');
    row.className = `msg ${side}`;
    row.dataset.date = m.date;
    row.dataset.id   = m.id;

    // Agent name label for incoming messages
    const senderLabel = (side === 'theirs' && m.sender_name)
        ? `<div class="msg-sender-label">${escapeHtml(m.sender_name)}${m.sender_role === 'agent' ? ' <span class="sender-agent-tag">Agent</span>' : ''}</div>`
        : '';

    const atts = (m.attachments || []).map(a => {
        if (a.is_image) {
            return `<a href="${a.url}" target="_blank" rel="noopener">
                <img class="att-img" src="${a.url}" alt="${escapeHtml(a.name)}" loading="lazy">
            </a>`;
        }
        return `<a class="att" href="${a.url}" target="_blank" rel="noopener">
            <span class="ico">📄</span>
            <span class="name">${escapeHtml(a.name)}</span>
            <span class="sz">${escapeHtml(String(a.size))}</span>
        </a>`;
    }).join('');

    const bodyHtml = m.body ? escapeHtml(m.body).replace(/\n/g, '<br>') : '';
    const tick = m.mine
        ? `<span class="tick ${m.is_read ? 'read' : ''}">${m.is_read ? '✓✓' : '✓'}</span>`
        : '';

    row.innerHTML = `
        ${senderLabel}
        <div class="bubble">${bodyHtml}${atts}</div>
        <div class="msg-meta">${escapeHtml(m.time)}${tick ? ' · ' + tick : ''}</div>
    `;
    return row;
}

  function formatDateLabel(iso) {
    const d     = new Date(iso + 'T00:00:00');
    const today = new Date(); today.setHours(0, 0, 0, 0);
    const ymd   = x => x.toISOString().slice(0, 10);
    if (ymd(d) === ymd(today)) return 'Today';
    const yest = new Date(today); yest.setDate(yest.getDate() - 1);
    if (ymd(d) === ymd(yest)) return 'Yesterday';
    return d.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
  }

  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c =>
      ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));
  }

  function autoGrow() {
    msgInput.style.height = 'auto';
    msgInput.style.height = Math.min(msgInput.scrollHeight, 130) + 'px';
  }

  let toastTimer = null;
  function showToast(msg) {
    toastEl.textContent = msg;
    toastEl.classList.add('visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('visible'), 3500);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>
@endpush