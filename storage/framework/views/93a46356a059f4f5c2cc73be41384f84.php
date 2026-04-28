<?php $__env->startSection('title', 'Chat with ' . $session->customer->name); ?>

<?php $__env->startPush('styles'); ?>
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
  --surface:     #ffffff;
  --surface2:    #f7f8fa;
  --surface3:    #efeae2;
  --border:      #e8eaed;
  --border2:     #d1d5db;
  --text1:       #0d1117;
  --text2:       #4b5563;
  --text3:       #9ca3af;
  --blue:        #3b82f6;
  --radius:      10px;
  --shadow-md:   0 4px 12px rgba(0,0,0,.08);
  --font:        'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

/* ─── Shell: 3-column layout ─────────────────────────────────────────────── */
.chat-shell {
  display: grid;
  grid-template-columns: 300px 1fr 260px;
  height: calc(100vh - 148px);
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-md);
  margin-top: 18px;
  background: var(--surface);
}

@media (max-width: 1100px) { .info-col { display: none; } }
@media (max-width: 780px)  { .sessions-col { display: none; } }

/* ══════════════════════════════════════════════════════════════
   LEFT COLUMN — Session List (mirrors inbox.blade.php exactly)
═══════════════════════════════════════════════════════════════ */
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

.session-card {
  display: flex;
  align-items: center;
  gap: 11px;
  padding: 11px 14px;
  cursor: pointer;
  border-bottom: 1px solid var(--border);
  border-left: 3px solid transparent;
  transition: background .12s, border-color .12s;
  color: inherit;
  text-decoration: none;
}

.session-card:hover { background: var(--surface2); }

.session-card.active-session {
  background: var(--green-bg);
  border-left-color: var(--green);
}

.session-card.has-unread { background: #f0faf5; }
.session-card.has-unread:not(.active-session) { border-left-color: transparent; }

.sess-av {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 15px;
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
  font-size: 13px;
  font-weight: 600;
  color: var(--text1);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.sess-time { font-size: 11px; color: var(--text3); flex-shrink: 0; }
.has-unread .sess-time { color: var(--green); font-weight: 600; }

.sess-preview {
  font-size: 12px;
  color: var(--text3);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-top: 2px;
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
  background: var(--surface2);
  padding: 1px 6px;
  border-radius: 4px;
  border: 1px solid var(--border);
  font-weight: 500;
}

.unread-dot {
  width: 18px; height: 18px;
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

/* ══════════════════════════════════════════════════════════════
   CENTRE COLUMN — Chat
═══════════════════════════════════════════════════════════════ */
.chat-col {
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: var(--surface3);
}

/* Chat Header */
.chat-hdr {
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 12px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.hdr-back {
  width: 34px; height: 34px;
  border-radius: 8px;
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text2);
  font-size: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  transition: all .14s;
  flex-shrink: 0;
}
.hdr-back:hover { background: var(--surface2); border-color: var(--green); color: var(--green-dark); }

.hdr-av {
  width: 38px; height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--green), var(--green-mid));
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  color: #fff;
  flex-shrink: 0;
}

.hdr-body { flex: 1; min-width: 0; }

.hdr-name {
  font-size: 14.5px;
  font-weight: 700;
  color: var(--text1);
  letter-spacing: -.01em;
  line-height: 1.2;
}

.hdr-sub {
  font-size: 11px;
  color: var(--green);
  display: flex;
  align-items: center;
  gap: 4px;
  margin-top: 2px;
}

.online-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--green);
  flex-shrink: 0;
}

/* Messages */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px 16px 8px;
  display: flex;
  flex-direction: column;
  gap: 3px;
}
.chat-messages::-webkit-scrollbar { width: 3px; }
.chat-messages::-webkit-scrollbar-thumb { background: #c0c8d0; border-radius: 2px; }

/* Day divider */
.day-divider {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
}
.day-divider::before,
.day-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: rgba(0,0,0,.1);
}
.day-divider span {
  font-size: 11px;
  color: var(--text3);
  white-space: nowrap;
  background: rgba(255,255,255,.85);
  border: 1px solid rgba(0,0,0,.07);
  padding: 2px 11px;
  border-radius: 20px;
}

/* System message */
.sys-msg {
  background: rgba(255,255,255,.85);
  border: 1px solid rgba(0,0,0,.07);
  border-radius: 8px;
  padding: 4px 14px;
  font-size: 11px;
  color: var(--text3);
  text-align: center;
  align-self: center;
  max-width: 86%;
  margin: 6px auto;
}

/* Message rows */
.msg-wrap {
  display: flex;
  gap: 8px;
  align-items: flex-end;
  animation: msgIn .14s ease;
}
@keyframes msgIn {
  from { opacity: 0; transform: translateY(5px); }
  to   { opacity: 1; transform: none; }
}

.msg-wrap.mine { flex-direction: row-reverse; }

.msg-av {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--green), var(--green-mid));
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 11px;
  flex-shrink: 0;
}
.msg-wrap.mine .msg-av { display: none; }

.msg-grp {
  max-width: 68%;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.msg-wrap.mine .msg-grp { align-items: flex-end; }

/* Bubbles */
.bubble {
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 13.5px;
  line-height: 1.5;
  word-break: break-word;
  position: relative;
  color: var(--text1);
}

.msg-wrap:not(.mine) .bubble {
  background: #ffffff;
  border-top-left-radius: 3px;
  box-shadow: 0 1px 2px rgba(0,0,0,.07);
}

.msg-wrap.mine .bubble {
  background: #d9fdd3;
  border-top-right-radius: 3px;
  box-shadow: 0 1px 2px rgba(0,0,0,.07);
}

/* Bubble footer: time + ticks */
.bubble-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 3px;
  margin-top: 3px;
}

.b-time { font-size: 10px; color: var(--text3); }

.ticks {
  font-size: 13px;
  line-height: 1;
  color: #adb5bd; /* gray = sent */
}

.ticks.read {
  color: var(--green); /* green = read */
}

/* Attachment pill */
.att-pill {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(0,0,0,.04);
  border: 1px solid rgba(0,0,0,.08);
  border-radius: 8px;
  padding: 8px 11px;
  margin-top: 4px;
  cursor: pointer;
  transition: background .14s;
}
.att-pill:hover { background: rgba(0,0,0,.08); }
.att-icon { font-size: 18px; flex-shrink: 0; }
.att-name { font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
.att-size { font-size: 10px; color: var(--text3); margin-top: 1px; }
.att-img  { max-width: 220px; border-radius: 8px; display: block; margin-top: 5px; cursor: pointer; }

/* ── Input Bar ─────────────────────────────────────────────────────────────── */
.chat-input-bar {
  padding: 10px 14px;
  background: var(--surface);
  border-top: 1px solid var(--border);
  flex-shrink: 0;
}

.file-chips { display: none; flex-wrap: wrap; gap: 6px; padding: 4px 2px 8px; }
.file-chips.active { display: flex; }

.file-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 4px 10px 4px 8px;
  font-size: 12px;
  max-width: 200px;
}
.file-chip .remove { cursor: pointer; color: var(--text3); font-size: 14px; }

.input-grp {
  display: flex;
  align-items: flex-end;
  gap: 8px;
}

.input-box {
  flex: 1;
  background: var(--surface2);
  border: 1.5px solid var(--border);
  border-radius: 22px;
  padding: 8px 12px 8px 14px;
  display: flex;
  align-items: flex-end;
  gap: 8px;
  transition: border-color .15s, background .15s;
}
.input-box:focus-within {
  border-color: var(--green);
  background: var(--surface);
  box-shadow: 0 0 0 3px var(--green-ring);
}

#agent-input {
  flex: 1;
  resize: none;
  border: none;
  outline: none;
  background: transparent;
  font-size: 13.5px;
  font-family: var(--font);
  color: var(--text1);
  min-height: 22px;
  max-height: 96px;
  line-height: 1.5;
  padding: 2px 0;
}
#agent-input::placeholder { color: var(--text3); }

.attach-icon {
  cursor: pointer;
  color: var(--text3);
  font-size: 18px;
  line-height: 1;
  padding: 3px;
  transition: color .14s;
}
.attach-icon:hover { color: var(--green); }

.send-fab {
  width: 42px; height: 42px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--green), var(--green-mid));
  border: none;
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform .18s, box-shadow .18s;
  box-shadow: 0 2px 8px rgba(0,135,90,.35);
}
.send-fab:hover  { transform: scale(1.06); box-shadow: 0 4px 14px rgba(0,135,90,.45); }
.send-fab:active { transform: scale(.94); }
.send-fab:disabled { opacity: .45; transform: none; box-shadow: none; cursor: not-allowed; }

/* ══════════════════════════════════════════════════════════════
   RIGHT COLUMN — Customer Info
═══════════════════════════════════════════════════════════════ */
.info-col {
  background: var(--surface);
  border-left: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}
.info-col::-webkit-scrollbar { width: 3px; }
.info-col::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }

.info-profile {
  padding: 24px 18px 18px;
  text-align: center;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.info-big-av {
  width: 70px; height: 70px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--green), var(--green-mid));
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 800;
  font-size: 28px;
  margin: 0 auto 11px;
}

.info-cust-name {
  font-size: 15px;
  font-weight: 700;
  color: var(--text1);
  margin-bottom: 6px;
  letter-spacing: -.01em;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: var(--green-dark);
  background: var(--green-bg);
  padding: 3px 10px;
  border-radius: 20px;
  font-weight: 600;
  border: 1px solid #bbf7d0;
}

.info-actions {
  display: flex;
  gap: 8px;
  padding: 14px 16px;
  border-bottom: 1px solid var(--border);
  flex-shrink: 0;
}

.info-btn {
  flex: 1;
  padding: 7px 6px;
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 11px;
  font-weight: 600;
  color: var(--text2);
  cursor: pointer;
  font-family: var(--font);
  transition: all .14s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
}
.info-btn:hover { background: var(--green-bg); border-color: var(--green); color: var(--green-dark); }

.info-section {
  padding: 14px 16px;
  border-bottom: 1px solid var(--border);
}

.info-section h4 {
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: var(--text3);
  font-weight: 700;
  margin-bottom: 10px;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 5px 0;
  font-size: 12.5px;
}

.info-icon { color: var(--text3); width: 16px; flex-shrink: 0; font-size: 13px; line-height: 1.45; }

.info-body { flex: 1; min-width: 0; color: var(--text2); word-break: break-word; }

.info-body small {
  display: block;
  font-size: 10px;
  color: var(--text3);
  margin-bottom: 1px;
  font-weight: 500;
}

.link-copy-block {
  background: var(--surface2);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 10px 12px;
  font-family: 'SFMono-Regular', 'Consolas', monospace;
  font-size: 10.5px;
  color: var(--text2);
  word-break: break-all;
  line-height: 1.55;
  margin-bottom: 8px;
}

.copy-session-btn {
  width: 100%;
  padding: 8px;
  background: var(--green-bg);
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  color: var(--green-dark);
  cursor: pointer;
  font-family: var(--font);
  transition: background .14s;
}
.copy-session-btn:hover { background: #d1fae5; }

/* ─── Lightbox ──────────────────────────────────────────────────────────────── */
#lb {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.9);
  z-index: 9999;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
#lb.open { display: flex; }
#lb img { max-width: 100%; max-height: 90vh; border-radius: 8px; }
#lb-close {
  position: absolute;
  top: 18px; right: 18px;
  background: rgba(255,255,255,.15);
  border: none;
  color: #fff;
  width: 38px; height: 38px;
  border-radius: 50%;
  font-size: 18px;
  cursor: pointer;
}

/* ─── Toast ─────────────────────────────────────────────────────────────────── */
#ag-toast {
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
#ag-toast.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}
/* Sender name label */
.msg-sender-label {
  font-size: 11px;
  font-weight: 700;
  color: var(--green-dark);
  margin-bottom: 3px;
  padding-left: 2px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.sender-role-tag {
  font-size: 9px;
  font-weight: 700;
  background: var(--green-bg);
  color: var(--green-dark);
  border: 1px solid #bbf7d0;
  padding: 1px 5px;
  border-radius: 4px;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.msg-sender-label.label-mine {
  justify-content: flex-end;
  color: var(--green-dark);
}
/* ═══════════════════════════════════════════════════════
   MOBILE RESPONSIVE — 600px and below
═══════════════════════════════════════════════════════ */
@media (max-width: 600px) {

  /* Full-screen shell, no rounded corners, no margin */
  .chat-shell {
    grid-template-columns: 1fr;
    height: 100dvh;
    height: -webkit-fill-available;
    border-radius: 0;
    border: none;
    margin-top: 0;
    box-shadow: none;
  }

  /* Hide side columns entirely on mobile */
  .sessions-col,
  .info-col { display: none !important; }

  /* Chat column fills full screen */
  .chat-col {
    display: flex;
    flex-direction: column;
    height: 100dvh;
    height: -webkit-fill-available;
    overflow: hidden;
  }

  /* Tighter header */
  .chat-hdr {
    padding: 10px 14px;
    gap: 10px;
  }

  .hdr-back {
    width: 32px;
    height: 32px;
    font-size: 14px;
  }

  .hdr-av {
    width: 34px;
    height: 34px;
    font-size: 13px;
  }

  .hdr-name  { font-size: 14px; }
  .hdr-sub   { font-size: 10px; }

  /* Messages area — let it grow and scroll */
  .chat-messages {
    flex: 1;
    padding: 12px 10px 6px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
  }

  /* Wider bubbles on small screens */
  .msg-grp { max-width: 82%; }

  .bubble {
    font-size: 14px;
    padding: 8px 11px;
  }

  /* Input bar — taller tap targets, safe area for notched phones */
  .chat-input-bar {
    padding: 8px 10px;
    padding-bottom: calc(8px + env(safe-area-inset-bottom));
    flex-shrink: 0;
  }

  .input-box {
    padding: 7px 10px 7px 12px;
    border-radius: 20px;
  }

  #agent-input {
    font-size: 16px; /* prevents iOS zoom on focus */
    min-height: 24px;
  }

  .send-fab {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
  }

  .attach-icon {
    font-size: 20px;
    padding: 4px;
  }

  /* File chips tighter */
  .file-chips {
    padding: 4px 2px 6px;
    gap: 4px;
  }

  .file-chip {
    font-size: 11px;
    padding: 3px 8px 3px 6px;
    max-width: 160px;
  }

  /* Sender label smaller */
  .msg-sender-label {
    font-size: 10px;
    margin-bottom: 2px;
  }

  .sender-role-tag {
    font-size: 8px;
    padding: 1px 4px;
  }

  /* Day divider tighter */
  .day-divider { margin: 8px 0; }
  .day-divider span { font-size: 10px; padding: 2px 9px; }

  /* System message */
  .sys-msg {
    font-size: 10px;
    padding: 3px 10px;
  }

  /* Attachment pill */
  .att-name { max-width: 130px; }
  .att-img  { max-width: 180px; }

  /* Lightbox close button — easier tap */
  #lb-close {
    width: 44px;
    height: 44px;
    top: 12px;
    right: 12px;
    font-size: 20px;
  }

  /* Toast above keyboard */
  #ag-toast {
    bottom: calc(70px + env(safe-area-inset-bottom));
    font-size: 12px;
    padding: 8px 16px;
    max-width: 90vw;
    white-space: normal;
    text-align: center;
  }
}

/* ═══════════════════════════════════════════════════════
   TABLET — 601px to 780px (no sidebar, but wider chat)
═══════════════════════════════════════════════════════ */
@media (min-width: 601px) and (max-width: 780px) {

  .chat-shell {
    grid-template-columns: 1fr;
    height: calc(100vh - 100px);
    border-radius: 10px;
    margin-top: 12px;
  }

  .sessions-col { display: none !important; }

  .msg-grp { max-width: 74%; }

  #agent-input { font-size: 16px; }

  .chat-input-bar {
    padding-bottom: calc(10px + env(safe-area-inset-bottom));
  }
}

/* ═══════════════════════════════════════════════════════
   FIX: iOS viewport height (100vh includes browser chrome)
═══════════════════════════════════════════════════════ */
@supports (-webkit-touch-callout: none) {
  .chat-shell,
  .chat-col {
    height: -webkit-fill-available;
  }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="page-container" style="padding-top:14px; max-width:1600px">

  <div class="chat-shell">

    
    <div class="sessions-col">

      <div class="sessions-hdr">
        <h3>Conversations</h3>
        <?php $totalUnreadSidebar = $allSessions->sum('unread_count'); ?>
        <?php if($totalUnreadSidebar > 0): ?>
          <span class="unread-pill"><?php echo e($totalUnreadSidebar); ?> unread</span>
        <?php endif; ?>
      </div>

      <div class="sessions-search">
        <input type="text" id="sb-search" placeholder="Search conversations…"
               oninput="filterSessions(this.value)">
      </div>

      <div class="sessions-scroll">
        <?php $__empty_1 = true; $__currentLoopData = $allSessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sess): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <a href="<?php echo e(route('direct-chat.agent.session', $sess->id)); ?>"
             class="session-card
                    <?php echo e($sess->id === $session->id ? 'active-session' : ''); ?>

                    <?php echo e($sess->unread_count > 0 ? 'has-unread' : ''); ?>"
             data-search="<?php echo e(strtolower($sess->customer->name . ' ' . $sess->customer_ref)); ?>">

            <div class="sess-av"><?php echo e(strtoupper(substr($sess->customer->name, 0, 1))); ?></div>

            <div class="sess-body">
              <div class="sess-top">
                <span class="sess-name"><?php echo e($sess->customer->name); ?></span>
                <span class="sess-time">
                  <?php echo e($sess->last_activity_at?->diffForHumans(null, true) ?? $sess->created_at->diffForHumans(null, true)); ?>

                </span>
              </div>

              <div class="sess-preview">
                <?php if($sess->lastMsg): ?>
                  <?php if($sess->lastMsg->sender_id === $sess->agent_id): ?>
                    <span style="color:var(--text3)">You: </span>
                  <?php endif; ?>
                  <?php if($sess->lastMsg->type === 'attachment'): ?>
                    📎 Attachment
                  <?php else: ?>
                    <?php echo e(Str::limit($sess->lastMsg->body, 40)); ?>

                  <?php endif; ?>
                <?php else: ?>
                  <em>No messages yet</em>
                <?php endif; ?>
              </div>

              <div class="sess-footer">
                <?php if($sess->customer_ref): ?>
                  <span class="sess-ref"><?php echo e($sess->customer_ref); ?></span>
                <?php else: ?>
                  <span></span>
                <?php endif; ?>
                <?php if($sess->unread_count > 0): ?>
                  <span class="unread-dot"><?php echo e($sess->unread_count > 9 ? '9+' : $sess->unread_count); ?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div style="text-align:center;padding:48px 16px;color:var(--text3)">
            <div style="font-size:36px;margin-bottom:10px;opacity:.5">💬</div>
            <p style="font-size:13px">No conversations yet</p>
          </div>
        <?php endif; ?>
      </div>

    </div>

    
    <div class="chat-col">

      
      <div class="chat-hdr">
        <a href="<?php echo e(route('direct-chat.agent.inbox')); ?>" class="hdr-back" title="Back to inbox">←</a>
        <div class="hdr-av"><?php echo e(strtoupper(substr($session->customer->name, 0, 1))); ?></div>
        <div class="hdr-body">
          <div class="hdr-name"><?php echo e($session->customer->name); ?></div>
          <div class="hdr-sub">
            <span class="online-dot"></span>
            <?php if($session->customer_ref): ?>
              Customer ID: <?php echo e($session->customer_ref); ?>

            <?php else: ?>
              Direct customer
            <?php endif; ?>
          </div>
        </div>
      </div>

      
      
      <div class="chat-messages" id="messages">
  <?php $lastDate = null; ?>
  <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

    
    <?php if($msg['date'] !== $lastDate): ?>
      <div class="day-divider">
        <span><?php echo e(\Carbon\Carbon::parse($msg['date'])->isToday() ? 'Today' : (\Carbon\Carbon::parse($msg['date'])->isYesterday() ? 'Yesterday' : \Carbon\Carbon::parse($msg['date'])->format('D, M j, Y'))); ?></span>
      </div>
      <?php $lastDate = $msg['date']; ?>
    <?php endif; ?>

    <?php if($msg['type'] === 'system'): ?>
      <div class="sys-msg" id="m-<?php echo e($msg['id']); ?>"><?php echo e($msg['body']); ?></div>
    <?php else: ?>
     <!--<div class="msg-wrap <?php echo e($msg['mine'] ? 'mine' : ''); ?>" id="m-<?php echo e($msg['id']); ?>">-->
         <div class="msg-wrap <?php echo e(($msg['mine'] || (isset($msg['sender_role']) && $msg['sender_role'] === 'agent')) ? 'mine' : ''); ?>" id="m-<?php echo e($msg['id']); ?>">
  <?php if(!$msg['mine']): ?>
    <div class="msg-av"><?php echo e($msg['initial'] ?? '?'); ?></div>
  <?php endif; ?>
  <div class="msg-grp">
    
    <div class="msg-sender-label <?php echo e($msg['mine'] ? 'label-mine' : ''); ?>">
      <?php echo e($msg['sender_name']); ?>

      <?php if(isset($msg['sender_role']) && $msg['sender_role'] === 'agent'): ?>
        <span class="sender-role-tag">Agent</span>
      <?php endif; ?>
    </div>
          <div class="bubble">
            <?php if($msg['body']): ?><?php echo nl2br(e($msg['body'])); ?><?php endif; ?>
            <?php $__currentLoopData = $msg['attachments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($att['is_image']): ?>
                <img src="<?php echo e($att['url']); ?>" class="att-img"
                     onclick="lightbox('<?php echo e($att['url']); ?>')"
                     alt="<?php echo e($att['name']); ?>">
              <?php else: ?>
                <div class="att-pill" onclick="window.open('<?php echo e($att['url']); ?>')">
                  <span class="att-icon"><?php echo e(str_contains($att['mime'], 'pdf') ? '📄' : '📎'); ?></span>
                  <div>
                    <div class="att-name"><?php echo e($att['name']); ?></div>
                    <div class="att-size"><?php echo e($att['size']); ?></div>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <div class="bubble-footer">
              <span class="b-time"><?php echo e($msg['time']); ?></span>
              <?php if($msg['mine']): ?>
                <span class="ticks <?php echo e($msg['is_read'] ? 'read' : ''); ?>">
                  <?php echo e($msg['is_read'] ? '✓✓' : '✓'); ?>

                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="sys-msg">No messages yet. Say hello! 👋</div>
  <?php endif; ?>
</div>

      
      <?php if(!isset($isAdminView) || !$isAdminView): ?>

      <div class="chat-input-bar">
        <div class="file-chips" id="file-chips"></div>
        <div class="input-grp">
          <div class="input-box">
            <textarea id="agent-input" rows="1" placeholder="Type a message…"
                      oninput="resizeInput(this)"
                      onkeydown="onKey(event)"
                      maxlength="4000"></textarea>
            <label class="attach-icon" title="Attach file">
              📎
              <input type="file" style="display:none" multiple id="agent-files"
                     accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"
                     onchange="filesPicked(this)">
            </label>
          </div>
          <button class="send-fab" id="send-btn" onclick="agentSend()" disabled>
            <svg width="17" height="17" viewBox="0 0 24 24" fill="white">
              <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
            </svg>
          </button>
        </div>
      </div>
      <?php else: ?>
<div style="padding:10px 16px;background:#fff;border-top:1px solid #e8eaed;text-align:center;font-size:12px;color:#9ca3af;">
  👁 Admin view — read only
</div>
<?php endif; ?>
    </div>

    
    <div class="info-col">

      <div class="info-profile">
        <div class="info-big-av"><?php echo e(strtoupper(substr($session->customer->name, 0, 1))); ?></div>
        <div class="info-cust-name"><?php echo e($session->customer->name); ?></div>
        <span class="status-badge">
          <span class="online-dot"></span>
          <?php echo e(ucfirst($session->status)); ?>

        </span>
      </div>

      <div class="info-actions">
        <button class="info-btn" onclick="copyLink()">📋 Copy Link</button>
        <button class="info-btn" onclick="window.open('mailto:<?php echo e($session->customer->email); ?>')">✉️ Email</button>
      </div>

      
      <div class="info-section">
        <h4>Customer Details</h4>
        <div class="info-item">
          <span class="info-icon">👤</span>
          <div class="info-body">
            <small>Customer Ref</small>
            <?php echo e($session->customer_ref ?? '—'); ?>

          </div>
        </div>
        <div class="info-item">
          <span class="info-icon">📧</span>
          <div class="info-body">
            <small>Email</small>
            <?php echo e($session->customer->email); ?>

          </div>
        </div>
        <?php if($session->customer->phone): ?>
        <div class="info-item">
          <span class="info-icon">📱</span>
          <div class="info-body">
            <small>Phone</small>
            <?php echo e($session->customer->phone); ?>

          </div>
        </div>
        <?php endif; ?>
      </div>

      
      <div class="info-section">
        <h4>Session Info</h4>
        <div class="info-item">
          <span class="info-icon">🕐</span>
          <div class="info-body">
            <small>Started</small>
            <?php echo e($session->created_at->format('d M Y · H:i')); ?>

          </div>
        </div>
        <div class="info-item">
          <span class="info-icon">💬</span>
          <div class="info-body">
            <small>Last Activity</small>
            <?php echo e($session->last_activity_at?->diffForHumans() ?? '—'); ?>

          </div>
        </div>
        <div class="info-item">
          <span class="info-icon">⏰</span>
          <div class="info-body">
            <small>Expires</small>
            <?php echo e($session->expires_at?->diffForHumans() ?? '—'); ?>

          </div>
        </div>
      </div>

       
      <div class="info-section">
        <h4>Bitrix Deal</h4>
        <div class="link-copy-block"><a href="<?php echo e($session->bitrix_deal_link ?? ''); ?>" target="_blank" rel="noopener noreferrer"><?php echo e($session->bitrix_deal_link ?? ''); ?></a></div>
      </div>
      
      
      <div class="info-section">
        <h4>Resumable Link</h4>
        <p style="font-size:11px;color:var(--text3);line-height:1.55;margin-bottom:8px">
          Send this link to let the customer resume the conversation:
        </p>
        <div class="link-copy-block"><?php echo e(url('/c/'.$session->agent_id)); ?>?cid=<?php echo e(urlencode($session->customer_ref ?? '')); ?>&token=<?php echo e($session->session_token); ?></div>
        <button class="copy-session-btn" onclick="copyLink()">📋 Copy Session Link</button>
      </div>

    </div>
  </div>
</div>

<!-- Lightbox -->
<div id="lb" onclick="closeLb()">
  <button id="lb-close" onclick="closeLb()">✕</button>
  <img id="lb-img" src="" alt="">
</div>

<div id="ag-toast"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // ── Notification sound ───────────────────────────────────────────────────────
const _audioCtx = (() => {
  try { return new (window.AudioContext || window.webkitAudioContext)(); } catch { return null; }
})();

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
const TOKEN      = '<?php echo e($session->session_token); ?>';
const SEND_URL   = '<?php echo e(route("direct-chat.send")); ?>';
const POLL_URL   = '<?php echo e(route("direct-chat.poll")); ?>';
const CSRF       = '<?php echo e(csrf_token()); ?>';
const AGENT_ID   = <?php echo e($session->agent_id); ?>;
const RESUME_URL = '<?php echo e(url("/c/".$session->agent_id)); ?>?cid=<?php echo e(urlencode($session->customer_ref ?? "")); ?>&token=<?php echo e($session->session_token); ?>';

let lastMsgId   = <?php echo e(!empty($messages) ? end($messages)['id'] : 0); ?>;
let pendingFiles = [];
let sending     = false;

window.addEventListener('load', () => {
  scrollBottom();
  startPoll();
  document.getElementById('agent-input').focus();
});

// ── Sidebar search ───────────────────────────────────────────────────────────
function filterSessions(q) {
  q = q.trim().toLowerCase();
  document.querySelectorAll('.session-card').forEach(el => {
    el.style.display = el.dataset.search.includes(q) ? '' : 'none';
  });
}

// ── Auto-resize ──────────────────────────────────────────────────────────────
function resizeInput(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 96) + 'px';
  updateSendButton();
}

function updateSendButton() {
  const has = document.getElementById('agent-input').value.trim() !== '' || pendingFiles.length > 0;
  document.getElementById('send-btn').disabled = !has || sending;
}

function onKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); agentSend(); }
}

// ── File pick ────────────────────────────────────────────────────────────────
function filesPicked(input) {
  Array.from(input.files).forEach(f => {
    if (f.size > 10 * 1024 * 1024) { toast('File too large (max 10 MB)'); return; }
    pendingFiles.push(f);
  });
  input.value = '';
  renderChips();
  updateSendButton();
}

function renderChips() {
  const wrap = document.getElementById('file-chips');
  if (!pendingFiles.length) { wrap.className = 'file-chips'; wrap.innerHTML = ''; return; }
  wrap.className = 'file-chips active';
  wrap.innerHTML = pendingFiles.map((f, i) =>
    `<div class="file-chip">
       <span>${fileIcon(f.type)}</span>
       <span>${esc(f.name)}</span>
       <span class="remove" onclick="removeFile(${i})">✕</span>
     </div>`
  ).join('');
}

function removeFile(i) { pendingFiles.splice(i, 1); renderChips(); updateSendButton(); }

function fileIcon(m) {
  if (m.startsWith('image/')) return '🖼️';
  if (m.includes('pdf'))       return '📄';
  if (m.includes('word'))      return '📝';
  return '📎';
}

// ── Send ─────────────────────────────────────────────────────────────────────
async function agentSend() {
    playMsgSound(true);
  if (sending) return;
  const inp  = document.getElementById('agent-input');
  const body = inp.value.trim();
  if (!body && !pendingFiles.length) return;

  sending = true;
  document.getElementById('send-btn').disabled = true;

  const fd = new FormData();
  fd.append('session_token', TOKEN);
  if (body) fd.append('body', body);
  pendingFiles.forEach(f => fd.append('attachments[]', f));

  const optId = 'opt_' + Date.now();
  appendMsg({
    id: optId, body,
    type: pendingFiles.length ? 'attachment' : 'text',
    mine: true, sender_name: 'You', initial: 'Y',
    time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false }),
    is_read: false, attachments: [],
    pending: true,
  });

  inp.value = '';
  inp.style.height = 'auto';
  pendingFiles = [];
  renderChips();
  scrollBottom(true);

  try {
    const res  = await fetch(SEND_URL, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: fd,
    });
    if (!res.ok) throw new Error('Send failed');
    const data = await res.json();

    document.getElementById('m-' + optId)?.remove();
    appendMsg(data);
    lastMsgId = Math.max(lastMsgId, data.id);
    scrollBottom(true);
  } catch (e) {
    toast('❌ Failed to send');
    const opt = document.getElementById('m-' + optId);
    if (opt) opt.style.opacity = '.4';
  } finally {
    sending = false;
    updateSendButton();
    document.getElementById('agent-input').focus();
  }
}

// ── Poll: current chat messages ──────────────────────────────────────────────
async function poll() {
  try {
    const url = new URL(POLL_URL, window.location.origin);
    url.searchParams.set('session_token', TOKEN);
    url.searchParams.set('after_id', lastMsgId);

    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!res.ok) return;
    const data = await res.json();

    (data.read_updates || []).forEach(id => {
      const t = document.querySelector('#m-' + id + ' .ticks');
      if (t) { t.textContent = '✓✓'; t.classList.add('read'); }
    });

    const msgs = data.messages || [];
    if (!msgs.length) return;

    const wrap  = document.getElementById('messages');
    const atBot = wrap.scrollHeight - wrap.scrollTop - wrap.clientHeight < 80;

    msgs.forEach(msg => {
      if (document.getElementById('m-' + msg.id)) return;
      appendMsg(msg);
      if (!msg.mine) playMsgSound(false);

      lastMsgId = Math.max(lastMsgId, msg.id);
    });

    if (atBot) scrollBottom(true);
  } catch (_) {}
}

// ── Poll: sidebar unread dots + previews ─────────────────────────────────────
const INBOX_URL        = '<?php echo e(route("direct-chat.agent.inbox")); ?>';
const CURRENT_SID      = <?php echo e($session->id); ?>;

// async function pollSidebar() {
//   try {
//     const res = await fetch(INBOX_URL);
//     if (!res.ok) return;
//     const html = await res.text();
//     const doc  = new DOMParser().parseFromString(html, 'text/html');

//     doc.querySelectorAll('.session-card').forEach(newCard => {
//       const href  = newCard.getAttribute('href') || '';
//       const match = href.match(/\/(\d+)$/);
//       if (!match) return;
//       const sid     = match[1];
//       const oldCard = document.querySelector('.session-card[href$="/' + sid + '"]');
//       if (!oldCard) return;

//       const isActive = parseInt(sid) === CURRENT_SID;

//       // ── Unread dot ──────────────────────────────────────────────────────────
//       const newDot = newCard.querySelector('.unread-dot');
//       const oldDot = oldCard.querySelector('.unread-dot');

//       if (newDot) {
//         if (oldDot) {
//           oldDot.textContent = newDot.textContent;
//         } else {
//           oldCard.querySelector('.sess-footer')?.appendChild(newDot.cloneNode(true));
//         }
//         if (!isActive) {
//           oldCard.classList.add('has-unread');
//           const t = oldCard.querySelector('.sess-time');
//           if (t) { t.style.color = 'var(--green)'; t.style.fontWeight = '600'; }
//         }
//       } else {
//         if (oldDot) oldDot.remove();
//         if (!isActive) {
//           oldCard.classList.remove('has-unread');
//           const t = oldCard.querySelector('.sess-time');
//           if (t) { t.style.color = ''; t.style.fontWeight = ''; }
//         }
//       }

//       // ── Preview text ────────────────────────────────────────────────────────
//       const np = newCard.querySelector('.sess-preview');
//       const op = oldCard.querySelector('.sess-preview');
//       if (np && op) op.innerHTML = np.innerHTML;

//       // ── Timestamp ──────────────────────────────────────────────────────────
//       const nt = newCard.querySelector('.sess-time');
//       const ot = oldCard.querySelector('.sess-time');
//       if (nt && ot) ot.textContent = nt.textContent;
//     });

//     // ── Header unread pill ────────────────────────────────────────────────────
//     const newPill = doc.querySelector('.unread-pill');
//     const oldPill = document.querySelector('.unread-pill');
//     if (newPill && oldPill) {
//       oldPill.textContent = newPill.textContent;
//       oldPill.style.display = '';
//     } else if (!newPill && oldPill) {
//       oldPill.style.display = 'none';
//     }

//   } catch (_) {}
// }
async function pollSidebar() {
  try {
    const res = await fetch(INBOX_URL);
    if (!res.ok) return;
    const html = await res.text();
    const doc  = new DOMParser().parseFromString(html, 'text/html');

    const newScroll = doc.getElementById('sessions-scroll');
    if (!newScroll) return;

    const container  = document.querySelector('.sessions-scroll');
    const searchVal  = document.getElementById('sb-search')?.value?.trim()?.toLowerCase() || '';
    const scrollTop  = container.scrollTop;

    // Re-render entire list to get correct sort order and new sessions
    container.innerHTML = newScroll.innerHTML;

    // Re-mark active session
    container.querySelectorAll('.session-card').forEach(card => {
      const href = card.getAttribute('href') || '';
      if (href.endsWith('/' + CURRENT_SID)) {
        card.classList.add('active-session');
      }
    });

    // Re-apply search filter
    if (searchVal) {
      container.querySelectorAll('.session-card').forEach(el => {
        el.style.display = el.dataset.search.includes(searchVal) ? '' : 'none';
      });
    }

    // Restore scroll position
    container.scrollTop = scrollTop;

    // Update header pill
    const newPill = doc.querySelector('.unread-pill');
    const oldPill = document.querySelector('.unread-pill');
    if (newPill && oldPill) {
      oldPill.textContent = newPill.textContent;
      oldPill.style.display = '';
    } else if (!newPill && oldPill) {
      oldPill.style.display = 'none';
    }

  } catch (_) {}
}
function startPoll() {
  // Chat messages: every 3 s
  let chatT = setInterval(poll, 3000);
  // Sidebar indicators: every 7 s
  let sideT = setInterval(pollSidebar, 7000);

  document.addEventListener('visibilitychange', () => {
    clearInterval(chatT);
    clearInterval(sideT);
    if (!document.hidden) {
      poll();
      pollSidebar();
      chatT = setInterval(poll, 3000);
      sideT = setInterval(pollSidebar, 7000);
    }
  });
}

// ── Append message ───────────────────────────────────────────────────────────
function appendMsg(msg) {
  const wrap = document.getElementById('messages');

  if (msg.type === 'system') {
    const el = document.createElement('div');
    el.className = 'sys-msg'; el.id = 'm-' + msg.id;
    el.textContent = msg.body;
    wrap.appendChild(el);
    return;
  }

  const row = document.createElement('div');
  row.className = 'msg-wrap' + (msg.mine ? ' mine' : '');
  row.id = 'm-' + msg.id;
  if (msg.pending) row.style.opacity = '.65';

  const bodyHtml = msg.body
    ? msg.body.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>')
    : '';

  const attsHtml = (msg.attachments || []).map(att => att.is_image
    ? `<img src="${att.url}" class="att-img" onclick="lightbox('${att.url}')" alt="${esc(att.name)}">`
    : `<div class="att-pill" onclick="window.open('${att.url}')">
         <span class="att-icon">${fileIcon(att.mime)}</span>
         <div>
           <div class="att-name">${esc(att.name)}</div>
           <div class="att-size">${att.size}</div>
         </div>
       </div>`
  ).join('');

  const ticks = msg.mine
    ? `<span class="ticks ${msg.is_read ? 'read' : ''}">${msg.is_read ? '✓✓' : '✓'}</span>`
    : '';

  // ── Sender label (only for incoming) ──
  const senderLabel = !msg.mine && msg.sender_name
    ? `<div class="msg-sender-label">...`
    : '';
//   const senderLabel = !msg.mine && msg.sender_name
//     ? `<div class="msg-sender-label">
//          ${esc(msg.sender_name)}
//          ${msg.sender_role === 'agent' ? '<span class="sender-role-tag">Agent</span>' : ''}
//       </div>`
//     : '';

  const avHtml = msg.mine ? '' : `<div class="msg-av">${esc(msg.initial || '?')}</div>`;

  row.innerHTML = `
    ${avHtml}
    <div class="msg-grp">
      ${senderLabel}
      <div class="bubble">
        ${bodyHtml}${attsHtml}
        <div class="bubble-footer">
          <span class="b-time">${msg.time}</span>
          ${ticks}
        </div>
      </div>
    </div>`;

  wrap.appendChild(row);
}

function scrollBottom(smooth = false) {
  const w = document.getElementById('messages');
  w.scrollTo({ top: w.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}

function lightbox(src) {
  document.getElementById('lb-img').src = src;
  document.getElementById('lb').classList.add('open');
}
function closeLb() { document.getElementById('lb').classList.remove('open'); }

function copyLink() {
  navigator.clipboard.writeText(RESUME_URL).then(() => toast('Link copied!'));
}

function toast(msg) {
  const t = document.getElementById('ag-toast');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(window._tt);
  window._tt = setTimeout(() => t.classList.remove('show'), 2500);
}

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u804993635/domains/imagespark.in/public_html/a/resources/views/chat/agent-session.blade.php ENDPATH**/ ?>