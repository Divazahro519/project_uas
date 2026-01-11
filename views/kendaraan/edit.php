<?php
// 1. PINDAHKAN SEMUA LOGIKA PHP KE PALING ATAS
ob_start();
session_start();

require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'] ?? 0;

// Ambil data kendaraan lama
$query = "SELECT * FROM kendaraan WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$kendaraan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kendaraan) {
    header("Location: index.php");
    exit();
}

// Proses jika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $plat_nomor = strtoupper(trim($_POST['plat_nomor']));
    $tahun_produksi = $_POST['tahun_produksi']; 
    $kapasitas = $_POST['kapasitas']; 
    $harga_sewa = $_POST['harga_sewa']; 
    $status = $_POST['status'];
    
    // Cek duplikat plat nomor (kecuali untuk ID ini sendiri)
    $check_query = "SELECT id FROM kendaraan WHERE plat_nomor = :plat_nomor AND id != :id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':plat_nomor', $plat_nomor);
    $check_stmt->bindParam(':id', $id);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $error = "Nomor plat <strong>$plat_nomor</strong> sudah digunakan oleh kendaraan lain!";
    } else {
        $gambar = $kendaraan['gambar']; // Default gunakan gambar lama

        // Proses Upload Gambar Baru jika ada
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_name_raw = $_FILES['gambar']['name'];
            $file_ext = strtolower(pathinfo($file_name_raw, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_ext, $allowed_ext) && $_FILES['gambar']['size'] < 2097152) {
                $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_dir = '../../assets/img/';
                
                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    // Hapus gambar lama jika bukan default
                    if ($kendaraan['gambar'] != 'default.jpg' && file_exists($upload_dir . $kendaraan['gambar'])) {
                        unlink($upload_dir . $kendaraan['gambar']);
                    }
                    $gambar = $new_file_name;
                }
            } else {
                $error = "Format gambar tidak valid atau ukuran terlalu besar (max 2MB)!";
            }
        }
        
        if (!isset($error)) {
            $query_update = "UPDATE kendaraan SET 
                             jenis = :jenis, merk = :merk, model = :model, plat_nomor = :plat_nomor, 
                             tahun_produksi = :tahun_produksi, kapasitas = :kapasitas, 
                             harga_sewa = :harga_sewa, gambar = :gambar, status = :status 
                             WHERE id = :id";
            
            $stmt_upd = $db->prepare($query_update);
            $stmt_upd->bindParam(':jenis', $jenis);
            $stmt_upd->bindParam(':merk', $merk);
            $stmt_upd->bindParam(':model', $model);
            $stmt_upd->bindParam(':plat_nomor', $plat_nomor);
            $stmt_upd->bindParam(':tahun_produksi', $tahun_produksi);
            $stmt_upd->bindParam(':kapasitas', $kapasitas);
            $stmt_upd->bindParam(':harga_sewa', $harga_sewa);
            $stmt_upd->bindParam(':gambar', $gambar);
            $stmt_upd->bindParam(':status', $status);
            $stmt_upd->bindParam(':id', $id);
            
            if ($stmt_upd->execute()) {
                $_SESSION['success'] = "Data kendaraan berhasil diperbarui!";
                header("Location: index.php");
                exit();
            }
        }
    }
}

// 2. BARU TAMPILKAN HEADER DAN HTML
$page_title = "Edit Kendaraan";
$breadcrumb = [
    ['text' => 'Master Data', 'link' => '#', 'active' => false],
    ['text' => 'Kendaraan', 'link' => 'index.php', 'active' => false],
    ['text' => 'Edit', 'link' => '', 'active' => true]
];
require_once '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h4 class="mb-0 fw-bold">Edit Data Kendaraan</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">JENIS KENDARAAN</label>
                                <select class="form-select bg-light border-0" name="jenis" required>
                                    <option value="bus" <?php echo $kendaraan['jenis'] == 'bus' ? 'selected' : ''; ?>>Bus</option>
                                    <option value="minibus" <?php echo $kendaraan['jenis'] == 'minibus' ? 'selected' : ''; ?>>Minibus</option>
                                    <option value="motor" <?php echo $kendaraan['jenis'] == 'motor' ? 'selected' : ''; ?>>Motor</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">NOMOR PLAT</label>
                                <input type="text" class="form-control bg-light border-0" name="plat_nomor" value="<?php echo htmlspecialchars($kendaraan['plat_nomor']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">MERK</label>
                                <input type="text" class="form-control bg-light border-0" name="merk" value="<?php echo htmlspecialchars($kendaraan['merk']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">MODEL</label>
                                <input type="text" class="form-control bg-light border-0" name="model" value="<?php echo htmlspecialchars($kendaraan['model']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted small fw-bold">TAHUN</label>
                                <input type="number" class="form-control bg-light border-0" name="tahun_produksi" value="<?php echo $kendaraan['tahun_produksi']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted small fw-bold">KAPASITAS</label>
                                <input type="number" class="form-control bg-light border-0" name="kapasitas" value="<?php echo $kendaraan['kapasitas']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-muted small fw-bold">HARGA / HARI</label>
                                <input type="number" class="form-control bg-light border-0" name="harga_sewa" value="<?php echo $kendaraan['harga_sewa']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">STATUS</label>
                                <select class="form-select bg-light border-0" name="status" required>
                                    <option value="tersedia" <?php echo $kendaraan['status'] == 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                                    <option value="disewa" <?php echo $kendaraan['status'] == 'disewa' ? 'selected' : ''; ?>>Disewa</option>
                                    <option value="perbaikan" <?php echo $kendaraan['status'] == 'perbaikan' ? 'selected' : ''; ?>>Perbaikan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold">GANTI GAMBAR</label>
                                <input type="file" class="form-control bg-light border-0" name="gambar" id="gambarInput">
                            </div>

                            <div class="col-12 mt-3 mb-4 text-center">
                                <p class="text-muted small fw-bold">PREVIEW GAMBAR</p>
                                <div class="img-preview-container mx-auto" style="width: 200px; height: 150px; overflow: hidden; border-radius: 10px; border: 2px solid #eee;">
                                    <?php 
                                    $base_url = 'http://localhost/sewa-kendaraan';
                                    $img_src = $base_url . "/assets/img/" . $kendaraan['gambar'];
                                    ?>
                                    <img id="imgPreview" src="<?php echo $img_src; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='<?php echo $base_url; ?>/assets/img/default.jpg'">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="index.php" class="btn btn-light px-4 border-0">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Update Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview gambar saat dipilih
document.getElementById('gambarInput').onchange = evt => {
    const [file] = document.getElementById('gambarInput').files;
    if (file) {
        document.getElementById('imgPreview').src = URL.createObjectURL(file);
    }
}
</script>

<?php require_once '../../includes/footer.php'; ?>