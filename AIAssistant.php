<?php include "auth.php"; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>ATFC Assistant</title>

<!--
  Upgraded AIAssistant.php
  - Blue gradient theme (option B)
  - Typing animation + animated dots loader
  - Voice input (Web Speech API) with mic button
  - Clear chat button (client-side)
  - Full-screen toggle
  - Assistant avatar
  - Smooth fades, message bubbles like ChatGPT
  - Light/Dark toggle (persisted)
  - Floating Home button
  - All logic contained here; DOES NOT modify other PHP files
-->

<style>
:root{
  --bg1: #0A84FF;
  --bg2: #0052CC;
  --glass: rgba(255,255,255,0.08);
  --muted: rgba(255,255,255,0.75);
  --radius: 14px;
  --accent: #27a2ff;
  --bot-bubble: rgba(255,255,255,0.06);
  --user-bubble: linear-gradient(90deg,#27a2ff,#005eff);
  --shadow: 0 18px 50px rgba(3,12,28,0.35);
}

/* Dark / Light theme switch (small adjustments) */
body.light {
  --glass: rgba(255,255,255,0.95);
  --bot-bubble: #f6f8fb;
  --muted: #334155;
  color: #072b2a;
  background: linear-gradient(180deg,#f7fbff,#eef6ff);
}
body {
  margin:0;
  font-family: "Inter", "Segoe UI", Arial, sans-serif;
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
  min-height:100vh;
  background: linear-gradient(135deg, var(--bg1), var(--bg2));
  display:flex;
  align-items:center;
  justify-content:center;
  color: #fff;
}

/* top fixed bar */
.topbar {
  position: fixed;
  left: 12px;
  top: 12px;
  right: 12px;
  height:60px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:8px 12px;
  border-radius:12px;
  background: linear-gradient(90deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
  backdrop-filter: blur(8px);
  box-shadow: var(--shadow);
  z-index:999;
  border:1px solid rgba(255,255,255,0.06);
}

/* title area */
.brand {
  display:flex;
  align-items:center;
  gap:12px;
}
.brand .logo {
  width:44px;height:44px;border-radius:10px;background:linear-gradient(90deg,#fff,#e6f3ff);display:flex;align-items:center;justify-content:center;color:#0052CC;font-weight:800;font-size:18px;
}
.brand h1 { margin:0;font-size:18px;letter-spacing:0.2px;color:inherit; font-weight:800; }
.top-actions { display:flex; gap:10px; align-items:center; }

/* floating home button (left) */
.home-float {
  position: fixed;
  left: 18px;
  top: 86px;
  z-index:998;
  background:linear-gradient(90deg,#27a2ff,#005eff);
  color:white;
  border:none;
  padding:10px 12px;
  border-radius:12px;
  box-shadow:0 8px 26px rgba(0,0,0,0.2);
  cursor:pointer;
  font-weight:700;
  display:flex;
  gap:8px;
  align-items:center;
}

/* chat container */
.container {
  width: min(950px, 96vw);
  max-width: 1100px;
  margin: 18vh auto 60px;
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 28px;
  align-items: start;
  padding: 18px;
}

/* left: information / title box */
.left {
  background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
  padding: 28px;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  border:1px solid rgba(255,255,255,0.04);
}
.left h2 { margin:0 0 8px 0; font-size:26px; }
.left p { margin: 0 0 12px 0; color:var(--muted); line-height:1.45; }

/* right: chat card */
.card {
  background: var(--glass);
  border-radius: 18px;
  padding: 10px;
  height: 720px;
  box-shadow: var(--shadow);
  border: 1px solid rgba(255,255,255,0.05);
  display:flex;
  flex-direction:column;
  overflow:hidden;
}

/* header inside card */
.card-header {
  display:flex;
  align-items:center;
  gap:10px;
  padding:8px 12px;
  border-bottom:1px solid rgba(255,255,255,0.03);
}
.assistant-avatar {
  width:44px;height:44px;border-radius:10px;flex:0 0 44px;background:linear-gradient(90deg,#fff,#e6f3ff);display:flex;align-items:center;justify-content:center;color:#0052CC;font-weight:800;
}
.card-header .title { font-weight:800; font-size:16px; }
.card-body {
  padding:12px;
  flex:1;
  overflow:auto;
  scroll-behavior:smooth;
  background: linear-gradient(180deg, rgba(255,255,255,0.015), transparent);
}

/* message bubbles */
.msg-row { display:flex; gap:12px; margin:12px 0; align-items:flex-end; opacity:0; transform: translateY(6px); animation: fadeInUp .18s ease forwards; }
.msg-row.user { justify-content:flex-end; }
.msg-bubble {
  max-width:78%;
  padding:12px 14px;
  border-radius:12px;
  line-height:1.35;
  box-shadow: 0 6px 18px rgba(7,12,20,0.18);
  font-size:14px;
}
.msg-bubble.bot {
  background: var(--bot-bubble);
  color: var(--muted);
  border:1px solid rgba(255,255,255,0.04);
  border-bottom-left-radius:4px;
}
.msg-bubble.user {
  background: var(--user-bubble);
  color: white;
  border-bottom-right-radius:4px;
  font-weight:700;
}

/* small meta */
.msg-meta { font-size:12px; opacity:0.7; margin-top:6px; }

/* input area */
.card-input {
  padding:12px;
  border-top:1px solid rgba(255,255,255,0.03);
  display:flex;
  gap:10px;
  align-items:center;
}
.input {
  flex:1;
  padding:12px 14px;
  border-radius:12px;
  border:1px solid rgba(255,255,255,0.06);
  background:rgba(0,0,0,0.06);
  color:inherit;
  outline:none;
  font-size:14px;
}
.btn {
  padding:10px 12px;
  border-radius:12px;
  border:none;
  cursor:pointer;
  font-weight:800;
}
.btn.primary { background: linear-gradient(90deg,#27a2ff,#005eff); color:white; }
.btn.ghost { background: transparent; color: white; border:1px solid rgba(255,255,255,0.06); }

/* small controls row */
.controls { display:flex; gap:8px; align-items:center; }

/* typing dots */
.typing-dots { display:inline-block; vertical-align:middle; width:48px; text-align:left; }
.dot { display:inline-block; width:8px; height:8px; border-radius:50%; margin-right:6px; opacity:0.2; background:white; transform:translateY(0); animation: dotJump 1s infinite; }
.dot:nth-child(2){ animation-delay:0.12s }
.dot:nth-child(3){ animation-delay:0.24s }

/* fade/slide in */
@keyframes fadeInUp {
  to { opacity:1; transform: translateY(0); }
}

/* dots */
@keyframes dotJump {
  0% { opacity:0.15; transform: translateY(0); }
  50% { opacity:1; transform: translateY(-6px); }
  100% { opacity:0.15; transform: translateY(0); }
}

/* typing cursor animation for typed text */
@keyframes caretBlink { 50% { border-color:transparent; } }

/* utility for full-screen */
.fullscreen {
  position: fixed !important;
  inset: 6px !important;
  width: auto !important;
  height: auto !important;
  z-index: 2000 !important;
}

/* responsive */
@media (max-width: 980px) {
  .container { grid-template-columns: 1fr; margin-top: 26vh; padding: 14px; }
  .home-float { top: 130px; }
  .card { height: calc(100vh - 32vh); }
}

</style>
</head>
<body>

<!-- topbar: small brand + quick controls -->
<div class="topbar" role="banner">
  <div class="brand" aria-hidden="false">
    <div class="logo">AT</div>
    <div>
      <h1 style="margin:0">ATFC Assistant</h1>
      <div style="font-size:12px;opacity:0.85">AI help for your booking flow</div>
    </div>
  </div>

  <div class="top-actions">
    <button id="themeBtn" class="btn ghost" title="Toggle light / dark">Theme</button>
    <button id="fullBtn" class="btn ghost" title="Full screen">Full</button>
    <button id="clearBtn" class="btn ghost" title="Clear chat">Clear</button>
  </div>
</div>

<!-- floating home -->
<button class="home-float" onclick="location.href='index.php'">⬅ Home</button>

<!-- main content -->
<div class="container" id="mainWrap" role="main">

  <div class="left" aria-label="Assistant info">
    <h2>ATFC Assistant</h2>
    <p>Ask about rides, drivers, or the app. You can use voice, copy/paste queries, or type. This assistant uses your local backend (api/chat.php) and proxies to the configured OpenAI backend server bridge.</p>

    <div style="margin-top:12px; display:flex; gap:8px; flex-wrap:wrap;">
      <div style="background:rgba(255,255,255,0.02); padding:10px; border-radius:10px; font-weight:700">Pay later flow</div>
      <div style="background:rgba(255,255,255,0.02); padding:10px; border-radius:10px; font-weight:700">Driver ETA info</div>
      <div style="background:rgba(255,255,255,0.02); padding:10px; border-radius:10px; font-weight:700">Booking help</div>
    </div>

    <hr style="margin:18px 0; border:none; border-top:1px solid rgba(255,255,255,0.04)">

    <div style="font-size:13px; color:var(--muted)">Shortcuts</div>
    <ul style="color:var(--muted); margin-top:8px">
      <li>Type: "Where is my driver?"</li>
      <li>Type: "Show last booking details"</li>
      <li>Use mic to ask aloud (English)</li>
    </ul>
  </div>

  <!-- chat card -->
  <div class="card" id="chatCard" role="region" aria-label="Chat with ATFC assistant">
    <div class="card-header">
      <div class="assistant-avatar">AI</div>
      <div style="flex:1">
        <div class="title">ATFC Assistant</div>
        <div style="font-size:12px;opacity:0.8">Online</div>
      </div>

      <div style="display:flex; gap:8px; align-items:center;">
        <button id="micBtn" class="btn ghost" title="Voice input (mic)">🎤</button>
        <button id="historyBtn" class="btn ghost" title="Reload history">⟳</button>
      </div>
    </div>

    <div class="card-body" id="messages" aria-live="polite" aria-atomic="true">
      <!-- messages appended here -->
    </div>

    <div class="card-input" role="form" aria-label="Send message">
      <input id="inputBox" class="input" placeholder="Ask about this trip or type anything..." aria-label="Message input"/>
      <div style="display:flex; gap:8px; align-items:center;">
        <button id="sendBtn" class="btn primary">Send</button>
      </div>
    </div>
  </div>
</div>

<script>
/* ---------- Utilities & State ---------- */
const messagesEl = document.getElementById('messages');
const inputBox = document.getElementById('inputBox');
const sendBtn = document.getElementById('sendBtn');
const micBtn = document.getElementById('micBtn');
const clearBtn = document.getElementById('clearBtn');
const fullBtn = document.getElementById('fullBtn');
const themeBtn = document.getElementById('themeBtn');
const historyBtn = document.getElementById('historyBtn');
const chatCard = document.getElementById('chatCard');
const mainWrap = document.getElementById('mainWrap');

let isFull = false;
let recognizer = null;
let listening = false;
let userId = "<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user'], ENT_QUOTES) : 'guest'; ?>";

/* theme persistence */
(function initTheme(){
  const saved = localStorage.getItem('atfc_theme') || 'dark';
  if (saved === 'light') document.body.classList.add('light');
})();
themeBtn.addEventListener('click', ()=>{
  document.body.classList.toggle('light');
  localStorage.setItem('atfc_theme', document.body.classList.contains('light') ? 'light' : 'dark');
  themeBtn.textContent = document.body.classList.contains('light') ? 'Light' : 'Dark';
});

/* full screen toggle */
fullBtn.addEventListener('click', ()=>{
  if (!isFull){
    chatCard.classList.add('fullscreen');
    isFull = true;
    fullBtn.textContent = 'Exit';
  } else {
    chatCard.classList.remove('fullscreen');
    isFull = false;
    fullBtn.textContent = 'Full';
  }
});

/* clear chat (client-side only) */
clearBtn.addEventListener('click', ()=> {
  messagesEl.innerHTML = '';
  // optionally show small placeholder
  addBotMessage("Chat cleared. Ask me anything about the app or bookings.");
});

/* reload history */
historyBtn.addEventListener('click', loadHistory);

/* helpers to create message nodes */
function createMsgElement(role, text, options = {}) {
  const row = document.createElement('div');
  row.className = 'msg-row ' + (role==='user' ? 'user' : 'bot');

  const bubble = document.createElement('div');
  bubble.className = 'msg-bubble ' + (role==='user' ? 'user' : 'bot');
  bubble.setAttribute('role', 'article');

  if (options.html) bubble.innerHTML = text;
  else bubble.textContent = text;

  row.appendChild(bubble);
  return { row, bubble };
}

/* smooth append with fade */
function appendMessage(role, text, opts = {}) {
  const {row, bubble} = createMsgElement(role, text, opts);
  messagesEl.appendChild(row);
  // ensure scroll
  messagesEl.scrollTop = messagesEl.scrollHeight;
  return {row, bubble};
}

/* ---------- Typing + Dots simulation ---------- */
function showTypingDots() {
  const {row, bubble} = createMsgElement('bot', '', {html:true});
  bubble.innerHTML = `<span class="typing-dots"><span class="dot"></span><span class="dot"></span><span class="dot"></span></span>`;
  messagesEl.appendChild(row);
  messagesEl.scrollTop = messagesEl.scrollHeight;
  return row;
}

function removeNode(node){ if(node && node.parentNode) node.parentNode.removeChild(node); }

/* type writer effect for assistant text (friendly) */
function typeTextInto(node, text, speed = 18) {
  node.textContent = '';
  let i = 0;
  return new Promise(res => {
    const t = setInterval(() => {
      node.textContent += text.charAt(i);
      i++;
      messagesEl.scrollTop = messagesEl.scrollHeight;
      if (i >= text.length) { clearInterval(t); res(); }
    }, speed);
  });
}

/* ---------- Network: send message to backend ---------- */
async function sendToBackend(message) {
  try {
    const res = await fetch('api/chat.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ message })
    });
    if (!res.ok) return {error: 'Server error: ' + res.status};
    const json = await res.json();
    return json;
  } catch (e) {
    return { error: e.message || 'Network error' };
  }
}

/* ---------- Main flow: user sends a message ---------- */
async function handleSend() {
  const text = inputBox.value.trim();
  if (!text) return;
  // show user bubble
  appendMessage('user', text);
  inputBox.value = '';
  inputBox.focus();

  // show typing dots
  const dotsNode = showTypingDots();

  // send to backend
  const payload = await sendToBackend(text);

  // remove dots
  removeNode(dotsNode);

  if (payload.error) {
    appendMessage('bot', '⚠️ ' + payload.error);
    return;
  }

  // payload.reply is expected (chat.php returns {reply: ...})
  const reply = payload.reply ?? (payload.raw ?? 'No reply');

  // create bot bubble and type
  const {row, bubble} = createMsgElement('bot', '', {html:false});
  messagesEl.appendChild(row);
  messagesEl.scrollTop = messagesEl.scrollHeight;

  // type effect
  await typeTextInto(bubble, String(reply), 14);
  messagesEl.scrollTop = messagesEl.scrollHeight;
}

/* shortcut: Add a simple assistant message */
function addBotMessage(text){
  appendMessage('bot', text);
}

/* send via button or enter */
sendBtn.addEventListener('click', handleSend);
inputBox.addEventListener('keydown', (e)=>{
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); handleSend(); }
});

/* ---------- Voice input (Web Speech API) ---------- */
function initSpeech() {
  if (!('webkitSpeechRecognition' in window || 'SpeechRecognition' in window)) {
    micBtn.title = 'Voice not supported';
    micBtn.disabled = true;
    return;
  }
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  recognizer = new SpeechRecognition();
  recognizer.lang = 'en-IN';
  recognizer.interimResults = true;
  recognizer.maxAlternatives = 1;
  recognizer.continuous = false;

  recognizer.onstart = () => { listening = true; micBtn.textContent = '🎙️'; micBtn.classList.add('active'); };
  recognizer.onend = () => { listening = false; micBtn.textContent = '🎤'; micBtn.classList.remove('active'); };
  recognizer.onerror = (ev) => { console.warn('Speech error', ev); micBtn.textContent = '🎤'; listening=false; };

  let interim = '';
  recognizer.onresult = (ev) => {
    interim = '';
    for (let i=ev.resultIndex;i<ev.results.length;i++){
      const transcript = ev.results[i][0].transcript;
      if (ev.results[i].isFinal) {
        // final result: send it
        inputBox.value = transcript;
        handleSend();
      } else {
        interim += transcript;
        inputBox.value = interim;
      }
    }
  };
}

micBtn.addEventListener('click', ()=>{
  if (!recognizer) return;
  if (!listening) {
    try { recognizer.start(); } catch(e){}
  } else {
    recognizer.stop();
  }
});

/* ---------- Load recent history (calls api/history.php) ---------- */
async function loadHistory(){
  try {
    const res = await fetch('api/history.php');
    if (!res.ok) { addBotMessage('Could not load history.'); return; }
    const json = await res.json();
    messagesEl.innerHTML = '';
    if (Array.isArray(json.history)) {
      json.history.forEach(m => {
        appendMessage(m.role === 'user' ? 'user' : 'bot', m.content);
      });
    } else {
      addBotMessage('No previous chat history found.');
    }
  } catch (e) {
    addBotMessage('Error loading history.');
  }
}

/* ---------- Initialize everything ---------- */
(function init(){
  initSpeech();
  // load history if available
  loadHistory();

  // initial assistant greeting
  setTimeout(()=> {
    addBotMessage("Hello! I'm ATFC Assistant. Ask me about bookings, drivers, or the app. Use the mic or type your question.");
  }, 350);
})();

</script>
</body>
</html>
