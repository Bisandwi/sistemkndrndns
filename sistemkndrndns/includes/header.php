<?php
require_once __DIR__ . '/auth.php';
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$isAdmin = (($_SESSION['role'] ?? 'admin') === 'admin');
function navActive(array $pages, string $current): string { return in_array($current, $pages, true) ? ' active' : ''; }
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($pageTitle ?? 'Sistem Kendaraan Dinas') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root{--navy:#123b68;--navy2:#0d2d50;--accent:#e8f1fb;--ink:#243447}
body{background:#f4f7fb;color:var(--ink);font-family:system-ui,-apple-system,"Segoe UI",sans-serif}
.app-navbar{background:linear-gradient(110deg,var(--navy2),var(--navy));box-shadow:0 3px 14px rgba(13,45,80,.16);padding:.65rem 0}
.navbar-brand{font-size:1rem;letter-spacing:.1px;white-space:normal;max-width:260px}
.navbar-brand img{height:40px;width:auto;background:#fff;border-radius:8px;padding:3px 7px}
.app-navbar .nav-link{color:rgba(255,255,255,.82);font-size:.92rem;font-weight:500;padding:.6rem .8rem!important;border-radius:8px;margin:0 .12rem}
.app-navbar .nav-link:hover,.app-navbar .nav-link.active,.app-navbar .show>.nav-link{color:#fff;background:rgba(255,255,255,.15)}
.app-navbar .dropdown-menu{border:0;border-radius:12px;padding:.45rem;box-shadow:0 12px 28px rgba(20,40,70,.18);min-width:210px}
.app-navbar .dropdown-item{border-radius:8px;padding:.58rem .75rem;font-size:.9rem}
.app-navbar .dropdown-item:hover,.app-navbar .dropdown-item.active{background:var(--accent);color:var(--navy);font-weight:600}
.user-chip{display:flex;align-items:center;gap:.5rem;color:#fff;font-size:.88rem;padding:.35rem .65rem;border:1px solid rgba(255,255,255,.22);border-radius:999px;max-width:180px}
.user-chip span:last-child{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
main.container{min-height:calc(100vh - 100px);padding-top:1.6rem!important;padding-bottom:2rem!important}
@media(max-width:1199.98px){.app-navbar .navbar-collapse{padding-top:.8rem}.app-navbar .nav-link{margin:.1rem 0}.user-area{padding-top:.6rem;display:flex;align-items:center;gap:.65rem;flex-wrap:wrap}}
</style>
</head>
<body>
<nav class="navbar navbar-expand-xl navbar-dark app-navbar">
 <div class="container-fluid px-3 px-lg-4">
  <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
   <img src="assets/logo-bpad.svg" alt="Logo BPAD Jakarta">
   <span class="fw-bold">Sistem Kendaraan Dinas</span>
  </a>
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Buka menu"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse" id="mainNavigation">
   <ul class="navbar-nav me-auto ms-xl-3">
   <?php if ($isAdmin): ?>
    <li class="nav-item"><a class="nav-link<?=navActive(['index.php'],$currentPage)?>" href="index.php">Dashboard</a></li>
    <li class="nav-item dropdown">
     <a class="nav-link dropdown-toggle<?=navActive(['kendaraan.php','pegawai.php','perangkat_daerah.php','pengguna.php'],$currentPage)?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Data Master</a>
     <ul class="dropdown-menu">
      <li><a class="dropdown-item<?=navActive(['kendaraan.php'],$currentPage)?>" href="kendaraan.php">Data Kendaraan</a></li>
      <li><a class="dropdown-item<?=navActive(['pegawai.php'],$currentPage)?>" href="pegawai.php">Data Pegawai</a></li>
      <li><a class="dropdown-item<?=navActive(['perangkat_daerah.php'],$currentPage)?>" href="perangkat_daerah.php">Perangkat Daerah</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item<?=navActive(['pengguna.php'],$currentPage)?>" href="pengguna.php">Manajemen Pengguna</a></li>
     </ul>
    </li>
    <li class="nav-item dropdown">
     <a class="nav-link dropdown-toggle<?=navActive(['riwayat.php','chat.php','jadwal_perawatan.php'],$currentPage)?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Operasional</a>
     <ul class="dropdown-menu">
      <li><a class="dropdown-item<?=navActive(['riwayat.php'],$currentPage)?>" href="riwayat.php">Riwayat Kendaraan</a></li>
      <li><a class="dropdown-item<?=navActive(['chat.php'],$currentPage)?>" href="chat.php">Chat Koordinasi Dinas</a></li>
     </ul>
    </li>
    <li class="nav-item"><a class="nav-link<?=navActive(['monitoring.php'],$currentPage)?>" href="monitoring.php">Monitoring</a></li>
    <li class="nav-item"><a class="nav-link<?=navActive(['laporan.php'],$currentPage)?>" href="laporan.php">Laporan</a></li>
   <?php else: ?>
    <li class="nav-item"><a class="nav-link<?=navActive(['chat.php'],$currentPage)?>" href="chat.php">Chat Koordinasi Dinas</a></li>
   <?php endif; ?>
   </ul>
   <div class="user-area d-flex align-items-center gap-2">
    <div class="user-chip"><span aria-hidden="true">●</span><span><?=htmlspecialchars($_SESSION['nama'] ?? 'Pengguna')?></span></div>
    <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="logout.php">Keluar</a>
   </div>
  </div>
 </div>
</nav>
<main class="container-fluid px-3 px-lg-4">
