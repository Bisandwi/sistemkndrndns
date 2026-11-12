<?php
session_start();
require 'config/database.php';
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $u = $stmt->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $u['id']; $_SESSION['nama'] = $u['nama'];
        $_SESSION['role'] = $u['role'] ?? 'admin';
        $_SESSION['perangkat_daerah_id'] = $u['perangkat_daerah_id'] ?? null;
        header('Location: index.php'); exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login - Kendaraan Dinas</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container"><div class="row justify-content-center align-items-center min-vh-100"><div class="col-md-4">
<div class="card shadow-sm"><div class="card-body p-4 text-center"><img src="assets/logo-bpad.svg" alt="Logo BPAD Jakarta" class="img-fluid mb-3" style="max-height:90px"><h4 class="mb-3">Sistem Kendaraan Dinas</h4><p class="text-muted small">Badan Pengelolaan Aset Daerah<br>Provinsi DKI Jakarta</p><hr><h5 class="mb-3">Login Administrator</h5>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post"><div class="mb-3"><label class="form-label">Username</label><input class="form-control" name="username" required autofocus></div>
<div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
<button class="btn btn-primary w-100">Masuk</button></form>
<p class="text-muted small mt-3 mb-0">Login awal: admin / password</p></div></div></div></div></div></body></html>
