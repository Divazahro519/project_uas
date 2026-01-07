<?php

ob_start();
session_start();
$page_title = "Sewa Kendaraan";
require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

$pelanggan = $db->query("SELECT id, nama FROM pelanggan")->fetchAll(PDO::FETCH_ASSOC);
$kendaraan = $db->query("SELECT id, merk, model, harga_sewa FROM kendaraan WHERE status = 'tersedia'")->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    $kode = "TRX-" . time();
    $tgl_sewa = $_POST['tanggal_sewa'];
    $lama = $_POST['lama_sewa'];
    $tgl_kembali = date('Y-m-d', strtotime($tgl_sewa . " + $lama days"));
    
    $getKnd = $db->prepare("SELECT harga_sewa FROM kendaraan WHERE id = ?");
    $getKnd->execute([$_POST['kendaraan_id']]);
    $knd = $getKnd->fetch();
    $total = $knd['harga_sewa'] * $lama;

    $query = "INSERT INTO transaksi (kode_transaksi, pelanggan_id, kendaraan_id, tanggal_sewa, tanggal_kembali, lama_sewa, total_harga, status) 
              VALUES (?, ?, ?, ?, ?, ?, ?, 'berjalan')";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$kode, $_POST['pelanggan_id'], $_POST['kendaraan_id'], $tgl_sewa, $tgl_kembali, $lama, $total])) {
        $db->prepare("UPDATE kendaraan SET status = 'disewa' WHERE id = ?")->execute([$_POST['kendaraan_id']]);
        header("Location: index.php");
        exit();
    }
}
require_once '../../includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h5>Form Sewa Baru</h5></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pilih Pelanggan</label>
                            <select name="pelanggan_id" class="form-select" required>
                                <?php foreach($pelanggan as $p): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo $p['nama']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pilih Kendaraan (Tersedia)</label>
                            <select name="kendaraan_id" class="form-select" required>
                                <?php foreach($kendaraan as $k): ?>
                                    <option value="<?php echo $k['id']; ?>"><?php echo $k['merk']." ".$k['model']." (Rp ".number_format($k['harga_sewa'])."/hari)"; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Sewa</label>
                                <input type="date" name="tanggal_sewa" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lama Sewa (Hari)</label>
                                <input type="number" name="lama_sewa" class="form-control" min="1" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Proses Sewa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>