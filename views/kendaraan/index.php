<?php

$page_title = "Data Kendaraan";
$breadcrumb = [
    ['text' => 'Master Data', 'link' => '#', 'active' => false],
    ['text' => 'Kendaraan', 'link' => '', 'active' => true]
];

$base_dir = __DIR__;
require_once $base_dir . '/../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$base_url = 'http://localhost/sewa-kendaraan';
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$where = '';
if (!empty($search)) {
    $where = "WHERE merk LIKE :search OR model LIKE :search OR plat_nomor LIKE :search";
}

$query_count = "SELECT COUNT(*) as total FROM kendaraan $where";
$stmt_count = $db->prepare($query_count);
if (!empty($search)) { $stmt_count->bindValue(':search', "%$search%"); }
$stmt_count->execute();
$total_rows = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_rows / $limit);
$query = "SELECT * FROM kendaraan $where ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($query);
if (!empty($search)) { $stmt->bindValue(':search', "%$search%"); }
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$kendaraan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h4 mb-0"><i class="bi bi-bicycle"></i> Data Kendaraan</h2>
            <small class="text-muted">Total: <?php echo $total_rows; ?> unit ditemukan</small>
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
                           placeholder="Cari kendaraan (merk, model, plat)..." 
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
                            <th width="100">Gambar</th>
                            <th>Nama Kendaraan</th>
                            <th>Kategori</th>
                            <th>Harga Sewa</th>
                            <th>Tahun</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($kendaraan) > 0): ?>
                            <?php $no = $offset + 1; foreach ($kendaraan as $row): 
                                $img_name = !empty($row['gambar']) ? $row['gambar'] : 'default.jpg';
                                $image_path = $base_url . "/assets/img/" . $img_name;
                            ?>
                            <tr>
                                <td class="text-muted"><?php echo $no++; ?></td>
                                <td>
                                    <div class="img-wrapper">
                                        <img src="<?php echo $image_path; ?>" 
                                             alt="kendaraan" 
                                             onerror="this.src='<?php echo $base_url; ?>/assets/img/default.jpg'">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['merk']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($row['model']); ?> - <?php echo $row['plat_nomor']; ?></small>
                                </td>
                                <td><span class="badge badge-category"><?php echo ucfirst($row['jenis']); ?></span></td>
                                <td><span class="text-primary fw-bold">Rp <?php echo number_format($row['harga_sewa'], 0, ',', '.'); ?></span></td>
                                <td><span class="badge badge-stock"><?php echo $row['tahun_produksi']; ?></span></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm action-btn"><i class="bi bi-pencil-square"></i></a>
                                        <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm action-btn" onclick="return confirm('Hapus data?')"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <p class="text-muted small mb-0">
                    Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?> 
                    (Total <?php echo $total_rows; ?> data)
                </p>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>

                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link shadow-none" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
.custom-table thead th { border-bottom: 2px solid #f1f1f1; font-weight: 600; font-size: 0.85rem; padding-bottom: 15px; }
.custom-table tbody td { border-bottom: 1px solid #f8f9fa; padding: 15px 10px; }
.img-wrapper { width: 65px; height: 50px; background: #fdfdfd; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid #eee; }
.img-wrapper img { max-width: 100%; max-height: 100%; object-fit: contain; }
.badge-category { background-color: #0dcaf0; color: white; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; }
.badge-stock { background-color: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 6px; font-size: 0.75rem; border: 1px solid #ffeeba; }
.action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }
.pagination .page-link { border: none; margin: 0 2px; border-radius: 6px !important; min-width: 32px; text-align: center; }
.pagination .page-item.active .page-link { background-color: #0d6efd; color: white; }
</style>

<?php require_once '../../includes/footer.php'; ?>