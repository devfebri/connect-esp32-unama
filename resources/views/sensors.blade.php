<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Monitor Sensor ESP32</title>

    <link rel="stylesheet" href="{{ asset('template/css/bootstrap.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-ZbnZ6k4kIQ+mgGZkA2vBlkWIcT0I6DXvl3W7jZiYCedVncxwdSXpK/5fBxh3FhFT" crossorigin="anonymous">


</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
            <div>
                <p class="text-uppercase text-muted small mb-1">ESP32 Monitoring</p>
                <h1 class="h2 mb-2">Dashboard Sensor</h1>
                <p class="text-muted mb-0">Pantau data suhu, kelembaban tanah, kelembaban udara, serta tingkat baterai
                    secara real-time.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="/" class="btn btn-outline-secondary btn-sm">Kembali ke Beranda</a>
                <button id="refresh-button" class="btn btn-primary btn-sm">Segarkan</button>
            </div>
        </div>

        <div class="row gy-3">
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted mb-3">Suhu Udara</h6>
                        <h2 id="sensor-temperature" class="card-title display-6">— °C</h2>
                        <p class="text-muted mb-0">Data terbaru dari sensor suhu.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted mb-3">Kelembaban Tanah</h6>
                        <h2 id="sensor-soil-moisture" class="card-title display-6">— %</h2>
                        <p class="text-muted mb-0">Nilai kelembaban tanah dari probe.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted mb-3">Suhu Tanah</h6>
                        <h2 id="sensor-soil-temperature" class="card-title display-6">— °C</h2>
                        <p class="text-muted mb-0">Suhu pada permukaan tanah.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted mb-3">Kelembaban Udara</h6>
                        <h2 id="sensor-air-humidity" class="card-title display-6">— %</h2>
                        <p class="text-muted mb-0">Kelembaban udara di sekitar lokasi.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row gy-4 mt-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div
                        class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <div>
                            <p class="text-uppercase text-muted small mb-1">Riwayat Pengukuran</p>
                            <h2 class="h5 mb-0">10 data terakhir</h2>
                        </div>
                        <span class="badge bg-info text-dark py-2">Update otomatis setiap 15 detik</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Waktu</th>
                                    <th scope="col">Suhu Udara</th>
                                    <th scope="col">Kelembaban Tanah</th>
                                    <th scope="col">Suhu Tanah</th>
                                    <th scope="col">Kelembaban Udara</th>
                                    <th scope="col">Baterai</th>
                                </tr>
                            </thead>
                            <tbody id="history-body">
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">Memuat data sensor...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <p class="text-uppercase text-muted small mb-1">Status Baterai</p>
                            <h2 id="sensor-battery" class="card-title display-6">— V</h2>
                            <p class="text-muted mb-3">Tingkat baterai dari modul ESP32.</p>
                        </div>
                        <div class="alert alert-secondary mb-0" role="alert">
                            Pastikan ESP32 terhubung ke internet dan mengirimkan data ke endpoint <code>POST
                                /esp32/readings</code>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('template/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        const fallback = {
            temperature: '— °C',
            soil_moisture: '— %',
            soil_temperature: '— °C',
            air_humidity: '— %',
            battery: '— V',
        };

        function buildRow(reading) {
            return `
                    <tr>
                        <td>${new Date(reading.created_at).toLocaleString()}</td>
                        <td>${reading.temperature === null ? '—' : reading.temperature.toFixed(1) + ' °C'}</td>
                        <td>${reading.soil_moisture === null ? '—' : reading.soil_moisture + ' %'}</td>
                        <td>${reading.soil_temperature === null ? '—' : reading.soil_temperature.toFixed(1) + ' °C'}</td>
                        <td>${reading.air_humidity === null ? '—' : reading.air_humidity.toFixed(1) + ' %'}</td>
                        <td>${reading.battery === null ? '—' : reading.battery.toFixed(2) + ' V'}</td>
                    </tr>
                `;
        }

        async function loadSensorData() {
            try {
                const response = await fetch('/esp32/readings');
                if (!response.ok) {
                    throw new Error('Gagal memuat data sensor');
                }

                const readings = await response.json();
                const latest = readings[0] || null;

                document.getElementById('sensor-temperature').textContent = latest?.temperature !== null ?
                    `${latest.temperature.toFixed(1)} °C` : fallback.temperature;
                document.getElementById('sensor-soil-moisture').textContent = latest?.soil_moisture !== null ?
                    `${latest.soil_moisture} %` : fallback.soil_moisture;
                document.getElementById('sensor-soil-temperature').textContent = latest?.soil_temperature !== null ?
                    `${latest.soil_temperature.toFixed(1)} °C` : fallback.soil_temperature;
                document.getElementById('sensor-air-humidity').textContent = latest?.air_humidity !== null ?
                    `${latest.air_humidity.toFixed(1)} %` : fallback.air_humidity;
                document.getElementById('sensor-battery').textContent = latest?.battery !== null ?
                    `${latest.battery.toFixed(2)} V` : fallback.battery;

                const body = document.getElementById('history-body');
                if (!readings.length) {
                    body.innerHTML =
                        '<tr><td colspan="6" class="text-center py-5 text-muted">Tidak ada data sensor.</td></tr>';
                    return;
                }

                body.innerHTML = readings.map(buildRow).join('');
            } catch (error) {
                document.getElementById('history-body').innerHTML =
                    '<tr><td colspan="6" class="text-center py-5 text-danger">Gagal memuat data. Periksa koneksi server.</td></tr>';
                console.error(error);
            }
        }

        document.getElementById('refresh-button').addEventListener('click', loadSensorData);
        loadSensorData();
        setInterval(loadSensorData, 15000);
    </script>
</body>

</html>
