<?php

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit();
}

$query = "SELECT t.*, k.harga_sewa FROM transaksi t 
          JOIN kendaraan k ON t.kendaraan_id = k.id 
          WHERE t.id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $tgl_kembali_seharusnya = new DateTime($data['tanggal_kembali']);
    $tgl_kembali_aktual = new DateTime(date('Y-m-d'));
    
    $denda = 0;
    if ($tgl_kembali_aktual > $tgl_kembali_seharusnya) {
        $selisih = $tgl_kembali_aktual->diff($tgl_kembali_seharusnya)->days;
        $denda = $selisih * ($data['harga_sewa'] * 0.5); 
    }

    try {
        $db->beginTransaction();
        $update = "UPDATE transaksi SET 
                   tanggal_kembali_aktual = ?, 
                   denda = ?, 
                   status = 'selesai' 
                   WHERE id = ?";
        $db->prepare($update)->execute([date('Y-m-d'), $denda, $id]);
        $db->prepare("UPDATE kendaraan SET status = 'tersedia' WHERE id = ?")
           ->execute([$data['kendaraan_id']]);
        $db->commit();
        $_SESSION['success'] = "Pengembalian berhasil diproses. " . ($denda > 0 ? "Denda: Rp " . number_format($denda, 0, ',', '.') : "Tidak ada denda.");

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Gagal memproses pengembalian: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Data transaksi tidak ditemukan.";
}

header("Location: index.php");
exit();
?>