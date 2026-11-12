<?php
require 'config/database.php';require 'includes/auth.php';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $kid=(int)($_POST['kendaraan_id']??0);$jenis=$_POST['jenis_peristiwa']??'';$tanggal=$_POST['tanggal_peristiwa']??date('Y-m-d');
 $catatan=trim($_POST['catatan']??'');$tujuanDinas=($_POST['perangkat_daerah_tujuan_id']??'')!==''?(int)$_POST['perangkat_daerah_tujuan_id']:null;
 $tujuanPegawai=($_POST['pegawai_tujuan_id']??'')!==''?(int)$_POST['pegawai_tujuan_id']:null;
 $allowed=['PENYERAHAN','PENGEMBALIAN','PINDAH_TANGAN','PEMELIHARAAN','PERUBAHAN_PENANGGUNG_JAWAB'];
 if(!in_array($jenis,$allowed,true))$error='Jenis peristiwa tidak valid.';
 else{
  $s=$pdo->prepare('SELECT * FROM kendaraan WHERE id=?');$s->execute([$kid]);$k=$s->fetch();
  if(!$k)$error='Kendaraan tidak ditemukan.';
  elseif(in_array($jenis,['PENYERAHAN','PINDAH_TANGAN'])&&!$tujuanDinas)$error='Pilih perangkat daerah tujuan.';
  else{
   try{
    $pdo->beginTransaction();
    $stmt=$pdo->prepare('INSERT INTO riwayat_kendaraan(kendaraan_id,jenis_peristiwa,tanggal_peristiwa,perangkat_daerah_asal_id,perangkat_daerah_tujuan_id,pegawai_asal_id,pegawai_tujuan_id,catatan,dibuat_oleh) VALUES(?,?,?,?,?,?,?,?,?)');
    $stmt->execute([$kid,$jenis,$tanggal,$k['perangkat_daerah_id'],$tujuanDinas??$k['perangkat_daerah_id'],$k['pegawai_id'],$tujuanPegawai,$catatan,$_SESSION['user_id']]);
    if($jenis==='PENGEMBALIAN'){
      $u=$pdo->prepare('UPDATE kendaraan SET pegawai_id=NULL WHERE id=?');$u->execute([$kid]);
    }elseif(in_array($jenis,['PENYERAHAN','PINDAH_TANGAN','PERUBAHAN_PENANGGUNG_JAWAB'],true)){
      $newDinas=$tujuanDinas??$k['perangkat_daerah_id'];
      $u=$pdo->prepare('UPDATE kendaraan SET perangkat_daerah_id=?,pegawai_id=? WHERE id=?');$u->execute([$newDinas,$tujuanPegawai,$kid]);
    }
    $pdo->commit();header('Location: riwayat.php?sukses=1');exit;
   }catch(Throwable $x){if($pdo->inTransaction())$pdo->rollBack();$error='Gagal menyimpan riwayat. Periksa kembali data tujuan.';}
  }
 }
}
$vehicles=$pdo->query('SELECT id,nomor_polisi,merk,tipe FROM kendaraan ORDER BY nomor_polisi')->fetchAll();
$dinases=$pdo->query('SELECT * FROM perangkat_daerah ORDER BY nama_dinas')->fetchAll();
$pegawai=$pdo->query('SELECT id,nama_pegawai,nip,perangkat_daerah_id FROM pegawai ORDER BY nama_pegawai')->fetchAll();
$rows=$pdo->query("SELECT r.*,k.nomor_polisi,k.merk,k.tipe,da.nama_dinas asal,dj.nama_dinas tujuan,pa.nama_pegawai pegawai_asal,pj.nama_pegawai pegawai_tujuan,u.nama pencatat FROM riwayat_kendaraan r JOIN kendaraan k ON k.id=r.kendaraan_id LEFT JOIN perangkat_daerah da ON da.id=r.perangkat_daerah_asal_id LEFT JOIN perangkat_daerah dj ON dj.id=r.perangkat_daerah_tujuan_id LEFT JOIN pegawai pa ON pa.id=r.pegawai_asal_id LEFT JOIN pegawai pj ON pj.id=r.pegawai_tujuan_id LEFT JOIN users u ON u.id=r.dibuat_oleh ORDER BY r.tanggal_peristiwa DESC,r.id DESC")->fetchAll();
$pageTitle='Riwayat Kendaraan';require 'includes/header.php';
?>
<h3 class="mb-3">Riwayat Penyerahan, Pengembalian & Pindah Tangan</h3>
<div class="alert alert-info">Setiap peristiwa dicatat sebagai histori. Pengembalian akan mengosongkan penanggung jawab aktif; pindah tangan memperbarui perangkat daerah dan/atau pegawai pada data kendaraan.</div>
<?php if($error):?><div class="alert alert-danger"><?=e($error)?></div><?php endif;?><?php if(isset($_GET['sukses'])):?><div class="alert alert-success">Riwayat berhasil disimpan dan data terkini kendaraan diperbarui.</div><?php endif;?>
<div class="card shadow-sm mb-4"><div class="card-header bg-white fw-semibold">Catat Peristiwa Kendaraan</div><div class="card-body"><form method="post" class="row g-3">
<div class="col-md-4"><label class="form-label">Kendaraan</label><select name="kendaraan_id" class="form-select" required><option value="">Pilih kendaraan</option><?php foreach($vehicles as $v):?><option value="<?=$v['id']?>"><?=e($v['nomor_polisi'].' - '.$v['merk'].' '.$v['tipe'])?></option><?php endforeach;?></select></div>
<div class="col-md-3"><label class="form-label">Jenis Peristiwa</label><select name="jenis_peristiwa" class="form-select" required><option value="PENYERAHAN">Penyerahan</option><option value="PENGEMBALIAN">Pengembalian</option><option value="PINDAH_TANGAN">Pindah Tangan</option><option value="PERUBAHAN_PENANGGUNG_JAWAB">Ganti Penanggung Jawab</option><option value="PEMELIHARAAN">Pemeliharaan (catatan saja)</option></select></div>
<div class="col-md-2"><label class="form-label">Tanggal Peristiwa</label><input type="date" name="tanggal_peristiwa" class="form-control" value="<?=date('Y-m-d')?>" required></div>
<div class="col-md-3"><label class="form-label">Dinas Tujuan</label><select name="perangkat_daerah_tujuan_id" class="form-select"><option value="">Tidak berubah / pilih jika perlu</option><?php foreach($dinases as $d):?><option value="<?=$d['id']?>"><?=e($d['nama_dinas'])?></option><?php endforeach;?></select></div>
<div class="col-md-6"><label class="form-label">Pegawai Penerima / Penanggung Jawab Baru</label><select name="pegawai_tujuan_id" class="form-select"><option value="">Tidak ada / belum ditentukan</option><?php foreach($pegawai as $p):?><option value="<?=$p['id']?>"><?=e($p['nama_pegawai'].' - '.$p['nip'])?></option><?php endforeach;?></select></div>
<div class="col-md-6"><label class="form-label">Catatan / Nomor Berita Acara</label><input name="catatan" class="form-control" placeholder="Nomor BA, kondisi saat serah terima, alasan mutasi, dll."></div>
<div class="col-12"><button class="btn btn-primary">Simpan Peristiwa & Perbarui Data</button></div>
</form></div></div>
<div class="card shadow-sm"><div class="card-header bg-white fw-semibold">Histori Kendaraan</div><div class="card-body table-responsive"><table class="table table-sm table-hover align-middle"><thead><tr><th>Tanggal</th><th>Nomor Polisi</th><th>Peristiwa</th><th>Dari Dinas</th><th>Ke Dinas</th><th>Pegawai Lama</th><th>Pegawai Baru</th><th>Catatan</th><th>Pencatat</th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=e($r['tanggal_peristiwa'])?></td><td><?=e($r['nomor_polisi'])?></td><td><?=e(str_replace('_',' ',$r['jenis_peristiwa']))?></td><td><?=e($r['asal']??'-')?></td><td><?=e($r['tujuan']??'-')?></td><td><?=e($r['pegawai_asal']??'-')?></td><td><?=e($r['pegawai_tujuan']??'-')?></td><td><?=e($r['catatan'])?></td><td><?=e($r['pencatat']??'-')?></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="9" class="text-center text-muted">Belum ada histori kendaraan.</td></tr><?php endif;?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
