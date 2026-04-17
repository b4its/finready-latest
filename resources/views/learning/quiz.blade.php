<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Quiz: {{ $room->name }} — FinReady Learn</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'DM Sans', sans-serif; background: #f7f6f3; }
    .option-card { position: relative; padding: 14px 16px 14px 50px; border: 2px solid #e8e4dc; border-radius: 12px; background: #fff; cursor: pointer; transition: all .18s ease; user-select: none; }
    .option-card:hover { border-color: #16a34a; background: #f0fdf4; }
    .option-card.selected { border-color: #16a34a; background: #f0fdf4; box-shadow: 0 0 0 3px rgba(22,163,74,.12); }
    .option-card.correct  { border-color: #16a34a; background: #dcfce7; pointer-events:none; }
    .option-card.wrong    { border-color: #ef4444; background: #fef2f2; pointer-events:none; }
    .option-card.reveal-correct { border-color: #16a34a; background: #dcfce7; pointer-events:none; }
    .option-letter { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 26px; height: 26px; border-radius: 8px; background: #f0ece6; color: #7a756b; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; transition: all .18s; }
    .option-card.selected .option-letter, .option-card.correct .option-letter, .option-card.reveal-correct .option-letter { background: #16a34a; color: #fff; }
    .option-card.wrong .option-letter { background: #ef4444; color: #fff; }
    .q-dot { width: 36px; height: 36px; border-radius: 10px; border: 2px solid #e8e4dc; background: #fff; font-size: 13px; font-weight: 600; color: #7a756b; cursor: pointer; transition: all .15s; display: flex; align-items: center; justify-content: center; }
    .q-dot:hover { border-color: #16a34a; color: #16a34a; }
    .q-dot.current { border-color: #16a34a; background: #16a34a; color: #fff; }
    .q-dot.answered { border-color: #86efac; background: #f0fdf4; color: #16a34a; }
    .q-dot.answered.current { background: #16a34a; color: #fff; border-color: #16a34a; }
    .timer-warn { color: #ef4444 !important; animation: blink 1s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.5} }
    .q-enter { animation: qIn .25s ease forwards; }
    @keyframes qIn { from{opacity:0;transform:translateX(16px)} to{opacity:1;transform:translateX(0)} }
    .result-card { animation: pop .45s cubic-bezier(.175,.885,.32,1.275); }
    @keyframes pop { from{opacity:0;transform:scale(.88)} to{opacity:1;transform:scale(1)} }
    .score-ring { stroke-dasharray: 283; stroke-dashoffset: 283; transition: stroke-dashoffset 1.4s ease .3s; transform-origin: center; transform: rotate(-90deg); }
    #fwCanvas { position:fixed; inset:0; pointer-events:none; z-index:9999; }
    code { font-family: 'DM Mono', monospace; font-size: 13px; }
    #progressBar { transition: width .4s ease; }
  </style>
</head>
<body class="min-h-screen">

<header class="sticky top-0 z-50 bg-white border-b border-[#e8e4dc] shadow-sm">
  <div class="max-w-6xl mx-auto px-5 h-14 flex items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <a href="{{ route('learning.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-[#7a756b] hover:text-[#1a1814] transition">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
        Kembali
      </a>
      <div class="w-px h-5 bg-[#e8e4dc]"></div>
      <div class="flex items-center gap-1.5">
        <span class="text-xs text-[#7a756b]">Soal</span>
        <span class="text-sm font-bold text-[#1a1814]" id="qCounter">1</span>
        <span class="text-xs text-[#7a756b]">dari</span>
        <span class="text-sm font-bold text-[#1a1814]" id="qTotal">{{ $room->questions->count() }}</span>
      </div>
      <div class="hidden sm:block w-28 h-1.5 bg-[#e8e4dc] rounded-full overflow-hidden">
        <div id="progressBar" class="h-full bg-green-500 rounded-full" style="width:10%"></div>
      </div>
    </div>
    <div class="hidden md:text-center md:block">
      <p class="text-sm font-semibold text-[#1a1814]">Quiz: {{ $room->name }}</p>
      <p class="text-xs text-[#7a756b]">{{ $room->module->name ?? 'Modul' }}</p>
    </div>
    <div class="flex items-center gap-2 bg-[#f7f6f3] border border-[#e8e4dc] rounded-xl px-3.5 py-1.5">
      <svg id="timerIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7a756b" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      <span id="timerDisplay" class="text-sm font-bold text-[#1a1814] tabular-nums">15:00</span>
    </div>
  </div>
</header>

<div id="quizScreen" class="max-w-6xl mx-auto px-4 sm:px-5 py-6 pb-24">
  <div class="flex gap-5 items-start">
    <div class="flex-1 min-w-0">
      <div class="mb-4 inline-flex px-3 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full border border-blue-100">
         Percobaan ke-{{ $attempts + 1 }}
      </div>
      
      <div id="questionWrap"></div>
      
      <div class="flex items-center justify-between mt-6">
        <button id="prevBtn" onclick="navigate(-1)" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-[#e8e4dc] bg-white text-sm font-medium text-[#7a756b] hover:border-[#16a34a] hover:text-[#16a34a] transition disabled:opacity-40 disabled:cursor-not-allowed">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg> Sebelumnya
        </button>
        <button id="nextBtn" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#16a34a] text-white text-sm font-semibold hover:bg-green-700 transition shadow-sm shadow-green-200">
          Selanjutnya <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
      </div>
    </div>

    <div class="hidden lg:block w-64 flex-shrink-0">
      <div class="bg-white border border-[#e8e4dc] rounded-2xl p-4 sticky top-20">
        <p class="text-xs font-semibold text-[#7a756b] uppercase tracking-wider mb-3">Navigasi Soal</p>
        <div class="grid grid-cols-5 gap-2 mb-4" id="dotGrid"></div>
        <div class="space-y-2 pt-3 border-t border-[#f0ece6] text-xs text-[#7a756b]">
          <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-md bg-[#16a34a]"></div> Soal saat ini</div>
          <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-md bg-[#f0fdf4] border border-[#86efac]"></div> Sudah dijawab</div>
          <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-md bg-white border-2 border-[#e8e4dc]"></div> Belum dijawab</div>
        </div>
        <button id="submitBtnPanel" onclick="submitQuiz()" class="hidden w-full mt-4 bg-[#16a34a] hover:bg-green-700 text-white font-semibold py-2.5 rounded-xl text-sm transition items-center justify-center gap-2 shadow-sm shadow-green-200">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Kumpulkan
        </button>
        <div class="mt-3 text-center"><span id="answeredCount" class="text-xs text-[#7a756b]">0 dari {{ $room->questions->count() }} dijawab</span></div>
      </div>
    </div>
  </div>
</div>

<div id="mobileSubmit" class="hidden fixed bottom-5 left-1/2 -translate-x-1/2 z-40">
  <button onclick="submitQuiz()" class="inline-flex items-center gap-2 px-7 py-3 bg-[#16a34a] text-white font-semibold rounded-2xl shadow-lg shadow-green-300 text-sm hover:bg-green-700 transition">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Kumpulkan Quiz
  </button>
</div>

<div id="resultScreen" class="hidden max-w-2xl mx-auto px-4 py-10">
  <div class="result-card bg-white border border-[#e8e4dc] rounded-3xl p-8 shadow-sm">
    <div class="flex flex-col items-center mb-8">
      <div class="relative w-36 h-36 mb-4">
        <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
          <circle cx="50" cy="50" r="45" fill="none" stroke="#e8e4dc" stroke-width="8"/>
          <circle cx="50" cy="50" r="45" fill="none" stroke="#16a34a" stroke-width="8" stroke-linecap="round" class="score-ring" id="scoreRing"/>
        </svg>
        <div class="absolute inset-0 flex flex-col items-center justify-center">
          <span id="scorePct" class="text-3xl font-bold text-[#1a1814]">0%</span>
          <span class="text-xs text-[#7a756b] mt-0.5">Skor</span>
        </div>
      </div>
      <h2 id="resultTitle" class="text-2xl font-bold text-[#1a1814] mb-1 text-center"></h2>
      <p id="resultSub" class="text-sm text-[#7a756b] text-center"></p>
    </div>
    <div class="grid grid-cols-3 gap-3 mb-6">
      <div class="text-center p-3 bg-[#f0fdf4] rounded-xl border border-[#bbf7d0]"><p class="text-2xl font-bold text-green-600" id="statCorrect">0</p><p class="text-xs text-[#7a756b] mt-0.5">Benar</p></div>
      <div class="text-center p-3 bg-red-50 rounded-xl border border-red-100"><p class="text-2xl font-bold text-red-500" id="statWrong">0</p><p class="text-xs text-[#7a756b] mt-0.5">Salah</p></div>
      <div class="text-center p-3 bg-[#f7f6f3] rounded-xl border border-[#e8e4dc]"><p class="text-2xl font-bold text-[#7a756b]" id="statSkip">0</p><p class="text-xs text-[#7a756b] mt-0.5">Dilewati</p></div>
    </div>
    <div class="flex items-center justify-center gap-2 text-sm text-[#7a756b] mb-6">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      Waktu: <strong id="timeUsed" class="text-[#1a1814]">—</strong>
    </div>
    <div id="reviewList" class="space-y-2.5 mb-6 max-h-64 overflow-y-auto pr-1"></div>
    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-[#e8e4dc]">
      <button onclick="restartQuiz()" class="flex-1 py-3 rounded-xl border-2 border-[#16a34a] text-[#16a34a] font-semibold text-sm hover:bg-[#f0fdf4] transition">Coba Lagi</button>
      <a href="{{ route('learning.index') }}" class="flex-1 py-3 rounded-xl bg-[#16a34a] text-white font-semibold text-sm text-center hover:bg-green-700 transition shadow-sm shadow-green-200">Kembali ke Kursus</a>
    </div>
  </div>
</div>

<canvas id="fwCanvas"></canvas>

@php
  // Mapping key_answer misal 'A' menjadi index 0, 'B' menjadi 1, dst.
  $jsQuestions = $room->questions->map(function($q) {
      $ansMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
      $key = strtoupper(trim($q->key_answer));
      return [
          'text' => $q->question,
          'code' => null, 
          'options' => [$q->optionA, $q->optionB, $q->optionC, $q->optionD],
          'answer' => $ansMap[$key] ?? 0,
          'keyAnswer' => $key,
          'explanation' => 'Kunci jawaban yang benar adalah ' . $key . '.'
      ];
  });
@endphp

<script>
const questions = {!! json_encode($jsQuestions) !!};
const roomId = {{ $room->id }};
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Logika untuk menampilkan kunci (Attempts >= 5)
// Attempts berbasis 0 untuk perhitungan, jadi jika di database tercatat 4 kali, yang sekarang adalah ke-5.
const attemptsDb = {{ $attempts ?? 0 }};
const showKey = attemptsDb >= 4; // Artinya percobaan ke-5 dan seterusnya

let cur = 0;
let answers = new Array(questions.length).fill(null);
let timerSec = 15 * 60;
let timerInt = null;
let startTime = Date.now();

document.getElementById('qTotal').textContent = questions.length;
buildDots();
if(questions.length > 0) { renderQ(0); startTimer(); } else { document.getElementById('questionWrap').innerHTML = "<p class='p-5'>Belum ada soal.</p>"; }

function startTimer() {
  timerInt = setInterval(() => {
    timerSec--;
    if (timerSec <= 0) { timerSec = 0; clearInterval(timerInt); submitQuiz(); }
    const m = String(Math.floor(timerSec/60)).padStart(2,'0');
    const s = String(timerSec%60).padStart(2,'0');
    const el = document.getElementById('timerDisplay');
    el.textContent = m+':'+s;
    if (timerSec <= 60) { el.classList.add('timer-warn'); document.getElementById('timerIcon').setAttribute('stroke','#ef4444'); }
  }, 1000);
}

function renderQ(idx) {
  const q = questions[idx];
  const wrap = document.getElementById('questionWrap');
  const codeBlock = q.code ? '<pre class="bg-[#1e293b] text-[#86efac] rounded-xl p-4 text-sm font-mono leading-relaxed overflow-x-auto mt-3"><code>'+escHtml(q.code)+'</code></pre>' : '';
  
  const opts = q.options.map((o,i) => {
    const ua = answers[idx];
    let cls = '';
    
    // Logika pewarnaan card berdasarkan showKey
    if (ua !== null) {
      if (showKey) {
        if (i === q.answer) cls = 'reveal-correct'; 
        else if (i === ua) cls = 'wrong';
      } else {
        if (i === ua) cls = 'selected';
      }
    }
    
    return `<div class="option-card ${cls}" onclick="selectAnswer(${i})" id="opt${i}">
      <span class="option-letter">${String.fromCharCode(65+i)}</span>
      <span class="text-sm text-[#1a1814] leading-snug">${o}</span>
    </div>`;
  }).join('');

  const expShow = answers[idx] !== null && showKey;
  wrap.innerHTML = `<div class="q-enter">
    <div class="flex items-center gap-2 mb-4">
      <span class="px-3 py-1 bg-[#16a34a]/10 text-[#16a34a] text-xs font-bold rounded-full">Soal ${idx+1}</span>
    </div>
    <div class="bg-white border border-[#e8e4dc] rounded-2xl p-5 mb-4">
      <p class="text-base font-semibold text-[#1a1814] leading-relaxed">${q.text}</p>
      ${codeBlock}
    </div>
    <div class="space-y-2.5" id="optsWrap">${opts}</div>
    <div id="expBox" class="${expShow ? 'mt-3 p-3.5 rounded-xl border-l-4 border-[#16a34a] bg-[#f0fdf4] text-sm text-[#166534] leading-relaxed' : 'hidden'}">${expShow ? '💡 '+q.explanation : ''}</div>
  </div>`;
  updateNavBtns(); updateCounter(); updateDots(); updateAnsweredCount();
}

function selectAnswer(i) {
  // Opsi: Jika showKey (attempts >= 5) sudah aktif dan jawaban diklik, 
  // kita kunci agar tidak bisa diubah karena jawaban yang benar sudah terungkap.
  // Namun jika showKey false (percobaan 1-4), user BEBAS mengubah jawaban.
  if (showKey && answers[cur] !== null) return; 

  // Simpan jawaban baru
  answers[cur] = i;
  const q = questions[cur];
  
  q.options.forEach((_,j) => {
    const el = document.getElementById('opt'+j);
    if (!el) return;
    
    // 1. Reset semua class ke default (hapus warna hijau/merah/selected dari pilihan sebelumnya)
    el.className = 'option-card';
    
    // 2. Terapkan ulang class sesuai jawaban yang baru dipilih
    if (showKey) {
      if (j === q.answer) el.className = 'option-card reveal-correct';
      else if (j === i) el.className = 'option-card wrong';
    } else {
      if (j === i) el.className = 'option-card selected';
    }
  });

  // Tampilkan kotak penjelasan jika showKey aktif
  if (showKey) {
    const exp = document.getElementById('expBox');
    exp.classList.remove('hidden');
    exp.className = 'mt-3 p-3.5 rounded-xl border-l-4 border-[#16a34a] bg-[#f0fdf4] text-sm text-[#166534] leading-relaxed';
    exp.textContent = '💡 ' + q.explanation;
  }
  
  // Perbarui indikator UI lainnya
  updateDots(); updateAnsweredCount(); updateNavBtns();
}

function navigate(dir) {
  const n = cur + dir;
  if (n < 0 || n >= questions.length) return;
  cur = n; renderQ(cur);
}

function goToQ(idx) { cur = idx; renderQ(idx); }

function updateNavBtns() {
  document.getElementById('prevBtn').disabled = cur === 0;
  const isLast = cur === questions.length - 1;
  const nb = document.getElementById('nextBtn');
  if (isLast) {
    nb.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Kumpulkan';
    nb.onclick = submitQuiz;
    document.getElementById('submitBtnPanel').classList.remove('hidden'); document.getElementById('submitBtnPanel').classList.add('flex');
    document.getElementById('mobileSubmit').classList.remove('hidden');
  } else {
    nb.innerHTML = 'Selanjutnya <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m9 18 6-6-6-6"/></svg>';
    nb.onclick = () => navigate(1);
    document.getElementById('submitBtnPanel').classList.add('hidden'); document.getElementById('submitBtnPanel').classList.remove('flex');
    document.getElementById('mobileSubmit').classList.add('hidden');
  }
}

function updateCounter() {
  document.getElementById('qCounter').textContent = cur + 1;
  document.getElementById('progressBar').style.width = ((cur+1)/questions.length*100)+'%';
}

function updateAnsweredCount() {
  const c = answers.filter(a => a !== null).length;
  document.getElementById('answeredCount').textContent = c+' dari '+questions.length+' dijawab';
}

function buildDots() {
  document.getElementById('dotGrid').innerHTML = questions.map((_,i) => `<button class="q-dot ${i===0?'current':''}" id="dot${i}" onclick="goToQ(${i})">${i+1}</button>`).join('');
}

function updateDots() {
  questions.forEach((_,i) => {
    const d = document.getElementById('dot'+i);
    if (!d) return;
    d.className = 'q-dot';
    if (i === cur) d.classList.add('current');
    else if (answers[i] !== null) d.classList.add('answered');
  });
}

function submitQuiz() {
  clearInterval(timerInt);
  const elapsed = Math.round((Date.now()-startTime)/1000);
  const correct = answers.filter((a,i) => a === questions[i].answer).length;
  const wrong   = answers.filter((a,i) => a !== null && a !== questions[i].answer).length;
  const skip    = answers.filter(a => a === null).length;
  const pct     = questions.length > 0 ? Math.round(correct/questions.length*100) : 0;

  // ==== API CALL BACKEND ====
  // Mengirim data score untuk disimpan ke tabel Score dan LearnProgress
  fetch(`/kuis/${roomId}/submit`, {
      method: 'POST',
      headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Content-Type': 'application/json'
      },
      body: JSON.stringify({
          correct_answers: correct,
          total_questions: questions.length,
          score_percentage: pct
      })
  }).then(res => res.json())
    .then(data => console.log('Skor berhasil disimpan:', data))
    .catch(err => console.error('Gagal menyimpan skor:', err));

  document.getElementById('scorePct').textContent    = pct+'%';
  document.getElementById('statCorrect').textContent = correct;
  document.getElementById('statWrong').textContent   = wrong;
  document.getElementById('statSkip').textContent    = skip;
  document.getElementById('timeUsed').textContent    = Math.floor(elapsed/60)+'m '+elapsed%60+'s';

  const grades = [
    {min:90, t:'Luar Biasa! 🏆', s:'Penguasaan materi sangat baik. Terus pertahankan!'},
    {min:70, t:'Bagus Sekali! 🎉', s:'Pemahaman Anda sudah baik. Sedikit lagi sempurna.'},
    {min:50, t:'Cukup Baik 👍', s:'Ada beberapa konsep yang perlu diperdalam lagi.'},
    {min:0,  t:'Perlu Belajar Lagi 📚', s:'Jangan menyerah! Review materi dan coba lagi.'},
  ];
  const g = grades.find(x => pct >= x.min);
  document.getElementById('resultTitle').textContent = g.t;
  document.getElementById('resultSub').textContent   = g.s;

  document.getElementById('reviewList').innerHTML = questions.map((q,i) => {
    const ua = answers[i], ok = ua === q.answer, skipped = ua === null;
    return `<div class="flex items-start gap-3 p-3 rounded-xl ${ok?'bg-[#f0fdf4] border border-[#bbf7d0]':skipped?'bg-[#f7f6f3] border border-[#e8e4dc]':'bg-red-50 border border-red-100'}">
      <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold ${ok?'bg-green-500 text-white':skipped?'bg-[#e8e4dc] text-[#7a756b]':'bg-red-500 text-white'}">${ok?'✓':skipped?'—':'✗'}</div>
      <div class="min-w-0">
        <p class="text-xs font-semibold text-[#1a1814]">Soal ${i+1}: ${q.text.slice(0,55)}${q.text.length>55?'…':''}</p>
        <p class="text-xs text-[#7a756b] mt-0.5">
          ${skipped ? 'Tidak dijawab' : 'Jawaban Anda: <b>'+q.options[ua]+'</b>'}
          ${!ok && !skipped && showKey ? `<br>Benar: <b class="text-green-600">${q.keyAnswer} (${q.options[q.answer]})</b>` : ''}
        </p>
      </div>
    </div>`;
  }).join('');

  document.getElementById('quizScreen').style.display  = 'none';
  document.getElementById('mobileSubmit').style.display = 'none';
  document.getElementById('resultScreen').classList.remove('hidden');

  setTimeout(() => {
    const ring = document.getElementById('scoreRing');
    ring.style.strokeDashoffset = 283 - (pct/100)*283;
    ring.style.stroke = pct>=70?'#16a34a':pct>=50?'#f59e0b':'#ef4444';
  }, 120);

  if (pct >= 70) fireworks(pct);
}

function restartQuiz() {
  // Reload halaman untuk mereset dan memanggil database guna mengambil attempts terbaru
  window.location.reload();
}

// Visual Effects
function fireworks(score) {
  const cv = document.getElementById('fwCanvas'); const ctx = cv.getContext('2d');
  cv.width = window.innerWidth; cv.height = window.innerHeight;
  const COLS = ['#16a34a','#22c55e','#4ade80','#fbbf24','#f59e0b','#60a5fa','#a78bfa','#fb7185','#fff'];
  const pts = [];
  function burst(x,y,n=80) { for(let i=0;i<n;i++){ pts.push({ x,y, vx:(Math.random()-.5)*9, vy:(Math.random()-.5)*9-2, alpha:1, color:COLS[Math.floor(Math.random()*COLS.length)], r:Math.random()*4+1.5, decay:Math.random()*.015+.011, g:.18 }); } }
  const w=cv.width,h=cv.height;
  burst(w*.25,h*.35,90); burst(w*.75,h*.3,90); burst(w*.5,h*.18,80);
  if(score>=90){ setTimeout(()=>burst(w*.15,h*.5,80),350); setTimeout(()=>burst(w*.85,h*.45,80),600); setTimeout(()=>burst(w*.5,h*.65,100),850); }
  let bc=0; const bi=setInterval(()=>{ burst(Math.random()*w,Math.random()*h*.6,60); if(++bc>=5) clearInterval(bi); },320);
  function draw(){
    ctx.clearRect(0,0,w,h);
    for(let i=pts.length-1;i>=0;i--){
      const p=pts[i]; p.x+=p.vx; p.vy+=p.g; p.y+=p.vy; p.alpha-=p.decay;
      if(p.alpha<=0){pts.splice(i,1);continue;}
      ctx.save(); ctx.globalAlpha=p.alpha; ctx.fillStyle=p.color; ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2); ctx.fill(); ctx.restore();
    }
    if(pts.length>0) requestAnimationFrame(draw); else ctx.clearRect(0,0,w,h);
  }
  draw();
}
function escHtml(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
window.addEventListener('resize',()=>{ const c=document.getElementById('fwCanvas'); c.width=window.innerWidth; c.height=window.innerHeight; });
</script>
</body>
</html>