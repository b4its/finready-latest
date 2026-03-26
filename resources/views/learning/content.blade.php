<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $content->title }} - Pembelajaran</title>
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
    .header { background: var(--card); border-bottom: 1px solid var(--border); transition: background .25s, border-color .25s; }
    .header-inner { max-width: 1200px; margin: 0 auto; padding: 36px 24px; display: flex; flex-direction: column; gap: 24px; }
    .top-bar { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .top-bar-left { display: flex; align-items: center; gap: 20px; }
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--muted); cursor: pointer; transition: color .15s; }
    .breadcrumb:hover { color: var(--text); }
    .breadcrumb svg { width: 14px; height: 14px; }
    .dark-toggle { display: flex; align-items: center; gap: 8px; background: var(--secondary); border: 1px solid var(--border); border-radius: 99px; padding: 5px 12px 5px 8px; cursor: pointer; font-size: 12px; font-weight: 600; color: var(--muted); font-family: inherit; transition: background .2s, border-color .2s, color .2s; white-space: nowrap; }
    .dark-toggle:hover { color: var(--text); border-color: var(--muted); }
    .toggle-track { width: 32px; height: 18px; background: var(--border); border-radius: 99px; position: relative; transition: background .25s; flex-shrink: 0; }
    [data-theme="dark"] .toggle-track { background: var(--primary); }
    .toggle-thumb { width: 14px; height: 14px; background: #fff; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: transform .25s; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
    [data-theme="dark"] .toggle-thumb { transform: translateX(14px); }
    .toggle-icon { width: 14px; height: 14px; }
    .header-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
    .header-text h1 { font-size: clamp(26px, 4vw, 36px); font-weight: 700; letter-spacing: -0.03em; line-height: 1.15; margin-bottom: 10px; color: var(--text); }
    .header-text p { max-width: 580px; color: var(--muted); font-size: 15px; line-height: 1.65; }
    .btn-continue { display: inline-flex; align-items: center; gap: 8px; background: var(--primary); color: #fff; border: none; border-radius: 12px; padding: 13px 22px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; white-space: nowrap; transition: background .18s, transform .12s; flex-shrink: 0; align-self: flex-start; text-decoration: none; }
    .btn-continue:hover { background: #15803d; transform: translateY(-1px); }
    [data-theme="dark"] .btn-continue:hover { background: #16a34a; }
    .btn-continue svg { width: 15px; height: 15px; }
    .layout { max-width: 1200px; margin: 0 auto; padding: 36px 24px; display: flex; gap: 32px; align-items: flex-start; }
    .main-col { flex: 1; min-width: 0; }
    .sidebar-col { width: 296px; flex-shrink: 0; position: sticky; top: 24px; display: flex; flex-direction: column; gap: 16px; }
    .card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); transition: background .25s, border-color .25s; }
    .card-header { padding: 20px 20px 12px; border-bottom: 1px solid var(--border); }
    .card-header h3 { font-size: 15px; font-weight: 600; }
    .card-body { padding: 16px 20px; }
    .stat-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; }
    .stat-row + .stat-row { border-top: 1px solid var(--border); }
    .stat-icon { width: 40px; height: 40px; background: var(--secondary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .25s; }
    .stat-icon svg { width: 18px; height: 18px; color: var(--text); }
    .stat-label { font-size: 14px; font-weight: 600; }
    .stat-sub { font-size: 12px; color: var(--muted); margin-top: 1px; }
    @media (max-width: 900px) { .sidebar-col { display: none; } .header-row { flex-direction: column; } }
    @media (max-width: 600px) { .layout { padding: 24px 16px; } .header-inner { padding: 24px 16px; } }
    svg { vertical-align: middle; }
    
    /* Tambahan styling khusus konten materi */
    .content-viewer-title { font-size: 22px; font-weight: 700; margin-bottom: 16px; color: var(--text); }
    .video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; margin-bottom: 24px; background: #000; }
    .video-wrapper iframe, .video-wrapper video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
    .content-body { color: var(--text); line-height: 1.8; font-size: 15px; }
    .content-body img { max-width: 100%; border-radius: 8px; margin: 16px 0; }
    .doc-list { display: flex; flex-direction: column; gap: 12px; margin-top: 24px; }
    .doc-item { display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--secondary); border-radius: 12px; border: 1px solid var(--border); transition: all .2s; text-decoration: none; color: var(--text); }
    .doc-item:hover { border-color: var(--primary); background: var(--card); }
    .doc-item-left { display: flex; align-items: center; gap: 12px; }
    .doc-item-icon { color: var(--primary); }
  </style>
</head>
<body>

<header class="header">
  <div class="header-inner">
    <div class="top-bar">
      <div class="top-bar-left">
        <div class="breadcrumb" onclick="window.location='{{ route('learning.index') }}'">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
          <span>Course / Modul: {{ $content->module->name ?? 'Materi' }}</span>
        </div>
      </div>
      <button class="dark-toggle" onclick="toggleDark()" aria-label="Toggle dark mode">
        <svg class="toggle-icon" id="icon-light" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
        </svg>
        <svg class="toggle-icon" id="icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
        <div class="toggle-track"><div class="toggle-thumb"></div></div>
        <span id="toggle-label">Dark</span>
      </button>
    </div>

    <div class="header-row">
      <a href="{{ route('learning.index') }}" class="btn-continue" style="background: var(--secondary); color: var(--text); border: 1px solid var(--border);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Modul
      </a>
      <button class="btn-continue">
        Tandai Selesai
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
      </button>
    </div>
    
    <div class="header-row">
      <div class="header-text">
          <h1>{{ $content->title }}</h1>
        <p>Tipe Konten: <span style="text-transform: capitalize; font-weight: 600; color: var(--primary);">{{ $content->type ?? 'Materi' }}</span></p>
      </div>
    </div>
  </div>
</header>

<div class="layout">
  <main class="main-col">
    <div class="card">
      <div class="card-body" style="padding: 32px;">
        
        {{-- LOGIKA PEMISAHAN DATA URL DAN DOKUMEN --}}
        @php
            // 1. Logika Embed YouTube URL
            $youtubeEmbedUrl = null;
            if ($content->url && (str_contains($content->url, 'youtube.com') || str_contains($content->url, 'youtu.be'))) {
                preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $content->url, $match);
                if (isset($match[1])) {
                    $youtubeEmbedUrl = 'https://www.youtube.com/embed/' . $match[1];
                }
            }

            // 2. Logika Pemisahan Video dan Dokumen dari `document_json` (Membaca format Repeater Filament)
            $uploadedVideos = [];
            $uploadedDocs = [];
            if (!empty($content->document_json) && is_array($content->document_json)) {
                foreach ($content->document_json as $item) {
                    // Cek struktur repeater array: ambil dokumen_url dan title
                    $filePath = isset($item['dokumen_url']) ? $item['dokumen_url'] : null;
                    $docTitle = isset($item['title']) ? $item['title'] : 'Dokumen Pendukung';

                    if (is_string($filePath) && !empty($filePath)) {
                        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        // Jika ekstensinya video, masukkan ke array video
                        if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                            $uploadedVideos[] = [
                                'url' => $filePath,
                                'title' => $docTitle
                            ];
                        } else {
                            // Selain video, masukkan ke dokumen biasa
                            $uploadedDocs[] = [
                                'url' => $filePath,
                                'title' => $docTitle
                            ];
                        }
                    }
                }
            }
        @endphp

        {{-- ================= TAMPILAN VIDEO YOUTUBE ================= --}}
        @if($youtubeEmbedUrl)
          <div class="video-wrapper">
            <iframe src="{{ $youtubeEmbedUrl }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
        @elseif($content->url)
          {{-- Jika URL ada tapi bukan YouTube --}}
          <div style="margin-bottom: 24px;">
            <a href="{{ $content->url }}" target="_blank" class="btn-continue" style="background: var(--secondary); color: var(--text); border: 1px solid var(--border);">
               <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
               Buka Link Materi
            </a>
          </div>
        @endif

        {{-- ================= TAMPILAN UPLOAD VIDEO INTERNAL ================= --}}
        @if(count($uploadedVideos) > 0)
          @foreach($uploadedVideos as $vid)
            <div style="margin-bottom: 24px;">
              <h4 style="font-size: 15px; font-weight: 600; margin-bottom: 8px;">{{ $vid['title'] }}</h4>
              <div class="video-wrapper">
                <video controls controlsList="nodownload">
                  <source src="{{ asset($vid['url']) }}" type="video/{{ pathinfo($vid['url'], PATHINFO_EXTENSION) == 'mov' ? 'mp4' : pathinfo($vid['url'], PATHINFO_EXTENSION) }}">
                  Browser Anda tidak mendukung tag video.
                </video>
              </div>
            </div>
          @endforeach
        @endif

        {{-- ================= TAMPILAN TEKS KONTEN ================= --}}
        @if($content->content)
          <div class="content-body">
            {!! $content->content !!}
          </div>
        @else
          <div class="content-body">
            Saat ini tidak ada data
          </div>
        @endif

        {{-- ================= TAMPILAN DOKUMEN PENDUKUNG ================= --}}
        @if(count($uploadedDocs) > 0)
          <div style="margin-top: 40px; border-top: 1px solid var(--border); padding-top: 24px;">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 12px;">Dokumen Pendukung</h3>
            <div class="doc-list">
              @foreach($uploadedDocs as $doc)
                <a href="{{ asset($doc['url']) }}" target="_blank" class="doc-item">
                  <div class="doc-item-left">
                    <svg class="doc-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="24" height="24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span style="font-weight: 500;">{{ $doc['title'] }} ({{ strtoupper(pathinfo($doc['url'], PATHINFO_EXTENSION)) }})</span>
                  </div>
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" style="color: var(--muted);"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                </a>
              @endforeach
            </div>
          </div>
        @endif

      </div>
    </div>
  </main>

  <aside class="sidebar-col">
    <div class="card">
      <div class="card-header"><h3>Informasi Modul</h3></div>
      <div class="card-body">
        <div class="stat-row">
          <div class="stat-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg></div>
          <div><div class="stat-label">Modul: {{ $content->module->name ?? '-' }}</div></div>
        </div>
        <div class="stat-row">
          <div class="stat-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg></div>
          <div><div class="stat-label">Poin Maksimal</div><div class="stat-sub">{{ $content->module->max_point ?? 0 }} Point</div></div>
        </div>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header"><h3>Instruktur</h3></div>
      <div class="card-body">
        <div class="instructor-row">
          <div class="avatar"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg></div>
          <div><div class="stat-label">Admin Sistem</div><div class="stat-sub">Mentor</div></div>
        </div>
      </div>
    </div>
  </aside>
</div>

<script>
  function toggleDark() {
    const html = document.documentElement;
    const isDark = html.getAttribute('data-theme') === 'dark';
    html.setAttribute('data-theme', isDark ? 'light' : 'dark');

    const iconLight = document.getElementById('icon-light');
    const iconDark = document.getElementById('icon-dark');
    const label = document.getElementById('toggle-label');

    if (isDark) {
      iconLight.style.display = '';
      iconDark.style.display = 'none';
      label.textContent = 'Dark';
    } else {
      iconLight.style.display = 'none';
      iconDark.style.display = '';
      label.textContent = 'Light';
    }
  }
</script>
</body>
</html>