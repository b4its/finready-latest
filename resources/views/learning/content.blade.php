{{-- resources/views/learning/pdf-viewer.blade.php --}}
@php
    $docList = [];
    
    // Helper function untuk deteksi apakah path adalah gambar
    $isImage = function($path) {
        $exts = ['.png', '.jpg', '.jpeg', '.gif', '.webp', '.svg'];
        foreach ($exts as $ext) {
            if (str_ends_with(strtolower($path), $ext)) return true;
        }
        return false;
    };

    // 1. Cek dari field URL utama (jika ada input manual)
    if (!empty($content->url)) {
        $docList[] = [
            'title' => 'Dokumen Utama',
            'url'   => str_starts_with($content->url, 'http') ? $content->url : asset($content->url),
            'type'  => $isImage($content->url) ? 'image' : 'pdf'
        ];
    } 
    
    // 2. Ekstraksi semua dokumen dari document_json (Dari Filament)
    if (!empty($content->document_json)) {
        $docs = is_string($content->document_json) ? json_decode($content->document_json, true) : $content->document_json;
        
        if (is_array($docs)) {
            foreach ($docs as $idx => $item) {
                // Sesuai dengan nama field di database-mu
                $path = $item['dokumen_url'] ?? null;
                $title = $item['title'] ?? ('Dokumen ' . ($idx + 1));
                
                if ($path) {
                    $url = str_starts_with($path, 'http') ? $path : asset($path);
                    
                    // Cek tipe dari dropdown Filament 'dokumen_type' atau ekstensi file
                    $typeStr = strtolower($item['dokumen_type'] ?? '');
                    $type = (str_contains($typeStr, 'gambar') || $isImage($path)) ? 'image' : 'pdf';

                    $docList[] = [
                        'title' => $title,
                        'url'   => $url,
                        'type'  => $type
                    ];
                }
            }
        }
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $content->title }} — FinReady Learn</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  
  {{-- Library External --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #f7f6f3; --card: #ffffff; --border: #e8e4dc; --text: #1a1814;
      --muted: #7a756b; --primary: #16a34a; --primary-light: #dcfce7;
      --radius: 16px; --shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.05);
    }
    body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; font-size: 15px; }
    ::-webkit-scrollbar { width: 4px; height: 4px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #d4cfc7; border-radius: 99px; }

    /* HEADER */
    .topbar { position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,.95); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); height: 56px; display: flex; align-items: center; }
    .topbar-inner { width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
    .back-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; color: var(--muted); text-decoration: none; padding: 6px 10px; border-radius: 8px; transition: all .15s; }
    .back-btn:hover { background: var(--bg); color: var(--text); }
    .topbar-title { font-size: 14px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px; }
    .topbar-sub { font-size: 11px; color: var(--muted); }
    .progress-pill { display: flex; align-items: center; gap: 8px; background: var(--primary-light); border: 1px solid #bbf7d0; border-radius: 99px; padding: 5px 12px 5px 8px; white-space: nowrap; }
    .progress-pill .ring { width: 26px; height: 26px; position: relative; }
    .progress-pill .ring svg { transform: rotate(-90deg); }
    .progress-pill .ring-text { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 700; color: var(--primary); }
    .progress-pill span { font-size: 12px; font-weight: 600; color: var(--primary); }

    /* LAYOUT */
    .layout { display: flex; height: calc(100vh - 56px); overflow: hidden; max-width: 1400px; margin: 0 auto; }

    /* LEFT PANEL: TOC */
    .toc-panel { width: 256px; flex-shrink: 0; background: var(--card); border-right: 1px solid var(--border); display: flex; flex-direction: column; overflow: hidden; }
    .toc-header { padding: 16px 16px 12px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .toc-header h3 { font-size: 11px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); margin-bottom: 10px; }
    .module-progress { display: flex; align-items: center; gap: 8px; background: var(--bg); border-radius: 10px; padding: 8px 10px; }
    .module-progress-bar { flex: 1; height: 5px; background: var(--border); border-radius: 99px; overflow: hidden; }
    .module-progress-fill { height: 100%; background: var(--primary); border-radius: 99px; transition: width .4s ease; }
    .module-progress-text { font-size: 11px; font-weight: 600; color: var(--primary); white-space: nowrap; }
    .toc-list { flex: 1; overflow-y: auto; padding: 10px 10px; }
    .toc-section-label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); padding: 12px 8px 6px; }
    .toc-item { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 10px; cursor: pointer; transition: all .15s; margin-bottom: 2px; position: relative; text-decoration: none; }
    .toc-item:hover { background: var(--bg); }
    .toc-item.active { background: var(--primary-light); }
    .toc-item.active::before { content: ''; position: absolute; left: 0; top: 8px; bottom: 8px; width: 3px; background: var(--primary); border-radius: 0 2px 2px 0; }
    .toc-status { width: 20px; height: 20px; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: 1.5px solid var(--border); background: var(--card); transition: all .2s; }
    .toc-item.done .toc-status { background: var(--primary); border-color: var(--primary); }
    .toc-item.active .toc-status { border-color: var(--primary); background: var(--primary-light); }
    .toc-text { flex: 1; min-width: 0; }
    .toc-name { font-size: 13px; font-weight: 500; color: var(--text); line-height: 1.35; }
    .toc-item.active .toc-name { font-weight: 600; color: var(--primary); }
    .toc-meta { font-size: 11px; color: var(--muted); margin-top: 1px; }

    /* CENTER: Viewer */
    .viewer { flex: 1; min-width: 0; display: flex; flex-direction: column; background: #ddd9d0; position: relative; }
    .pdf-controls { background: var(--card); border-bottom: 1px solid var(--border); padding: 8px 16px; display: flex; align-items: center; gap: 10px; flex-shrink: 0; flex-wrap: wrap; }
    .ctrl-group { display: flex; align-items: center; gap: 6px; }
    .ctrl-sep { width: 1px; height: 20px; background: var(--border); flex-shrink: 0; }
    .ctrl-btn { width: 30px; height: 30px; border-radius: 7px; border: 1.5px solid var(--border); background: var(--card); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all .15s; color: var(--muted); flex-shrink: 0; }
    .ctrl-btn:hover:not(:disabled) { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
    .ctrl-btn:disabled { opacity: .3; cursor: not-allowed; }
    .page-nav { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--muted); }
    .page-nav input { width: 40px; text-align: center; border: 1.5px solid var(--border); border-radius: 7px; padding: 4px 4px; font-size: 13px; font-family: 'DM Mono', monospace; background: var(--card); color: var(--text); }
    .page-nav input:focus { outline: none; border-color: var(--primary); }
    .zoom-select { border: 1.5px solid var(--border); border-radius: 7px; padding: 4px 6px; font-size: 12px; background: var(--card); color: var(--text); cursor: pointer; }
    .reading-est { margin-left: auto; display: flex; align-items: center; gap: 5px; font-size: 11.5px; color: var(--muted); background: var(--bg); border-radius: 8px; padding: 4px 10px; flex-shrink: 0; }
    .canvas-wrap { flex: 1; overflow-y: auto; overflow-x: auto; display: flex; flex-direction: column; align-items: center; padding: 24px 20px; gap: 16px; }
    .pdf-page-block { position: relative; flex-shrink: 0; }
    .pdf-page-block canvas { display: block; box-shadow: 0 4px 24px rgba(0,0,0,.2), 0 1px 4px rgba(0,0,0,.1); border-radius: 3px; }
    .pdf-page-num { text-align: center; font-size: 11px; color: #9e998f; font-family: 'DM Mono', monospace; margin-top: 8px; }
    .page-skeleton { width: 595px; background: #fff; box-shadow: 0 4px 24px rgba(0,0,0,.2); border-radius: 3px; overflow: hidden; background: linear-gradient(90deg, #e8e4dc 25%, #f0ece6 50%, #e8e4dc 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    .load-progress { position: absolute; top: 0; left: 0; right: 0; height: 3px; z-index: 10; overflow: hidden; pointer-events: none; }
    .load-progress-fill { height: 100%; background: var(--primary); border-radius: 0 2px 2px 0; transition: width .3s ease; width: 0; }
    .page-badge { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: rgba(26,24,20,.75); backdrop-filter: blur(8px); color: #fff; font-size: 12px; font-family: 'DM Mono', monospace; padding: 5px 14px; border-radius: 99px; opacity: 0; pointer-events: none; transition: opacity .25s; z-index: 50; }
    .page-badge.show { opacity: 1; }

    /* RIGHT PANEL */
    .right-panel { width: 272px; flex-shrink: 0; background: var(--card); border-left: 1px solid var(--border); display: flex; flex-direction: column; overflow: hidden; }
    .right-tabs { display: flex; border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .right-tab { flex: 1; padding: 10px 0; font-size: 12px; font-weight: 600; color: var(--muted); cursor: pointer; text-align: center; border-bottom: 2px solid transparent; transition: all .15s; }
    .right-tab.active { color: var(--primary); border-color: var(--primary); }
    .right-body { flex: 1; overflow-y: auto; padding: 14px; }
    .info-section { margin-bottom: 20px; }
    .info-label { font-size: 10px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); margin-bottom: 8px; }
    .info-card { background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 12px 14px; }
    .info-card p { font-size: 13px; color: var(--text); line-height: 1.6; }
    .stat-row { display: flex; gap: 8px; }
    .stat-box { flex: 1; background: var(--bg); border: 1px solid var(--border); border-radius: 10px; padding: 10px 12px; text-align: center; }
    .stat-box-val { font-size: 18px; font-weight: 700; color: var(--text); }
    .stat-box-lbl { font-size: 10.5px; color: var(--muted); margin-top: 2px; }

    .note-textarea { width: 100%; border: 1.5px solid var(--border); border-radius: 10px; padding: 10px 12px; font-size: 13px; font-family: 'DM Sans', sans-serif; color: var(--text); resize: none; height: 110px; background: var(--bg); line-height: 1.6; transition: border-color .15s; }
    .note-textarea:focus { outline: none; border-color: var(--primary); background: #fff; }
    .note-page-label { font-size: 11px; color: var(--muted); margin-bottom: 6px; display: flex; align-items: center; gap: 4px; }
    .note-save-btn { width: 100%; margin-top: 8px; padding: 8px 12px; background: var(--primary); color: #fff; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: background .15s; font-family: inherit; }
    .note-save-btn:hover { background: #15803d; }
    .saved-note { background: #fefce8; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; font-size: 12.5px; color: #713f12; line-height: 1.6; position: relative; }
    .saved-note-page { font-size: 10px; font-weight: 700; color: #92400e; margin-bottom: 3px; }
    .saved-note-del { position: absolute; top: 8px; right: 10px; background: none; border: none; cursor: pointer; color: #d97706; font-size: 16px; line-height: 1; }
    .hidden { display: none !important; }

    @media (max-width: 1100px) { .right-panel { display: none; } }
    @media (max-width: 700px)  { .toc-panel   { display: none; } }

    .complete-strip { background: var(--card); border-top: 1px solid var(--border); padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-shrink: 0; }
    .complete-strip .info { font-size: 13px; color: var(--muted); }
    .complete-strip strong { color: var(--text); }
    .btn-complete { display: inline-flex; align-items: center; gap: 7px; background: var(--primary); color: #fff; border: none; border-radius: 10px; padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all .15s; white-space: nowrap; }
    .btn-complete:hover { background: #15803d; transform: translateY(-1px); }
    .btn-complete.done { background: var(--bg); color: var(--primary); border: 1.5px solid #bbf7d0; }

    .toast { position: fixed; bottom: 28px; right: 28px; z-index: 999; background: var(--text); color: #fff; border-radius: 12px; padding: 12px 18px; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.2); transform: translateY(16px); opacity: 0; transition: all .25s; pointer-events: none; }
    .toast.show { transform: translateY(0); opacity: 1; }
    .toast.green { background: var(--primary); }
  </style>
</head>
<body>

<header class="topbar">
  <div class="topbar-inner">
    <a href="{{ route('learning.index') }}" class="back-btn">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
      Kembali
    </a>

    <div class="text-center" style="flex:1;min-width:0; text-align: center;">
      <div class="topbar-title">{{ $content->title }}</div>
      <div class="topbar-sub">Modul {{ $content->module->id ?? '-' }} · {{ $content->module->name ?? '-' }}</div>
    </div>

    <div class="progress-pill">
      <div class="ring">
        <svg width="26" height="26" viewBox="0 0 36 36">
          <circle cx="18" cy="18" r="15" fill="none" stroke="#bbf7d0" stroke-width="3.5"/>
          <circle cx="18" cy="18" r="15" fill="none" stroke="#16a34a" stroke-width="3.5"
            stroke-dasharray="94.2" stroke-dashoffset="28" stroke-linecap="round"/>
        </svg>
        <div class="ring-text">70%</div>
      </div>
      <span>Dalam Proses</span>
    </div>
  </div>
</header>

<div class="layout">

  <nav class="toc-panel">
    <div class="toc-header">
      <h3>Modul {{ $content->module->id ?? '-' }} — {{ $content->module->name ?? 'Materi' }}</h3>
      <div class="module-progress">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        <div class="module-progress-bar"><div class="module-progress-fill" style="width:60%"></div></div>
        <span class="module-progress-text">60%</span>
      </div>
    </div>

    <div class="toc-list">
      <div class="toc-section-label">Daftar Konten</div>

      @if($content->module)
          @foreach($content->module->contents as $item)
          <a href="{{ route('learning.content', $item->id) }}" class="toc-item {{ $item->id == $content->id ? 'active' : '' }}">
            <div class="toc-status">
              @if($item->id == $content->id)
                  <svg width="8" height="8" viewBox="0 0 24 24" fill="#16a34a"><circle cx="12" cy="12" r="8"/></svg>
              @endif
            </div>
            <div class="toc-text">
              <div class="toc-name">{{ $item->title }}</div>
              <div class="toc-meta">{{ $item->type ?? 'Materi' }}</div>
            </div>
          </a>
          @endforeach

          @if($content->module->rooms->isNotEmpty())
              <div class="toc-section-label">Latihan & Quiz</div>
              @foreach($content->module->rooms as $room)
              <a href="{{ route('learning.quiz', $room->id) }}" class="toc-item">
                <div class="toc-status"></div>
                <div class="toc-text">
                  <div class="toc-name">{{ $room->name }}</div>
                  <div class="toc-meta">{{ $room->questions->count() }} soal</div>
                </div>
              </a>
              @endforeach
          @endif
      @endif
    </div>
  </nav>

  <main class="viewer">
    <div class="load-progress"><div class="load-progress-fill" id="loadFill"></div></div>

    <div class="pdf-controls" id="pdfControls">
      <select id="docSelector" onchange="switchDoc(this.value)" style="display:none; border: 1.5px solid var(--border); border-radius: 7px; padding: 4px 8px; font-size: 13px; font-weight: 500; background: var(--bg); color: var(--text); cursor: pointer; max-width: 200px; margin-right: 12px;"></select>

      <div class="ctrl-group page-nav" id="pdfNavWrapper">
        <button class="ctrl-btn" id="btnPrev" onclick="changePage(-1)" disabled>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"/></svg>
        </button>
        <input id="pageInput" type="number" value="1" min="1">
        <span style="color:var(--muted);font-size:12px">/ <span id="totalPagesLabel">—</span></span>
        <button class="ctrl-btn" id="btnNext" onclick="changePage(1)" disabled>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m9 18 6-6-6-6"/></svg>
        </button>
      </div>

      <div class="ctrl-sep"></div>

      <div class="ctrl-group" id="pdfZoomWrapper">
        <button class="ctrl-btn" onclick="zoom(-0.2)">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
        </button>
        <select class="zoom-select" id="zoomSelect" onchange="setZoom(this.value)">
          <option value="0.6">60%</option>
          <option value="0.8">80%</option>
          <option value="1" selected>100%</option>
          <option value="1.25">125%</option>
          <option value="1.5">150%</option>
          <option value="2">200%</option>
          <option value="fit">Fit Lebar</option>
        </select>
        <button class="ctrl-btn" onclick="zoom(0.2)">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
        </button>
      </div>

      <div class="ctrl-sep"></div>

      <button class="ctrl-btn" onclick="toggleFS()" title="Layar penuh">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
      </button>

      <div class="reading-est" id="readingEst">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span id="readingText">Menghitung...</span>
      </div>
    </div>

    <div class="canvas-wrap" id="canvasWrap">
      <div id="loadingState" style="display:flex;flex-direction:column;align-items:center;gap:16px;margin-top:40px">
        <div class="page-skeleton" style="height:780px;border-radius:3px"></div>
        <div style="font-size:12px;color:#9e998f;font-family:'DM Mono',monospace">Memuat materi...</div>
      </div>
    </div>

    <div class="complete-strip" id="completeStrip" style="display:none">
      <div class="info">
        <strong>{{ $content->title }}</strong>
        <span> · Halaman <span id="stripPage">1</span> dari <span id="stripTotal">1</span></span>
      </div>
    </div>
  </main>

  <aside class="right-panel">
    <div class="right-tabs">
      <div class="right-tab active" onclick="switchTab('info')" id="rtab-info">Info</div>
      <div class="right-tab" onclick="switchTab('notes')" id="rtab-notes">Catatan</div>
      <div class="right-tab" onclick="switchTab('thumb')" id="rtab-thumb">Halaman</div>
    </div>

    <div class="right-body" id="rbody-info">
      <div class="info-section">
        <div class="info-label">Tentang Materi</div>
        <div class="info-card">
          <p>{{ $content->content ? strip_tags($content->content) : 'Deskripsi materi belum tersedia.' }}</p>
        </div>
      </div>

      <div class="info-section">
        <div class="info-label">Statistik Dokumen</div>
        <div class="stat-row">
          <div class="stat-box">
            <div class="stat-box-val" id="statPages">—</div>
            <div class="stat-box-lbl">Halaman</div>
          </div>
          <div class="stat-box">
            <div class="stat-box-val" id="statTime">—</div>
            <div class="stat-box-lbl">Mnt baca</div>
          </div>
        </div>
      </div>

      <div class="info-section">
        <div class="info-label">Kemajuan Baca</div>
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:12px 14px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
            <span style="font-size:12px;color:var(--muted)">Progres</span>
            <span style="font-size:12px;font-weight:700;color:var(--primary)" id="readPct">0%</span>
          </div>
          <div style="height:6px;background:var(--border);border-radius:99px;overflow:hidden">
            <div style="height:100%;background:var(--primary);border-radius:99px;transition:width .4s" id="readBar"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="right-body hidden" id="rbody-notes">
      <div class="note-page-label">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/></svg>
        Catatan – Hal. <span id="notePageNum">1</span>
      </div>
      <textarea id="noteTA" class="note-textarea" placeholder="Tulis catatan untuk halaman ini..."></textarea>
      <button class="note-save-btn" onclick="saveNote()">Simpan Catatan</button>

      <div style="margin-top:16px">
        <div class="info-label" style="margin-bottom:8px">Catatan Tersimpan</div>
        <div id="notesList"><p style="font-size:12px;color:var(--muted)">Belum ada catatan.</p></div>
      </div>
    </div>

    <div class="right-body hidden" id="rbody-thumb">
      <div id="thumbGrid" style="display:flex;flex-direction:column;gap:6px">
        <p style="font-size:12px;color:var(--muted);text-align:center;margin-top:16px">Memuat thumbnail...</p>
      </div>
    </div>
  </aside>
</div>

<div class="page-badge" id="pageBadge"></div>

<div class="toast green" id="toast">
  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>
  <span id="toastMsg">Berhasil!</span>
</div>

<script>
const docList = @json($docList);
const contentId = {{ $content->id }};
const isQuestion = {{ $content->is_question }}; // Nilai dari database: 0 atau 1

pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let pdfDoc       = null;
let curPage      = 1;
let totalPages   = 0;
let curScale     = 1.0;
let notes        = {}; 
let badgeTimer   = null;
let activeDocIdx = 0; 
let completedDocs = new Set(); // Mencegah popup muncul berkali-kali

window.addEventListener('DOMContentLoaded', () => {
  initViewer();
  document.getElementById('pageInput').addEventListener('change', e => goToPage(+e.target.value));
});

function initViewer() {
  if (!docList || docList.length === 0) {
    showNoContent();
    return;
  }

  const docSelector = document.getElementById('docSelector');
  if (docList.length > 1) {
    docSelector.style.display = 'block';
    docSelector.innerHTML = docList.map((doc, i) => `<option value="${i}">${doc.title}</option>`).join('');
  } else {
    docSelector.style.display = 'none';
  }

  loadDoc(0);
}

function switchDoc(index) {
  activeDocIdx = index;
  loadDoc(index);
}

function loadDoc(index) {
  const doc = docList[index];
  const wrap = document.getElementById('canvasWrap');
  const fill = document.getElementById('loadFill');
  
  // Tampilkan loading skeleton
  wrap.innerHTML = `
    <div id="loadingState" style="display:flex;flex-direction:column;align-items:center;gap:16px;margin-top:40px">
      <div class="page-skeleton" style="height:780px;border-radius:3px"></div>
      <div style="font-size:12px;color:#9e998f;font-family:'DM Mono',monospace">Memuat dokumen...</div>
    </div>`;
  document.getElementById('completeStrip').style.display = 'none';
  fill.style.width = '20%';

  if (doc.type === 'image') {
    // Mode Gambar
    document.getElementById('pdfNavWrapper').style.display = 'none';
    document.getElementById('pdfZoomWrapper').style.display = 'none';
    
    const img = new Image();
    img.src = doc.url;
    img.style.maxWidth = '100%';
    img.style.borderRadius = '8px';
    img.style.boxShadow = '0 4px 24px rgba(0,0,0,.1)';
    
    img.onload = () => {
      wrap.innerHTML = '';
      wrap.appendChild(img);
      fill.style.width = '100%';
      setTimeout(() => { fill.style.width = '0'; }, 500);
      
      // Setup progress statis untuk gambar
      document.getElementById('totalPagesLabel').textContent = '1';
      document.getElementById('statPages').textContent = '1';
      document.getElementById('statTime').textContent  = '1';
      document.getElementById('readingText').textContent = `Gambar`;
      document.getElementById('stripTotal').textContent = '1';
      document.getElementById('stripPage').textContent = '1';
      
      // Update Progress langsung 100%
      document.getElementById('readPct').textContent = '100%';
      document.getElementById('readBar').style.width = '100%';
      
      // Trigger Completion Popup
      triggerCompletionAlert();
    };
    
    img.onerror = () => {
      showNoContent();
      showToast('Gagal memuat gambar.', false);
    }
  } else {
    // Mode PDF
    document.getElementById('pdfNavWrapper').style.display = 'flex';
    document.getElementById('pdfZoomWrapper').style.display = 'flex';

    pdfjsLib.getDocument(doc.url).promise.then(pdf => {
      pdfDoc     = pdf;
      totalPages = pdf.numPages;
      curPage    = 1;
      curScale   = 1.0;

      document.getElementById('totalPagesLabel').textContent = totalPages;
      document.getElementById('pageInput').max   = totalPages;
      document.getElementById('pageInput').value = 1;
      document.getElementById('btnPrev').disabled = true;
      document.getElementById('btnNext').disabled = totalPages <= 1;
      
      document.getElementById('statPages').textContent = totalPages;
      document.getElementById('statTime').textContent  = Math.ceil(totalPages * 1.5);
      document.getElementById('readingText').textContent = `~${Math.ceil(totalPages*1.5)} mnt baca`;
      document.getElementById('stripTotal').textContent = totalPages;

      fill.style.width = '60%';

      return renderAllPages(pdf);
    }).then(() => {
      fill.style.width = '100%';
      setTimeout(() => { fill.style.width = '0'; }, 500);
      document.getElementById('completeStrip').style.display = 'flex';
      updateReadProgress();
      buildThumbs();
      
      document.getElementById('noteTA').value = notes['doc_' + activeDocIdx + '_pg_1'] || '';
      renderNotes();
    }).catch(err => {
      console.error(err);
      fill.style.width = '0';
      showNoContent();
      showToast('Gagal memuat PDF.', false);
    });
  }
}

async function renderAllPages(pdf) {
  const wrap = document.getElementById('canvasWrap');
  wrap.innerHTML = '';

  for (let i = 1; i <= pdf.numPages; i++) {
    const block   = document.createElement('div');
    block.className = 'pdf-page-block';
    block.id       = 'pg_' + i;
    block.dataset.page = i;

    const canvas  = document.createElement('canvas');
    canvas.id     = 'cv_' + i;
    block.appendChild(canvas);

    const num   = document.createElement('div');
    num.className = 'pdf-page-num';
    num.textContent = i + ' / ' + pdf.numPages;
    block.appendChild(num);

    wrap.appendChild(block);
  }

  for (let i = 1; i <= pdf.numPages; i++) {
    await renderOnePage(i, pdf);
  }
  setupObserver(wrap);
}

async function renderOnePage(pageNum, pdf) {
  if (!pdf) return;
  try {
    const page = await pdf.getPage(pageNum);
    const cv   = document.getElementById('cv_' + pageNum);
    if (!cv) return;

    let scale = curScale;
    if (curScale === 'fit') {
      const vp0  = page.getViewport({ scale: 1 });
      const avail = document.getElementById('canvasWrap').clientWidth - 48;
      scale = Math.min(avail / vp0.width, 1.5);
    }

    const vp = page.getViewport({ scale });
    cv.width  = vp.width;
    cv.height = vp.height;
    await page.render({ canvasContext: cv.getContext('2d'), viewport: vp }).promise;
  } catch(e) {}
}

function setupObserver(wrap) {
  const obs = new IntersectionObserver(entries => {
    let max = 0, pg = curPage;
    entries.forEach(e => {
      if (e.intersectionRatio > max) { max = e.intersectionRatio; pg = +e.target.dataset.page; }
    });
    if (pg !== curPage) {
      curPage = pg;
      document.getElementById('pageInput').value = pg;
      document.getElementById('btnPrev').disabled = pg <= 1;
      document.getElementById('btnNext').disabled = pg >= totalPages;
      document.getElementById('stripPage').textContent = pg;
      document.getElementById('notePageNum').textContent = pg;
      
      document.getElementById('noteTA').value = notes['doc_' + activeDocIdx + '_pg_' + pg] || '';

      document.querySelectorAll('.thumb-item').forEach((el,i) => {
        el.classList.toggle('t-active', i + 1 === pg);
      });

      updateReadProgress();
      showBadge();
    }
  }, { root: wrap, threshold: Array.from({length:11},(_,i)=>i/10) });

  document.querySelectorAll('.pdf-page-block').forEach(el => obs.observe(el));
}

function changePage(d) { goToPage(curPage + d); }
function goToPage(n) {
  const p = Math.max(1, Math.min(n, totalPages));
  document.getElementById('pg_' + p)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function zoom(delta) {
  if (curScale === 'fit') curScale = 1.0;
  curScale = +(Math.min(2.5, Math.max(0.4, curScale + delta)).toFixed(2));
  applyZoom();
}
function setZoom(v) {
  curScale = v === 'fit' ? 'fit' : +v;
  applyZoom();
}
async function applyZoom() {
  if (!pdfDoc) return;
  for (let i = 1; i <= totalPages; i++) await renderOnePage(i, pdfDoc);
}

// Fitur Baru: Memunculkan Pop up Modal SweetAlert2 jika 100%
function updateReadProgress() {
  const pct = totalPages ? Math.round((curPage / totalPages) * 100) : 0;
  document.getElementById('readPct').textContent = pct + '%';
  document.getElementById('readBar').style.width = pct + '%';
  
  if (pct === 100) {
    triggerCompletionAlert();
  }
}

function triggerCompletionAlert() {
  // Cegah modal muncul berulang-ulang saat di-scroll
  if (completedDocs.has(activeDocIdx)) return;
  completedDocs.add(activeDocIdx);
  var item_score = {{ $moduleKey->max_point }} / {{ $moduleContent_total }};
  
  console.log(item_score);
  if (isQuestion == 1) {
    // Modal Jika ada pertanyaan / quiz
    Swal.fire({
      icon: 'success',
      title: 'Materi Selesai!',
      text: 'Kamu telah menyelesaikan materi ini. Ada quiz lanjutan yang harus kamu kerjakan.',
      input: 'textarea', // Menambahkan input textarea
      inputLabel: 'Berikan kesimpulan Anda mengenai materi ini:',
      inputPlaceholder: 'Tulis kesimpulan singkat di sini...',
      confirmButtonText: 'Simpan & Lanjut Quiz',
      confirmButtonColor: '#16a34a',
      showCancelButton: true,
      cancelButtonText: 'Tutup',
      inputValidator: (value) => {
        if (!value) {
          return 'Kesimpulan tidak boleh kosong!';
        }
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Menangkap teks kesimpulan dari user
        const kesimpulanUser = result.value; 
        console.log("Kesimpulan user:", kesimpulanUser);
        Swal.fire({
          title: "Selamat",
          text: "Kamu mendapatkan ", item_score, " poin",
          icon: "success"
        });

        // TODO: Kamu bisa masukkan nilai ini ke dalam form input hidden sebelum submit, 
        // atau kirim via fetch/AJAX ke server.
        
        markComplete();
      }
    });
  } else {
    // Modal Jika tidak ada pertanyaan
    Swal.fire({
      icon: 'success',
      title: 'Luar Biasa!',
      text: 'Kamu telah membaca semua halaman materi ini.',
      input: 'textarea', // Menambahkan input textarea
      inputLabel: 'Berikan kesimpulan Anda mengenai materi ini:',
      inputPlaceholder: 'Tulis kesimpulan singkat di sini...',
      confirmButtonText: 'Simpan & Tandai Selesai',
      confirmButtonColor: '#16a34a',
      showCancelButton: true,
      cancelButtonText: 'Nanti',
      inputValidator: (value) => {
        if (!value) {
          return 'Kesimpulan tidak boleh kosong!';
        }
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Menangkap teks kesimpulan dari user
        const kesimpulanUser = result.value; 
        console.log("Kesimpulan user:", kesimpulanUser);

        // TODO: Kirim data kesimpulan ini ke backend
        
        markComplete();
      }
    });
  }
}

async function buildThumbs() {
  if (!pdfDoc) return;
  const grid = document.getElementById('thumbGrid');
  grid.innerHTML = '';

  const max = Math.min(pdfDoc.numPages, 60);
  for (let i = 1; i <= max; i++) {
    const item   = document.createElement('div');
    item.className = 'thumb-item' + (i === 1 ? ' t-active' : '');
    item.onclick  = () => goToPage(i);
    item.style.cssText = 'display:flex;align-items:center;gap:8px;padding:5px 6px;border-radius:8px;cursor:pointer;transition:all .15s;';
    
    const num  = document.createElement('span');
    num.textContent = i;
    num.style.cssText = 'font-size:10px;font-family:"DM Mono",monospace;color:var(--muted);min-width:20px;text-align:right';

    const cv   = document.createElement('canvas');
    cv.style.cssText = 'border:1.5px solid var(--border);border-radius:3px;display:block;max-width:160px';

    item.appendChild(num);
    item.appendChild(cv);
    grid.appendChild(item);

    ;(async(pg, canvas) => {
      try {
        const page = await pdfDoc.getPage(pg);
        const vp   = page.getViewport({ scale: 0.18 });
        canvas.width  = vp.width;
        canvas.height = vp.height;
        await page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
      } catch(e) {}
    })(i, cv);
  }
  
  const style = document.createElement('style');
  style.textContent = `.thumb-item:hover{background:var(--primary-light)}.t-active{background:var(--primary-light)!important}.t-active canvas{border-color:var(--primary)!important}`;
  document.head.appendChild(style);
}

function saveNote() {
  const text = document.getElementById('noteTA').value.trim();
  const key  = 'doc_' + activeDocIdx + '_pg_' + curPage;
  if (text) notes[key] = text;
  else delete notes[key];
  renderNotes();

  const btn = document.querySelector('.note-save-btn');
  btn.textContent = '✓ Tersimpan';
  setTimeout(() => { btn.textContent = 'Simpan Catatan'; }, 1400);
}

function renderNotes() {
  const list = document.getElementById('notesList');
  const prefix = 'doc_' + activeDocIdx + '_pg_';
  const entries = Object.entries(notes).filter(([k]) => k.startsWith(prefix));
  
  if (!entries.length) {
    list.innerHTML = '<p style="font-size:12px;color:var(--muted)">Belum ada catatan.</p>';
    return;
  }
  
  list.innerHTML = entries.map(([k, txt]) => {
    const pg = k.replace(prefix,'');
    return `<div class="saved-note">
      <div class="saved-note-page">Halaman ${pg}</div>
      ${esc(txt).replace(/\n/g,'<br>')}
      <button class="saved-note-del" onclick="delNote('${k}')">×</button>
    </div>`;
  }).join('');
}

function delNote(key) { delete notes[key]; renderNotes(); }

function markComplete() {
  // Tambahkan visual completed di UI
  const btnList = document.querySelectorAll('.btn-complete');
  if(btnList) {
      btnList.forEach(btn => {
          btn.className = 'btn-complete done';
          btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Selesai Dibaca`;
      });
  }
  
  showToast('Materi selesai ditandai!', true);
  
  // Submit via form atau fetch API ke Laravel controller
  // document.getElementById('formComplete').submit();
}

function switchTab(name) {
  ['info','notes','thumb'].forEach(t => {
    document.getElementById('rtab-' + t).classList.toggle('active', t === name);
    document.getElementById('rbody-' + t).classList.toggle('hidden', t !== name);
  });
}

function showNoContent() {
  const wrap = document.getElementById('canvasWrap');
  wrap.innerHTML = `
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:16px;padding:60px 32px;text-align:center">
      <div style="width:72px;height:72px;background:var(--primary-light);border-radius:20px;display:flex;align-items:center;justify-content:center;margin-bottom:4px">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round">
          <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/>
          <line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
      </div>
      <div>
        <h3 style="font-size:17px;font-weight:700;color:var(--text);margin-bottom:6px">File Tidak Tersedia</h3>
        <p style="font-size:13.5px;color:var(--muted);max-width:340px;line-height:1.7;margin:0 auto">Materi ini tidak memiliki file untuk ditampilkan.</p>
      </div>
    </div>`;
  document.getElementById('totalPagesLabel').textContent = '—';
  document.getElementById('statPages').textContent = '—';
  document.getElementById('statTime').textContent  = '—';
  document.getElementById('readingText').textContent = '—';
}

function showBadge() {
  const b = document.getElementById('pageBadge');
  b.textContent = curPage + ' / ' + totalPages;
  b.classList.add('show');
  clearTimeout(badgeTimer);
  badgeTimer = setTimeout(() => b.classList.remove('show'), 1800);
}

function showToast(msg, green = true) {
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.className = 'toast show' + (green ? ' green' : '');
  setTimeout(() => t.classList.remove('show'), 3000);
}

function toggleFS() {
  if (!document.fullscreenElement) document.documentElement.requestFullscreen();
  else document.exitFullscreen();
}

document.addEventListener('keydown', e => {
  if (!pdfDoc) return;
  if (['INPUT','TEXTAREA'].includes(document.activeElement.tagName)) return;
  if (e.key === 'ArrowRight' || e.key === 'ArrowDown') changePage(1);
  if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   changePage(-1);
  if (e.key === '+') zoom(0.2);
  if (e.key === '-') zoom(-0.2);
});

function esc(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
</body>
</html>