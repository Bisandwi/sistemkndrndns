<?php
$pageTitle = 'Dashboard';
require 'config/database.php';
require 'includes/header.php';

$counts = [
 'kendaraan' => (int)$pdo->query('SELECT COUNT(*) FROM kendaraan')->fetchColumn(),
 'pegawai' => (int)$pdo->query('SELECT COUNT(*) FROM pegawai')->fetchColumn(),
 'dinas' => (int)$pdo->query('SELECT COUNT(*) FROM perangkat_daerah')->fetchColumn(),
 'aktif' => (int)$pdo->query("SELECT COUNT(*) FROM kendaraan WHERE status_kendaraan='Aktif'")->fetchColumn(),
 'rusak' => (int)$pdo->query("SELECT COUNT(*) FROM kendaraan WHERE kondisi IN ('Rusak Ringan','Rusak Berat')")->fetchColumn(),
];
$conditionRows = $pdo->query("SELECT CASE WHEN kondisi='Baik' THEN 'Baik' ELSE 'Tidak Baik' END AS kondisi, COUNT(*) total FROM kendaraan GROUP BY CASE WHEN kondisi='Baik' THEN 'Baik' ELSE 'Tidak Baik' END ORDER BY CASE WHEN kondisi='Baik' THEN 1 ELSE 2 END")->fetchAll();
$dinasRows = $pdo->query("SELECT d.nama_dinas, COUNT(k.id) total FROM perangkat_daerah d LEFT JOIN kendaraan k ON k.perangkat_daerah_id=d.id GROUP BY d.id ORDER BY d.nama_dinas")->fetchAll();
$statusRows = $pdo->query("SELECT status_kendaraan, COUNT(*) total FROM kendaraan GROUP BY status_kendaraan")->fetchAll();
$recent = $pdo->query("SELECT k.nomor_polisi,k.merk,k.tipe,k.kondisi,k.status_kendaraan,d.nama_dinas FROM kendaraan k JOIN perangkat_daerah d ON d.id=k.perangkat_daerah_id ORDER BY k.created_at DESC LIMIT 7")->fetchAll();
$brandRows = $pdo->query("SELECT merk, COUNT(*) total FROM kendaraan GROUP BY merk ORDER BY total DESC LIMIT 10")->fetchAll();
$yearRows = $pdo->query("SELECT tahun_produksi, COUNT(*) total FROM kendaraan GROUP BY tahun_produksi ORDER BY tahun_produksi")->fetchAll();

?>
<style>.dashboard-card{transition:transform .15s ease,box-shadow .15s ease;cursor:pointer}.dashboard-card:hover{transform:translateY(-3px);box-shadow:0 .5rem 1.2rem rgba(0,0,0,.12)!important;border-color:#9ec5fe}</style>
<h3 class="mb-4">Dashboard Pengelolaan Kendaraan Dinas</h3>
<div class="row g-3 mb-4">
<?php
$cards = [
 ['Total Kendaraan',$counts['kendaraan'],'bi-truck-front','kendaraan.php'],
 ['Kendaraan Aktif',$counts['aktif'],'bi-check-circle','kendaraan.php?filter=aktif'],
 ['Kendaraan Perlu Perhatian',$counts['rusak'],'bi-exclamation-triangle','kendaraan.php?filter=perhatian'],
 ['Total Pegawai',$counts['pegawai'],'bi-people','pegawai.php'],
 ['Perangkat Daerah',$counts['dinas'],'bi-building','perangkat_daerah.php'],
];
foreach($cards as $c): ?>
<div class="col-6 col-lg">
 <a href="<?=htmlspecialchars($c[3])?>" class="text-decoration-none text-reset">
 <div class="card shadow-sm h-100 dashboard-card"><div class="card-body">
  <div class="text-muted small"><?= htmlspecialchars($c[0]) ?></div>
  <div class="display-6 fw-semibold"><?= number_format($c[1]) ?></div>
  <div class="small text-primary mt-2">Lihat data <span aria-hidden="true">→</span></div>
 </div></div></a>
</div>
<?php endforeach; ?>
</div>
<div class="row g-3 mb-4">
 <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">1. Kondisi Kendaraan (Baik vs Tidak Baik)</div><div class="card-body"><canvas id="conditionChart" height="220"></canvas></div></div></div>
 <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">2. Kendaraan per Perangkat Daerah</div><div class="card-body"><canvas id="dinasChart" height="220"></canvas></div></div></div>
 <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">3. Kendaraan Berdasarkan Merk (Top 10)</div><div class="card-body"><canvas id="brandChart" height="220"></canvas></div></div></div>
 <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">4. Kendaraan Berdasarkan Tahun Produksi</div><div class="card-body"><canvas id="yearChart" height="220"></canvas></div></div></div>
 <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">5. Perbandingan Status Kendaraan</div><div class="card-body"><canvas id="statusChart" height="220"></canvas></div></div></div>
</div>
<div class="card shadow-sm mb-4"><div class="card-header bg-white fw-semibold">Data Kendaraan Terbaru</div><div class="card-body table-responsive">
<table class="table table-hover align-middle mb-0"><thead><tr><th>Nomor Polisi</th><th>Merk / Tipe</th><th>Kondisi</th><th>Status</th><th>Perangkat Daerah</th></tr></thead><tbody>
<?php foreach($recent as $r): ?><tr><td><?=htmlspecialchars($r['nomor_polisi'])?></td><td><?=htmlspecialchars($r['merk'].' / '.$r['tipe'])?></td><td><?=htmlspecialchars($r['kondisi'])?></td><td><?=htmlspecialchars($r['status_kendaraan'])?></td><td><?=htmlspecialchars($r['nama_dinas'])?></td></tr><?php endforeach; ?>
<?php if(!$recent): ?><tr><td colspan="5" class="text-center text-muted">Belum ada data kendaraan.</td></tr><?php endif; ?>
</tbody></table></div></div>
<div class="d-flex flex-wrap gap-2 mb-3">
<a href="kendaraan.php" class="btn btn-primary">Kelola Kendaraan</a>
<a href="laporan.php" class="btn btn-outline-primary">Buka Laporan Lengkap</a>
<a href="monitoring.php" class="btn btn-outline-danger">Monitoring & Notifikasi</a>
<a href="chat.php" class="btn btn-outline-success">Chat Koordinasi Dinas</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
Chart.register(ChartDataLabels);

const valueLabels = {
  color: '#243447',
  font: {weight: 'bold', size: 11},
  formatter: (value) => Number(value).toLocaleString('id-ID'),
  anchor: 'end',
  align: 'end',
  clamp: true
};
const pieValueLabels = {
  color: '#fff',
  font: {weight: 'bold', size: 12},
  formatter: (value, context) => {
    const values = context.dataset.data.map(Number);
    const total = values.reduce((sum, item) => sum + item, 0);
    const percent = total ? (Number(value) / total * 100).toFixed(1) : 0;
    return Number(value).toLocaleString('id-ID') + ' (' + percent + '%)';
  }
};
const commonOptions = {responsive:true, maintainAspectRatio:true,
  plugins:{legend:{position:'bottom'}, datalabels:valueLabels}};

new Chart(document.getElementById('conditionChart'), {
 type:'doughnut',
 data:{labels:<?=json_encode(array_column($conditionRows,'kondisi'), JSON_UNESCAPED_UNICODE)?>,
 datasets:[{label:'Jumlah kendaraan',data:<?=json_encode(array_map('intval',array_column($conditionRows,'total')))?>}]},
 options:{...commonOptions, plugins:{...commonOptions.plugins, datalabels:pieValueLabels}}
});
new Chart(document.getElementById('dinasChart'), {
 type:'bar',
 data:{labels:<?=json_encode(array_column($dinasRows,'nama_dinas'), JSON_UNESCAPED_UNICODE)?>,
 datasets:[{label:'Jumlah kendaraan',data:<?=json_encode(array_map('intval',array_column($dinasRows,'total')))?>}]},
 options:{...commonOptions,scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});
new Chart(document.getElementById('brandChart'), {
 type:'bar',
 data:{labels:<?=json_encode(array_column($brandRows,'merk'), JSON_UNESCAPED_UNICODE)?>,
 datasets:[{label:'Jumlah kendaraan',data:<?=json_encode(array_map('intval',array_column($brandRows,'total')))?>}]},
 options:{...commonOptions,indexAxis:'y',scales:{x:{beginAtZero:true,ticks:{precision:0}}}}
});
new Chart(document.getElementById('yearChart'), {
 type:'line',
 data:{labels:<?=json_encode(array_map('strval',array_column($yearRows,'tahun_produksi')))?>,
 datasets:[{label:'Jumlah kendaraan',data:<?=json_encode(array_map('intval',array_column($yearRows,'total')))?>,tension:0.25,fill:false}]},
 options:{...commonOptions,scales:{y:{beginAtZero:true,ticks:{precision:0}}}}
});
new Chart(document.getElementById('statusChart'), {
 type:'pie',
 data:{labels:<?=json_encode(array_column($statusRows,'status_kendaraan'), JSON_UNESCAPED_UNICODE)?>,
 datasets:[{label:'Jumlah kendaraan',data:<?=json_encode(array_map('intval',array_column($statusRows,'total')))?>}]},
 options:{...commonOptions, plugins:{...commonOptions.plugins, datalabels:pieValueLabels}}
});
</script>
<?php require 'includes/footer.php'; ?>
