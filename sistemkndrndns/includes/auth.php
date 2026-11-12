<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Operator hanya diberi akses ke ruang chat dinas yang ditetapkan.
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
if (($_SESSION['role'] ?? 'admin') === 'operator' && !in_array($currentPage, ['chat.php', 'logout.php'], true)) {
    header('Location: chat.php');
    exit;
}
