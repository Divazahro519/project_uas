<?php

$page_title = "Edit Kendaraan";
$breadcrumb = [
    ['text' => 'Master Data', 'link' => '../kendaraan/index.php', 'active' => false],
    ['text' => 'Kendaraan', 'link' => 'index.php', 'active' => false],
    ['text' => 'Edit', 'link' => '', 'active' => true]
];
require_once '../../includes/header.php';

require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'] ?? 0;

$query = "SELECT * FROM kendaraan WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$kendaraan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kendaraan) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis = $_POST['jenis'];
    $merk = $_POST['merk'];
    $model = $_POST['model'];
    $plat_nomor = strtoupper(trim($_POST['plat_nomor']));
    $tahun_produksi = $_POST['tahun_produksi']; 
    $kapasitas = $_POST['kapasitas']; 
    $harga_sewa = $_POST['harga_sewa']; 
    $status = $_POST['status'];
    
    $check_query = "SELECT id FROM kendaraan WHERE plat_nomor = :plat_nomor AND id != :id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':plat_nomor', $plat_nomor);
    $check_stmt->bindParam(':id', $id);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        $error = "Nomor plat <strong>$plat_nomor</strong> sudah digunakan oleh kendaraan lain!";
    } else {
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $file_tmp = $_FILES['gambar']['tmp_name'];
            $original_name = $_FILES['gambar']['name'];
            $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        
            $file_name = time() . '_' . str_replace(' ', '_', $merk . '_' . $model) . '.' . $file_ext;
            $file_name = preg_replace('/[^a-z0-9_.]/', '', strtolower($file_name));
            
            $upload_dir = '../../assets/img/kendaraan/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_path = $upload_dir . $file_name;
     
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            $file_type = mime_content_type($file_tmp);
            
            if (in_array($file_type, $allowed_types) && in_array($file_ext, $allowed_ext) && 
                $_FILES['gambar']['size'] < 2097152) { // 2MB
                
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $old_image = $kendaraan['gambar'];
                    $default_images = ['bus.jpg', 'minibus.jpg', 'motor.jpg', 'default.jpg'];
                    if (!in_array($old_image, $default_images) && file_exists($upload_dir . $old_image)) {
                        unlink($upload_dir . $old_image);
                    }
                    
                    $gambar = $file_name;
                } else {
                    $error = "Gagal mengupload gambar!";
                    $gambar = $kendaraan['gambar'];
                }
            } else {
                $error = "Format gambar tidak valid atau terlalu besar (max 2MB)!";
                $gambar = $kendaraan['gambar'];
            }
        } else {
            $gambar = $kendaraan['gambar'];
        }
        
        $query = "UPDATE kendaraan SET 
                  jenis = :jenis,
                  merk = :merk,
                  model = :model,
                  plat_nomor = :plat_nomor,
                  tahun_produksi = :tahun_produksi,
                  kapasitas = :kapasitas,
                  harga_sewa = :harga_sewa,
                  gambar = :gambar,
                  status = :status
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':jenis', $jenis);
        $stmt->bindParam(':merk', $merk);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':plat_nomor', $plat_nomor);
        $stmt->bindParam(':tahun_produksi', $tahun_produksi);
        $stmt->bindParam(':kapasitas', $kapasitas);
        $stmt->bindParam(':harga_sewa', $harga_sewa);
        $stmt->bindParam(':gambar', $gambar);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Data kendaraan berhasil diperbarui!";
            header("Location: index.php");
            exit();
        } else {
            $error = isset($error) ? $error : "Gagal memperbarui data kendaraan!";
        }
    }
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Edit Data Kendaraan</h4>
        <div>
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jenis" class="form-label">Jenis Kendaraan <span class="text-danger">*</span></label>
                    <select class="form-select" id="jenis" name="jenis" required>
                        <option value="bus" <?php echo $kendaraan['jenis'] == 'bus' ? 'selected' : ''; ?>>Bus</option>
                        <option value="minibus" <?php echo $kendaraan['jenis'] == 'minibus' ? 'selected' : ''; ?>>Minibus</option>
                        <option value="motor" <?php echo $kendaraan['jenis'] == 'motor' ? 'selected' : ''; ?>>Motor</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="plat_nomor" class="form-label">Nomor Plat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="plat_nomor" name="plat_nomor" 
                           value="<?php echo htmlspecialchars($kendaraan['plat_nomor']); ?>" 
                           placeholder="Contoh: B 1234 ABC" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="merk" class="form-label">Merk <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="merk" name="merk" 
                           value="<?php echo htmlspecialchars($kendaraan['merk']); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="model" name="model" 
                           value="<?php echo htmlspecialchars($kendaraan['model']); ?>" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="tahun_produksi" class="form-label">Tahun Produksi <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="tahun_produksi" name="tahun_produksi" 
                           value="<?php echo $kendaraan['tahun_produksi']; ?>" 
                           min="2000" max="<?php echo date('Y'); ?>" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="kapasitas" class="form-label">Kapasitas (orang) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="kapasitas" name="kapasitas" 
                           value="<?php echo $kendaraan['kapasitas']; ?>" 
                           min="1" max="50" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="harga_sewa" class="form-label">Harga Sewa per Hari <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="harga_sewa" name="harga_sewa" 
                               value="<?php echo $kendaraan['harga_sewa']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="tersedia" <?php echo $kendaraan['status'] == 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                        <option value="disewa" <?php echo $kendaraan['status'] == 'disewa' ? 'selected' : ''; ?>>Disewa</option>
                        <option value="perbaikan" <?php echo $kendaraan['status'] == 'perbaikan' ? 'selected' : ''; ?>>Perbaikan</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="gambar" class="form-label">Gambar Kendaraan</label>
                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                    <div class="form-text">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, GIF. Maksimal 2MB.</div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Gambar Saat Ini</h6>
                        </div>
                        <div class="card-body text-center">
                            <?php 
                            $base_url = 'http://localhost/sewa-kendaraan';
                            $image_url = $base_url . '/assets/img/kendaraan/' . $kendaraan['gambar'];
                            $fallback_url = $base_url . '/assets/img/kendaraan/default.jpg';
                            ?>
                            <img src="<?php echo $image_url; ?>" 
                                 alt="<?php echo htmlspecialchars($kendaraan['merk'] . ' ' . $kendaraan['model']); ?>" 
                                 style="max-width: 300px; max-height: 200px;"
                                 class="img-thumbnail mb-2"
                                 onerror="this.src='<?php echo $fallback_url; ?>'">
                            <div>
                                <small class="text-muted"><?php echo $kendaraan['gambar']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const platInput = document.getElementById('plat_nomor');
    platInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    
    const hargaInput = document.getElementById('harga_sewa');
    hargaInput.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
    
    const fileInput = document.getElementById('gambar');
    const currentImage = document.querySelector('.img-thumbnail');
    
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            if (file.size > 2097152) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }
  
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            
            reader.addEventListener('load', function() {
                currentImage.src = reader.result;
            });
            
            reader.readAsDataURL(file);
        }
    });

    const tahunInput = document.getElementById('tahun_produksi');
    tahunInput.addEventListener('input', function() {
        const currentYear = new Date().getFullYear();
        if (this.value > currentYear) {
            this.value = currentYear;
        }
        if (this.value < 2000) {
            this.value = 2000;
        }
    });
});
</script>

<style>
.img-thumbnail {
    border: 2px solid #dee2e6;
    padding: 5px;
    transition: transform 0.2s;
}

.img-thumbnail:hover {
    transform: scale(1.05);
    cursor: pointer;
}

.form-label {
    font-weight: 500;
}
</style>

<?php require_once '../../includes/footer.php'; ?>