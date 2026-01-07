<?php

$page_title = "Edit Pelanggan";
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'];
$query = "SELECT * FROM pelanggan WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $update = "UPDATE pelanggan SET nik=:nik, nama=:nama, alamat=:alamat, no_telepon=:no_telepon, email=:email WHERE id=:id";
    $stmt_upd = $db->prepare($update);
    $stmt_upd->bindParam(':nik', $_POST['nik']);
    $stmt_upd->bindParam(':nama', $_POST['nama']);
    $stmt_upd->bindParam(':alamat', $_POST['alamat']);
    $stmt_upd->bindParam(':no_telepon', $_POST['no_telepon']);
    $stmt_upd->bindParam(':email', $_POST['email']);
    $stmt_upd->bindParam(':id', $id);
    
    if ($stmt_upd->execute()) {
        echo "<script>alert('Data berhasil diperbarui'); window.location='index.php';</script>";
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Edit Data Pelanggan</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3"><label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="<?php echo $data['nik']; ?>" required>
                        </div>
                        <div class="mb-3"><label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" value="<?php echo $data['nama']; ?>" required>
                        </div>
                        <div class="mb-3"><label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" required><?php echo $data['alamat']; ?></textarea>
                        </div>
                        <div class="mb-3"><label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" value="<?php echo $data['no_telepon']; ?>" required>
                        </div>
                        <div class="mb-3"><label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Data</button>
                        <a href="index.php" class="btn btn-light border">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>