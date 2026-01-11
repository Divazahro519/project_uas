<?php
ob_start(); 
session_start();

require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $plat_nomor = strtoupper(trim($_POST['plat_nomor']));
    $tahun_produksi = $_POST['tahun_produksi'];
    $kapasitas = $_POST['kapasitas'];
    $harga_sewa = $_POST['harga_sewa'];
    
    $check_query = "SELECT id FROM kendaraan WHERE plat_nomor = :plat_nomor";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':plat_nomor', $plat_nomor);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $error = "Nomor plat <strong>$plat_nomor</strong> sudah terdaftar!";
    } else {
        $gambar = '';
        if (!empty($_FILES['gambar']['name'])) {
            $file_name = $_FILES['gambar']['name'];
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $file_size = $_FILES['gambar']['size'];
            $file_error = $_FILES['gambar']['error'];
            
            if ($file_error === 0) {
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($file_ext, $allowed_ext)) {
                    if ($file_size <= 2097152) { 
                        $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
                        $upload_path = '../../assets/img/' . $new_file_name;
                        
                        if (!is_dir('../../assets/img/')) {
                            mkdir('../../assets/img/', 0777, true);
                        }
                        
                        if (move_uploaded_file($file_tmp, $upload_path)) {
                            $gambar = $new_file_name;
                        }
                    } else {
                        $error = "Ukuran gambar terlalu besar (max 2MB)";
                    }
                } else {
                    $error = "Format gambar tidak didukung (hanya JPG, PNG, GIF)";
                }
            }
        }
        if (empty($gambar)) {
            $gambar = 'default.jpg';
        }
        if (!isset($error)) {
            $query = "INSERT INTO kendaraan (jenis, merk, model, plat_nomor, tahun_produksi, kapasitas, harga_sewa, gambar, status) 
                      VALUES (:jenis, :merk, :model, :plat_nomor, :tahun_produksi, :kapasitas, :harga_sewa, :gambar, 'tersedia')";
            
            try {
                $stmt = $db->prepare($query);
                $stmt->bindParam(':jenis', $jenis);
                $stmt->bindParam(':merk', $merk);
                $stmt->bindParam(':model', $model);
                $stmt->bindParam(':plat_nomor', $plat_nomor);
                $stmt->bindParam(':tahun_produksi', $tahun_produksi);
                $stmt->bindParam(':kapasitas', $kapasitas);
                $stmt->bindParam(':harga_sewa', $harga_sewa);
                $stmt->bindParam(':gambar', $gambar);
                
                if ($stmt->execute()) {
                    header("Location: index.php");
                    exit();
                }
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    } 
}

$page_title = "Tambah Kendaraan";
$breadcrumb = [
    ['text' => 'Master Data', 'link' => '#', 'active' => false],
    ['text' => 'Kendaraan', 'link' => 'index.php', 'active' => false],
    ['text' => 'Tambah', 'link' => '', 'active' => true]
];
require_once '../../includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">Tambah Data Kendaraan</h4>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label text-muted small fw-bold">JENIS KENDARAAN</label>
                                <select class="form-select bg-light border-0" id="jenis" name="jenis" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="bus">Bus</option>
                                    <option value="minibus">Minibus</option>
                                    <option value="motor">Motor</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="plat_nomor" class="form-label text-muted small fw-bold">NOMOR PLAT</label>
                                <input type="text" class="form-control bg-light border-0" id="plat_nomor" name="plat_nomor" placeholder="B 1234 ABC" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="merk" class="form-label text-muted small fw-bold">MERK</label>
                                <input type="text" class="form-control bg-light border-0" id="merk" name="merk" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="model" class="form-label text-muted small fw-bold">MODEL</label>
                                <input type="text" class="form-control bg-light border-0" id="model" name="model" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="tahun_produksi" class="form-label text-muted small fw-bold">TAHUN PRODUKSI</label>
                                <input type="number" class="form-control bg-light border-0" id="tahun_produksi" name="tahun_produksi" min="2000" max="<?php echo date('Y'); ?>" value="<?php echo date('Y'); ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="kapasitas" class="form-label text-muted small fw-bold">KAPASITAS</label>
                                <input type="number" class="form-control bg-light border-0" id="kapasitas" name="kapasitas" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="harga_sewa" class="form-label text-muted small fw-bold">HARGA SEWA / HARI</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">Rp</span>
                                    <input type="number" class="form-control bg-light border-0" id="harga_sewa" name="harga_sewa" required>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mb-4">
                                <label for="gambar" class="form-label text-muted small fw-bold">GAMBAR KENDARAAN</label>
                                <input type="file" class="form-control bg-light border-0" id="gambar" name="gambar" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="index.php" class="btn btn-light border-0 px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('plat_nomor').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require_once '../../includes/footer.php'; ?>
