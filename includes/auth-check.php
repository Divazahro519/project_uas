<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /sewa-kendaraan/views/auth/login.php");
    exit();
}

function checkRole($allowedRoles) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
        header("Location: /sewa-kendaraan/index.php");
        exit();
    }
}
?>