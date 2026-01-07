<?php

$page_title = "Data Pelanggan";
$base_dir = __DIR__;
require_once $base_dir . '/../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$base_url = 'http://localhost/sewa-kendaraan';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = !empty($search) ? "WHERE nama LIKE :search OR nik LIKE :search OR email LIKE :search" : '';
$query = "SELECT * FROM pelanggan $where ORDER BY id DESC";
$stmt = $db->prepare($query);
if (!empty($search)) { $stmt->bindValue(':search', "%$search%"); }
$stmt->execute();
$pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-0"><i class="bi bi-people"></i> Data Pelanggan</h2>
            <small class="text-muted">Total: <?php echo count($pelanggan); ?> pelanggan terdaftar</small>
        </div>
        <div>
            <a href="../../index.php" class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="create.php" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Pelanggan
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-10">
                    <input type="text" class="form-control bg-light border-0" name="search" placeholder="Cari NIK, Nama, atau Email..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle custom-table">
                    <thead>
                        <tr class="text-muted">
                            <th>#</th>
                            <th>Informasi Pelanggan</th>
                            <th>Alamat</th>
                            <th>Kontak</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pelanggan) > 0): $no = 1; foreach ($pelanggan as $row): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['nama']); ?></div>
                                <small class="text-muted">NIK: <?php echo htmlspecialchars($row['nik']); ?></small>
                            </td>
                            <td><small><?php echo htmlspecialchars($row['alamat']); ?></small></td>
                            <td>
                                <div class="small"><i class="bi bi-telephone text-primary"></i> <?php echo htmlspecialchars($row['no_telepon']); ?></div>
                                <div class="small"><i class="bi bi-envelope text-primary"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                            </td>
                            <td class="text-center">
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus pelanggan ini?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.custom-table thead th { border-bottom: 2px solid #f8f9fa; padding: 15px 10px; font-size: 0.85rem; }
.custom-table tbody td { border-bottom: 1px solid #f8f9fa; padding: 12px 10px; }
.bg-light { background-color: #f8f9fa !important; }
</style>

<?php require_once '../../includes/footer.php'; ?>