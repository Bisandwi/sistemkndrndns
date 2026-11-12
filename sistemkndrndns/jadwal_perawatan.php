<?php
require 'config/database.php';
require 'includes/auth.php';
if (($_SESSION['role'] ?? 'admin') !== 'admin') { header('Location: chat.php'); exit; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$error = '';
$edit = null;

if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare('DELETE FROM jadwal_perawatan WHERE id=?');
    $stmt->execute([(int)$_GET['hapus']]);
    header('Location: jadwal_perawatan.php?pesan=hapus'); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $kendaraanId = (int)($_POST['kendaraan_id'] ?? 0);
    $tanggal = $_POST['tanggal_jadwal'] ?? '';
    $kerusakanId = ($_POST['kerusakan_id'] ?? '') !== '' ? (int)$_POST['kerusakan_id'] : null;
    $tanggalBerikutnya = ($_POST['tanggal_perawatan_berikutnya'] ?? '') ?: null;
    $jenis = trim($_POST['jenis_perawatan'] ?? '');
    $kerusakan = trim($_POST['spesifik_kerusakan'] ?? '');
    $tindakan = trim($_POST['rencana_tindakan'] ?? '');
    $bengkel = trim($_POST['bengkel'] ?? '');
    $biaya = ($_POST['estimasi_biaya'] ?? '') !== '' ? (float)$_POST['estimasi_biaya'] : null;
    $status = $_POST['status'] ?? 'Terjadwal';
    $catatan = trim($_POST['catatan'] ?? '');
    if (!$kendaraanId || !$tanggal || !$jenis || !$kerusakan) {
        $error = 'Kendaraan, tanggal jadwal, jenis perawatan, dan spesifik kerusakan wajib diisi.';
    } elseif (!in_array($status, ['Terjadwal','Dalam Perbaikan','Selesai','Dibatalkan'], true)) {
        $error = 'Status perawatan tidak valid.';
    } else {
        try {
            if ($id) {
                $sql = 'UPDATE jadwal_perawatan SET kendaraan_id=?,kerusakan_id=?,tanggal_jadwal=?,tanggal_perawatan_berikutnya=?,jenis_perawatan=?,spesifik_kerusakan=?,rencana_tindakan=?,bengkel=?,estimasi_biaya=?,status=?,catatan=? WHERE id=?';
                $params = [$kendaraanId,$kerusakanId,$tanggal,$tanggalBerikutnya,$jenis,$kerusakan,$tindakan,$bengkel,$biaya,$status,$catatan,$id];
            } else {
                $sql = 'INSERT INTO jadwal_perawatan (kendaraan_id,kerusakan_id,tanggal_jadwal,tanggal_perawatan_berikutnya,jenis_perawatan,spesifik_kerusakan,rencana_tindakan,bengkel,estimasi_biaya,status,catatan,dibuat_oleh) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
                $params = [$kendaraanId,$kerusakanId,$tanggal,$tanggalBerikutnya,$jenis,$kerusakan,$tindakan,$bengkel,$biaya,$status,$catatan,$_SESSION['user_id'] ?? null];
            }
            $pdo->prepare($sql)->execute($params);
            header('Location: jadwal_perawatan.php?pesan=simpan'); exit;
        } catch (PDOException $ex) { $error = 'Gagal menyimpan jadwal. Periksa data kendaraan dan struktur database.'; }
    }
}
if (isset($_GET['edit'])) {
    $st=$pdo->prepare('SELECT * FROM jadwal_perawatan WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit=$st->fetch();
}
$kendaraan=$pdo->query('SELECT id,nomor_polisi,merk,tipe FROM kendaraan ORDER BY nomor_polisi')->fetchAll();
$katalog=$pdo->query('SELECT * FROM referensi_kerusakan ORDER BY kategori,nama_kerusakan')->fetchAll();
$rows=$pdo->query("SELECT j.*,k.nomor_polisi,k.merk,k.tipe,d.nama_dinas FROM jadwal_perawatan j JOIN kendaraan k ON k.id=j.kendaraan_id JOIN perangkat_daerah d ON d.id=k.perangkat_daerah_id ORDER BY j.tanggal_jadwal DESC,j.id DESC")->fetchAll();
$pageTitle='Jadwal Perawatan Kendaraan';
require 'includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
 <div><h3 class="mb-1">Jadwal Perawatan Kendaraan</h3><div class="text-muted">Atur jadwal servis dan catat kerusakan kendaraan secara spesifik.</div></div>
 <span class="badge text-bg-primary fs-6"><?=count($rows)?> Jadwal</span>
</div>
<?php if($error): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
<?php if(isset($_GET['pesan'])): ?><div class="alert alert-success"><?=($_GET['pesan']==='hapus'?'Jadwal berhasil dihapus.':'Jadwal berhasil disimpan.')?></div><?php endif; ?>
<div class="card shadow-sm mb-4">
 <div class="card-header bg-white fw-semibold"><?=$edit?'Ubah Jadwal Perawatan':'Tambah Jadwal Perawatan'?></div>
 <div class="card-body">
 <form method="post" class="row g-3">
  <input type="hidden" name="id" value="<?=e($edit['id']??'')?>">
  <div class="col-lg-6"><label class="form-label">Kendaraan *</label><select name="kendaraan_id" class="form-select" required><option value="">Pilih kendaraan</option><?php foreach($kendaraan as $k): ?><option value="<?=$k['id']?>" <?=($edit['kendaraan_id']??'')==$k['id']?'selected':''?>><?=e($k['nomor_polisi'].' - '.$k['merk'].' '.$k['tipe'])?></option><?php endforeach; ?></select></div>
  <div class="col-lg-3"><label class="form-label">Tanggal Jadwal *</label><input type="date" name="tanggal_jadwal" class="form-control" required value="<?=e($edit['tanggal_jadwal']??'')?>"></div>
  <div class="col-lg-3"><label class="form-label">Jadwal Perawatan Berikutnya</label><input type="date" name="tanggal_perawatan_berikutnya" class="form-control" value="<?=e($edit['tanggal_perawatan_berikutnya']??'')?>"></div>
  <div class="col-lg-3"><label class="form-label">Jenis Perawatan *</label><select name="jenis_perawatan" class="form-select" required><?php foreach(['Servis Berkala','Perbaikan Kerusakan','Ganti Oli','Penggantian Suku Cadang','Pemeriksaan/KIR','Lainnya'] as $v): ?><option <?=($edit['jenis_perawatan']??'')===$v?'selected':''?>><?=e($v)?></option><?php endforeach; ?></select></div>
  <div class="col-md-6"><label class="form-label">Daftar Kerusakan (Katalog)</label><select name="kerusakan_id" id="kerusakan_id" class="form-select"><option value="">Pilih kerusakan atau isi manual</option><?php foreach($katalog as $kr): ?><option value="<?=$kr['id']?>" data-gejala="<?=e($kr['gejala']??'')?>" data-tindakan="<?=e($kr['rekomendasi_tindakan']??'')?>" <?=($edit['kerusakan_id']??'')==$kr['id']?'selected':''?>><?=e($kr['kategori'].' - '.$kr['nama_kerusakan'])?></option><?php endforeach; ?></select></div>
  <div class="col-md-6"><label class="form-label">Gejala / Referensi</label><div id="detailKerusakan" class="form-control bg-light" style="min-height:38px">Pilih katalog untuk melihat gejala umum.</div></div>
  <div class="col-12"><label class="form-label">Spesifik Kerusakan Kendaraan *</label><textarea name="spesifik_kerusakan" class="form-control" rows="2" required placeholder="Contoh: rem depan kurang pakem, kampas rem aus, terdengar bunyi saat pengereman."><?=e($edit['spesifik_kerusakan']??'')?></textarea><div class="form-text">Tuliskan gejala, bagian kendaraan yang rusak, dan kondisi yang ditemukan.</div></div>
  <div class="col-lg-6"><label class="form-label">Rencana Tindakan Perawatan</label><textarea name="rencana_tindakan" class="form-control" rows="2" placeholder="Contoh: pemeriksaan sistem rem dan penggantian kampas rem."><?=e($edit['rencana_tindakan']??'')?></textarea></div>
  <div class="col-lg-6"><label class="form-label">Bengkel / Penyedia Jasa</label><input name="bengkel" class="form-control" value="<?=e($edit['bengkel']??'')?>"></div>
  <div class="col-md-4"><label class="form-label">Estimasi Biaya (Rp)</label><input type="number" min="0" step="1000" name="estimasi_biaya" class="form-control" value="<?=e($edit['estimasi_biaya']??'')?>"></div>
  <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><?php foreach(['Terjadwal','Dalam Perbaikan','Selesai','Dibatalkan'] as $v): ?><option <?=($edit['status']??'Terjadwal')===$v?'selected':''?>><?=e($v)?></option><?php endforeach; ?></select></div>
  <div class="col-md-4"><label class="form-label">Catatan</label><input name="catatan" class="form-control" value="<?=e($edit['catatan']??'')?>"></div>
  <div class="col-12"><button class="btn btn-primary">Simpan Jadwal</button> <a href="jadwal_perawatan.php" class="btn btn-outline-secondary">Reset</a></div>
 </form>
 </div>
</div>
<div class="card shadow-sm"><div class="card-header bg-white fw-semibold">Daftar Jadwal dan Kerusakan</div><div class="card-body table-responsive">
<table class="table table-hover align-middle"><thead><tr><th>No</th><th>Tanggal</th><th>Berikutnya</th><th>Kendaraan</th><th>Jenis</th><th>Spesifik Kerusakan</th><th>Rencana Tindakan</th><th>Estimasi Biaya</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $i=>$r): $color=['Terjadwal'=>'primary','Dalam Perbaikan'=>'warning','Selesai'=>'success','Dibatalkan'=>'secondary'][$r['status']]??'secondary'; ?>
<tr><td><?=$i+1?></td><td><?=e(date('d-m-Y',strtotime($r['tanggal_jadwal'])))?></td><td><?=!empty($r['tanggal_perawatan_berikutnya'])?e(date('d-m-Y',strtotime($r['tanggal_perawatan_berikutnya']))):'-'?></td><td><strong><?=e($r['nomor_polisi'])?></strong><br><small><?=e($r['merk'].' '.$r['tipe'])?></small><br><small class="text-muted"><?=e($r['nama_dinas'])?></small></td><td><?=e($r['jenis_perawatan'])?></td><td style="min-width:220px;white-space:normal"><?=nl2br(e($r['spesifik_kerusakan']))?></td><td style="min-width:180px;white-space:normal"><?=nl2br(e($r['rencana_tindakan']??'-'))?></td><td><?= $r['estimasi_biaya']!==null?'Rp '.number_format((float)$r['estimasi_biaya'],0,',','.'): '-' ?></td><td><span class="badge text-bg-<?=$color?>"><?=e($r['status'])?></span></td><td class="text-nowrap"><a class="btn btn-sm btn-warning" href="?edit=<?=$r['id']?>">Edit</a> <a class="btn btn-sm btn-danger" href="?hapus=<?=$r['id']?>" onclick="return confirm('Hapus jadwal perawatan ini?')">Hapus</a></td></tr>
<?php endforeach; if(!$rows): ?><tr><td colspan="10" class="text-center text-muted">Belum ada jadwal perawatan.</td></tr><?php endif; ?>
</tbody></table></div></div>
<script>
document.getElementById('kerusakan_id')?.addEventListener('change', function(){
 const opt=this.options[this.selectedIndex];
 if(!this.value) return;
 document.getElementById('detailKerusakan').textContent=opt.dataset.gejala || 'Tidak ada gejala referensi.';
 const tindakan=document.querySelector('[name="rencana_tindakan"]');
 if(tindakan && !tindakan.value) tindakan.value=opt.dataset.tindakan || '';
});
</script>
<?php require 'includes/footer.php'; ?>
