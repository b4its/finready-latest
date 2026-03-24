<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FinReady — Kuasai Laporan Keuangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&family=Playfair+Display:wght@700;900&display=swap"
      rel="stylesheet"
    />
    <style>
      *,
      *::before,
      *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
      }

      :root {
        --bg: #f7f6f3;
        --card: #ffffff;
        --border: #e8e4dc;
        --text: #1a1814;
        --muted: #7a756b;
        --primary: #16a34a;
        --primary-light: #dcfce7;
        --success: #16a34a;
        --success-light: #dcfce7;
        --accent: #f59e0b;
        --accent-light: #fef3c7;
        --radius: 16px;
        --shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 4px 12px rgba(0, 0, 0, 0.04);
        --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.1);
      }

      body {
        font-family: "DM Sans", sans-serif;
        background: var(--bg);
        color: var(--text);
        min-height: 100vh;
        font-size: 15px;
        line-height: 1.6;
        overflow-x: hidden;
      }

      /* ── NAV ── */
      nav {
        background: var(--card);
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        z-index: 100;
      }
      .nav-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 18px;
        color: var(--text);
        text-decoration: none;
      }
      .logo-icon {
        width: 36px;
        height: 36px;
        background: var(--primary);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .logo-icon svg {
        width: 20px;
        height: 20px;
      }
      .nav-links {
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .nav-links a {
        color: var(--muted);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        padding: 8px 14px;
        border-radius: 8px;
        transition: color 0.15s, background 0.15s;
      }
      .nav-links a:hover {
        color: var(--text);
        background: var(--bg);
      }

      /* ── HAMBURGER ── */
      .hamburger-btn {
        display: none;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        width: 38px;
        height: 38px;
        padding: 8px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        background: var(--bg);
        cursor: pointer;
        transition: all .15s;
      }
      .hamburger-btn:hover { border-color: var(--primary); background: var(--primary-light); }
      .hamburger-btn span {
        display: block;
        height: 2px;
        border-radius: 2px;
        background: var(--text);
        transition: all .25s;
      }
      .hamburger-btn.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
      .hamburger-btn.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
      .hamburger-btn.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

      /* Mobile drawer */
      .mobile-menu {
        display: none;
        position: fixed;
        top: 64px; left: 0; right: 0;
        background: var(--card);
        border-bottom: 1px solid var(--border);
        padding: 16px;
        z-index: 99;
        flex-direction: column;
        gap: 6px;
        box-shadow: 0 8px 24px rgba(0,0,0,.08);
        animation: slideDown .2s ease;
      }
      @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
      }
      .mobile-menu.show { display: flex; }
      .mobile-menu a, .mobile-menu button {
        display: block; width: 100%;
        padding: 11px 16px; border-radius: 10px;
        font-size: 14px; font-weight: 500;
        color: var(--text); text-decoration: none;
        border: none; background: transparent;
        text-align: left; cursor: pointer;
        font-family: inherit;
        transition: background .15s;
      }
      .mobile-menu a:hover, .mobile-menu button:hover { background: var(--bg); }
      .mobile-menu .mobile-divider {
        height: 1px; background: var(--border);
        margin: 6px 0; border: none; padding: 0;
      }
      .mobile-menu .mobile-btn-primary {
        background: var(--primary); color: #fff !important;
        font-weight: 600; margin-top: 4px;
        text-align: center;
      }
      .mobile-menu .mobile-btn-primary:hover { background: #15803d; }
      .mobile-menu .mobile-btn-outline {
        border: 1.5px solid var(--border);
        text-align: center; font-weight: 600;
      }
      .mobile-menu .mobile-btn-outline:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
      .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 12px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all 0.18s;
      }
      .btn-outline {
        background: transparent;
        border: 1.5px solid var(--border);
        color: var(--text);
      }
      .btn-outline:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
      }
      .btn-primary {
        background: var(--primary);
        color: #fff;
      }
      .btn-primary:hover {
        background: #15803d;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
      }

      /* ── HERO ── */
      .hero {
        padding: 80px 24px 64px;
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 64px;
        align-items: center;
      }
      .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--success-light);
        color: var(--success);
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin-bottom: 20px;
      }
      .hero-badge span.dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--success);
        animation: pulse 2s infinite;
      }
      @keyframes pulse {
        0%,
        100% {
          opacity: 1;
        }
        50% {
          opacity: 0.4;
        }
      }
      .hero h1 {
        font-family: "Playfair Display", serif;
        font-size: clamp(38px, 5vw, 58px);
        font-weight: 900;
        line-height: 1.1;
        letter-spacing: -0.02em;
        color: var(--text);
        margin-bottom: 20px;
      }
      .hero h1 em {
        font-style: italic;
        color: var(--primary);
      }
      .hero p {
        font-size: 17px;
        color: var(--muted);
        line-height: 1.7;
        margin-bottom: 36px;
        max-width: 480px;
      }
      .hero-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 40px;
      }
      .btn-hero {
        padding: 14px 28px;
        font-size: 15px;
        border-radius: 14px;
      }
      .hero-stats {
        display: flex;
        gap: 32px;
        padding-top: 32px;
        border-top: 1px solid var(--border);
      }
      .stat-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
      }
      .stat-num {
        font-size: 26px;
        font-weight: 700;
        color: var(--text);
        font-family: "Playfair Display", serif;
      }
      .stat-lbl {
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
      }

      /* ── HERO VISUAL ── */
      .hero-visual {
        position: relative;
      }
      .hero-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 28px;
        box-shadow: var(--shadow-lg);
      }
      .hero-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
      }
      .hero-card-title {
        font-weight: 700;
        font-size: 15px;
      }
      .hero-card-badge {
        background: var(--success-light);
        color: var(--success);
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
      }
      .chart-bars {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        height: 100px;
        margin-bottom: 16px;
      }
      .chart-bar {
        flex: 1;
        border-radius: 6px 6px 0 0;
        background: var(--primary-light);
        transition: height 0.3s;
        position: relative;
        cursor: pointer;
      }
      .chart-bar.active {
        background: var(--primary);
      }
      .chart-bar:hover {
        opacity: 0.8;
      }
      .chart-months {
        display: flex;
        gap: 8px;
        font-size: 11px;
        color: var(--muted);
        font-family: "DM Mono", monospace;
      }
      .chart-months span {
        flex: 1;
        text-align: center;
      }

      .mini-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 16px;
      }
      .mini-card {
        background: var(--bg);
        border-radius: 12px;
        padding: 14px;
      }
      .mini-card-lbl {
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 4px;
      }
      .mini-card-val {
        font-size: 17px;
        font-weight: 700;
      }
      .mini-card-val.green {
        color: var(--success);
      }
      .mini-card-val.blue {
        color: var(--primary);
      }

      .float-badge {
        position: absolute;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 10px 16px;
        box-shadow: var(--shadow-lg);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 600;
      }
      .float-badge.top-left {
        top: -20px;
        left: -30px;
      }
      .float-badge.bottom-right {
        bottom: -20px;
        right: -20px;
      }
      .float-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
      }

      /* ── FEATURES ── */
      .section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 80px 24px;
      }
      .section-header {
        text-align: center;
        margin-bottom: 48px;
      }
      .section-eyebrow {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--primary);
        margin-bottom: 12px;
      }
      .section-title {
        font-family: "Playfair Display", serif;
        font-size: clamp(28px, 3.5vw, 40px);
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: -0.02em;
        margin-bottom: 14px;
      }
      .section-sub {
        color: var(--muted);
        max-width: 520px;
        margin: 0 auto;
        font-size: 15px;
      }

      .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
      }
      .feature-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 28px;
        transition:
          box-shadow 0.2s,
          transform 0.2s;
      }
      .feature-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
      }
      .feature-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        font-size: 22px;
      }
      .feature-icon.blue {
        background: var(--primary-light);
      }
      .feature-icon.green {
        background: var(--success-light);
      }
      .feature-icon.amber {
        background: var(--accent-light);
      }
      .feature-card h3 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
      }
      .feature-card p {
        font-size: 14px;
        color: var(--muted);
        line-height: 1.65;
      }

      /* ── COURSES ── */
      .courses-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
      }
      .course-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        transition:
          box-shadow 0.2s,
          transform 0.2s;
        text-decoration: none;
        color: var(--text);
        display: flex;
        flex-direction: column;
      }
      .course-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
      }
      .course-thumb {
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: 900;
        font-family: "DM Mono", monospace;
        letter-spacing: -0.05em;
      }
      .course-thumb.blue {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #15803d;
      }
      .course-thumb.green {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #15803d;
      }
      .course-thumb.amber {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #b45309;
      }
      .course-body {
        padding: 20px;
        flex: 1;
      }
      .course-tag {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 999px;
        display: inline-block;
        margin-bottom: 10px;
      }
      .course-tag.blue {
        background: var(--primary-light);
        color: var(--primary);
      }
      .course-tag.green {
        background: var(--success-light);
        color: var(--success);
      }
      .course-tag.amber {
        background: var(--accent-light);
        color: #b45309;
      }
      .course-body h3 {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 6px;
      }
      .course-body p {
        font-size: 13px;
        color: var(--muted);
        line-height: 1.6;
        margin-bottom: 16px;
      }
      .course-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 20px;
        border-top: 1px solid var(--border);
        font-size: 12px;
        color: var(--muted);
      }
      .course-meta {
        display: flex;
        gap: 12px;
      }
      .course-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
      }

      /* ── TESTIMONIALS ── */
      .testi-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
      }
      .testi-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 28px;
      }
      .testi-stars {
        color: var(--accent);
        font-size: 14px;
        margin-bottom: 14px;
        letter-spacing: 2px;
      }
      .testi-text {
        font-size: 15px;
        line-height: 1.7;
        margin-bottom: 20px;
        color: var(--text);
      }
      .testi-author {
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .testi-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        color: #fff;
      }
      .testi-name {
        font-weight: 600;
        font-size: 14px;
      }
      .testi-role {
        font-size: 12px;
        color: var(--muted);
      }

      /* ── CTA ── */
      .cta-section {
        margin: 0 24px 80px;
        max-width: calc(1200px - 48px);
        margin-left: auto;
        margin-right: auto;
        background: var(--primary);
        border-radius: 28px;
        padding: 64px;
        text-align: center;
        position: relative;
        overflow: hidden;
      }
      .cta-section::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
          radial-gradient(
            ellipse at 20% 50%,
            rgba(255, 255, 255, 0.12) 0%,
            transparent 60%
          ),
          radial-gradient(
            ellipse at 80% 50%,
            rgba(255, 255, 255, 0.08) 0%,
            transparent 60%
          );
      }
      .cta-section h2 {
        font-family: "Playfair Display", serif;
        font-size: clamp(28px, 3.5vw, 42px);
        font-weight: 900;
        color: #fff;
        margin-bottom: 14px;
        position: relative;
      }
      .cta-section p {
        color: rgba(255, 255, 255, 0.75);
        font-size: 16px;
        margin-bottom: 32px;
        position: relative;
      }
      .cta-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        position: relative;
      }
      .btn-white {
        background: #fff;
        color: var(--primary);
        font-size: 15px;
        padding: 14px 28px;
        border-radius: 14px;
      }
      .btn-white:hover {
        background: #f0f4ff;
        transform: translateY(-1px);
      }
      .btn-ghost {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        font-size: 15px;
        padding: 14px 28px;
        border-radius: 14px;
        border: 1.5px solid rgba(255, 255, 255, 0.3);
      }
      .btn-ghost:hover {
        background: rgba(255, 255, 255, 0.22);
        transform: translateY(-1px);
      }

      /* ── FOOTER ── */
      footer {
        background: var(--card);
        border-top: 1px solid var(--border);
        padding: 48px 24px 32px;
      }
      .footer-inner {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
      }
      .footer-brand p {
        font-size: 13px;
        color: var(--muted);
        margin-top: 12px;
        line-height: 1.65;
        max-width: 260px;
      }
      .footer-col h4 {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 14px;
      }
      .footer-col a {
        display: block;
        color: var(--text);
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 8px;
        transition: color 0.15s;
      }
      .footer-col a:hover {
        color: var(--primary);
      }
      .footer-bottom {
        max-width: 1200px;
        margin: 32px auto 0;
        padding-top: 24px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: var(--muted);
      }

      /* ── RESPONSIVE ── */
      @media (max-width: 900px) {
        .hero {
          grid-template-columns: 1fr;
          gap: 48px;
        }
        .hero-visual {
          display: none;
        }
        .features-grid,
        .courses-grid {
          grid-template-columns: 1fr 1fr;
        }
        .footer-inner {
          grid-template-columns: 1fr 1fr;
        }
      }
      @media (max-width: 600px) {
        .features-grid,
        .courses-grid,
        .testi-grid {
          grid-template-columns: 1fr;
        }
        .footer-inner {
          grid-template-columns: 1fr;
        }
        .cta-section {
          padding: 40px 24px;
        }
      }

      /* Hamburger breakpoint */
      @media (max-width: 768px) {
        .nav-links { display: none; }
        .hamburger-btn { display: flex; }
        .hero {
          grid-template-columns: 1fr;
          gap: 40px;
          padding: 48px 20px 40px;
        }
        .hero-visual { display: none; }
        .hero h1 { font-size: clamp(30px, 8vw, 42px); }
        .hero-stats { gap: 20px; flex-wrap: wrap; }
        .hero-actions { flex-direction: column; }
        .hero-actions .btn { width: 100%; justify-content: center; }
      }

      /* ── ANIMATIONS ── */
      .fade-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.5s forwards;
      }
      @keyframes fadeUp {
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      .fade-up:nth-child(1) {
        animation-delay: 0.05s;
      }
      .fade-up:nth-child(2) {
        animation-delay: 0.12s;
      }
      .fade-up:nth-child(3) {
        animation-delay: 0.19s;
      }

      .dropdown {
        position: relative;
        display: inline-block;
      }

      .dropdown-menu {
        display: none;
        position: absolute;
        background: white;
        min-width: 150px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 6px;
        margin-top: 5px;
      }

      .dropdown-menu a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #333;
      }

      .dropdown-menu a:hover {
        background: #f5f5f5;
      }

      /* ── ROLE SELECTOR MODAL ── */
      .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(3px);
      }
      .modal-overlay.show { display: flex; }
      .role-modal {
        background: var(--card);
        border-radius: 24px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 24px 64px rgba(0,0,0,.18);
        animation: modalIn .22s ease;
        overflow: hidden;
      }
      @keyframes modalIn {
        from { transform: translateY(16px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
      }
      .role-modal-header {
        padding: 28px 28px 0;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
      }
      .role-modal-title { font-size: 19px; font-weight: 700; letter-spacing: -0.02em; }
      .role-modal-sub { font-size: 13px; color: var(--muted); margin-top: 4px; }
      .modal-close-btn {
        width: 32px; height: 32px; border-radius: 8px;
        border: 1.5px solid var(--border); background: var(--bg);
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--muted); font-size: 18px; flex-shrink: 0;
        transition: all .15s; line-height: 1;
      }
      .modal-close-btn:hover { background: var(--border); color: var(--text); }
      .role-cards {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 14px; padding: 22px 28px 28px;
      }
      .role-card {
        border: 2px solid var(--border); border-radius: 16px;
        padding: 22px 16px; cursor: pointer; text-decoration: none;
        color: var(--text); background: var(--bg);
        display: flex; flex-direction: column; align-items: center;
        text-align: center; gap: 10px; transition: all .18s;
      }
      .role-card:hover {
        border-color: var(--primary); background: var(--primary-light);
        transform: translateY(-2px); box-shadow: 0 8px 24px rgba(22,163,74,.15);
      }
      .role-card-icon {
        width: 54px; height: 54px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 26px; background: var(--card);
        border: 1.5px solid var(--border); transition: all .18s;
      }
      .role-card:hover .role-card-icon { background: #dcfce7; border-color: var(--primary); }
      .role-card-name { font-size: 15px; font-weight: 700; }
      .role-card-desc { font-size: 12px; color: var(--muted); line-height: 1.5; }
      .role-card:hover .role-card-desc { color: #15803d; }
    </style>
  </head>
  <body>
    <!-- NAV -->
    <nav>
      <div class="nav-inner">
        <a href="index.html" class="logo">
          <div class="logo-icon">
            <svg
              viewBox="0 0 24 24"
              fill="none"
              stroke="white"
              stroke-width="2.5"
              stroke-linecap="round"
            >
              <path d="M3 3v18h18" />
              <path d="m7 16 4-4 4 4 4-8" />
            </svg>
          </div>
          FinReady
        </a>
        <div class="nav-links">
          <a href="#fitur" class="hide-mobile">Fitur</a>
          <a href="#kursus" class="hide-mobile">Kursus</a>
          <a href="#testimoni" class="hide-mobile">Testimoni</a>
         @auth
            @if (Auth::user()->role == "umkm")
                <a href="{{ route('filament.umkm.pages.dashboard') }}" class="hide-mobile">
                    <div class="btn btn-outline">Panel UMKM</div>
                </a>
            @elseif (Auth::user()->role == "investor")
                <a href="{{ route('filament.investor.pages.dashboard') }}" class="hide-mobile">
                    <div class="btn btn-outline">Panel Investor</div>
                </a>
            @else
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="hide-mobile">
                    <div class="btn btn-outline">Panel Admin</div>
                </a>
            @endif
          @else
              <a href="{{ route('authenticate.login') }}" class="hide-mobile">
                  <div class="btn btn-outline">Masuk</div>
              </a>
              
              <a href="{{ route('authenticate.register') }}" class="hide-mobile">
                  <div class="btn btn-primary btn-hero">Daftar Gratis</div>
              </a>
          @endauth



        </div>

        <!-- Hamburger (mobile only) -->
        <button class="hamburger-btn" id="hamburgerBtn" onclick="toggleMobileMenu()" aria-label="Buka menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </nav>

    <!-- MOBILE DRAWER -->
    <div class="mobile-menu" id="mobileMenu">
      <a href="#fitur" onclick="closeMobileMenu()">Fitur</a>
      <a href="#kursus" onclick="closeMobileMenu()">Kursus</a>
      <a href="#testimoni" onclick="closeMobileMenu()">Testimoni</a>
      <div class="mobile-divider"></div>
      <a href="{{ route('authenticate.login') }}" class="mobile-btn-outline" >Masuk</a>
      <a href="{{ route('authenticate.register') }}" class="mobile-btn-primary" >Daftar Gratis</a>
    </div>

    <!-- HERO -->
    <div class="hero">
      <div>
        <div class="hero-badge">
          <span class="dot"></span>
          Sudah dipercaya 10.000+ pelajar
        </div>
        <h1>Kuasai <em>Laporan Keuangan</em> dari Nol</h1>
        <p>
          Platform pembelajaran interaktif yang membantu Anda memahami neraca,
          laba-rugi, arus kas, dan analisis keuangan dengan cara yang mudah dan
          menyenangkan.
        </p>
        <div class="hero-actions">
          <button onclick="openModal('registerModal')" class="btn btn-primary btn-hero">
            Mulai Belajar Gratis
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="m9 18 6-6-6-6" />
            </svg>
          </button>
          <a href="#kursus" class="btn btn-outline btn-hero">Lihat Kursus</a>
        </div>
        <div class="hero-stats">
          <div class="stat-item">
            <span class="stat-num">10K+</span>
            <span class="stat-lbl">Pelajar Aktif</span>
          </div>
          <div class="stat-item">
            <span class="stat-num">48</span>
            <span class="stat-lbl">Modul Pelajaran</span>
          </div>
          <div class="stat-item">
            <span class="stat-num">4.9</span>
            <span class="stat-lbl">Rating Rata-rata</span>
          </div>
        </div>
      </div>

      <!-- Hero Visual -->
      <div class="hero-visual">
        <div class="float-badge top-left">
          <div class="float-icon" style="background: #dcfce7">📈</div>
          <div>
            <div style="font-size: 11px; color: #7a756b">Keuntungan Bersih</div>
            <div style="color: #16a34a">+24.5%</div>
          </div>
        </div>
        <div class="hero-card">
          <div class="hero-card-header">
            <span class="hero-card-title">Laporan Laba Rugi</span>
            <span class="hero-card-badge">Q4 2024</span>
          </div>
          <div class="chart-bars">
            <div class="chart-bar" style="height: 45%"></div>
            <div class="chart-bar" style="height: 60%"></div>
            <div class="chart-bar" style="height: 52%"></div>
            <div class="chart-bar" style="height: 70%"></div>
            <div class="chart-bar active" style="height: 88%"></div>
            <div class="chart-bar" style="height: 65%"></div>
          </div>
          <div class="chart-months">
            <span>Jul</span><span>Ags</span><span>Sep</span><span>Okt</span
            ><span>Nov</span><span>Des</span>
          </div>
          <div class="mini-cards">
            <div class="mini-card">
              <div class="mini-card-lbl">Total Pendapatan</div>
              <div class="mini-card-val blue">Rp 4.2M</div>
            </div>
            <div class="mini-card">
              <div class="mini-card-lbl">Laba Bersih</div>
              <div class="mini-card-val green">Rp 1.1M</div>
            </div>
          </div>
        </div>
        <div class="float-badge bottom-right">
          <div class="float-icon" style="background: #dcfce7">🎓</div>
          <div>
            <div style="font-size: 11px; color: #7a756b">Modul Selesai</div>
            <div style="color: #16a34a">3 dari 8</div>
          </div>
        </div>
      </div>
    </div>

    <!-- FEATURES -->
    <section class="section" id="fitur">
      <div class="section-header">
        <p class="section-eyebrow">Mengapa FinReady?</p>
        <h2 class="section-title">Belajar lebih cepat, lebih efektif</h2>
        <p class="section-sub">
          Kami merancang pengalaman belajar yang terstruktur dan interaktif
          khusus untuk laporan keuangan.
        </p>
      </div>
      <div class="features-grid">
        <div class="feature-card fade-up">
          <div class="feature-icon blue">📊</div>
          <h3>Visualisasi Interaktif</h3>
          <p>
            Pahami laporan keuangan melalui grafik dan diagram interaktif yang
            memudahkan pemahaman konsep yang kompleks.
          </p>
        </div>
        <div class="feature-card fade-up">
          <div class="feature-icon green">✅</div>
          <h3>Kuis & Latihan Soal</h3>
          <p>
            Uji pemahaman Anda dengan ratusan soal latihan yang dirancang oleh
            pakar akuntansi dan keuangan berpengalaman.
          </p>
        </div>
        <div class="feature-card fade-up">
          <div class="feature-icon amber">🏆</div>
          <h3>Sertifikat Resmi</h3>
          <p>
            Dapatkan sertifikat penyelesaian yang dapat Anda tambahkan ke
            LinkedIn dan portofolio profesional Anda.
          </p>
        </div>
        <div class="feature-card fade-up">
          <div class="feature-icon blue">🎯</div>
          <h3>Jalur Belajar Terstruktur</h3>
          <p>
            Kurikulum yang dirancang dari dasar hingga mahir, memastikan tidak
            ada konsep penting yang terlewatkan.
          </p>
        </div>
        <div class="feature-card fade-up">
          <div class="feature-icon green">📱</div>
          <h3>Belajar di Mana Saja</h3>
          <p>
            Akses semua materi dari perangkat apapun — desktop, tablet, atau
            smartphone — kapanpun Anda mau.
          </p>
        </div>
        <div class="feature-card fade-up">
          <div class="feature-icon amber">👥</div>
          <h3>Komunitas Aktif</h3>
          <p>
            Bergabung dengan ribuan pelajar lain, diskusi, tanya jawab, dan
            berbagi pengalaman belajar bersama.
          </p>
        </div>
      </div>
    </section>

    <!-- COURSES -->
    <section class="section" id="kursus" style="padding-top: 0">
      <div class="section-header">
        <p class="section-eyebrow">Kursus Populer</p>
        <h2 class="section-title">Pilih topik yang ingin Anda pelajari</h2>
        <p class="section-sub">
          Dari dasar akuntansi hingga analisis laporan keuangan tingkat lanjut.
        </p>
      </div>
      <div class="courses-grid">
        <a href="pembelajaran.html" class="course-card">
          <div class="course-thumb blue">N/L/CF</div>
          <div class="course-body">
            <span class="course-tag blue">Pemula</span>
            <h3>Dasar Laporan Keuangan</h3>
            <p>
              Pelajari tiga laporan keuangan utama: Neraca, Laba Rugi, dan Arus
              Kas dari nol.
            </p>
          </div>
          <div class="course-footer">
            <div class="course-meta">
              <span>
                <svg
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <circle cx="12" cy="12" r="10" />
                  <path d="M12 6v6l4 2" />
                </svg>
                8 jam
              </span>
              <span>
                <svg
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg>
                24 modul
              </span>
            </div>
            <span style="color: #16a34a; font-weight: 600">Gratis</span>
          </div>
        </a>
        <a href="pembelajaran.html" class="course-card">
          <div class="course-thumb green">ROI%</div>
          <div class="course-body">
            <span class="course-tag green">Menengah</span>
            <h3>Analisis Rasio Keuangan</h3>
            <p>
              Menghitung dan menginterpretasikan rasio likuiditas,
              profitabilitas, dan solvabilitas perusahaan.
            </p>
          </div>
          <div class="course-footer">
            <div class="course-meta">
              <span>
                <svg
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <circle cx="12" cy="12" r="10" />
                  <path d="M12 6v6l4 2" />
                </svg>
                12 jam
              </span>
              <span>
                <svg
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg>
                18 modul
              </span>
            </div>
            <span style="color: #b45309; font-weight: 600">Pro</span>
          </div>
        </a>
        <a href="pembelajaran.html" class="course-card">
          <div class="course-thumb amber">IFRS</div>
          <div class="course-body">
            <span class="course-tag amber">Lanjutan</span>
            <h3>Standar Akuntansi PSAK/IFRS</h3>
            <p>
              Memahami penerapan standar akuntansi internasional dalam
              penyusunan laporan keuangan perusahaan publik.
            </p>
          </div>
          <div class="course-footer">
            <div class="course-meta">
              <span>
                <svg
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <circle cx="12" cy="12" r="10" />
                  <path d="M12 6v6l4 2" />
                </svg>
                16 jam
              </span>
              <span>
                <svg
                  width="12"
                  height="12"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                  <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                </svg>
                30 modul
              </span>
            </div>
            <span style="color: #b45309; font-weight: 600">Pro</span>
          </div>
        </a>
      </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="section" id="testimoni" style="padding-top: 0">
      <div class="section-header">
        <p class="section-eyebrow">Testimoni</p>
        <h2 class="section-title">Dipercaya ribuan pelajar</h2>
      </div>
      <div class="testi-grid">
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text">
            "Awalnya saya bingung banget baca laporan keuangan. Setelah ikut
            kursus di FinReady, sekarang saya bisa analisis laporan perusahaan
            sendiri. Materinya sangat terstruktur dan mudah dipahami!"
          </p>
          <div class="testi-author">
            <div class="testi-avatar" style="background: #16a34a">BR</div>
            <div>
              <div class="testi-name">Budi Raharjo</div>
              <div class="testi-role">Financial Analyst, Jakarta</div>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text">
            "Platform terbaik untuk belajar akuntansi! Visualisasi interaktifnya
            membantu saya memahami konsep yang selama ini terasa rumit.
            Sertifikatnya juga langsung saya pakai di CV saya."
          </p>
          <div class="testi-author">
            <div class="testi-avatar" style="background: #16a34a">SW</div>
            <div>
              <div class="testi-name">Sari Wulandari</div>
              <div class="testi-role">Mahasiswi Akuntansi, Surabaya</div>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text">
            "Sebagai pemilik UMKM, saya akhirnya bisa baca laporan keuangan
            bisnis sendiri tanpa harus bergantung akuntan. Sangat membantu untuk
            mengambil keputusan bisnis yang lebih baik."
          </p>
          <div class="testi-author">
            <div class="testi-avatar" style="background: #f59e0b">DK</div>
            <div>
              <div class="testi-name">Dimas Kurniawan</div>
              <div class="testi-role">Pemilik UMKM, Bandung</div>
            </div>
          </div>
        </div>
        <div class="testi-card">
          <div class="testi-stars">★★★★☆</div>
          <p class="testi-text">
            "Kuis dan latihan soalnya sangat berguna untuk persiapan ujian CPA
            saya. Penjelasannya detail dan contoh kasusnya relevan dengan
            kondisi bisnis di Indonesia."
          </p>
          <div class="testi-author">
            <div class="testi-avatar" style="background: #dc2626">RP</div>
            <div>
              <div class="testi-name">Rina Puspita</div>
              <div class="testi-role">CPA Candidate, Medan</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <div class="cta-section">
      <h2>Mulai belajar sekarang, gratis!</h2>
      <p>
        Bergabunglah dengan 10.000+ pelajar yang sudah meningkatkan kemampuan
        keuangan mereka bersama FinReady.
      </p>
      <div class="cta-actions">
        <button onclick="openModal('registerModal')" class="btn btn-white btn-hero">Daftar Gratis Sekarang</button>
        <a href="login.html" class="btn btn-ghost btn-hero">Sudah punya akun? Masuk</a>
      </div>
    </div>

    <!-- FOOTER -->
    <footer>
      <div class="footer-inner">
        <div class="footer-brand">
          <a href="index.html" class="logo" style="display: inline-flex">
            <div class="logo-icon">
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="white"
                stroke-width="2.5"
                stroke-linecap="round"
              >
                <path d="M3 3v18h18" />
                <path d="m7 16 4-4 4 4 4-8" />
              </svg>
            </div>
            FinReady
          </a>
          <p>
            Platform pembelajaran laporan keuangan terpercaya untuk profesional
            dan pelajar di Indonesia.
          </p>
        </div>
        <div class="footer-col">
          <h4>Produk</h4>
          <a href="#">Kursus</a>
          <a href="#">Sertifikasi</a>
          <a href="#">Latihan Soal</a>
          <a href="#">Live Class</a>
        </div>
        <div class="footer-col">
          <h4>Perusahaan</h4>
          <a href="#">Tentang Kami</a>
          <a href="#">Blog</a>
          <a href="#">Karir</a>
          <a href="#">Kontak</a>
        </div>
        <div class="footer-col">
          <h4>Bantuan</h4>
          <a href="#">FAQ</a>
          <a href="#">Kebijakan Privasi</a>
          <a href="#">Syarat & Ketentuan</a>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2024 FinReady. Hak cipta dilindungi.</span>
        <span>Made with ❤️ in Indonesia</span>
      </div>
    <!-- ROLE SELECTOR MODAL: REGISTER -->
    <div class="modal-overlay" id="registerModal" onclick="handleOverlayClick(event,'registerModal')">
      <div class="role-modal">
        <div class="role-modal-header">
          <div>
            <div class="role-modal-title">Daftar sebagai apa?</div>
            <div class="role-modal-sub">Pilih peran Anda untuk memulai</div>
          </div>
          <button class="modal-close-btn" onclick="closeModal('registerModal')">✕</button>
        </div>
        <div class="role-cards">
          <a href="register.html?role=umkm" class="role-card">
            <div class="role-card-icon">🏪</div>
            <div class="role-card-name">UMKM</div>
            <div class="role-card-desc">Pemilik usaha yang ingin mengelola laporan keuangan</div>
          </a>
          <a href="register.html?role=investor" class="role-card">
            <div class="role-card-icon">📈</div>
            <div class="role-card-name">Investor</div>
            <div class="role-card-desc">Investor yang ingin menganalisis peluang bisnis</div>
          </a>
        </div>
      </div>
    </div>

    <!-- ROLE SELECTOR MODAL: LOGIN (Nav) -->
    <div class="modal-overlay" id="loginModal" onclick="handleOverlayClick(event,'loginModal')">
      <div class="role-modal">
        <div class="role-modal-header">
          <div>
            <div class="role-modal-title">Masuk sebagai apa?</div>
            <div class="role-modal-sub">Pilih peran Anda untuk melanjutkan</div>
          </div>
          <button class="modal-close-btn" onclick="closeModal('loginModal')">✕</button>
        </div>
        <div class="role-cards">
          <a href="login.html?role=umkm" class="role-card">
            <div class="role-card-icon">🏪</div>
            <div class="role-card-name">UMKM</div>
            <div class="role-card-desc">Login sebagai pemilik usaha</div>
          </a>
          <a href="login.html?role=investor" class="role-card">
            <div class="role-card-icon">📈</div>
            <div class="role-card-name">Investor</div>
            <div class="role-card-desc">Login sebagai investor</div>
          </a>
        </div>
      </div>
    </div>

  </body>
  <script>
    function openModal(id) {
      document.getElementById(id).classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
      document.getElementById(id).classList.remove('show');
      document.body.style.overflow = '';
    }
    function handleOverlayClick(e, id) {
      if (e.target === document.getElementById(id)) closeModal(id);
    }
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
        ['registerModal','loginModal'].forEach(closeModal);
        closeMobileMenu();
      }
    });

    // Hamburger
    function toggleMobileMenu() {
      const menu = document.getElementById('mobileMenu');
      const btn = document.getElementById('hamburgerBtn');
      const isOpen = menu.classList.toggle('show');
      btn.classList.toggle('open', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
    }
    function closeMobileMenu() {
      document.getElementById('mobileMenu').classList.remove('show');
      document.getElementById('hamburgerBtn').classList.remove('open');
      document.body.style.overflow = '';
    }
    // Close drawer on resize to desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth > 768) closeMobileMenu();
    });
  </script>
</html>