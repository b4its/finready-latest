{{-- resources/views/learning/index_learning.blade.php --}}
@php
    // 1. Ambil ID User yang sedang login
    $userId = $user_account->id ?? auth()->id(); 
    
    // 2. Tarik semua idModulContent yang sudah dikerjakan
    $completedContentIds = \App\Models\LearnProgress::where('idUsers', $userId)
                            ->pluck('idModulContent')
                            ->toArray();
                            
    // 3. Hitung Total Poin dari learn_progress milik user ini
    $totalPoints = \App\Models\LearnProgress::where('idUsers', $userId)->sum('point');
                            
    // 4. Kalkulasi Progres Keseluruhan
    $totalAllContents = 0;
    $totalCompletedAll = 0;
    $completedModulesCount = 0;
    foreach($modules as $m) {
        $cCount = $m->contents->count();
        $compCount = $m->contents->whereIn('id', $completedContentIds)->count();
        
        $totalAllContents += $cCount;
        $totalCompletedAll += $compCount;
        
        if($cCount > 0 && $compCount == $cCount) {
            $completedModulesCount++;
        }
    }
    
    $overallProgressPct = $totalAllContents > 0 ? round(($totalCompletedAll / $totalAllContents) * 100) : 0;
    
    // 5. Global Flag untuk sistem Kunci (Lock) berurutan
    $isGlobalUnlock = true;
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Finready - Learning</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #f7f6f3; --card: #ffffff; --border: #e8e4dc; --text: #1a1814;
      --muted: #7a756b; --primary: #16a34a; --primary-light: #dcfce7;
      --success: #16a34a; --success-light: #dcfce7; --secondary: #f0ede7;
      --radius: 16px; --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
    }
    [data-theme="dark"] {
      --bg: #111110; --card: #1c1b19; --border: #2e2c28; --text: #f0ede7;
      --muted: #8a8479; --primary: #22c55e; --primary-light: #14291e;
      --success: #22c55e; --success-light: #14291e; --secondary: #252320;
      --shadow: 0 1px 3px rgba(0,0,0,.3), 0 4px 12px rgba(0,0,0,.2);
    }
    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; font-size: 15px; line-height: 1.6; transition: background .25s, color .25s; }

    /* ── HEADER ── */
    .header { background: var(--card); border-bottom: 1px solid var(--border); transition: background .25s, border-color .25s; }
    .header-inner { max-width: 1200px; margin: 0 auto; padding: 36px 24px; display: flex; flex-direction: column; gap: 24px; }
    .top-bar { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .top-bar-left { display: flex; align-items: center; gap: 20px; }
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--muted); cursor: pointer; transition: color .15s; }
    .breadcrumb:hover { color: var(--text); }
    .breadcrumb svg { width: 14px; height: 14px; }

    /* ── DARK MODE TOGGLE ── */
    .dark-toggle { display: flex; align-items: center; gap: 8px; background: var(--secondary); border: 1px solid var(--border); border-radius: 99px; padding: 5px 12px 5px 8px; cursor: pointer; font-size: 12px; font-weight: 600; color: var(--muted); font-family: inherit; transition: background .2s, border-color .2s, color .2s; white-space: nowrap; }
    .dark-toggle:hover { color: var(--text); border-color: var(--muted); }
    .toggle-track { width: 32px; height: 18px; background: var(--border); border-radius: 99px; position: relative; transition: background .25s; flex-shrink: 0; }
    [data-theme="dark"] .toggle-track { background: var(--primary); }
    .toggle-thumb { width: 14px; height: 14px; background: #fff; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: transform .25s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
    [data-theme="dark"] .toggle-thumb { transform: translateX(14px); }
    .toggle-icon { width: 14px; height: 14px; }

    /* ── HEADER ROW ── */
    .header-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
    .header-text h1 { font-size: clamp(26px, 4vw, 36px); font-weight: 700; letter-spacing: -0.03em; line-height: 1.15; margin-bottom: 10px; color: var(--text); }
    .header-text p { max-width: 580px; color: var(--muted); font-size: 15px; line-height: 1.65; }
    .btn-continue { display: inline-flex; align-items: center; gap: 8px; background: var(--primary); color: #fff; border: none; border-radius: 12px; padding: 13px 22px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; white-space: nowrap; transition: background .18s, transform .12s; flex-shrink: 0; align-self: flex-start; text-decoration: none; }
    .btn-continue:hover { background: #15803d; transform: translateY(-1px); }
    [data-theme="dark"] .btn-continue:hover { background: #16a34a; }
    .btn-continue svg { width: 15px; height: 15px; }
    .progress-wrap { display: flex; flex-direction: column; gap: 8px; }
    .progress-labels { display: flex; justify-content: space-between; font-size: 13px; }
    .progress-labels span:first-child { color: var(--muted); }
    .progress-labels span:last-child { font-weight: 600; }
    .progress-bar-bg { height: 8px; background: var(--secondary); border-radius: 99px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: var(--primary); border-radius: 99px; transition: width .6s ease; }

    /* ── LAYOUT ── */
    .layout { max-width: 1200px; margin: 0 auto; padding: 36px 24px; display: flex; gap: 32px; align-items: flex-start; }
    .main-col { flex: 1; min-width: 0; }
    .sidebar-col { width: 296px; flex-shrink: 0; position: sticky; top: 24px; display: flex; flex-direction: column; gap: 16px; }

    /* ── CARD ── */
    .card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); transition: background .25s, border-color .25s; }
    .card-header { padding: 20px 20px 12px; border-bottom: 1px solid var(--border); }
    .card-header h3 { font-size: 15px; font-weight: 600; }
    .card-body { padding: 16px 20px; }

    /* ── SIDEBAR STAT ── */
    .stat-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; }
    .stat-row + .stat-row { border-top: 1px solid var(--border); }
    .stat-icon { width: 40px; height: 40px; background: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .25s; }
    .stat-icon svg { width: 18px; height: 18px; color: var(--text); }
    .stat-icon.highlight { background: var(--primary-light); color: var(--primary); }
    .stat-label { font-size: 14px; font-weight: 600; }
    .stat-sub { font-size: 12px; color: var(--muted); margin-top: 1px; }
    .stat-point-text { font-size: 18px; font-weight: 700; color: var(--primary); }
    .instructor-row { display: flex; align-items: center; gap: 12px; }
    .avatar { width: 44px; height: 44px; background: var(--primary-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .25s; }
    .avatar svg { width: 22px; height: 22px; color: var(--primary); }

    /* ── LEARNING PATH ── */
    .lp-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
    .lp-header h2 { font-size: 20px; font-weight: 700; letter-spacing: -0.02em; }
    .lp-header span { font-size: 13px; color: var(--muted); }

    /* ── MODULE CARD ── */
    .module-wrap { display: flex; gap: 16px; position: relative; }
    .module-wrap + .module-wrap { margin-top: 24px; }
    .timeline-col { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; width: 40px; }
    .module-dot { width: 40px; height: 40px; border-radius: 50%; border: 2px solid var(--border); background: var(--card); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: var(--muted); flex-shrink: 0; position: relative; z-index: 1; transition: all .2s; }
    .module-dot.completed { border-color: var(--success); background: var(--success); color: #fff; }
    .module-dot.in-progress { border-color: var(--primary); background: var(--primary); color: #fff; }
    .module-dot svg { width: 17px; height: 17px; }
    .timeline-line { flex: 1; width: 2px; background: var(--border); margin-top: 4px; min-height: 20px; transition: background .25s; }
    .timeline-line.completed { background: var(--success); }
    .module-card { flex: 1; border: 1px solid var(--border); border-radius: var(--radius); background: var(--card); box-shadow: var(--shadow); overflow: hidden; transition: box-shadow .2s, background .25s, border-color .25s; margin-bottom: 4px; }
    .module-card.in-progress { box-shadow: 0 0 0 2px var(--primary), var(--shadow); }
    .module-card.completed { border-color: var(--success); }
    .module-card-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 20px; background: none; border: none; cursor: pointer; text-align: left; font-family: inherit; gap: 16px; }
    .module-card-info { flex: 1; min-width: 0; }
    .module-badge-row { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .module-num-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--muted); }
    .module-num-label.completed { color: var(--success); }
    .module-num-label.in-progress { color: var(--primary); }
    .badge { font-size: 11px; font-weight: 600; padding: 2px 9px; border-radius: 99px; }
    .badge-success { background: var(--success-light); color: var(--success); }
    .badge-primary { background: var(--primary-light); color: var(--primary); }
    .module-title { font-size: 17px; font-weight: 700; letter-spacing: -0.01em; color: var(--text); margin-bottom: 4px; }
    .module-title.locked { color: var(--muted); }
    .module-desc { font-size: 13px; color: var(--muted); line-height: 1.55; }
    .module-meta { display: flex; align-items: center; gap: 4px; font-size: 13px; color: var(--muted); white-space: nowrap; }
    .module-meta svg { width: 14px; height: 14px; }
    .chevron { width: 18px; height: 18px; color: var(--muted); transition: transform .25s; flex-shrink: 0; }
    .chevron.open { transform: rotate(180deg); }
    .module-progress-bar { border-top: 1px solid var(--border); padding: 12px 20px; }
    .module-progress-labels { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px; }
    .module-progress-labels span:first-child { color: var(--muted); }
    .module-progress-labels span:last-child { font-weight: 600; }
    .mini-bar-bg { height: 5px; background: var(--secondary); border-radius: 99px; overflow: hidden; transition: background .25s; }
    .mini-bar-fill { height: 100%; background: var(--success); border-radius: 99px; transition: width .4s ease; }
    .lessons-list { border-top: 1px solid var(--border); padding: 12px; display: none; }
    .lessons-list.open { display: block; }
    .lesson-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 10px; cursor: pointer; transition: background .15s; text-decoration: none; }
    .lesson-item:hover { background: var(--secondary); }
    .lesson-item.locked { opacity: .6; cursor: not-allowed; }
    .lesson-item.locked:hover { background: transparent; }
    .lesson-left { display: flex; align-items: center; gap: 10px; }
    .lesson-icon-wrap { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .25s; background: var(--primary-light); color: var(--primary); }
    .lesson-icon-wrap.completed { background: var(--success-light); color: var(--success); }
    .lesson-icon-wrap.locked { background: var(--secondary); color: var(--muted); }
    .lesson-icon-wrap svg { width: 15px; height: 15px; }
    .lesson-name { font-size: 13px; font-weight: 600; color: var(--text); }
    .lesson-item.locked .lesson-name { color: var(--muted); }
    .lesson-type { font-size: 11px; color: var(--muted); margin-top: 1px; text-transform: capitalize; }
    .lesson-dur { display: flex; align-items: center; gap: 4px; font-size: 12px; color: var(--muted); }
    .lesson-dur svg { width: 12px; height: 12px; }

    /* ── EMPTY STATE ── */
    .empty-state-global {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 72px 32px;
      background: var(--card);
      border: 1.5px dashed var(--border);
      border-radius: var(--radius);
      text-align: center;
      gap: 20px;
      transition: background .25s, border-color .25s;
    }
    .empty-state-global .empty-icon-wrap {
      width: 72px;
      height: 72px;
      background: var(--secondary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background .25s;
    }
    .empty-state-global .empty-icon-wrap svg {
      width: 32px;
      height: 32px;
      color: var(--muted);
    }
    .empty-state-global .empty-title {
      font-size: 16px;
      font-weight: 700;
      color: var(--text);
      letter-spacing: -0.01em;
      margin-bottom: 6px;
    }
    .empty-state-global .empty-desc {
      font-size: 13px;
      color: var(--muted);
      max-width: 340px;
      line-height: 1.65;
    }
    .empty-state-inline {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 16px 14px;
      background: var(--secondary);
      border-radius: 10px;
      margin: 4px 0;
      transition: background .25s;
    }
    .empty-state-inline .empty-inline-icon {
      width: 32px;
      height: 32px;
      background: var(--border);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: background .25s;
    }
    .empty-state-inline .empty-inline-icon svg {
      width: 15px;
      height: 15px;
      color: var(--muted);
    }
    .empty-state-inline span {
      font-size: 13px;
      color: var(--muted);
      font-weight: 500;
      line-height: 1.5;
    }

    /* ── MOBILE ── */
    .mobile-sidebar { display: none; border-top: 1px solid var(--border); background: var(--card); padding: 24px; transition: background .25s, border-color .25s; }
    @media (max-width: 900px) { .sidebar-col { display: none; } .mobile-sidebar { display: block; } .header-row { flex-direction: column; } }
    @media (max-width: 600px) { .layout { padding: 24px 16px; } .header-inner { padding: 24px 16px; } .module-meta { display: none; } }
    svg { vertical-align: middle; }
    a { text-decoration: none; color: inherit; }
  </style>
</head>
<body>

{{-- ════════════════════════════════════════
     HEADER
════════════════════════════════════════ --}}
<header class="header">
  <div class="header-inner">
    <div class="top-bar">
      <div class="top-bar-left">
        <div class="breadcrumb">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
          </svg>
          <span>Pembelajaran</span>
        </div>
      </div>
      <button class="dark-toggle" onclick="toggleDark()" aria-label="Toggle dark mode">
        <svg class="toggle-icon" id="icon-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="4"/>
          <path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
        </svg>
        <svg class="toggle-icon" id="icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
        <div class="toggle-track"><div class="toggle-thumb"></div></div>
        <span id="toggle-label">Dark</span>
      </button>
    </div>

    <div class="header-row">
      <a href="{{ route('filament.umkm.pages.dashboard') }}" class="btn-continue">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
      </a>
    </div>

    <div class="header-row">
      <div class="header-text">
        <h1>Pembelajaran Akuntansi</h1>
        <p>Menu ini menyediakan beberapa materi berupa e-book, video edukasi yang relevan untuk Anda pelajari dan pastikan Anda memahaminya karena akan ada kuis yang akan mempengaruhi poin Anda.</p>
      </div>
    </div>

    <div class="progress-wrap">
      <div class="progress-labels">
        <span>Progress Keseluruhan Anda</span>
        <span>{{ $overallProgressPct }}% selesai</span>
      </div>
      <div class="progress-bar-bg">
        <div class="progress-bar-fill" style="width:{{ $overallProgressPct }}%"></div>
      </div>
    </div>
  </div>
</header>

{{-- ════════════════════════════════════════
     MAIN LAYOUT
════════════════════════════════════════ --}}
<div class="layout">
  <main class="main-col">

    <div class="lp-header">
      <h2>Learning Path</h2>
      <span>{{ $totalModules }} modul</span>
    </div>

    {{-- ════════════════════════════════════════
         LOOP MODUL — jika $modules kosong, tampilkan empty state global
    ════════════════════════════════════════ --}}
    @forelse($modules as $index => $module)

    @php
        // Kalkulasi Progres Per Modul
        $moduleContentsCount = $module->contents->count();
        $completedInModule   = $module->contents->whereIn('id', $completedContentIds)->count();
        $moduleProgressPct   = $moduleContentsCount > 0
                                ? round(($completedInModule / $moduleContentsCount) * 100)
                                : 0;

        // Status Modul
        if ($moduleContentsCount > 0 && $completedInModule == $moduleContentsCount) {
            $modStatusClass = 'completed';
        } elseif ($completedInModule > 0) {
            $modStatusClass = 'in-progress';
        } else {
            $modStatusClass = '';
        }
    @endphp

    <div class="module-wrap">
      <div class="timeline-col">
        <div class="module-dot {{ $modStatusClass }}">
          @if($modStatusClass == 'completed')
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          @else
            <span>{{ $loop->iteration }}</span>
          @endif
        </div>
        @if(!$loop->last)
          <div class="timeline-line {{ $modStatusClass == 'completed' ? 'completed' : '' }}"></div>
        @endif
      </div>

      <div class="module-card {{ $modStatusClass }}" id="mc{{ $module->id }}">
        <button class="module-card-btn" onclick="toggleModule({{ $module->id }})">
          <div class="module-card-info">
            <div class="module-badge-row">
              <span class="module-num-label {{ $modStatusClass }}">Module {{ $loop->iteration }}</span>
              <span class="badge badge-primary">Tersedia</span>
            </div>
            <div class="module-title">{{ $module->name }}</div>
            <div class="module-desc">{{ $module->description ?? 'Deskripsi modul belum tersedia.' }}</div>
          </div>
          <span class="module-meta">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/>
              <path stroke-linecap="round" d="M12 6v6l4 2"/>
            </svg>
            Point: {{ $module->max_point }}
          </span>
          <svg class="chevron" id="chev{{ $module->id }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/>
          </svg>
        </button>

        <div class="module-progress-bar">
          <div class="module-progress-labels">
            <span>{{ $completedInModule }} dari {{ $moduleContentsCount }} materi diselesaikan</span>
            <span>{{ $moduleProgressPct }}%</span>
          </div>
          <div class="mini-bar-bg">
            <div class="mini-bar-fill" style="width:{{ $moduleProgressPct }}%"></div>
          </div>
        </div>

        <div class="lessons-list" id="ll{{ $module->id }}">

          {{-- ────────────────────────────────────────
               LOOP KONTEN MATERI
               @empty hanya tampil jika contents kosong DAN rooms juga kosong
          ──────────────────────────────────────── --}}
          @forelse($module->contents as $content)
          @php
            // Cek apakah konten ini sudah dikerjakan
            $isCompleted = in_array($content->id, $completedContentIds);

            // Logika Kunci berurutan
            $isLocked = !$isGlobalUnlock;

            // Jika item belum selesai, flag global menjadi false untuk mengunci materi setelahnya
            if (!$isCompleted) {
                $isGlobalUnlock = false;
            }
          @endphp

          @if($isLocked)
          {{-- Materi Terkunci --}}
          <div class="lesson-item locked">
            <div class="lesson-left">
              <div class="lesson-icon-wrap locked">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <div class="lesson-name">{{ $content->title }}</div>
                <div class="lesson-type">{{ $content->type ?? 'Materi' }}</div>
              </div>
            </div>
            <div class="lesson-dur">
              <span style="color:var(--muted); font-size:12px; font-weight:600;">Terkunci</span>
            </div>
          </div>
          @else
          {{-- Materi Terbuka / Selesai --}}
          <a href="{{ route('learning.show', $content->id) }}" class="lesson-item">
            <div class="lesson-left">
              <div class="lesson-icon-wrap {{ $isCompleted ? 'completed' : 'in-progress' }}">
                @if($isCompleted)
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                  </svg>
                @else
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polygon points="10,8 16,12 10,16" fill="currentColor" stroke="none"/>
                  </svg>
                @endif
              </div>
              <div>
                <div class="lesson-name">{{ $content->title }}</div>
                <div class="lesson-type">{{ $content->type ?? 'Materi' }}</div>
              </div>
            </div>
            <div class="lesson-dur">
              <span style="color:{{ $isCompleted ? 'var(--success)' : 'var(--primary)' }}; font-size:12px; font-weight:600;">
                {{ $isCompleted ? 'Selesai' : 'Buka Materi' }}
              </span>
            </div>
          </a>
          @endif

          @empty
          {{-- Empty state konten — hanya tampil jika rooms juga benar-benar kosong --}}
          @if($module->rooms->isEmpty())
          <div class="empty-state-inline">
            <div class="empty-inline-icon">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/>
              </svg>
            </div>
            <span>Saat ini tidak ada data yang sedang tersedia</span>
          </div>
          @endif
          @endforelse

          {{-- ────────────────────────────────────────
               LOOP KUIS (ROOMS)
          ──────────────────────────────────────── --}}
          @foreach($module->rooms as $room)
          @php
            // Kuis (Room) juga ikut terpengaruh oleh sistem kunci global
            $isRoomLocked = !$isGlobalUnlock;
          @endphp

          @if($isRoomLocked)
          {{-- Kuis Terkunci --}}
          <div class="lesson-item locked">
            <div class="lesson-left">
              <div class="lesson-icon-wrap locked">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <div>
                <div class="lesson-name">{{ $room->name }}</div>
                <div class="lesson-type">Kuis ({{ $room->questions->count() }} Soal)</div>
              </div>
            </div>
            <div class="lesson-dur">
              <span style="color:var(--muted); font-size:12px; font-weight:700;">Terkunci</span>
            </div>
          </div>
          @else
          {{-- Kuis Terbuka --}}
          <div class="lesson-item" style="background: var(--success-light);">
            <div class="lesson-left">
              <div class="lesson-icon-wrap" style="background:var(--success); color:#fff;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:16px;height:16px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                </svg>
              </div>
              <div>
                <div class="lesson-name" style="color:var(--success);">{{ $room->name }}</div>
                <div class="lesson-type" style="color:var(--success);">Kuis ({{ $room->questions->count() }} Soal)</div>
              </div>
            </div>
            <div class="lesson-dur">
              <a href="{{ route('quiz.show', $room->id) }}" style="color:var(--success); font-size:12px; font-weight:700;">
                Mulai Kuis
              </a>
            </div>
          </div>
          @endif

          @endforeach

        </div>{{-- /lessons-list --}}
      </div>{{-- /module-card --}}
    </div>{{-- /module-wrap --}}

    @empty
    {{-- ════════════════════════════════════════
         EMPTY STATE GLOBAL — tidak ada modul sama sekali
    ════════════════════════════════════════ --}}
    <div class="empty-state-global">
      <div class="empty-icon-wrap">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
        </svg>
      </div>
      <div>
        <div class="empty-title">Saat ini tidak ada data yang sedang tersedia</div>
        <div class="empty-desc">Modul pembelajaran belum tersedia. Silakan kembali lagi nanti.</div>
      </div>
    </div>

    @endforelse

  </main>

  {{-- ════════════════════════════════════════
       SIDEBAR
  ════════════════════════════════════════ --}}
  <aside class="sidebar-col">
    <div class="card">
      <div class="card-header"><h3>Course Overview</h3></div>
      <div class="card-body">

        {{-- Total Poin --}}
        <div class="stat-row">
          <div class="stat-icon highlight">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"/>
            </svg>
          </div>
          <div>
            <div class="stat-point-text">{{ number_format($totalPoints) }} Poin</div>
            <div class="stat-sub">Total Diperoleh</div>
          </div>
        </div>

        {{-- Jumlah Materi --}}
        <div class="stat-row">
          <div class="stat-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
            </svg>
          </div>
          <div>
            <div class="stat-label">{{ $totalLessons ?? 0 }} Materi</div>
            <div class="stat-sub">{{ $totalModules }} modul</div>
          </div>
        </div>

        {{-- Modul Selesai --}}
        <div class="stat-row">
          <div class="stat-icon">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
          </div>
          <div>
            <div class="stat-label">{{ $completedModulesCount }} dari {{ $totalModules }}</div>
            <div class="stat-sub">Modul selesai</div>
          </div>
        </div>

      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>Akun Anda</h3></div>
      <div class="card-body">
        <div class="instructor-row">
          <div class="avatar">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
            </svg>
          </div>
          <div>
            <div class="stat-label">{{ $user_account->name ?? 'Anonymous' }}</div>
            <div class="stat-sub">{{ $user_account->role ?? 'UMKM' }}</div>
          </div>
        </div>
      </div>
    </div>
  </aside>
</div>

<script>
  function toggleModule(id) {
    const ll   = document.getElementById('ll'   + id);
    const chev = document.getElementById('chev' + id);
    if (!ll) return;
    const isOpen = ll.classList.contains('open');
    ll.classList.toggle('open', !isOpen);
    if (chev) chev.classList.toggle('open', !isOpen);
  }

  function toggleDark() {
    const html   = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    html.setAttribute('data-theme', isDark ? 'light' : 'dark');
    const iconLight = document.getElementById('icon-light');
    const iconDark  = document.getElementById('icon-dark');
    const label     = document.getElementById('toggle-label');
    if (isDark) {
      iconLight.style.display = '';
      iconDark.style.display  = 'none';
      label.textContent       = 'Dark';
    } else {
      iconLight.style.display = 'none';
      iconDark.style.display  = '';
      label.textContent       = 'Light';
    }
  }
</script>
</body>
</html>