<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Sensor – ESP32 UNAMA</title>
    <meta name="description" content="Dashboard monitoring real-time sensor lingkungan berbasis ESP32: kelembaban tanah, suhu udara, kelembaban udara, tekanan udara, dan curah hujan.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-deep:     #050b1a;
            --bg-card:     rgba(255,255,255,0.04);
            --bg-card-hov: rgba(255,255,255,0.08);
            --border:      rgba(255,255,255,0.08);

            --green:  #34d399;
            --blue:   #60a5fa;
            --purple: #a78bfa;
            --amber:  #fbbf24;
            --cyan:   #22d3ee;

            --text-primary:   #f0f6ff;
            --text-secondary: #94a3b8;
            --text-muted:     #475569;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-deep);
            background-image:
                radial-gradient(ellipse 80% 60% at 20% -10%, rgba(96,165,250,.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 110%, rgba(167,139,250,.10) 0%, transparent 60%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
            padding: .9rem 2rem;
            background: rgba(5,11,26,0.80);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
        }
        .topbar-left { display: flex; align-items: center; gap: 1.25rem; }
        .brand { display: flex; align-items: center; gap: .6rem; font-weight: 700; font-size: 1rem; }
        .brand-dot { width:9px; height:9px; border-radius:50%; background:var(--green); box-shadow:0 0 10px var(--green); animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{box-shadow:0 0 6px var(--green)} 50%{box-shadow:0 0 18px var(--green)} }

        .nav-links { display: flex; gap: .4rem; }
        .nav-link {
            font-size: .8rem; font-weight: 500; color: var(--text-secondary);
            text-decoration: none; padding: .35rem .75rem; border-radius: 8px;
            border: 1px solid transparent;
            transition: color .2s, background .2s, border-color .2s;
            display: flex; align-items: center; gap: .35rem;
        }
        .nav-link:hover { color: var(--text-primary); background: var(--bg-card); border-color: var(--border); }
        .nav-link.active { color: var(--blue); background: rgba(96,165,250,.1); border-color: rgba(96,165,250,.25); }

        .topbar-right { display: flex; align-items: center; gap: .65rem; }
        .live-badge {
            display: flex; align-items: center; gap: .4rem;
            font-size: .75rem; font-weight: 600;
            background: rgba(52,211,153,.1); color: var(--green);
            border: 1px solid rgba(52,211,153,.25);
            padding: .3rem .75rem; border-radius: 999px;
        }
        .live-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: pulse 2s infinite; }
        .btn-refresh {
            display: flex; align-items: center; gap: .4rem;
            font-size: .82rem; font-weight: 600;
            background: linear-gradient(135deg,#3b82f6,#6366f1);
            color: #fff; border: none; cursor: pointer;
            padding: .42rem 1rem; border-radius: 8px;
            transition: opacity .2s, transform .15s;
        }
        .btn-refresh:hover { opacity: .85; transform: translateY(-1px); }
        .btn-refresh:active { transform: translateY(0); }

        /* countdown ring */
        .countdown-wrap {
            display: flex; align-items: center; gap: .45rem;
            font-size: .75rem; color: var(--text-muted); font-weight: 500;
        }
        .countdown-ring { position: relative; width: 22px; height: 22px; }
        .countdown-ring svg { transform: rotate(-90deg); }
        .ring-bg { fill: none; stroke: rgba(255,255,255,.08); stroke-width: 3; }
        .ring-fill { fill: none; stroke: var(--green); stroke-width: 3; stroke-linecap: round;
            stroke-dasharray: 56.5; transition: stroke-dashoffset .9s linear; }
        #countdown-num { font-size: .75rem; font-weight: 700; color: var(--green); }

        /* ── HERO ── */
        .hero { text-align: center; padding: 2.5rem 1rem 1.5rem; }
        .hero-tag {
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: .73rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            color: var(--blue); background: rgba(96,165,250,.1);
            border: 1px solid rgba(96,165,250,.2);
            padding: .3rem .9rem; border-radius: 999px; margin-bottom: .9rem;
        }
        .hero h1 { font-size: clamp(1.7rem,4vw,2.6rem); font-weight: 800; letter-spacing: -.5px; line-height: 1.2; margin-bottom: .6rem; }
        .hero h1 span { background: linear-gradient(90deg,#60a5fa,#a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); font-size: .9rem; max-width: 500px; margin: 0 auto; }

        /* ── LAYOUT ── */
        .main { max-width: 1300px; margin: 0 auto; padding: 1.25rem 1.5rem 4rem; }

        /* ── SUMMARY ROW ── */
        .summary-row {
            display: grid; grid-template-columns: repeat(5,1fr); gap: .85rem;
            margin-bottom: 2rem;
        }
        .sum-card {
            background: var(--bg-card); border: 1px solid var(--border); border-radius: 14px;
            padding: .9rem 1rem; display: flex; align-items: center; gap: .75rem;
            transition: background .2s, transform .2s;
        }
        .sum-card:hover { background: var(--bg-card-hov); transform: translateY(-2px); }
        .sum-icon { font-size: 1.4rem; flex-shrink: 0; }
        .sum-label { font-size: .7rem; color: var(--text-secondary); font-weight: 500; }
        .sum-val { font-size: 1rem; font-weight: 700; }

        /* ── SECTION ── */
        .sensor-section { margin-bottom: 2.25rem; }
        .section-header {
            display: flex; align-items: center; gap: .75rem;
            margin-bottom: 1rem; padding-bottom: .75rem;
            border-bottom: 1px solid var(--border);
        }
        .section-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }
        .section-title { font-size: .95rem; font-weight: 700; }
        .section-sub { font-size: .76rem; color: var(--text-secondary); margin-top: .12rem; }
        .badge-count {
            margin-left: auto; font-size: .72rem; font-weight: 600;
            padding: .22rem .65rem; border-radius: 999px;
        }

        /* ── CARDS GRID ── */
        .cards-row { display: grid; gap: 1rem; }
        .three-col { grid-template-columns: repeat(3,1fr); }
        .one-col   { grid-template-columns: 1fr; max-width: 340px; }

        /* ── SENSOR CARD ── */
        .sensor-card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 16px; padding: 1.3rem 1.35rem;
            position: relative; overflow: hidden;
            transition: background .25s, border-color .25s, transform .2s, box-shadow .25s;
        }
        .sensor-card:hover { background: var(--bg-card-hov); transform: translateY(-3px); }

        /* accent colors */
        .acc-green  .sensor-card:hover { border-color: rgba(52,211,153,.45); box-shadow: 0 8px 28px rgba(52,211,153,.12); }
        .acc-blue   .sensor-card:hover { border-color: rgba(96,165,250,.45); box-shadow: 0 8px 28px rgba(96,165,250,.12); }
        .acc-purple .sensor-card:hover { border-color: rgba(167,139,250,.45); box-shadow: 0 8px 28px rgba(167,139,250,.12); }
        .acc-amber  .sensor-card:hover { border-color: rgba(251,191,36,.45); box-shadow: 0 8px 28px rgba(251,191,36,.12); }
        .acc-cyan   .sensor-card:hover { border-color: rgba(34,211,238,.45); box-shadow: 0 8px 28px rgba(34,211,238,.12); }

        .card-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .card-alat-label { font-size: .7rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: var(--text-secondary); }
        .card-alat-badge { font-size: .68rem; font-weight: 700; padding: .18rem .55rem; border-radius: 6px; }

        .card-value { font-size: 2.1rem; font-weight: 800; letter-spacing: -1.5px; line-height: 1; margin-bottom: .25rem; }
        .card-unit  { font-size: .78rem; font-weight: 500; }

        /* progress bar */
        .bar-wrap { margin-top: .8rem; }
        .bar-track { height: 4px; border-radius: 999px; background: rgba(255,255,255,.07); overflow: hidden; }
        .bar-fill  { height: 100%; border-radius: 999px; transition: width .9s cubic-bezier(.4,0,.2,1); }

        /* column label under value */
        .card-col-name {
            margin-top: .75rem; padding-top: .75rem; border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: .4rem;
            font-size: .7rem; color: var(--text-muted);
        }

        /* update flash */
        @keyframes flash-green { 0%{background:rgba(52,211,153,.12)} 100%{background:transparent} }
        .sensor-card.updated { animation: flash-green .8s ease; }

        /* shimmer skeleton */
        @keyframes shimmer { 0%{background-position:-600px 0} 100%{background-position:600px 0} }
        .shimmer {
            background: linear-gradient(90deg,rgba(255,255,255,.03) 25%,rgba(255,255,255,.09) 50%,rgba(255,255,255,.03) 75%);
            background-size: 600px 100%; animation: shimmer 1.5s infinite linear; border-radius: 8px;
        }

        /* footer strip */
        .footer-strip {
            text-align: center; font-size: .76rem; color: var(--text-muted);
            margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border);
        }
        .footer-strip span { color: var(--text-secondary); }

        /* responsive */
        @media(max-width:960px) { .three-col { grid-template-columns: 1fr 1fr; } .summary-row { grid-template-columns: repeat(3,1fr); } }
        @media(max-width:600px) {
            .topbar { padding: .75rem 1rem; }
            .nav-links { display: none; }
            .summary-row { grid-template-columns: 1fr 1fr; }
            .three-col { grid-template-columns: 1fr; }
            .one-col { max-width: 100%; }
            .main { padding: 1rem 1rem 3rem; }
        }
    </style>
</head>

<body>

<!-- ── TOPBAR ── -->
<nav class="topbar">
    <div class="topbar-left">
        <div class="brand">
            <div class="brand-dot"></div>
            ESP32 · UNAMA
        </div>
        <div class="nav-links">
            <a href="{{ route('home') }}" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="{{ route('sensor.monitor') }}" class="nav-link"><i class="bi bi-grid-3x2-gap"></i> Monitor</a>
            <a href="{{ route('sensor.report') }}" class="nav-link"><i class="bi bi-table"></i> Laporan</a>
        </div>
    </div>
    <div class="topbar-right">
        <div class="countdown-wrap">
            <div class="countdown-ring">
                <svg width="22" height="22" viewBox="0 0 22 22">
                    <circle class="ring-bg" cx="11" cy="11" r="9"/>
                    <circle class="ring-fill" id="ring-fill" cx="11" cy="11" r="9"/>
                </svg>
            </div>
            <span id="countdown-num">10</span>s
        </div>
        <div class="live-badge"><div class="live-dot"></div> Live</div>
        <button class="btn-refresh" id="btn-refresh">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
</nav>

<!-- ── HERO ── -->
<header class="hero">
    <div class="hero-tag"><i class="bi bi-broadcast"></i> Real-time · Refresh 10 detik</div>
    <h1>Dashboard <span>Sensor Lingkungan</span></h1>
    <p>Pantau 5 jenis sensor secara langsung dari 13 titik alat ukur yang tersebar di lapangan.</p>
</header>

<main class="main">

    <!-- SUMMARY -->
    <div class="summary-row">
        <div class="sum-card"><div class="sum-icon" style="color:var(--green)">🌱</div><div><div class="sum-label">Kel. Tanah (rata²)</div><div class="sum-val" id="sum-soil">— %</div></div></div>
        <div class="sum-card"><div class="sum-icon" style="color:var(--blue)">🌡️</div><div><div class="sum-label">Suhu Udara (rata²)</div><div class="sum-val" id="sum-temp">— °C</div></div></div>
        <div class="sum-card"><div class="sum-icon" style="color:var(--purple)">💧</div><div><div class="sum-label">Kel. Udara (rata²)</div><div class="sum-val" id="sum-airhum">— %</div></div></div>
        <div class="sum-card"><div class="sum-icon" style="color:var(--amber)">🌬️</div><div><div class="sum-label">Tek. Udara (rata²)</div><div class="sum-val" id="sum-pressure">— hPa</div></div></div>
        <div class="sum-card"><div class="sum-icon" style="color:var(--cyan)">🌧️</div><div><div class="sum-label">Curah Hujan</div><div class="sum-val" id="sum-rain">— mm</div></div></div>
    </div>

    <!-- ════ KELEMBABAN TANAH (3 Alat) ════ -->
    <section class="sensor-section acc-green">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(52,211,153,.12);color:var(--green)"><i class="bi bi-moisture"></i></div>
            <div>
                <div class="section-title" style="color:var(--green)">Kelembaban Tanah</div>
                <div class="section-sub">Persentase kadar air dalam tanah</div>
            </div>
            <span class="badge-count" style="background:rgba(52,211,153,.1);color:var(--green);border:1px solid rgba(52,211,153,.25)">3 Alat</span>
        </div>
        <div class="cards-row three-col">
            <div class="sensor-card" id="card-kelembaban-tanah-1">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #1</div>
                    <div class="card-alat-badge" style="background:rgba(52,211,153,.1);color:var(--green)">Kelembaban Tanah</div>
                </div>
                <div class="card-value" style="color:var(--green)" id="val-kelembaban-tanah-1">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-kelembaban-tanah-1" style="width:0%;background:linear-gradient(90deg,#059669,#34d399)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--green)"></i> kelembaban_tanah_1</div>
            </div>
            <div class="sensor-card" id="card-kelembaban-tanah-2">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #2</div>
                    <div class="card-alat-badge" style="background:rgba(52,211,153,.1);color:var(--green)">Kelembaban Tanah</div>
                </div>
                <div class="card-value" style="color:var(--green)" id="val-kelembaban-tanah-2">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-kelembaban-tanah-2" style="width:0%;background:linear-gradient(90deg,#059669,#34d399)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--green)"></i> kelembaban_tanah_2</div>
            </div>
            <div class="sensor-card" id="card-kelembaban-tanah-3">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #3</div>
                    <div class="card-alat-badge" style="background:rgba(52,211,153,.1);color:var(--green)">Kelembaban Tanah</div>
                </div>
                <div class="card-value" style="color:var(--green)" id="val-kelembaban-tanah-3">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-kelembaban-tanah-3" style="width:0%;background:linear-gradient(90deg,#059669,#34d399)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--green)"></i> kelembaban_tanah_3</div>
            </div>
        </div>
    </section>

    <!-- ════ SUHU UDARA (3 Alat) ════ -->
    <section class="sensor-section acc-blue">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(96,165,250,.12);color:var(--blue)"><i class="bi bi-thermometer-half"></i></div>
            <div>
                <div class="section-title" style="color:var(--blue)">Suhu Udara</div>
                <div class="section-sub">Temperatur udara di sekitar alat sensor</div>
            </div>
            <span class="badge-count" style="background:rgba(96,165,250,.1);color:var(--blue);border:1px solid rgba(96,165,250,.25)">3 Alat</span>
        </div>
        <div class="cards-row three-col">
            <div class="sensor-card" id="card-suhu-udara-1">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #1</div>
                    <div class="card-alat-badge" style="background:rgba(96,165,250,.1);color:var(--blue)">Suhu Udara</div>
                </div>
                <div class="card-value" style="color:var(--blue)" id="val-suhu-udara-1">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-suhu-udara-1" style="width:0%;background:linear-gradient(90deg,#1d4ed8,#60a5fa)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--blue)"></i> suhu_udara_1</div>
            </div>
            <div class="sensor-card" id="card-suhu-udara-2">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #2</div>
                    <div class="card-alat-badge" style="background:rgba(96,165,250,.1);color:var(--blue)">Suhu Udara</div>
                </div>
                <div class="card-value" style="color:var(--blue)" id="val-suhu-udara-2">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-suhu-udara-2" style="width:0%;background:linear-gradient(90deg,#1d4ed8,#60a5fa)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--blue)"></i> suhu_udara_2</div>
            </div>
            <div class="sensor-card" id="card-suhu-udara-3">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #3</div>
                    <div class="card-alat-badge" style="background:rgba(96,165,250,.1);color:var(--blue)">Suhu Udara</div>
                </div>
                <div class="card-value" style="color:var(--blue)" id="val-suhu-udara-3">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-suhu-udara-3" style="width:0%;background:linear-gradient(90deg,#1d4ed8,#60a5fa)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--blue)"></i> suhu_udara_3</div>
            </div>
        </div>
    </section>

    <!-- ════ KELEMBABAN UDARA (3 Alat) ════ -->
    <section class="sensor-section acc-purple">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(167,139,250,.12);color:var(--purple)"><i class="bi bi-droplet-half"></i></div>
            <div>
                <div class="section-title" style="color:var(--purple)">Kelembaban Udara</div>
                <div class="section-sub">Kadar uap air di atmosfer (RH)</div>
            </div>
            <span class="badge-count" style="background:rgba(167,139,250,.1);color:var(--purple);border:1px solid rgba(167,139,250,.25)">3 Alat</span>
        </div>
        <div class="cards-row three-col">
            <div class="sensor-card" id="card-kelembaban-udara-1">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #1</div>
                    <div class="card-alat-badge" style="background:rgba(167,139,250,.1);color:var(--purple)">Kelembaban Udara</div>
                </div>
                <div class="card-value" style="color:var(--purple)" id="val-kelembaban-udara-1">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-kelembaban-udara-1" style="width:0%;background:linear-gradient(90deg,#6d28d9,#a78bfa)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--purple)"></i> kelembaban_udara_1</div>
            </div>
            <div class="sensor-card" id="card-kelembaban-udara-2">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #2</div>
                    <div class="card-alat-badge" style="background:rgba(167,139,250,.1);color:var(--purple)">Kelembaban Udara</div>
                </div>
                <div class="card-value" style="color:var(--purple)" id="val-kelembaban-udara-2">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-kelembaban-udara-2" style="width:0%;background:linear-gradient(90deg,#6d28d9,#a78bfa)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--purple)"></i> kelembaban_udara_2</div>
            </div>
            <div class="sensor-card" id="card-kelembaban-udara-3">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #3</div>
                    <div class="card-alat-badge" style="background:rgba(167,139,250,.1);color:var(--purple)">Kelembaban Udara</div>
                </div>
                <div class="card-value" style="color:var(--purple)" id="val-kelembaban-udara-3">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-kelembaban-udara-3" style="width:0%;background:linear-gradient(90deg,#6d28d9,#a78bfa)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--purple)"></i> kelembaban_udara_3</div>
            </div>
        </div>
    </section>

    <!-- ════ TEKANAN UDARA (3 Alat) ════ -->
    <section class="sensor-section acc-amber">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(251,191,36,.12);color:var(--amber)"><i class="bi bi-speedometer2"></i></div>
            <div>
                <div class="section-title" style="color:var(--amber)">Tekanan Udara</div>
                <div class="section-sub">Tekanan atmosfer dalam satuan hPa</div>
            </div>
            <span class="badge-count" style="background:rgba(251,191,36,.1);color:var(--amber);border:1px solid rgba(251,191,36,.25)">3 Alat</span>
        </div>
        <div class="cards-row three-col">
            <div class="sensor-card" id="card-tekanan-udara-1">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #1</div>
                    <div class="card-alat-badge" style="background:rgba(251,191,36,.1);color:var(--amber)">Tekanan Udara</div>
                </div>
                <div class="card-value" style="color:var(--amber)" id="val-tekanan-udara-1">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-tekanan-udara-1" style="width:0%;background:linear-gradient(90deg,#b45309,#fbbf24)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--amber)"></i> tekanan_udara_1</div>
            </div>
            <div class="sensor-card" id="card-tekanan-udara-2">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #2</div>
                    <div class="card-alat-badge" style="background:rgba(251,191,36,.1);color:var(--amber)">Tekanan Udara</div>
                </div>
                <div class="card-value" style="color:var(--amber)" id="val-tekanan-udara-2">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-tekanan-udara-2" style="width:0%;background:linear-gradient(90deg,#b45309,#fbbf24)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--amber)"></i> tekanan_udara_2</div>
            </div>
            <div class="sensor-card" id="card-tekanan-udara-3">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #3</div>
                    <div class="card-alat-badge" style="background:rgba(251,191,36,.1);color:var(--amber)">Tekanan Udara</div>
                </div>
                <div class="card-value" style="color:var(--amber)" id="val-tekanan-udara-3">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-tekanan-udara-3" style="width:0%;background:linear-gradient(90deg,#b45309,#fbbf24)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--amber)"></i> tekanan_udara_3</div>
            </div>
        </div>
    </section>

    <!-- ════ CURAH HUJAN (1 Alat) ════ -->
    <section class="sensor-section acc-cyan">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(34,211,238,.12);color:var(--cyan)"><i class="bi bi-cloud-rain-heavy"></i></div>
            <div>
                <div class="section-title" style="color:var(--cyan)">Curah Hujan</div>
                <div class="section-sub">Volume air hujan yang terukur (mm)</div>
            </div>
            <span class="badge-count" style="background:rgba(34,211,238,.1);color:var(--cyan);border:1px solid rgba(34,211,238,.25)">1 Alat</span>
        </div>
        <div class="cards-row one-col">
            <div class="sensor-card" id="card-curah-hujan">
                <div class="card-header-row">
                    <div class="card-alat-label">Alat #1</div>
                    <div class="card-alat-badge" style="background:rgba(34,211,238,.1);color:var(--cyan)">Curah Hujan</div>
                </div>
                <div class="card-value" style="color:var(--cyan)" id="val-curah-hujan">—</div>
                <div class="bar-wrap"><div class="bar-track"><div class="bar-fill" id="bar-curah-hujan" style="width:0%;background:linear-gradient(90deg,#0e7490,#22d3ee)"></div></div></div>
                <div class="card-col-name"><i class="bi bi-database" style="color:var(--cyan)"></i> curah_hujan</div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <div class="footer-strip">
        <i class="bi bi-clock"></i> Terakhir diperbarui: <span id="last-time">—</span>
        &nbsp;·&nbsp; Auto-refresh setiap <strong style="color:var(--green)">10 detik</strong>
    </div>

</main>

<script>
/* ══════════════════════════════════════
   FIELD MAP: card-id → {col, unit, dec, min, max}
══════════════════════════════════════ */
const FIELDS = {
    'kelembaban-tanah-1': { col:'kelembaban_tanah_1', unit:'%',   dec:1, min:0,   max:100  },
    'kelembaban-tanah-2': { col:'kelembaban_tanah_2', unit:'%',   dec:1, min:0,   max:100  },
    'kelembaban-tanah-3': { col:'kelembaban_tanah_3', unit:'%',   dec:1, min:0,   max:100  },
    'suhu-udara-1':       { col:'suhu_udara_1',       unit:'°C',  dec:1, min:0,   max:50   },
    'suhu-udara-2':       { col:'suhu_udara_2',       unit:'°C',  dec:1, min:0,   max:50   },
    'suhu-udara-3':       { col:'suhu_udara_3',       unit:'°C',  dec:1, min:0,   max:50   },
    'kelembaban-udara-1': { col:'kelembaban_udara_1', unit:'%',   dec:1, min:0,   max:100  },
    'kelembaban-udara-2': { col:'kelembaban_udara_2', unit:'%',   dec:1, min:0,   max:100  },
    'kelembaban-udara-3': { col:'kelembaban_udara_3', unit:'%',   dec:1, min:0,   max:100  },
    'tekanan-udara-1':    { col:'tekanan_udara_1',    unit:'hPa', dec:1, min:900, max:1100 },
    'tekanan-udara-2':    { col:'tekanan_udara_2',    unit:'hPa', dec:1, min:900, max:1100 },
    'tekanan-udara-3':    { col:'tekanan_udara_3',    unit:'hPa', dec:1, min:900, max:1100 },
    'curah-hujan':        { col:'curah_hujan',        unit:'mm',  dec:1, min:0,   max:100  },
};

const REFRESH_MS = 10000;
const CIRCUMFERENCE = 56.5; // 2π×9

/* ── helpers ── */
const pct = (v, min, max) => v == null ? 0 : Math.min(100, Math.max(0, (v - min) / (max - min) * 100));
const avg = (arr) => arr.length ? (arr.reduce((a,b) => a+b, 0) / arr.length) : null;

/* ── update one card ── */
function updateCard(key, raw) {
    const { unit, dec } = FIELDS[key];
    const valEl = document.getElementById(`val-${key}`);
    const barEl = document.getElementById(`bar-${key}`);
    const card  = document.getElementById(`card-${key}`);

    const display = raw == null ? '—' : `${Number(raw).toFixed(dec)} <span class="card-unit">${unit}</span>`;
    if (valEl) valEl.innerHTML = display;
    if (barEl) barEl.style.width = pct(raw, FIELDS[key].min, FIELDS[key].max) + '%';

    // flash animation on update
    if (card && raw != null) {
        card.classList.remove('updated');
        void card.offsetWidth; // reflow
        card.classList.add('updated');
    }
}

/* ── update summary row ── */
function updateSummary(d) {
    const v = (col) => d[col];
    const fmtAvg = (vals, dec, unit) => {
        const nums = vals.filter(x => x != null);
        if (!nums.length) return '— ' + unit;
        return avg(nums).toFixed(dec) + ' ' + unit;
    };

    document.getElementById('sum-soil').textContent     = fmtAvg([v('kelembaban_tanah_1'),v('kelembaban_tanah_2'),v('kelembaban_tanah_3')], 1, '%');
    document.getElementById('sum-temp').textContent     = fmtAvg([v('suhu_udara_1'),v('suhu_udara_2'),v('suhu_udara_3')], 1, '°C');
    document.getElementById('sum-airhum').textContent   = fmtAvg([v('kelembaban_udara_1'),v('kelembaban_udara_2'),v('kelembaban_udara_3')], 1, '%');
    document.getElementById('sum-pressure').textContent = fmtAvg([v('tekanan_udara_1'),v('tekanan_udara_2'),v('tekanan_udara_3')], 1, 'hPa');
    document.getElementById('sum-rain').textContent     = v('curah_hujan') != null ? Number(v('curah_hujan')).toFixed(1) + ' mm' : '— mm';
}

/* ── main fetch ── */
async function loadData() {
    try {
        const res = await fetch('/api/esp32/readings');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const readings = await res.json();
        const latest = readings[0] ?? null;

        if (!latest) return;

        // update each card
        Object.keys(FIELDS).forEach(key => updateCard(key, latest[FIELDS[key].col]));

        // summary
        updateSummary(latest);

        // last time
        document.getElementById('last-time').textContent =
            new Date(latest.created_at).toLocaleString('id-ID', { dateStyle:'medium', timeStyle:'medium' });

    } catch (err) {
        console.error('Gagal memuat data sensor:', err);
    }
}

/* ══ COUNTDOWN RING ══ */
let countdownVal = 10;
const ringFill   = document.getElementById('ring-fill');
const countNum   = document.getElementById('countdown-num');

function resetCountdown() {
    countdownVal = 10;
    if (ringFill) ringFill.style.strokeDashoffset = '0';
    if (countNum) countNum.textContent = 10;
}

function tickCountdown() {
    countdownVal--;
    if (countdownVal < 0) countdownVal = 10;
    const offset = CIRCUMFERENCE * (1 - countdownVal / 10);
    if (ringFill) ringFill.style.strokeDashoffset = offset;
    if (countNum) countNum.textContent = countdownVal;
}

/* ══ INIT ══ */
loadData();
resetCountdown();

const refreshTimer = setInterval(() => {
    loadData();
    resetCountdown();
}, REFRESH_MS);

setInterval(tickCountdown, 1000);

document.getElementById('btn-refresh').addEventListener('click', () => {
    loadData();
    resetCountdown();
    clearInterval(refreshTimer);
    // restart auto-refresh
    setInterval(() => { loadData(); resetCountdown(); }, REFRESH_MS);
});
</script>

</body>
</html>
