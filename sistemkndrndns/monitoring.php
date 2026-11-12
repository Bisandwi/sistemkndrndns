<?php
require 'config/database.php';require 'includes/auth.php';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
$vehicleSql="SELECT k.*,d.nama_dinas,p.nama_pegawai,p.nip,p.tanggal_pensiun,
 COALESCE(k.tanggal_akhir_masa_pakai,
   CASE WHEN k.tanggal_diterima IS NOT NULL THEN DATE_ADD(k.tanggal_diterima, INTERVAL k.masa_pakai_tahun YEAR)
        ELSE DATE_ADD(CONCAT(k.tahun_produksi,'-01-01'), INTERVAL k.masa_pakai_tahun YEAR) END) AS estimasi_akhir
 FROM kendaraan k JOIN perangkat_daerah d ON d.id=k.perangkat_daerah_id
 LEFT JOIN pegawai p ON p.id=k.pegawai_id
 ORDER BY estimasi_akhir ASC";
$vehicles=$pdo->query($vehicleSql)->fetchAll();
$expiry=[];$retirement=[];
$today=new DateTimeImmutable('today');
foreach($vehicles as $v){
 if($v['estimasi_akhir']){
  $end=new DateTimeImmutable($v['estimasi_akhir']);$days=(int)$today->diff($end)->format('%r%a');
  if($days<=180)$expiry[]=$v+['days_left'=>$days];
 }
 if($v['pegawai_id'] && $v['tanggal_pensiun']){
  $ret=new DateTimeImmutable($v['tanggal_pensiun']);$days=(int)$today->diff($ret)->format('%r%a');
  if($days<=90)$retirement[]=$v+['retirement_days'=>$days];
 }
}
$pageTitle='Monitoring & Notifikasi';require 'includes/header.php';
?>
<h3 class="mb-3">Monitoring Masa Pakai & Pensiun Pegawai</h3>
<div class="alert alert-info">Pengingat masa pakai memakai tanggal akhir yang diisi manual. Jika kosong, sistem menghitung estimasi dari tanggal diterima + masa pakai; bila tanggal diterima kosong, digunakan 1 Januari tahun produksi + masa pakai. Nilai masa pakai default 7 tahun hanya asumsi aplikasi dan wajib disesuaikan dengan kebijakan/ketentuan aset yang berlaku.</div>
<div class="row g-3 mb-4">
 <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Kendaraan jatuh tempo / ≤180 hari</div><div class="display-6"><?=count($expiry)?></div></div></div></div>
 <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Penanggung jawab pensiun / ≤90 hari</div><div class="display-6"><?=count($retirement)?></div></div></div></div>
 <div class="col-md-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Total kendaraan dimonitor</div><div class="display-6"><?=count($vehicles)?></div></div></div></div>
</div>
<div class="card shadow-sm mb-4"><div class="card-header bg-white fw-semibold">Notifikasi Masa Pakai Kendaraan</div><div class="card-body table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Nomor Polisi</th><th>Merk / Tipe</th><th>Dinas</th><th>Tanggal Mulai Kelola</th><th>Lama Dikelola</th><th>Estimasi Akhir Masa Pakai</th><th>Sisa Waktu</th></tr></thead><tbody>
<?php foreach($expiry as $v):?><tr><td><?=e($v['nomor_polisi'])?></td><td><?=e($v['merk'].' / '.$v['tipe'])?></td><td><?=e($v['nama_dinas'])?></td><td><?=e($v['tanggal_diterima']??'Berdasar tahun produksi')?></td><td><?php if($v['tanggal_diterima']){$awal=new DateTimeImmutable($v['tanggal_diterima']);$selisih=$awal->diff($today);echo $selisih->y.' th '.$selisih->m.' bln';}else{echo 'Tanggal diterima belum diisi';}?></td><td><?=e($v['estimasi_akhir'])?></td><td><?php if($v['days_left']<0):?><span class="badge bg-danger">Lewat <?=abs($v['days_left'])?> hari</span><?php elseif($v['days_left']===0):?><span class="badge bg-danger">Jatuh tempo hari ini</span><?php else:?><span class="badge bg-warning text-dark"><?=$v['days_left']?> hari lagi</span><?php endif;?></td></tr><?php endforeach;?>
<?php if(!$expiry):?><tr><td colspan="7" class="text-center text-muted">Tidak ada kendaraan yang mendekati atau melewati estimasi akhir masa pakai.</td></tr><?php endif;?></tbody></table></div></div>
<div class="card shadow-sm"><div class="card-header bg-white fw-semibold">Notifikasi Pensiun Penanggung Jawab Kendaraan</div><div class="card-body table-responsive"><table class="table table-hover align-middle"><thead><tr><th>Nama Pegawai</th><th>NIP</th><th>Jabatan</th><th>Tanggal Pensiun</th><th>Kendaraan</th><th>Dinas</th><th>Status Pengingat</th></tr></thead><tbody>
<?php foreach($retirement as $v):?><tr><td><?=e($v['nama_pegawai'])?></td><td><?=e($v['nip'])?></td><td><?=e($v['jabatan']??'-')?></td><td><?=e($v['tanggal_pensiun'])?></td><td><?=e($v['nomor_polisi'])?></td><td><?=e($v['nama_dinas'])?></td><td><?php if($v['retirement_days']<0):?><span class="badge bg-danger">Sudah pensiun (<?=abs($v['retirement_days'])?> hari)</span><?php elseif($v['retirement_days']===0):?><span class="badge bg-danger">Pensiun hari ini</span><?php else:?><span class="badge bg-warning text-dark">Pensiun dalam <?=$v['retirement_days']?> hari</span><?php endif;?></td></tr><?php endforeach;?>
<?php if(!$retirement):?><tr><td colspan="7" class="text-center text-muted">Tidak ada penanggung jawab yang pensiun atau mendekati tanggal pensiun dalam 90 hari.</td></tr><?php endif;?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
