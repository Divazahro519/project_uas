<?php

$page_title = "Data Transaksi";
require_once '../../includes/header.php';
require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT t.*, p.nama as nama_pelanggan, k.merk, k.model 
          FROM transaksi t
          JOIN pelanggan p ON t.pelanggan_id = p.id
          JOIN kendaraan k ON t.kendaraan_id = k.id
          ORDER BY t.id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0"><i class="bi bi- receipt"></i> Riwayat Transaksi</h2>
            <small class="text-muted">Kelola penyewaan dan pengembalian kendaraan</small>
        </div>
        <a href="sewa.php" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg"></i> Sewa Baru
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Kode</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Tgl Sewa</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transaksi as $row): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-primary"><?php echo $row['kode_transaksi']; ?></td>
                            <td><?php echo $row['nama_pelanggan']; ?></td>
                            <td><?php echo $row['merk'] . " " . $row['model']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal_sewa'])); ?></td>
                            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php 
                                $status_class = [
                                    'berjalan' => 'bg-info',
                                    'selesai' => 'bg-success',
                                    'terlambat' => 'bg-danger'
                                ];
                                ?>
                                <span class="badge <?php echo $status_class[$row['status']]; ?>">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($row['status'] == 'berjalan' || $row['status'] == 'terlambat'): ?>
                                    <a href="pengembalian.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-arrow-return-left"></i> Kembalikan
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small italic">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/header.php'; ?>