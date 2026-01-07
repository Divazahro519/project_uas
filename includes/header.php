<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /sewa-kendaraan/views/auth/login.php");
    exit();
}

$user_display_name = $_SESSION['nama'] ?? ($_SESSION['username'] ?? 'User');

function checkRole($allowedRoles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
        header("Location: /sewa-kendaraan/index.php");
        exit();
    }
}

$base_url = '/sewa-kendaraan';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewa Motor - <?php echo $page_title ?? 'Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="<?php echo $base_url; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $base_url; ?>/index.php">
                <i class="bi bi-bicycle"></i> Sewa Motor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>/index.php">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="masterDropdown" role="button" data-bs-toggle="dropdown">
                            Master Data
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/views/kendaraan/index.php">Data Motor</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/views/pelanggan/index.php">Data Pelanggan</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="transaksiDropdown" role="button" data-bs-toggle="dropdown">
                            Transaksi
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm">
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/views/transaksi/sewa.php">Penyewaan</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/views/transaksi/pengembalian.php">Pengembalian</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_url; ?>/views/transaksi/index.php">Riwayat</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user_display_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo $base_url; ?>/views/auth/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="fw-bold mb-0"><?php echo $page_title ?? 'Dashboard'; ?></h2>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo $base_url; ?>/index.php" class="text-decoration-none">Home</a></li>
                        <?php if (isset($breadcrumb)): ?>
                            <?php foreach ($breadcrumb as $item): ?>
                                <li class="breadcrumb-item<?php echo $item['active'] ? ' active' : ''; ?>">
                                    <?php if ($item['active']): ?>
                                        <?php echo $item['text']; ?>
                                    <?php else: ?>
                                        <a href="<?php echo $item['link']; ?>" class="text-decoration-none"><?php echo $item['text']; ?></a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>
                </nav>
                <hr class="mt-0 mb-4">
            </div>
        </div>