<?php
$page_title = "Data Pelanggan";
$breadcrumb = [
    ['text' => 'Master Data', 'link' => '#', 'active' => false],
    ['text' => 'Pelanggan', 'link' => '', 'active' => true]
];

$base_dir = __DIR__;
require_once $base_dir . '/../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Konfigurasi Base URL
$base_url = 'http://localhost/sewa-kendaraan';

// Pagination & Search Logic
$limit = 5; // Samakan dengan data kendaraan (5 data per halaman)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fitur Pencarian Data
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if (!empty($search)) {
    // Mencari berdasarkan NIK, Nama, atau Email
    $where = "WHERE nama LIKE :search OR nik LIKE :search OR email LIKE :search";
    $params[':search'] = "%$search%";
}

// Get total rows untuk hitung halaman
$query_count = "SELECT COUNT(*) as total FROM pelanggan $where";
$stmt_count = $db->prepare($query_count);
foreach ($params as $key => $val) { $stmt_count->bindValue($key, $val); }
$stmt_count->execute();
$total_rows = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_rows / $limit);

// Get Data Pelanggan dengan LIMIT & OFFSET
$query = "SELECT * FROM pelanggan $where ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-0"><i class="bi bi-people"></i> Data Pelanggan</h2>
            <small class="text-muted">Total: <?php echo $total_rows; ?> pelanggan ditemukan</small>
        </div>
        <a href="../../index.php" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form method="GET" class="row g-2 mb-4">
                <div class="col-md-8">
                    <input type="text" class="form-control bg-light border-0 py-2" name="search" 
                           placeholder="Cari NIK, Nama, atau Email..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="index.php" class="btn btn-secondary w-100 py-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle custom-table">
                    <thead>
                        <tr class="text-muted">
                            <th width="40">#</th>
                            <th>Informasi Pelanggan</th>
                            <th>Alamat</th>
                            <th>Kontak</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($pelanggan) > 0): ?>
                            <?php $no = $offset + 1; foreach ($pelanggan as $row): ?>
                            <tr>
                                <td class="text-muted"><?php echo $no++; ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama']); ?></div>
                                    <small class="text-muted">NIK: <?php echo htmlspecialchars($row['nik']); ?></small>
                                </td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($row['alamat']); ?></small></td>
                                <td>
                                    <div class="small mb-1"><i class="bi bi-telephone text-primary me-1"></i> <?php echo htmlspecialchars($row['no_telepon']); ?></div>
                                    <div class="small"><i class="bi bi-envelope text-primary me-1"></i> <?php echo htmlspecialchars($row['email']); ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm action-btn">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm action-btn" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus data pelanggan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada data pelanggan ditemukan.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <p class="text-muted small mb-0">Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?></p>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link shadow-none" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                        <?php for($i=1; $i<=$total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link shadow-none" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* CSS Konsisten dengan Data Kendaraan */
.custom-table thead th { border-bottom: 2px solid #f1f1f1; font-weight: 600; font-size: 0.85rem; padding-bottom: 15px; }
.custom-table tbody td { border-bottom: 1px solid #f8f9fa; padding: 15px 10px; }
.action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }
.pagination .page-link { border: none; color: #0d6efd; margin: 0 2px; border-radius: 6px !important; min-width: 32px; text-align: center; }
.pagination .page-item.active .page-link { background-color: #0d6efd; color: white; }
</style>

<?php require_once '../../includes/footer.php'; ?>