<?php
require 'config/database.php'; require 'includes/auth.php';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
if(isset($_GET['hapus'])){$s=$pdo->prepare('DELETE FROM pegawai WHERE id=?');$s->execute([(int)$_GET['hapus']]);header('Location: pegawai.php');exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $id=(int)($_POST['id']??0);
 $status=in_array(($_POST['status_pegawai']??'Aktif'),['Aktif','Dicopot'],true)?$_POST['status_pegawai']:'Aktif';
 $d=[trim($_POST['nama_pegawai']),trim($_POST['nip']),trim($_POST['jabatan']),trim($_POST['golongan']),(int)$_POST['perangkat_daerah_id'],($_POST['tanggal_pensiun']??'')?:null,$status];
 if($id){$d[]=$id;$sql='UPDATE pegawai SET nama_pegawai=?,nip=?,jabatan=?,golongan=?,perangkat_daerah_id=?,tanggal_pensiun=?,status_pegawai=? WHERE id=?';}
 else{$sql='INSERT INTO pegawai(nama_pegawai,nip,jabatan,golongan,perangkat_daerah_id,tanggal_pensiun,status_pegawai) VALUES(?,?,?,?,?,?,?)';}
 try{$pdo->prepare($sql)->execute($d);header('Location: pegawai.php');exit;}catch(PDOException $x){$error='Gagal menyimpan. NIP harus unik.';}
}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM pegawai WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$dinases=$pdo->query('SELECT * FROM perangkat_daerah ORDER BY nama_dinas')->fetchAll();
$rows=$pdo->query('SELECT p.*,d.nama_dinas FROM pegawai p JOIN perangkat_daerah d ON d.id=p.perangkat_daerah_id ORDER BY p.nama_pegawai')->fetchAll();
$pageTitle='Data Pegawai';require 'includes/header.php';
?>
<h3 class="mb-3">Data Pegawai Negeri Sipil</h3>
<div class="alert alert-info">Tanggal pensiun digunakan untuk memunculkan pengingat. Pastikan tanggal diisi sesuai data kepegawaian resmi.</div>
<?php if(!empty($error)):?><div class="alert alert-danger"><?=e($error)?></div><?php endif;?>
<div class="card shadow-sm mb-4"><div class="card-header bg-white fw-semibold"><?= $edit?'Edit Pegawai':'Tambah Pegawai'?></div><div class="card-body"><form method="post" class="row g-3"><input type="hidden" name="id" value="<?=e($edit['id']??'')?>">
<div class="col-md-6"><label class="form-label">Nama Pegawai</label><input class="form-control" name="nama_pegawai" required value="<?=e($edit['nama_pegawai']??'')?>"></div>
<div class="col-md-6"><label class="form-label">NIP</label><input class="form-control" name="nip" required value="<?=e($edit['nip']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Jabatan</label><input class="form-control" name="jabatan" required value="<?=e($edit['jabatan']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Golongan</label><input class="form-control" name="golongan" required placeholder="Contoh: III/a" value="<?=e($edit['golongan']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Tanggal Pensiun</label><input type="date" class="form-control" name="tanggal_pensiun" value="<?=e($edit['tanggal_pensiun']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Status Pegawai</label><select class="form-select" name="status_pegawai"><option value="Aktif" <?=($edit['status_pegawai']??'Aktif')==='Aktif'?'selected':''?>>Aktif</option><option value="Dicopot" <?=($edit['status_pegawai']??'Aktif')==='Dicopot'?'selected':''?>>Dicopot</option></select></div>
<div class="col-md-6"><label class="form-label">Perangkat Daerah</label><select class="form-select" name="perangkat_daerah_id" required><option value="">Pilih dinas</option><?php foreach($dinases as $d):?><option value="<?=$d['id']?>" <?=($edit['perangkat_daerah_id']??'')==$d['id']?'selected':''?>><?=e($d['nama_dinas'])?></option><?php endforeach;?></select></div>
<div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-secondary" href="pegawai.php">Reset</a></div></form></div></div>
<div class="card shadow-sm"><div class="card-body table-responsive"><table class="table table-hover"><thead><tr><th>No</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Golongan</th><th>Dinas</th><th>Tanggal Pensiun</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $i=>$r):?><tr><td><?=$i+1?></td><td><?=e($r['nama_pegawai'])?></td><td><?=e($r['nip'])?></td><td><?=e($r['jabatan'])?></td><td><?=e($r['golongan'])?></td><td><?=e($r['nama_dinas'])?></td><td><?=e($r['tanggal_pensiun']??'-')?></td><td><span class="badge bg-<?=($r['status_pegawai']??'Aktif')==='Aktif'?'success':'secondary'?>"><?=e($r['status_pegawai']??'Aktif')?></span></td><td class="text-nowrap"><a class="btn btn-sm btn-warning" href="?edit=<?=$r['id']?>">Edit</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Hapus pegawai?')" href="?hapus=<?=$r['id']?>">Hapus</a></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="9" class="text-center text-muted">Belum ada data.</td></tr><?php endif;?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
