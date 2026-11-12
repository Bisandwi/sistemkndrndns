<?php
require 'config/database.php';
require 'includes/auth.php';
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$dinas = $pdo->query('SELECT id,nama_dinas FROM perangkat_daerah ORDER BY nama_dinas')->fetchAll();
$kondisiList = ['Baik','Rusak Ringan','Rusak Berat'];
$statusList = ['Aktif','Tidak Aktif'];

$where = [];
$params = [];
if (!empty($_GET['dinas'])) { $where[] = 'k.perangkat_daerah_id = ?'; $params[] = (int)$_GET['dinas']; }
if (!empty($_GET['kondisi']) && in_array($_GET['kondisi'],$kondisiList,true)) { $where[] = 'k.kondisi = ?'; $params[] = $_GET['kondisi']; }
if (!empty($_GET['status']) && in_array($_GET['status'],$statusList,true)) { $where[] = 'k.status_kendaraan = ?'; $params[] = $_GET['status']; }
if (!empty($_GET['tahun']) && preg_match('/^\d{4}$/',$_GET['tahun'])) { $where[] = 'k.tahun_produksi = ?'; $params[] = (int)$_GET['tahun']; }
if (!empty($_GET['tanggal_mulai']) && preg_match('/^\d{4}-\d{2}-\d{2}$/',$_GET['tanggal_mulai'])) { $where[] = 'DATE(k.created_at) >= ?'; $params[] = $_GET['tanggal_mulai']; }
if (!empty($_GET['tanggal_akhir']) && preg_match('/^\d{4}-\d{2}-\d{2}$/',$_GET['tanggal_akhir'])) { $where[] = 'DATE(k.created_at) <= ?'; $params[] = $_GET['tanggal_akhir']; }

$sql = "SELECT k.*,d.nama_dinas,p.nama_pegawai,p.nip FROM kendaraan k
 JOIN perangkat_daerah d ON d.id=k.perangkat_daerah_id
 LEFT JOIN pegawai p ON p.id=k.pegawai_id";
if ($where) $sql .= ' WHERE '.implode(' AND ',$where);
$sql .= ' ORDER BY d.nama_dinas,k.nomor_polisi';
$stmt=$pdo->prepare($sql); $stmt->execute($params); $rows=$stmt->fetchAll();

$total=count($rows); $baik=$ringan=$berat=$aktif=0;
foreach($rows as $r){ if($r['kondisi']==='Baik')$baik++; if($r['kondisi']==='Rusak Ringan')$ringan++; if($r['kondisi']==='Rusak Berat')$berat++; if($r['status_kendaraan']==='Aktif')$aktif++; }

$summary=[];
foreach($rows as $r){
 $key=$r['nama_dinas'];
 if(!isset($summary[$key]))$summary[$key]=['total'=>0,'baik'=>0,'ringan'=>0,'berat'=>0,'aktif'=>0];
 $summary[$key]['total']++;
 if($r['kondisi']==='Baik')$summary[$key]['baik']++;
 if($r['kondisi']==='Rusak Ringan')$summary[$key]['ringan']++;
 if($r['kondisi']==='Rusak Berat')$summary[$key]['berat']++;
 if($r['status_kendaraan']==='Aktif')$summary[$key]['aktif']++;
}

if(($_GET['export']??'')==='csv'){
 header('Content-Type: text/csv; charset=utf-8');
 header('Content-Disposition: attachment; filename="laporan-kendaraan-dinas-'.date('Ymd-His').'.csv"');
 $out=fopen('php://output','w');
 fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
 fputcsv($out,['Nomor Polisi','Merk','Tipe','Tahun Produksi','Warna','Kategori Kendaraan','Kondisi','Status','Perangkat Daerah','Pegawai Penanggung Jawab','NIP']);
 foreach($rows as $r) fputcsv($out,[$r['nomor_polisi'],$r['merk'],$r['tipe'],$r['tahun_produksi'],$r['warna'],$r['kategori_kendaraan'],$r['kondisi'],$r['status_kendaraan'],$r['nama_dinas'],$r['nama_pegawai'],$r['nip']]);
 fclose($out); exit;
}
$pageTitle='Laporan Kendaraan'; require 'includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
 <div><h3 class="mb-1">Laporan Kendaraan Dinas</h3><div class="text-muted small">Rekapitulasi dan analisis berdasarkan filter data.</div></div>
 <div class="d-flex gap-2"><a class="btn btn-success" href="?<?=e(http_build_query(array_merge($_GET,['export'=>'csv'])))?>">Export Excel / CSV</a><button class="btn btn-outline-secondary" onclick="window.print()">Cetak Laporan</button></div>
</div>
<div class="card shadow-sm mb-4">
 <div class="card-header bg-white fw-semibold">Filter Laporan</div><div class="card-body">
 <form method="get" class="row g-3">
  <div class="col-md-4"><label class="form-label">Perangkat Daerah</label><select name="dinas" class="form-select"><option value="">Semua Perangkat Daerah</option><?php foreach($dinas as $d):?><option value="<?=$d['id']?>" <?=($_GET['dinas']??'')==$d['id']?'selected':''?>><?=e($d['nama_dinas'])?></option><?php endforeach;?></select></div>
  <div class="col-md-2"><label class="form-label">Kondisi</label><select name="kondisi" class="form-select"><option value="">Semua</option><?php foreach($kondisiList as $v):?><option <?=($_GET['kondisi']??'')===$v?'selected':''?>><?=e($v)?></option><?php endforeach;?></select></div>
  <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Semua</option><?php foreach($statusList as $v):?><option <?=($_GET['status']??'')===$v?'selected':''?>><?=e($v)?></option><?php endforeach;?></select></div>
  <div class="col-md-2"><label class="form-label">Tahun Produksi</label><input type="number" name="tahun" class="form-control" min="1901" max="2155" value="<?=e($_GET['tahun']??'')?>"></div>
  <div class="col-md-2"><label class="form-label">Tanggal Input Mulai</label><input type="date" name="tanggal_mulai" class="form-control" value="<?=e($_GET['tanggal_mulai']??'')?>"></div>
  <div class="col-md-2"><label class="form-label">Tanggal Input Akhir</label><input type="date" name="tanggal_akhir" class="form-control" value="<?=e($_GET['tanggal_akhir']??'')?>"></div>
  <div class="col-12"><button class="btn btn-primary">Terapkan Filter</button> <a class="btn btn-outline-secondary" href="laporan.php">Reset</a></div>
 </form></div>
</div>
<div class="row g-3 mb-4">
 <?php foreach([['Total Kendaraan',$total],['Kondisi Baik',$baik],['Rusak Ringan',$ringan],['Rusak Berat',$berat],['Status Aktif',$aktif]] as $c):?>
 <div class="col-6 col-md"><div class="card shadow-sm h-100"><div class="card-body"><div class="text-muted small"><?=e($c[0])?></div><div class="h2 mb-0"><?=number_format($c[1])?></div></div></div></div>
 <?php endforeach;?>
</div>
<div class="row g-3 mb-4">
 <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">Komposisi Kondisi</div><div class="card-body"><canvas id="reportCondition" height="210"></canvas></div></div></div>
 <div class="col-lg-6"><div class="card shadow-sm h-100"><div class="card-header bg-white fw-semibold">Rekap per Perangkat Daerah</div><div class="card-body"><canvas id="reportDinas" height="210"></canvas></div></div></div>
</div>
<div class="card shadow-sm mb-4"><div class="card-header bg-white fw-semibold">Rekapitulasi Per Perangkat Daerah</div><div class="card-body table-responsive">
<table class="table table-bordered table-sm"><thead class="table-light"><tr><th>Perangkat Daerah</th><th>Total</th><th>Baik</th><th>Rusak Ringan</th><th>Rusak Berat</th><th>Aktif</th></tr></thead><tbody>
<?php foreach($summary as $nama=>$v):?><tr><td><?=e($nama)?></td><td><?=$v['total']?></td><td><?=$v['baik']?></td><td><?=$v['ringan']?></td><td><?=$v['berat']?></td><td><?=$v['aktif']?></td></tr><?php endforeach;?>
<?php if(!$summary):?><tr><td colspan="6" class="text-center text-muted">Tidak ada data untuk filter tersebut.</td></tr><?php endif;?></tbody></table></div></div>
<div class="card shadow-sm"><div class="card-header bg-white fw-semibold">Detail Data Kendaraan (<?=number_format($total)?> data)</div><div class="card-body table-responsive">
<table class="table table-hover table-sm align-middle"><thead class="table-light"><tr><th>No</th><th>Nomor Polisi</th><th>Merk / Tipe</th><th>Tahun</th><th>Warna</th><th>Kategori</th><th>Kondisi</th><th>Status</th><th>Dinas</th><th>Pegawai</th></tr></thead><tbody>
<?php foreach($rows as $i=>$r):?><tr><td><?=$i+1?></td><td><?=e($r['nomor_polisi'])?></td><td><?=e($r['merk'].' / '.$r['tipe'])?></td><td><?=e($r['tahun_produksi'])?></td><td><?=e($r['warna'])?></td><td><?=e(in_array(($r['kategori_kendaraan']??''),['Pribadi','Non Pribadi'],true)?'Non-Niaga':($r['kategori_kendaraan']??'Non-Niaga'))?></td><td><?=e($r['kondisi'])?></td><td><?=e($r['status_kendaraan'])?></td><td><?=e($r['nama_dinas'])?></td><td><?=e($r['nama_pegawai']??'-')?></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="10" class="text-center text-muted">Tidak ada data untuk filter tersebut.</td></tr><?php endif;?></tbody></table></div></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script>
new Chart(document.getElementById('reportCondition'),{type:'doughnut',data:{labels:['Baik','Rusak Ringan','Rusak Berat'],datasets:[{data:[<?=$baik?>,<?=$ringan?>,<?=$berat?>]}]},options:{responsive:true,plugins:{legend:{position:'bottom'}}}});
new Chart(document.getElementById('reportDinas'),{type:'bar',data:{labels:<?=json_encode(array_keys($summary))?>,datasets:[{label:'Jumlah kendaraan',data:<?=json_encode(array_column($summary,'total'))?>}]},options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{precision:0}}},plugins:{legend:{display:false}}}});
</script>
<style>@media print{nav,.btn,.card form{display:none!important}body{background:#fff!important}.card{box-shadow:none!important;border:1px solid #ddd!important}.container{max-width:100%!important}}</style>
<?php require 'includes/footer.php'; ?>
