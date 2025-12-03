<?php
// Ambil file JSON
$data = json_decode(file_get_contents("data_sampah.json"), true);

// Urutkan berdasarkan total_leak tertinggi
usort($data, function($a, $b) {
    return $b['total_leak'] <=> $a['total_leak'];
});

// Ambil top 10 kabkot
$top10 = array_slice($data, 0, 10);

// Hitung total leak nasional per kawasan
$total_pelabuhan = array_sum(array_column($data, 'leak_pelabuhan'));
$total_pesisir   = array_sum(array_column($data, 'leak_pesisir'));
$total_pulau     = array_sum(array_column($data, 'leak_pulau'));
$total_sungai    = array_sum(array_column($data, 'leak_sungai'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Informasi Kebocoran Sampah Laut</title>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>

<style>
body {
    font-family: Arial, sans-serif;
    padding: 30px;
    background: #f4f7fb;
}

.container {
    max-width: 1200px;
    margin: auto;
}

.card {
    background: white;
    padding: 20px;
    margin-bottom: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
}
</style>

</head>
<body>

<div class="container">

    <h2><b>Dashboard Kebocoran Sampah Laut</b></h2>
    <br>

    <!-- ======================== TABEL ========================= -->
    <div class="card">
        <h3>Data Kebocoran Sampah per Kabkot</h3>
        <table id="tabelData" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Kabkot</th>
                    <th>Pelabuhan</th>
                    <th>Pesisir</th>
                    <th>Pulau</th>
                    <th>Sungai</th>
                    <th>Total Leak</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= $row['kabkot'] ?></td>
                    <td><?= number_format($row['leak_pelabuhan'], 2) ?></td>
                    <td><?= number_format($row['leak_pesisir'], 2) ?></td>
                    <td><?= number_format($row['leak_pulau'], 2) ?></td>
                    <td><?= number_format($row['leak_sungai'], 2) ?></td>
                    <td><b><?= number_format($row['total_leak'], 2) ?></b></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- ======================== GRAFIK BATANG TOP 10 ========================= -->
    <div class="card">
        <h3>Top 10 Kabkot dengan Kebocoran Sampah Tertinggi</h3>
        <canvas id="barChart"></canvas>
    </div>

    <!-- ======================== GRAFIK PIE KONTRIBUSI NASIONAL ========================= -->
    <div class="card">
        <h3>Kontribusi Nasional per Kawasan</h3>
        <canvas id="pieChart"></canvas>
    </div>

</div>

<script>
$(document).ready(function() {
    $('#tabelData').DataTable();
});

// ===================== DATA UNTUK BAR CHART (TOP 10) ======================
const kabkot = <?= json_encode(array_column($top10, 'kabkot')) ?>;
const totalLeak = <?= json_encode(array_column($top10, 'total_leak')) ?>;

new Chart(document.getElementById('barChart'), {
    type: 'bar',
    data: {
        labels: kabkot,
        datasets: [{
            label: 'Total Kebocoran (ton)',
            data: totalLeak,
            backgroundColor: 'rgba(54,162,235,0.6)',
            borderColor: 'rgba(54,162,235,1)',
            borderWidth: 1
        }]
    }
});

// ===================== DATA UNTUK PIE CHART ======================
new Chart(document.getElementById('pieChart'), {
    type: 'pie',
    data: {
        labels: ['Pelabuhan', 'Pesisir', 'Pulau', 'Sungai'],
        datasets: [{
            data: [
                <?= $total_pelabuhan ?>,
                <?= $total_pesisir ?>,
                <?= $total_pulau ?>,
                <?= $total_sungai ?>
            ],
            backgroundColor: [
                '#4285F4',
                '#34A853',
                '#FBBC05',
                '#EA4335'
            ]
        }]
    }
});
</script>

</body>
</html>
