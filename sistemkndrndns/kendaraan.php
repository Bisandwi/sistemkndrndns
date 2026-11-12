<?php
require 'config/database.php'; require 'includes/auth.php';
function e($v){return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');}
$edit = null;
if (isset($_GET['hapus'])) { $s=$pdo->prepare('DELETE FROM kendaraan WHERE id=?'); $s->execute([(int)$_GET['hapus']]); header('Location: kendaraan.php'); exit; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $id=(int)($_POST['id']??0);
 $data=[trim($_POST['nomor_polisi']),trim($_POST['merk']),trim($_POST['tipe']),(int)$_POST['tahun_produksi'],trim($_POST['warna']),$_POST['kondisi'],$_POST['status_kendaraan'],(int)$_POST['perangkat_daerah_id'],($_POST['pegawai_id']??'')!==''?(int)$_POST['pegawai_id']:null,
 $_POST['kategori_kendaraan'],($_POST['tanggal_diterima']??'')?:null,(int)($_POST['masa_pakai_tahun']??7),($_POST['tanggal_akhir_masa_pakai']??'')?:null];
 if($id){$data[]=$id;$sql='UPDATE kendaraan SET nomor_polisi=?,merk=?,tipe=?,tahun_produksi=?,warna=?,kondisi=?,status_kendaraan=?,perangkat_daerah_id=?,pegawai_id=?,kategori_kendaraan=?,tanggal_diterima=?,masa_pakai_tahun=?,tanggal_akhir_masa_pakai=? WHERE id=?';}
 else{$sql='INSERT INTO kendaraan(nomor_polisi,merk,tipe,tahun_produksi,warna,kondisi,status_kendaraan,perangkat_daerah_id,pegawai_id,kategori_kendaraan,tanggal_diterima,masa_pakai_tahun,tanggal_akhir_masa_pakai) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)';}
 try{$pdo->prepare($sql)->execute($data);header('Location: kendaraan.php');exit;}catch(PDOException $ex){$error='Gagal menyimpan. Pastikan nomor polisi belum digunakan dan data relasi valid.';}
}
if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM kendaraan WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$dinases=$pdo->query('SELECT * FROM perangkat_daerah ORDER BY nama_dinas')->fetchAll();
$pegawais=$pdo->query('SELECT p.*,d.nama_dinas FROM pegawai p JOIN perangkat_daerah d ON d.id=p.perangkat_daerah_id ORDER BY p.nama_pegawai')->fetchAll();
$filter = $_GET['filter'] ?? '';
$allowedFilters = [
 'perhatian' => "k.kondisi IN ('Rusak Ringan','Rusak Berat')",
 'aktif' => "k.status_kendaraan='Aktif'",
 'tidak_aktif' => "k.status_kendaraan='Tidak Aktif'",
 'baik' => "k.kondisi='Baik'",
 'rusak_ringan' => "k.kondisi='Rusak Ringan'",
 'rusak_berat' => "k.kondisi='Rusak Berat'"
];
$where = isset($allowedFilters[$filter]) ? ' WHERE '.$allowedFilters[$filter] : '';
$rows=$pdo->query('SELECT k.*,d.nama_dinas,p.nama_pegawai FROM kendaraan k JOIN perangkat_daerah d ON d.id=k.perangkat_daerah_id LEFT JOIN pegawai p ON p.id=k.pegawai_id'.$where.' ORDER BY k.id DESC')->fetchAll();
$pageTitle='Data Kendaraan'; require 'includes/header.php';
?>
<h3 class="mb-3">Data Kendaraan Dinas Operasional</h3>
<?php if($filter && isset($allowedFilters[$filter])): ?><div class="alert alert-info d-flex justify-content-between align-items-center"><span>Filter aktif: <strong><?=e(['perhatian'=>'Kendaraan Perlu Perhatian','aktif'=>'Kendaraan Aktif','tidak_aktif'=>'Kendaraan Tidak Aktif','baik'=>'Kondisi Baik','rusak_ringan'=>'Rusak Ringan','rusak_berat'=>'Rusak Berat'][$filter])?></strong> — <?=count($rows)?> data</span><a class="btn btn-sm btn-outline-primary" href="kendaraan.php">Tampilkan Semua</a></div><?php endif; ?>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?=e($error)?></div><?php endif; ?>
<div class="card shadow-sm mb-4"><div class="card-header bg-white fw-semibold"><?= $edit?'Edit Kendaraan':'Tambah Kendaraan' ?></div><div class="card-body">
<form method="post" class="row g-3"><input type="hidden" name="id" value="<?=e($edit['id']??'')?>">
<div class="col-md-4"><label class="form-label">Nomor Polisi</label><input class="form-control" name="nomor_polisi" required value="<?=e($edit['nomor_polisi']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Merk Kendaraan</label><input class="form-control" name="merk" required value="<?=e($edit['merk']??'')?>"></div>
<div class="col-md-4"><label class="form-label">Tipe Kendaraan</label><input class="form-control" name="tipe" required value="<?=e($edit['tipe']??'')?>"></div>
<div class="col-md-3"><label class="form-label">Tahun Produksi</label><input type="number" min="1901" max="2155" class="form-control" name="tahun_produksi" required value="<?=e($edit['tahun_produksi']??date('Y'))?>"></div>
<div class="col-md-3"><label class="form-label">Warna</label><input class="form-control" name="warna" required value="<?=e($edit['warna']??'')?>"></div>
<div class="col-md-3"><label class="form-label">Kondisi</label><select class="form-select" name="kondisi"><?php foreach(['Baik','Rusak Ringan','Rusak Berat'] as $v):?><option <?=($edit['kondisi']??'Baik')===$v?'selected':''?>><?=e($v)?></option><?php endforeach;?></select></div>
<div class="col-md-3"><label class="form-label">Status Kendaraan</label><select class="form-select" name="status_kendaraan"><?php foreach(['Aktif','Tidak Aktif'] as $v):?><option <?=($edit['status_kendaraan']??'Aktif')===$v?'selected':''?>><?=e($v)?></option><?php endforeach;?></select></div>
<div class="col-md-6"><label class="form-label">Perangkat Daerah</label><select class="form-select" name="perangkat_daerah_id" required><option value="">Pilih dinas</option><?php foreach($dinases as $d):?><option value="<?=$d['id']?>" <?=($edit['perangkat_daerah_id']??'')==$d['id']?'selected':''?>><?=e($d['nama_dinas'])?></option><?php endforeach;?></select></div>
<div class="col-md-6"><label class="form-label">Pegawai Penanggung Jawab (opsional)</label><select class="form-select" name="pegawai_id"><option value="">Belum ditentukan</option><?php foreach($pegawais as $p):?><option value="<?=$p['id']?>" <?=($edit['pegawai_id']??'')==$p['id']?'selected':''?>><?=e($p['nama_pegawai'].' - '.$p['nama_dinas'])?></option><?php endforeach;?></select></div>
<div class="col-md-3"><label class="form-label">Kategori Kendaraan</label><select class="form-select" name="kategori_kendaraan"><option value="Non-Niaga" <?=in_array(($edit['kategori_kendaraan']??'Non-Niaga'),['Non-Niaga','Pribadi','Non Pribadi'],true)?'selected':''?>>Non-Niaga (Operasional)</option><option value="Niaga" <?=($edit['kategori_kendaraan']??'')==='Niaga'?'selected':''?>>Niaga (Angkutan Barang)</option></select><div class="form-text">Non-Niaga: sedan, SUV, MPV, pick up, blind van. Niaga: truk angkutan barang dan kendaraan niaga berat.</div></div>
<div class="col-md-3"><label class="form-label">Tanggal Diterima / Mulai Dikelola</label><input type="date" class="form-control" name="tanggal_diterima" value="<?=e($edit['tanggal_diterima']??'')?>"></div>
<div class="col-md-3"><label class="form-label">Masa Pakai (tahun)</label><input type="number" min="1" max="100" class="form-control" name="masa_pakai_tahun" value="<?=e($edit['masa_pakai_tahun']??7)?>"></div>
<div class="col-md-3"><label class="form-label">Tanggal Akhir Masa Pakai</label><input type="date" class="form-control" name="tanggal_akhir_masa_pakai" value="<?=e($edit['tanggal_akhir_masa_pakai']??'')?>"></div>
<div class="col-12"><button class="btn btn-primary">Simpan</button> <a class="btn btn-secondary" href="kendaraan.php">Reset</a></div></form></div></div>
<div class="card shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>No</th><th>Nomor Polisi</th><th>Merk / Tipe</th><th>Tahun</th><th>Warna</th><th>Kategori</th><th>Lama Dikelola</th><th>Kondisi</th><th>Status</th><th>Dinas</th><th>Pegawai</th><th>Aksi</th></tr></thead><tbody>
<?php foreach($rows as $i=>$r):?><tr><td><?=$i+1?></td><td><?=e($r['nomor_polisi'])?></td><td><?=e($r['merk'].' / '.$r['tipe'])?></td><td><?=e($r['tahun_produksi'])?></td><td><?=e($r['warna'])?></td><td><?=e(in_array(($r['kategori_kendaraan']??''),['Pribadi','Non Pribadi'],true)?'Non-Niaga':($r['kategori_kendaraan']??'Non-Niaga'))?></td><td><?php if(!empty($r['tanggal_diterima'])) { $mulai=new DateTime($r['tanggal_diterima']); $now=new DateTime(); echo $mulai->diff($now)->y.' th '.$mulai->diff($now)->m.' bln'; } else { echo '-'; } ?></td><td><?=e($r['kondisi'])?></td><td><?=e($r['status_kendaraan'])?></td><td><?=e($r['nama_dinas'])?></td><td><?=e($r['nama_pegawai']??'-')?></td><td class="text-nowrap"><a class="btn btn-sm btn-warning" href="?edit=<?=$r['id']?>">Edit</a> <a class="btn btn-sm btn-danger" onclick="return confirm('Hapus data kendaraan?')" href="?hapus=<?=$r['id']?>">Hapus</a></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="12" class="text-center text-muted">Belum ada data.</td></tr><?php endif;?></tbody></table></div></div></div>
<?php require 'includes/footer.php'; ?>
