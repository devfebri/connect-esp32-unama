<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Sensor – ESP32 UNAMA</title>
    <meta name="description" content="Dashboard monitoring real-time sensor lingkungan berbasis ESP32: kelembaban tanah, suhu udara, kelembaban udara, tekanan udara, dan curah hujan.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-deep:     #050b1a;
            --bg-card:     rgba(255, 255, 255, 0.04);
            --bg-card-hov: rgba(255, 255, 255, 0.08);
            --border:      rgba(255, 255, 255, 0.08);
            --border-glow: rgba(99, 179, 237, 0.35);

            --green:  #34d399;
            --blue:   #60a5fa;
            --purple: #a78bfa;
            --amber:  #fbbf24;
            --rose:   #f87171;
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
                radial-gradient(ellipse 80% 60% at 20% -10%, rgba(96, 165, 250, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 110%, rgba(167, 139, 250, 0.10) 0%, transparent 60%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* ── TOP NAV ── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 2rem;
            background: rgba(5, 11, 26, 0.75);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }
        .topbar-brand { display: flex; align-items: center; gap: .65rem; font-weight: 700; font-size: 1.1rem; letter-spacing: -.3px; }
        .topbar-brand .brand-dot {
            width: 10px; height: 10px; border-radius: 50%;
            background: var(--green);
            box-shadow: 0 0 10px var(--green);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot { 0%,100%{ box-shadow:0 0 6px var(--green); } 50%{ box-shadow:0 0 16px var(--green); } }
        .topbar-right { display: flex; align-items: center; gap: .75rem; }
        .status-badge {
            display: flex; align-items: center; gap: .4rem;
            font-size: .78rem; font-weight: 500;
            background: rgba(52, 211, 153, 0.12);
            color: var(--green);
            border: 1px solid rgba(52, 211, 153, 0.25);
            padding: .3rem .75rem; border-radius: 999px;
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: pulse-dot 2s infinite; }
        .btn-refresh {
            display: flex; align-items: center; gap: .4rem;
            font-size: .82rem; font-weight: 600;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff; border: none; cursor: pointer;
            padding: .4rem 1rem; border-radius: 8px;
            transition: opacity .2s, transform .15s;
        }
        .btn-refresh:hover { opacity: .88; transform: translateY(-1px); }
        .btn-refresh:active { transform: translateY(0); }

        /* ── HERO ── */
        .hero {
            text-align: center; padding: 3rem 1rem 2rem;
        }
        .hero-tag {
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: .75rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
            color: var(--blue); background: rgba(96, 165, 250, 0.1);
            border: 1px solid rgba(96, 165, 250, 0.2);
            padding: .3rem .9rem; border-radius: 999px; margin-bottom: 1rem;
        }
        .hero h1 { font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 800; letter-spacing: -.5px; line-height: 1.2; margin-bottom: .75rem; }
        .hero h1 span { background: linear-gradient(90deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { color: var(--text-secondary); font-size: 1rem; max-width: 520px; margin: 0 auto; }

        /* ── LAYOUT ── */
        .main { max-width: 1300px; margin: 0 auto; padding: 1.5rem 1.5rem 4rem; }

        /* ── SECTION LABEL ── */
        .sensor-section { margin-bottom: 2.5rem; }
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
        .section-title { font-size: .95rem; font-weight: 700; letter-spacing: -.2px; }
        .section-sub { font-size: .78rem; color: var(--text-secondary); margin-top: .15rem; }
        .badge-count {
            margin-left: auto; font-size: .72rem; font-weight: 600;
            padding: .2rem .65rem; border-radius: 999px;
        }

        /* ── SENSOR CARD ── */
        .cards-row { display: grid; gap: 1rem; }
        .cards-row.three-col { grid-template-columns: repeat(3, 1fr); }
        .cards-row.one-col   { grid-template-columns: 1fr; max-width: 340px; }

        .sensor-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.35rem 1.4rem;
            position: relative; overflow: hidden;
            transition: background .25s, border-color .25s, transform .2s, box-shadow .25s;
            cursor: default;
        }
        .sensor-card::before {
            content: ''; position: absolute; inset: 0; opacity: 0;
            border-radius: inherit;
            transition: opacity .3s;
        }
        .sensor-card:hover { background: var(--bg-card-hov); transform: translateY(-3px); }
        .sensor-card:hover::before { opacity: 1; }

        /* color accents per sensor type */
        .acc-green  .sensor-card { border-color: rgba(52,211,153,.18); }
        .acc-green  .sensor-card:hover { border-color: rgba(52,211,153,.4); box-shadow: 0 8px 32px rgba(52,211,153,.12); }
        .acc-blue   .sensor-card { border-color: rgba(96,165,250,.18); }
        .acc-blue   .sensor-card:hover { border-color: rgba(96,165,250,.4); box-shadow: 0 8px 32px rgba(96,165,250,.12); }
        .acc-purple .sensor-card { border-color: rgba(167,139,250,.18); }
        .acc-purple .sensor-card:hover { border-color: rgba(167,139,250,.4); box-shadow: 0 8px 32px rgba(167,139,250,.12); }
        .acc-amber  .sensor-card { border-color: rgba(251,191,36,.18); }
        .acc-amber  .sensor-card:hover { border-color: rgba(251,191,36,.4); box-shadow: 0 8px 32px rgba(251,191,36,.12); }
        .acc-cyan   .sensor-card { border-color: rgba(34,211,238,.18); }
        .acc-cyan   .sensor-card:hover { border-color: rgba(34,211,238,.4); box-shadow: 0 8px 32px rgba(34,211,238,.12); }

        .card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
        .card-device-label {
            font-size: .72rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase;
            color: var(--text-secondary);
        }
        .card-device-badge {
            font-size: .7rem; font-weight: 700; padding: .2rem .55rem;
            border-radius: 6px;
        }

        .card-value {
            font-size: 2rem; font-weight: 800; letter-spacing: -1px; line-height: 1;
            margin-bottom: .3rem;
        }
        .card-unit { font-size: .8rem; font-weight: 500; }
        .card-meta {
            margin-top: .8rem; padding-top: .8rem;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: .4rem;
            font-size: .73rem; color: var(--text-muted);
        }
        .card-meta i { font-size: .8rem; }

        /* progress bar */
        .card-bar { margin-top: .75rem; }
        .bar-track {
            height: 4px; border-radius: 999px;
            background: rgba(255,255,255,0.07);
            overflow: hidden;
        }
        .bar-fill {
            height: 100%; border-radius: 999px;
            transition: width .8s cubic-bezier(.4,0,.2,1);
        }

        /* loading skeleton shimmer */
        @keyframes shimmer {
            0%   { background-position: -600px 0; }
            100% { background-position:  600px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.09) 50%, rgba(255,255,255,0.04) 75%);
            background-size: 600px 100%;
            animation: shimmer 1.6s infinite linear;
            border-radius: 8px;
        }

        /* ── SUMMARY ROW ── */
        .summary-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem; margin-bottom: 2.5rem;
        }
        .summary-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 1.1rem;
            display: flex; align-items: center; gap: .9rem;
            transition: background .2s, transform .2s;
        }
        .summary-card:hover { background: var(--bg-card-hov); transform: translateY(-2px); }
        .summary-icon { font-size: 1.5rem; flex-shrink: 0; }
        .summary-label { font-size: .72rem; color: var(--text-secondary); font-weight: 500; }
        .summary-val { font-size: 1.05rem; font-weight: 700; }

        /* last updated */
        .last-updated {
            text-align: center; font-size: .78rem; color: var(--text-muted);
            margin-top: 2rem; padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }
        .last-updated span { color: var(--text-secondary); }

        /* responsive */
        @media (max-width: 900px) {
            .summary-row { grid-template-columns: repeat(3, 1fr); }
            .cards-row.three-col { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .topbar { padding: .75rem 1rem; }
            .topbar-brand span { display: none; }
            .summary-row { grid-template-columns: 1fr 1fr; }
            .hero { padding: 2rem .75rem 1.5rem; }
            .main { padding: 1rem 1rem 3rem; }
            .cards-row.one-col { max-width: 100%; }
        }
    </style>
</head>

<body>

    <!-- ── TOP NAV ── -->
    <nav class="topbar">
        <div class="topbar-brand">
            <div class="brand-dot"></div>
            <span>ESP32 Monitoring · UNAMA</span>
        </div>
        <div class="topbar-right">
            <div class="status-badge">
                <div class="status-dot"></div>
                Live
            </div>
            <a href="/monitor" style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;font-weight:600;color:var(--text-secondary);text-decoration:none;padding:.4rem .85rem;border-radius:8px;border:1px solid var(--border);transition:color .2s,background .2s;" onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-card)'" onmouseout="this.style.color='var(--text-secondary)';this.style.background='transparent'">
                <i class="bi bi-grid-3x2-gap"></i> Monitor
            </a>
            <a href="/report" style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;font-weight:600;color:var(--text-secondary);text-decoration:none;padding:.4rem .85rem;border-radius:8px;border:1px solid var(--border);transition:color .2s,background .2s;" onmouseover="this.style.color='var(--text-primary)';this.style.background='var(--bg-card)'" onmouseout="this.style.color='var(--text-secondary)';this.style.background='transparent'">
                <i class="bi bi-table"></i> Laporan
            </a>
            <button class="btn-refresh" id="btn-refresh">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </nav>

    <!-- ── HERO ── -->
    <header class="hero">
        <div class="hero-tag"><i class="bi bi-broadcast"></i> Real-time Monitoring</div>
        <h1>Dashboard <span>Sensor Lingkungan</span></h1>
        <p>Pantau 5 jenis sensor secara langsung dari 13 titik alat ukur yang tersebar di lapangan.</p>
    </header>

    <main class="main">

        <!-- ── SUMMARY ── -->
        <div class="summary-row" id="summary-row">
            <div class="summary-card">
                <div class="summary-icon" style="color:var(--green)">🌱</div>
                <div>
                    <div class="summary-label">Kelembaban Tanah</div>
                    <div class="summary-val" id="sum-soil">— %</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="color:var(--blue)">🌡️</div>
                <div>
                    <div class="summary-label">Suhu Udara</div>
                    <div class="summary-val" id="sum-temp">— °C</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="color:var(--purple)">💧</div>
                <div>
                    <div class="summary-label">Kelembaban Udara</div>
                    <div class="summary-val" id="sum-airhum">— %</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="color:var(--amber)">🌬️</div>
                <div>
                    <div class="summary-label">Tekanan Udara</div>
                    <div class="summary-val" id="sum-pressure">— hPa</div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon" style="color:var(--cyan)">🌧️</div>
                <div>
                    <div class="summary-label">Curah Hujan</div>
                    <div class="summary-val" id="sum-rain">— mm</div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════ -->
        <!-- SENSOR 1: KELEMBABAN TANAH (3 Alat) -->
        <!-- ══════════════════════════════════════ -->
        <section class="sensor-section acc-green">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(52,211,153,.12);color:var(--green)">
                    <i class="bi bi-moisture"></i>
                </div>
                <div>
                    <div class="section-title" style="color:var(--green)">Kelembaban Tanah</div>
                    <div class="section-sub">Persentase kadar air dalam tanah</div>
                </div>
                <span class="badge-count" style="background:rgba(52,211,153,.12);color:var(--green);border:1px solid rgba(52,211,153,.25)">3 Alat</span>
            </div>
            <div class="cards-row three-col" id="soil-cards">
                <!-- generated by JS -->
            </div>
        </section>

        <!-- ══════════════════════════════════════ -->
        <!-- SENSOR 2: SUHU UDARA (3 Alat) -->
        <!-- ══════════════════════════════════════ -->
        <section class="sensor-section acc-blue">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(96,165,250,.12);color:var(--blue)">
                    <i class="bi bi-thermometer-half"></i>
                </div>
                <div>
                    <div class="section-title" style="color:var(--blue)">Suhu Udara</div>
                    <div class="section-sub">Temperatur udara di sekitar alat sensor</div>
                </div>
                <span class="badge-count" style="background:rgba(96,165,250,.12);color:var(--blue);border:1px solid rgba(96,165,250,.25)">3 Alat</span>
            </div>
            <div class="cards-row three-col" id="temp-cards">
                <!-- generated by JS -->
            </div>
        </section>

        <!-- ══════════════════════════════════════ -->
        <!-- SENSOR 3: KELEMBABAN UDARA (3 Alat) -->
        <!-- ══════════════════════════════════════ -->
        <section class="sensor-section acc-purple">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(167,139,250,.12);color:var(--purple)">
                    <i class="bi bi-droplet-half"></i>
                </div>
                <div>
                    <div class="section-title" style="color:var(--purple)">Kelembaban Udara</div>
                    <div class="section-sub">Kadar uap air di atmosfer (RH)</div>
                </div>
                <span class="badge-count" style="background:rgba(167,139,250,.12);color:var(--purple);border:1px solid rgba(167,139,250,.25)">3 Alat</span>
            </div>
            <div class="cards-row three-col" id="airhum-cards">
                <!-- generated by JS -->
            </div>
        </section>

        <!-- ══════════════════════════════════════ -->
        <!-- SENSOR 4: TEKANAN UDARA (3 Alat) -->
        <!-- ══════════════════════════════════════ -->
        <section class="sensor-section acc-amber">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(251,191,36,.12);color:var(--amber)">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div>
                    <div class="section-title" style="color:var(--amber)">Tekanan Udara</div>
                    <div class="section-sub">Tekanan atmosfer dalam satuan hPa</div>
                </div>
                <span class="badge-count" style="background:rgba(251,191,36,.12);color:var(--amber);border:1px solid rgba(251,191,36,.25)">3 Alat</span>
            </div>
            <div class="cards-row three-col" id="pressure-cards">
                <!-- generated by JS -->
            </div>
        </section>

        <!-- ══════════════════════════════════════ -->
        <!-- SENSOR 5: CURAH HUJAN (1 Alat) -->
        <!-- ══════════════════════════════════════ -->
        <section class="sensor-section acc-cyan">
            <div class="section-header">
                <div class="section-icon" style="background:rgba(34,211,238,.12);color:var(--cyan)">
                    <i class="bi bi-cloud-rain-heavy"></i>
                </div>
                <div>
                    <div class="section-title" style="color:var(--cyan)">Curah Hujan</div>
                    <div class="section-sub">Volume air hujan yang terukur (mm)</div>
                </div>
                <span class="badge-count" style="background:rgba(34,211,238,.12);color:var(--cyan);border:1px solid rgba(34,211,238,.25)">1 Alat</span>
            </div>
            <div class="cards-row one-col" id="rain-cards">
                <!-- generated by JS -->
            </div>
        </section>

        <!-- LAST UPDATED -->
        <div class="last-updated">
            <i class="bi bi-clock"></i> Terakhir diperbarui: <span id="last-time">—</span> &nbsp;·&nbsp; Auto-refresh setiap 15 detik
        </div>

    </main>

    <script>
        /* ── CONFIG ── */
        const API_URL = '/esp32/readings';
        const REFRESH_MS = 15000;

        /* ── HELPERS ── */
        const fmt = (v, dec, unit) => v == null ? `— ${unit}` : `${Number(v).toFixed(dec)} ${unit}`;
        const pct = (v, min, max) => v == null ? 0 : Math.min(100, Math.max(0, ((v - min) / (max - min)) * 100));

        /* ── CARD BUILDER ── */
        function buildSensorCard({ label, index, valueFormatted, barPct, accentColor, barGrad, timestamp }) {
            const barW = barPct !== null ? barPct : 0;
            const showBar = barPct !== null;
            const timeStr = timestamp ? new Date(timestamp).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) : '—';

            return `
            <div class="sensor-card">
                <div class="card-top">
                    <div class="card-device-label">Alat #${index}</div>
                    <div class="card-device-badge" style="background:rgba(${hexToRgb(accentColor)},.12);color:${accentColor};">${label}</div>
                </div>
                <div class="card-value" style="color:${accentColor}">${valueFormatted}</div>
                ${showBar ? `
                <div class="card-bar">
                    <div class="bar-track">
                        <div class="bar-fill" style="width:${barW}%;background:${barGrad};"></div>
                    </div>
                </div>` : ''}
                <div class="card-meta">
                    <i class="bi bi-clock" style="color:${accentColor}"></i>
                    Diperbarui ${timeStr}
                </div>
            </div>`;
        }

        function hexToRgb(hex) {
            const m = hex.replace('#', '').match(/.{2}/g);
            if (!m) return '255,255,255';
            return m.map(x => parseInt(x, 16)).join(',');
        }

        /* ── SENSOR DEFINITIONS ── */
        const SENSORS = [
            {
                id: 'soil-cards',
                key: 'soil_moisture',
                label: 'Kelembaban Tanah',
                count: 3,
                accentColor: '#34d399',
                barGrad: 'linear-gradient(90deg,#059669,#34d399)',
                barMin: 0, barMax: 100,
                format: v => fmt(v, 0, '%'),
                summaryId: 'sum-soil',
            },
            {
                id: 'temp-cards',
                key: 'temperature',
                label: 'Suhu Udara',
                count: 3,
                accentColor: '#60a5fa',
                barGrad: 'linear-gradient(90deg,#1d4ed8,#60a5fa)',
                barMin: 0, barMax: 50,
                format: v => fmt(v, 1, '°C'),
                summaryId: 'sum-temp',
            },
            {
                id: 'airhum-cards',
                key: 'air_humidity',
                label: 'Kelembaban Udara',
                count: 3,
                accentColor: '#a78bfa',
                barGrad: 'linear-gradient(90deg,#6d28d9,#a78bfa)',
                barMin: 0, barMax: 100,
                format: v => fmt(v, 1, '%'),
                summaryId: 'sum-airhum',
            },
            {
                id: 'pressure-cards',
                key: 'air_pressure',
                label: 'Tekanan Udara',
                count: 3,
                accentColor: '#fbbf24',
                barGrad: 'linear-gradient(90deg,#b45309,#fbbf24)',
                barMin: 900, barMax: 1100,
                format: v => fmt(v, 1, 'hPa'),
                summaryId: 'sum-pressure',
            },
            {
                id: 'rain-cards',
                key: 'rainfall',
                label: 'Curah Hujan',
                count: 1,
                accentColor: '#22d3ee',
                barGrad: 'linear-gradient(90deg,#0e7490,#22d3ee)',
                barMin: 0, barMax: 100,
                format: v => fmt(v, 1, 'mm'),
                summaryId: 'sum-rain',
            },
        ];

        /* ── RENDER ── */
        function renderSensors(latest) {
            const ts = latest?.created_at ?? null;

            SENSORS.forEach(sensor => {
                const container = document.getElementById(sensor.id);
                const rawVal = latest ? latest[sensor.key] : null;
                const barPct = (sensor.barMin !== undefined) ? pct(rawVal, sensor.barMin, sensor.barMax) : null;
                const formatted = sensor.format(rawVal);

                let cards = '';
                for (let i = 1; i <= sensor.count; i++) {
                    cards += buildSensorCard({
                        label: sensor.label,
                        index: i,
                        valueFormatted: formatted,
                        barPct: barPct,
                        accentColor: sensor.accentColor,
                        barGrad: sensor.barGrad,
                        timestamp: ts,
                    });
                }
                container.innerHTML = cards;

                // update summary
                const sumEl = document.getElementById(sensor.summaryId);
                if (sumEl) sumEl.textContent = formatted;
            });

            // update last updated time
            const ltEl = document.getElementById('last-time');
            if (ltEl) {
                ltEl.textContent = ts
                    ? new Date(ts).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' })
                    : new Date().toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' });
            }
        }

        /* ── SKELETON LOADING ── */
        function renderSkeletons() {
            SENSORS.forEach(sensor => {
                const container = document.getElementById(sensor.id);
                let skeletons = '';
                for (let i = 1; i <= sensor.count; i++) {
                    skeletons += `
                    <div class="sensor-card">
                        <div class="card-top">
                            <div class="card-device-label">Alat #${i}</div>
                        </div>
                        <div class="skeleton" style="height:36px;width:70%;margin-bottom:.5rem"></div>
                        <div class="card-bar">
                            <div class="bar-track"><div class="bar-fill skeleton" style="width:60%"></div></div>
                        </div>
                        <div class="card-meta skeleton" style="height:14px;width:50%;margin-top:.8rem"></div>
                    </div>`;
                }
                container.innerHTML = skeletons;
            });
        }

        /* ── FETCH ── */
        async function loadData() {
            try {
                const res = await fetch(API_URL);
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const readings = await res.json();
                const latest = readings[0] ?? null;
                renderSensors(latest);
            } catch (err) {
                console.error('Gagal memuat data sensor:', err);
                // render dash values on error
                renderSensors(null);
            }
        }

        /* ── INIT ── */
        renderSkeletons();
        loadData();
        setInterval(loadData, REFRESH_MS);
        document.getElementById('btn-refresh').addEventListener('click', () => {
            renderSkeletons();
            loadData();
        });
    </script>

</body>
</html>
