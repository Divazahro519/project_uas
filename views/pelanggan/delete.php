<?php

require_once '../../config/database.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "DELETE FROM pelanggan WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    
    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "<script>alert('Gagal menghapus data'); window.location='index.php';</script>";
    }
}
?>