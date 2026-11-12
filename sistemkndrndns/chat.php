<?php
require 'config/database.php'; require 'includes/auth.php';
function e($v){return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
$isAdmin=($_SESSION['role']??'admin')==='admin';
$dinases=$pdo->query('SELECT id,nama_dinas FROM perangkat_daerah ORDER BY nama_dinas')->fetchAll();
$selected=$isAdmin?(int)($_GET['dinas']??$_POST['perangkat_daerah_id']??($dinases[0]['id']??0)):(int)($_SESSION['perangkat_daerah_id']??0);
if(!$isAdmin && !$selected){http_response_code(403);exit('Akun operator belum ditautkan ke perangkat daerah. Hubungi administrator.');}
if($_SERVER['REQUEST_METHOD']==='POST'){
 $pesan=trim($_POST['pesan']??'');
 if($pesan!=='' && mb_strlen($pesan)<=3000){
  $stmt=$pdo->prepare('INSERT INTO chat_dinas(perangkat_daerah_id,user_id,pesan) VALUES(?,?,?)');
  $stmt->execute([$selected,$_SESSION['user_id'],$pesan]);
 }
 header('Location: chat.php?dinas='.$selected);exit;
}
if($isAdmin){$q=$pdo->prepare('SELECT c.*,u.nama FROM chat_dinas c JOIN users u ON u.id=c.user_id WHERE c.perangkat_daerah_id=? ORDER BY c.created_at DESC LIMIT 100');$q->execute([$selected]);}
else{$q=$pdo->prepare('SELECT c.*,u.nama FROM chat_dinas c JOIN users u ON u.id=c.user_id WHERE c.perangkat_daerah_id=? ORDER BY c.created_at DESC LIMIT 100');$q->execute([$selected]);}
$messages=array_reverse($q->fetchAll());
$selectedName='Ruang koordinasi';foreach($dinases as $d){if($d['id']==$selected)$selectedName=$d['nama_dinas'];}
$pageTitle='Chat Koordinasi Dinas';require 'includes/header.php';
?>
<h3 class="mb-3">Chat Koordinasi Antar Perangkat Daerah</h3>
<div class="alert alert-info">Percakapan dikelompokkan per dinas. Administrator dapat memilih ruang dinas; operator hanya dapat mengakses ruang dinas yang ditetapkan pada akunnya.</div>
<div class="card shadow-sm">
 <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
  <strong><?=e($selectedName)?></strong>
  <a class="btn btn-sm btn-outline-secondary" href="chat.php?dinas=<?=$selected?>">Refresh pesan</a>
  <?php if($isAdmin):?><form method="get"><select class="form-select form-select-sm" name="dinas" onchange="this.form.submit()"><?php foreach($dinases as $d):?><option value="<?=$d['id']?>" <?=$selected===$d['id']?'selected':''?>><?=e($d['nama_dinas'])?></option><?php endforeach;?></select></form><?php endif;?>
 </div>
 <div class="card-body" id="chatBox" style="height:430px;overflow-y:auto;background:#f8f9fa">
 <?php if(!$messages):?><p class="text-center text-muted mt-5">Belum ada percakapan. Mulai koordinasi dengan mengirim pesan.</p><?php endif;?>
 <?php foreach($messages as $m):?><div class="mb-3"><div class="d-flex justify-content-between gap-2"><strong><?=e($m['nama'])?></strong><small class="text-muted"><?=e(date('d-m-Y H:i',strtotime($m['created_at'])))?></small></div><div class="bg-white border rounded p-3 mt-1" style="white-space:pre-wrap;overflow-wrap:anywhere"><?=e($m['pesan'])?></div></div><?php endforeach;?>
 </div>
 <div class="card-footer bg-white"><form method="post" action="chat.php?dinas=<?=$selected?>"><input type="hidden" name="perangkat_daerah_id" value="<?=$selected?>"><div class="input-group"><textarea class="form-control" name="pesan" rows="2" maxlength="3000" required placeholder="Tulis pesan koordinasi..."></textarea><button class="btn btn-primary">Kirim</button></div></form></div>
</div>
<script>const box=document.getElementById('chatBox'); if(box) box.scrollTop=box.scrollHeight;</script>
<?php require 'includes/footer.php'; ?>
