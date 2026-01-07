<?php

session_start();
require_once '../../config/database.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $checkQuery = "SELECT COUNT(*) as count FROM transaksi WHERE kendaraan_id = :id AND status = 'berlangsung'";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            $_SESSION['error'] = "Kendaraan sedang disewa, tidak dapat dihapus!";
        } else {
            $getImg = $db->prepare("SELECT gambar FROM kendaraan WHERE id = :id");
            $getImg->bindParam(':id', $id);
            $getImg->execute();
            $imgData = $getImg->fetch(PDO::FETCH_ASSOC);

            $query = "DELETE FROM kendaraan WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                if ($imgData && $imgData['gambar'] != 'default.jpg') {
                    $path = '../../assets/img/' . $imgData['gambar'];
                    if (file_exists($path)) { unlink($path); }
                }
                $_SESSION['success'] = "Data kendaraan berhasil dihapus!";
            } else {
                $_SESSION['error'] = "Gagal menghapus data kendaraan!";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
}

header("Location: index.php");
exit();
?>