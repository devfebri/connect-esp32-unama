<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Sensor – ESP32 UNAMA</title>
    <meta name="description"
        content="Laporan lengkap seluruh data sensor ESP32: kelembaban tanah, suhu udara, kelembaban udara, tekanan udara, dan curah hujan.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-deep: #050b1a;
            --bg-surface: #0c1526;
            --bg-card: rgba(255, 255, 255, 0.04);
            --bg-card-hov: rgba(255, 255, 255, 0.07);
            --border: rgba(255, 255, 255, 0.08);

            --green: #34d399;
            --blue: #60a5fa;
            --purple: #a78bfa;
            --amber: #fbbf24;
            --cyan: #22d3ee;
            --rose: #f87171;

            --text-primary: #f0f6ff;
            --text-secondary: #94a3b8;
            --text-muted: #475569;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-deep);
            background-image:
                radial-gradient(ellipse 70% 50% at 10% -5%, rgba(96, 165, 250, .10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 105%, rgba(167, 139, 250, .08) 0%, transparent 60%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* ── TOPBAR ── */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
            padding: .9rem 2rem;
            background: rgba(5, 11, 26, 0.80);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--border);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            font-size: 1rem;
        }

        .brand-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--blue);
            box-shadow: 0 0 10px var(--blue);
            animation: blink 2s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        .nav-links {
            display: flex;
            gap: .5rem;
        }

        .nav-link {
            font-size: .8rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            padding: .35rem .75rem;
            border-radius: 8px;
            border: 1px solid transparent;
            transition: color .2s, background .2s, border-color .2s;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background: var(--bg-card);
            border-color: var(--border);
        }

        .nav-link.active {
            color: var(--blue);
            background: rgba(96, 165, 250, .1);
            border-color: rgba(96, 165, 250, .25);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: .65rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .8rem;
            font-weight: 600;
            padding: .4rem 1rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
        }

        .btn:hover {
            opacity: .85;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: #fff;
        }

        .btn-outline {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .btn-outline:hover {
            color: var(--text-primary);
            background: var(--bg-card);
        }

        .btn-success {
            background: linear-gradient(135deg, #059669, #34d399);
            color: #fff;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem 1.25rem;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title-tag {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--blue);
            margin-bottom: .5rem;
        }

        .page-title {
            font-size: 1.7rem;
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .page-title span {
            background: linear-gradient(90deg, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-sub {
            color: var(--text-secondary);
            font-size: .88rem;
            margin-top: .3rem;
        }

        /* ── STATS STRIP ── */
        .stats-strip {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem 1.25rem;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: .85rem;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: .9rem 1.1rem;
            display: flex;
            align-items: center;
            gap: .8rem;
        }

        .stat-icon {
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-label {
            font-size: .7rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .stat-val {
            font-size: 1rem;
            font-weight: 700;
        }

        /* ── FILTER BAR ── */
        .filter-wrap {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem 1.25rem;
        }

        .filter-bar {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .75rem;
        }

        .filter-label {
            font-size: .78rem;
            font-weight: 600;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .filter-input {
            font-family: inherit;
            font-size: .8rem;
            font-weight: 500;
            background: rgba(255, 255, 255, .05);
            color: var(--text-primary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .38rem .75rem;
            outline: none;
            transition: border-color .2s;
        }

        .filter-input:focus {
            border-color: rgba(96, 165, 250, .5);
        }

        .filter-input::placeholder {
            color: var(--text-muted);
        }

        .filter-sep {
            color: var(--text-muted);
            font-size: .8rem;
        }

        .filter-select {
            font-family: inherit;
            font-size: .8rem;
            font-weight: 500;
            background: rgba(255, 255, 255, .05);
            color: var(--text-primary);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: .38rem .65rem;
            outline: none;
            cursor: pointer;
        }

        .filter-select option {
            background: #0c1526;
        }

        .filter-actions {
            display: flex;
            gap: .5rem;
            margin-left: auto;
        }

        /* ── TABLE WRAPPER ── */
        .table-wrap {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem 2rem;
        }

        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .table-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .table-info {
            font-size: .8rem;
            color: var(--text-secondary);
        }

        .table-info strong {
            color: var(--text-primary);
        }

        .table-actions {
            display: flex;
            gap: .5rem;
        }

        .tbl-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: var(--text-secondary);
            padding: .85rem 1rem;
            background: rgba(255, 255, 255, .025);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            text-align: left;
            cursor: pointer;
            user-select: none;
            transition: color .2s;
        }

        thead th:hover {
            color: var(--text-primary);
        }

        thead th .sort-icon {
            margin-left: .3rem;
            opacity: .4;
            transition: opacity .2s;
        }

        thead th.sort-asc .sort-icon,
        thead th.sort-desc .sort-icon {
            opacity: 1;
            color: var(--blue);
        }

        thead th.sort-desc .sort-icon {
            transform: rotate(180deg);
            display: inline-block;
        }

        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: var(--bg-card-hov);
        }

        tbody td {
            padding: .75rem 1rem;
            font-size: .82rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        .td-no {
            color: var(--text-muted);
            font-size: .75rem;
            font-weight: 600;
        }

        .td-time {
            color: var(--text-secondary);
        }

        .td-time .date {
            font-size: .75rem;
            color: var(--text-muted);
        }

        /* value badges */
        .val-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-weight: 600;
            font-size: .82rem;
            padding: .2rem .6rem;
            border-radius: 6px;
        }

        .val-null {
            color: var(--text-muted);
            font-style: italic;
        }

        .val-green {
            background: rgba(52, 211, 153, .1);
            color: var(--green);
        }

        .val-blue {
            background: rgba(96, 165, 250, .1);
            color: var(--blue);
        }

        .val-purple {
            background: rgba(167, 139, 250, .1);
            color: var(--purple);
        }

        .val-amber {
            background: rgba(251, 191, 36, .1);
            color: var(--amber);
        }

        .val-cyan {
            background: rgba(34, 211, 238, .1);
            color: var(--cyan);
        }

        .val-rose {
            background: rgba(248, 113, 113, .1);
            color: var(--rose);
        }

        /* ── PAGINATION ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }

        .page-meta {
            font-size: .78rem;
            color: var(--text-secondary);
        }

        .page-meta strong {
            color: var(--text-primary);
        }

        .page-btns {
            display: flex;
            gap: .35rem;
        }

        .page-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-secondary);
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, color .2s, border-color .2s;
        }

        .page-btn:hover:not(:disabled) {
            background: var(--bg-card-hov);
            color: var(--text-primary);
        }

        .page-btn.active {
            background: rgba(96, 165, 250, .15);
            color: var(--blue);
            border-color: rgba(96, 165, 250, .3);
        }

        .page-btn:disabled {
            opacity: .3;
            cursor: default;
        }

        /* ── EMPTY / ERROR STATE ── */
        .state-box {
            text-align: center;
            padding: 4rem 1rem;
        }

        .state-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: .4;
        }

        .state-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .state-sub {
            font-size: .82rem;
            color: var(--text-muted);
            margin-top: .35rem;
        }

        /* ── LOADING ROW ── */
        @keyframes shimmer {
            0% {
                background-position: -600px 0
            }

            100% {
                background-position: 600px 0
            }
        }

        .shimmer {
            background: linear-gradient(90deg, rgba(255, 255, 255, .03) 25%, rgba(255, 255, 255, .08) 50%, rgba(255, 255, 255, .03) 75%);
            background-size: 600px 100%;
            animation: shimmer 1.4s infinite linear;
            border-radius: 6px;
            height: 14px;
        }

        /* ── TOAST ── */
        .toast {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 999;
            background: rgba(14, 22, 42, .95);
            border: 1px solid var(--border);
            backdrop-filter: blur(12px);
            padding: .75rem 1.2rem;
            border-radius: 12px;
            font-size: .82rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .4);
            transform: translateY(80px);
            opacity: 0;
            transition: transform .35s cubic-bezier(.34, 1.56, .64, 1), opacity .3s;
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* responsive */
        @media(max-width:900px) {
            .stats-strip {
                grid-template-columns: repeat(3, 1fr);
            }

            .topbar {
                padding: .75rem 1rem;
            }
        }

        @media(max-width:600px) {
            .stats-strip {
                grid-template-columns: 1fr 1fr;
            }

            .page-header,
            .filter-wrap,
            .table-wrap,
            .stats-strip {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .nav-links {
                display: none;
            }
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
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <a href="{{ route('sensor.report') }}" class="nav-link active">
                    <i class="bi bi-table"></i> Laporan
                </a>
            </div>
        </div>
        <div class="topbar-right">
            <button class="btn btn-outline" id="btn-refresh">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
            <button class="btn btn-success" id="btn-export-csv">
                <i class="bi bi-download"></i> Export CSV
            </button>
        </div>
    </nav>

    <!-- ── PAGE HEADER ── -->
    <div class="page-header">
        <div>
            <div class="page-title-tag"><i class="bi bi-table"></i> &nbsp;Laporan Keseluruhan</div>
            <h1 class="page-title">Data <span>Sensor Lengkap</span></h1>
            <p class="page-sub">Seluruh riwayat pembacaan dari 13 titik alat ukur ESP32</p>
        </div>
    </div>

    <!-- ── STATS STRIP ── -->
    <div class="stats-strip" id="stats-strip">
        <div class="stat-card">
            <div class="stat-icon" style="color:var(--blue)">📊</div>
            <div>
                <div class="stat-label">Total Data</div>
                <div class="stat-val" id="st-total">—</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:var(--green)">🌱</div>
            <div>
                <div class="stat-label">Rata² Kel. Tanah</div>
                <div class="stat-val" id="st-soil">— %</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:var(--blue)">🌡️</div>
            <div>
                <div class="stat-label">Rata² Suhu Udara</div>
                <div class="stat-val" id="st-temp">— °C</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:var(--purple)">💧</div>
            <div>
                <div class="stat-label">Rata² Kel. Udara</div>
                <div class="stat-val" id="st-airhum">— %</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color:var(--cyan)">🌧️</div>
            <div>
                <div class="stat-label">Total Curah Hujan</div>
                <div class="stat-val" id="st-rain">— mm</div>
            </div>
        </div>
    </div>

    <!-- ── FILTER BAR ── -->
    <div class="filter-wrap">
        <div class="filter-bar">
            <span class="filter-label"><i class="bi bi-funnel"></i> Filter:</span>

            <div class="filter-group">
                <label class="filter-label" for="f-date-from">Dari</label>
                <input type="date" id="f-date-from" class="filter-input">
                <span class="filter-sep">–</span>
                <label class="filter-label" for="f-date-to">Sampai</label>
                <input type="date" id="f-date-to" class="filter-input">
            </div>

            <div class="filter-group">
                <label class="filter-label" for="f-per-page">Tampilkan</label>
                <select id="f-per-page" class="filter-select">
                    <option value="25">25 baris</option>
                    <option value="50">50 baris</option>
                    <option value="100">100 baris</option>
                </select>
            </div>

            <div class="filter-actions">
                <button class="btn btn-primary" id="btn-filter">
                    <i class="bi bi-search"></i> Terapkan
                </button>
                <button class="btn btn-outline" id="btn-reset">
                    <i class="bi bi-x-circle"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- ── TABLE ── -->
    <div class="table-wrap">
        <div class="table-card">

            <div class="table-toolbar">
                <div class="table-info" id="tbl-info">Memuat data…</div>
                <div class="table-actions">
                    <button class="btn btn-outline" id="btn-prev" disabled>
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <span id="tbl-page-label"
                        style="font-size:.8rem;color:var(--text-secondary);padding:0 .5rem;display:flex;align-items:center;">—</span>
                    <button class="btn btn-outline" id="btn-next" disabled>
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="tbl-scroll">
                <table id="report-table">
                    <thead>
                        <tr>
                            <th class="td-no" data-sort="" rowspan="2">#</th>
                            <th data-sort="waktu_pembacaan" class="sort-desc" rowspan="2">Waktu <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th colspan="3" style="color:var(--green);text-align:center"><i
                                    class="bi bi-moisture"></i> Kelembaban Tanah (%)</th>
                            <th colspan="3" style="color:var(--blue);text-align:center"><i
                                    class="bi bi-thermometer-half"></i> Suhu Udara (°C)</th>
                            <th colspan="3" style="color:var(--purple);text-align:center"><i
                                    class="bi bi-droplet-half"></i> Kelembaban Udara (%)</th>
                            <th colspan="3" style="color:var(--amber);text-align:center"><i
                                    class="bi bi-speedometer2"></i> Tekanan Udara (hPa)</th>
                            <th rowspan="2" style="color:var(--cyan)"><i class="bi bi-cloud-rain-heavy"></i> Curah
                                Hujan<br><small>(mm)</small></th>
                        </tr>
                        <tr>
                            <th data-sort="kelembaban_tanah_1" style="color:var(--green)">Alat 1 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="kelembaban_tanah_2" style="color:var(--green)">Alat 2 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="kelembaban_tanah_3" style="color:var(--green)">Alat 3 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="suhu_udara_1" style="color:var(--blue)">Alat 1 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="suhu_udara_2" style="color:var(--blue)">Alat 2 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="suhu_udara_3" style="color:var(--blue)">Alat 3 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="kelembaban_udara_1" style="color:var(--purple)">Alat 1 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="kelembaban_udara_2" style="color:var(--purple)">Alat 2 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="kelembaban_udara_3" style="color:var(--purple)">Alat 3 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="tekanan_udara_1" style="color:var(--amber)">Alat 1 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="tekanan_udara_2" style="color:var(--amber)">Alat 2 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                            <th data-sort="tekanan_udara_3" style="color:var(--amber)">Alat 3 <i
                                    class="bi bi-arrow-down sort-icon"></i></th>
                        </tr>
                    </thead>
                    <tbody id="report-body">
                        <tr>
                            <td colspan="15">
                                <div class="state-box">
                                    <div class="state-icon">⏳</div>
                                    <div class="state-title">Memuat data…</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap" id="pagination-wrap" style="display:none">
                <div class="page-meta" id="page-meta">—</div>
                <div class="page-btns" id="page-btns"></div>
            </div>

        </div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        /* ════════════════════════════════
               STATE
            ════════════════════════════════ */
        let state = {
            page: 1,
            perPage: 25,
            dateFrom: '',
            dateTo: '',
            sortCol: 'waktu_pembacaan',
            sortDir: 'desc',
            meta: null,
            allRows: [], // holds current page rows for CSV
            allForCsv: [], // holds ALL fetched rows for export
        };

        /* ════════════════════════════════
           API
        ════════════════════════════════ */
        async function fetchData() {
            const params = new URLSearchParams({
                page: state.page,
                per_page: state.perPage,
            });
            if (state.dateFrom) params.set('date_from', state.dateFrom);
            if (state.dateTo) params.set('date_to', state.dateTo);

            const res = await fetch(`/api/esp32/readings/all?${params}`);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        }

        /* ════════════════════════════════
           FORMAT HELPERS
        ════════════════════════════════ */
        const f = (v, dec, unit, cls) =>
            v == null ?
            `<span class="val-null">—</span>` :
            `<span class="val-badge ${cls}">${Number(v).toFixed(dec)} ${unit}</span>`;

        function formatDatetime(str) {
            if (!str) return '—';
            const d = new Date(str);
            const date = d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            const time = d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            return `<div style="font-weight:600">${time}</div><div class="date">${date}</div>`;
        }

        /* ════════════════════════════════
           RENDER TABLE
        ════════════════════════════════ */
        function renderSkeletons(n = 8) {
            const body = document.getElementById('report-body');
            let rows = '';
            for (let i = 0; i < n; i++) {
                rows += `<tr>` + Array(15).fill(`<td><div class="shimmer"></div></td>`).join('') + `</tr>`;
            }
            body.innerHTML = rows;
        }

        function renderTable(data) {
            const body = document.getElementById('report-body');
            const {
                data: rows,
                meta
            } = data;
            state.meta = meta;
            state.allRows = rows;

            if (!rows.length) {
                body.innerHTML = `<tr><td colspan="15"><div class="state-box">
                    <div class="state-icon">📭</div>
                    <div class="state-title">Tidak ada data ditemukan</div>
                    <div class="state-sub">Coba ubah filter tanggal atau reset filter</div>
                </div></td></tr>`;
                updatePagination(meta);
                updateInfo(meta);
                return;
            }

            // sort client-side within this page
            const sorted = [...rows].sort((a, b) => {
                let va = a[state.sortCol],
                    vb = b[state.sortCol];
                if (va == null) va = state.sortDir === 'asc' ? Infinity : -Infinity;
                if (vb == null) vb = state.sortDir === 'asc' ? Infinity : -Infinity;
                if (typeof va === 'string') return state.sortDir === 'asc' ? va.localeCompare(vb) : vb
                    .localeCompare(va);
                return state.sortDir === 'asc' ? va - vb : vb - va;
            });

            const offset = (meta.current_page - 1) * meta.per_page;
            body.innerHTML = sorted.map((r, i) => `
                <tr>
                    <td class="td-no">${offset + i + 1}</td>
                    <td class="td-time">${formatDatetime(r.waktu_pembacaan)}</td>
                    <td>${f(r.kelembaban_tanah_1, 1, '%',   'val-green')}</td>
                    <td>${f(r.kelembaban_tanah_2, 1, '%',   'val-green')}</td>
                    <td>${f(r.kelembaban_tanah_3, 1, '%',   'val-green')}</td>
                    <td>${f(r.suhu_udara_1,       1, '°C',  'val-blue')}</td>
                    <td>${f(r.suhu_udara_2,       1, '°C',  'val-blue')}</td>
                    <td>${f(r.suhu_udara_3,       1, '°C',  'val-blue')}</td>
                    <td>${f(r.kelembaban_udara_1, 1, '%',   'val-purple')}</td>
                    <td>${f(r.kelembaban_udara_2, 1, '%',   'val-purple')}</td>
                    <td>${f(r.kelembaban_udara_3, 1, '%',   'val-purple')}</td>
                    <td>${f(r.tekanan_udara_1,    1, 'hPa', 'val-amber')}</td>
                    <td>${f(r.tekanan_udara_2,    1, 'hPa', 'val-amber')}</td>
                    <td>${f(r.tekanan_udara_3,    1, 'hPa', 'val-amber')}</td>
                    <td>${f(r.curah_hujan,        1, 'mm',  'val-cyan')}</td>
                </tr>
            `).join('');

            updatePagination(meta);
            updateInfo(meta);
            updateStats(rows);
        }

        /* ════════════════════════════════
           STATS
        ════════════════════════════════ */
        function updateStats(rows) {
            const avgKeys = (keys) => {
                const vals = rows.flatMap(r => keys.map(k => r[k])).filter(v => v != null);
                if (!vals.length) return null;
                return vals.reduce((a, b) => a + b, 0) / vals.length;
            };
            const sumKey = (key) => rows.map(r => r[key]).filter(v => v != null).reduce((a, b) => a + b, 0);

            document.getElementById('st-total').textContent = state.meta?.total ?? rows.length;
            const avgSoil = avgKeys(['kelembaban_tanah_1', 'kelembaban_tanah_2', 'kelembaban_tanah_3']);
            const avgTemp = avgKeys(['suhu_udara_1', 'suhu_udara_2', 'suhu_udara_3']);
            const avgHum = avgKeys(['kelembaban_udara_1', 'kelembaban_udara_2', 'kelembaban_udara_3']);
            document.getElementById('st-soil').textContent = avgSoil != null ? avgSoil.toFixed(1) + ' %' : '— %';
            document.getElementById('st-temp').textContent = avgTemp != null ? avgTemp.toFixed(1) + ' °C' : '— °C';
            document.getElementById('st-airhum').textContent = avgHum != null ? avgHum.toFixed(1) + ' %' : '— %';
            document.getElementById('st-rain').textContent = sumKey('curah_hujan') > 0 ? sumKey('curah_hujan').toFixed(1) +
                ' mm' : '— mm';
        }

        /* ════════════════════════════════
           PAGINATION
        ════════════════════════════════ */
        function updatePagination(meta) {
            if (!meta) return;
            const wrap = document.getElementById('pagination-wrap');
            wrap.style.display = meta.last_page > 1 ? 'flex' : 'none';

            const btnsPrev = document.getElementById('btn-prev');
            const btnsNext = document.getElementById('btn-next');
            btnsPrev.disabled = meta.current_page <= 1;
            btnsNext.disabled = meta.current_page >= meta.last_page;

            document.getElementById('tbl-page-label').textContent =
                `Hal. ${meta.current_page} / ${meta.last_page}`;

            // page number buttons (show up to 7)
            const btns = document.getElementById('page-btns');
            const cur = meta.current_page;
            const last = meta.last_page;
            const pages = [];

            if (last <= 7) {
                for (let i = 1; i <= last; i++) pages.push(i);
            } else {
                pages.push(1);
                if (cur > 3) pages.push('…');
                for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i);
                if (cur < last - 2) pages.push('…');
                pages.push(last);
            }

            btns.innerHTML = pages.map(p =>
                p === '…' ?
                `<span class="page-btn" style="cursor:default">…</span>` :
                `<button class="page-btn${p === cur ? ' active' : ''}" data-pg="${p}">${p}</button>`
            ).join('');

            btns.querySelectorAll('[data-pg]').forEach(btn => {
                btn.addEventListener('click', () => {
                    state.page = +btn.dataset.pg;
                    load();
                });
            });
        }

        function updateInfo(meta) {
            const el = document.getElementById('tbl-info');
            if (!meta) {
                el.textContent = '—';
                return;
            }
            const from = (meta.current_page - 1) * meta.per_page + 1;
            const to = Math.min(from + meta.per_page - 1, meta.total);
            el.innerHTML = `Menampilkan <strong>${from}–${to}</strong> dari <strong>${meta.total}</strong> data`;
        }

        /* ════════════════════════════════
           SORT
        ════════════════════════════════ */
        document.querySelectorAll('thead th[data-sort]').forEach(th => {
            th.addEventListener('click', () => {
                const col = th.dataset.sort;
                if (!col) return;
                if (state.sortCol === col) {
                    state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    state.sortCol = col;
                    state.sortDir = 'desc';
                }
                document.querySelectorAll('thead th').forEach(t => {
                    t.classList.remove('sort-asc', 'sort-desc');
                });
                th.classList.add(state.sortDir === 'asc' ? 'sort-asc' : 'sort-desc');
                // re-render with same data but re-sorted
                if (state.allRows.length) {
                    renderTable({
                        data: state.allRows,
                        meta: state.meta
                    });
                }
            });
        });

        /* ════════════════════════════════
           MAIN LOAD
        ════════════════════════════════ */
        async function load() {
            renderSkeletons();
            try {
                const data = await fetchData();
                renderTable(data);
            } catch (err) {
                console.error(err);
                document.getElementById('report-body').innerHTML = `
                    <tr><td colspan="15"><div class="state-box">
                        <div class="state-icon">⚠️</div>
                        <div class="state-title">Gagal memuat data</div>
                        <div class="state-sub">Periksa koneksi server dan coba lagi</div>
                    </div></td></tr>`;
                document.getElementById('tbl-info').textContent = 'Error memuat data';
            }
        }

        /* ════════════════════════════════
           CSV EXPORT (all data)
        ════════════════════════════════ */
        async function exportCsv() {
            showToast('⏳ Mengambil semua data untuk export…');
            try {
                // fetch ALL data without pagination (max 100 per call, loop)
                let allRows = [];
                let page = 1;
                let lastPage = 1;
                do {
                    const params = new URLSearchParams({
                        page,
                        per_page: 100
                    });
                    if (state.dateFrom) params.set('date_from', state.dateFrom);
                    if (state.dateTo) params.set('date_to', state.dateTo);
                    const res = await fetch(`/api/esp32/readings/all?${params}`);
                    const json = await res.json();
                    allRows = allRows.concat(json.data);
                    lastPage = json.meta.last_page;
                    page++;
                } while (page <= lastPage);

                const headers = ['No', 'Waktu', 'Kel.Tanah-1 (%)', 'Kel.Tanah-2 (%)', 'Kel.Tanah-3 (%)',
                    'Suhu Udara-1 (°C)', 'Suhu Udara-2 (°C)', 'Suhu Udara-3 (°C)', 'Kel.Udara-1 (%)',
                    'Kel.Udara-2 (%)', 'Kel.Udara-3 (%)', 'Tek.Udara-1 (hPa)', 'Tek.Udara-2 (hPa)',
                    'Tek.Udara-3 (hPa)', 'Curah Hujan (mm)'
                ];
                const rows = allRows.map((r, i) => [
                    i + 1,
                    r.waktu_pembacaan ? new Date(r.waktu_pembacaan).toLocaleString('id-ID') : '',
                    r.kelembaban_tanah_1 != null ? Number(r.kelembaban_tanah_1).toFixed(1) : '',
                    r.kelembaban_tanah_2 != null ? Number(r.kelembaban_tanah_2).toFixed(1) : '',
                    r.kelembaban_tanah_3 != null ? Number(r.kelembaban_tanah_3).toFixed(1) : '',
                    r.suhu_udara_1 != null ? Number(r.suhu_udara_1).toFixed(1) : '',
                    r.suhu_udara_2 != null ? Number(r.suhu_udara_2).toFixed(1) : '',
                    r.suhu_udara_3 != null ? Number(r.suhu_udara_3).toFixed(1) : '',
                    r.kelembaban_udara_1 != null ? Number(r.kelembaban_udara_1).toFixed(1) : '',
                    r.kelembaban_udara_2 != null ? Number(r.kelembaban_udara_2).toFixed(1) : '',
                    r.kelembaban_udara_3 != null ? Number(r.kelembaban_udara_3).toFixed(1) : '',
                    r.tekanan_udara_1 != null ? Number(r.tekanan_udara_1).toFixed(1) : '',
                    r.tekanan_udara_2 != null ? Number(r.tekanan_udara_2).toFixed(1) : '',
                    r.tekanan_udara_3 != null ? Number(r.tekanan_udara_3).toFixed(1) : '',
                    r.curah_hujan != null ? Number(r.curah_hujan).toFixed(1) : '',
                ].join(','));

                const csv = [headers.join(','), ...rows].join('\n');
                const blob = new Blob(['\uFEFF' + csv], {
                    type: 'text/csv;charset=utf-8;'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `sensor_report_${new Date().toISOString().slice(0,10)}.csv`;
                a.click();
                URL.revokeObjectURL(url);
                showToast('✅ Export berhasil! ' + allRows.length + ' baris');
            } catch (e) {
                showToast('❌ Gagal export CSV');
            }
        }

        /* ════════════════════════════════
           TOAST
        ════════════════════════════════ */
        let toastTimer;

        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => t.classList.remove('show'), 3500);
        }

        /* ════════════════════════════════
           EVENT LISTENERS
        ════════════════════════════════ */
        document.getElementById('btn-filter').addEventListener('click', () => {
            state.dateFrom = document.getElementById('f-date-from').value;
            state.dateTo = document.getElementById('f-date-to').value;
            state.perPage = +document.getElementById('f-per-page').value;
            state.page = 1;
            load();
        });

        document.getElementById('btn-reset').addEventListener('click', () => {
            document.getElementById('f-date-from').value = '';
            document.getElementById('f-date-to').value = '';
            document.getElementById('f-per-page').value = '25';
            state.dateFrom = '';
            state.dateTo = '';
            state.perPage = 25;
            state.page = 1;
            load();
        });

        document.getElementById('btn-refresh').addEventListener('click', load);
        document.getElementById('btn-export-csv').addEventListener('click', exportCsv);

        document.getElementById('btn-prev').addEventListener('click', () => {
            if (state.page > 1) {
                state.page--;
                load();
            }
        });
        document.getElementById('btn-next').addEventListener('click', () => {
            if (state.meta && state.page < state.meta.last_page) {
                state.page++;
                load();
            }
        });

        /* ════════════════════════════════
           INIT
        ════════════════════════════════ */
        load();
    </script>

</body>

</html>
