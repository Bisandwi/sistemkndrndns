<?php
require 'config/database.php';require 'includes/auth.php';
if(($_SESSION['role']??'admin')!=='admin'){http_response_code(403);exit('Akses hanya untuk administrator.');}
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $nama=trim($_POST['nama']??'');$username=trim($_POST['username']??'');$password=$_POST['password']??'';
 $role=$_POST['role']??'operator';$dinas=($_POST['perangkat_daerah_id']??'')!==''?(int)$_POST['perangkat_daerah_id']:null;
 if($nama===''||$username===''||strlen($password)<8){$error='Nama, username, dan password minimal 8 karakter wajib diisi.';}
 elseif(!in_array($role,['admin','operator'],true)||($role==='operator'&&!$dinas)){$error='Operator wajib ditautkan ke perangkat daerah.';}
 else{try{$s=$pdo->prepare('INSERT INTO users(nama,username,password,role,perangkat_daerah_id) VALUES(?,?,?,?,?)');$s->execute([$nama,$username,password_hash($password,PASSWORD_DEFAULT),$role,$dinas]);header('Location: pengguna.php');exit;}catch(PDOException $x){$error='Gagal membuat akun. Username mungkin sudah digunakan.';}}
}
$dinases=$pdo->query('SELECT * FROM perangkat_daerah ORDER BY nama_dinas')->fetchAll();
$users=$pdo->query('SELECT u.id,u.nama,u.username,u.role,d.nama_dinas,u.created_at FROM users u LEFT JOIN perangkat_daerah d ON d.id=u.perangkat_daerah_id ORDER BY u.id')->fetchAll();
$pageTitle='Manajemen Pengguna';require 'includes/header.php';
?>
<h3 class="mb-3">Manajemen Akun Pengguna</h3>
<div class="alert alert-warning">Buat akun operator untuk setiap dinas agar mereka dapat masuk dan berkoordinasi melalui ruang chat dinas masing-masing. Password minimal 8 karakter.</div>
<?php if($error):?><div class="alert alert-danger"><?=e($error)?></div><?php endif;?>
<div class="card shadow-sm mb-4"><div class="card-header bg-white fw-semibold">Tambah Akun</div><div class="card-body"><form method="post" class="row g-3">
<div class="col-md-3"><label class="form-label">Nama Pengguna</label><input name="nama" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Username</label><input name="username" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Password</label><input name="password" type="password" minlength="8" class="form-control" required></div>
<div class="col-md-3"><label class="form-label">Role</label><select name="role" id="role" class="form-select"><option value="operator">Operator Dinas</option><option value="admin">Administrator</option></select></div>
<div class="col-md-6"><label class="form-label">Perangkat Daerah (wajib untuk operator)</label><select name="perangkat_daerah_id" class="form-select"><option value="">Tidak ditentukan</option><?php foreach($dinases as $d):?><option value="<?=$d['id']?>"><?=e($d['nama_dinas'])?></option><?php endforeach;?></select></div>
<div class="col-12"><button class="btn btn-primary">Buat Akun</button></div>
</form></div></div>
<div class="card shadow-sm"><div class="card-body table-responsive"><table class="table"><thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Dinas</th><th>Dibuat</th></tr></thead><tbody><?php foreach($users as $u):?><tr><td><?=e($u['nama'])?></td><td><?=e($u['username'])?></td><td><?=e($u['role'])?></td><td><?=e($u['nama_dinas']??'-')?></td><td><?=e($u['created_at'])?></td></tr><?php endforeach;?></tbody></table></div></div>
<?php require 'includes/footer.php'; ?>
