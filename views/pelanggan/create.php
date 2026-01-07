<?php

$page_title = "Tambah Pelanggan";
require_once '../../includes/header.php';
require_once '../../config/database.php';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO pelanggan (nik, nama, alamat, no_telepon, email) VALUES (:nik, :nama, :alamat, :no_telepon, :email)";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(':nik', $_POST['nik']);
    $stmt->bindParam(':nama', $_POST['nama']);
    $stmt->bindParam(':alamat', $_POST['alamat']);
    $stmt->bindParam(':no_telepon', $_POST['no_telepon']);
    $stmt->bindParam(':email', $_POST['email']);
    
    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan'); window.location='index.php';</script>";
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Tambah Pelanggan Baru</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" required maxlength="16">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required text-capitalize>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">Simpan</button>
                            <a href="index.php" class="btn btn-light border px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>