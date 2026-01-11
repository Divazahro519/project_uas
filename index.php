<?php

$page_title = "Dashboard Utama";
require_once 'includes/header.php';
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$stats = [];
$stats['total_kendaraan'] = $db->query("SELECT COUNT(*) FROM kendaraan")->fetchColumn();
$stats['kendaraan_tersedia'] = $db->query("SELECT COUNT(*) FROM kendaraan WHERE status = 'tersedia'")->fetchColumn();
$stats['total_pelanggan'] = $db->query("SELECT COUNT(*) FROM pelanggan")->fetchColumn();
$stats['transaksi_aktif'] = $db->query("SELECT COUNT(*) FROM transaksi WHERE status = 'berjalan'")->fetchColumn();

$query_recent = "SELECT t.*, p.nama as nama_pelanggan, k.merk, k.model 
                 FROM transaksi t 
                 JOIN pelanggan p ON t.pelanggan_id = p.id 
                 JOIN kendaraan k ON t.kendaraan_id = k.id 
                 ORDER BY t.created_at DESC LIMIT 5";
$recent_transaksi = $db->query($query_recent)->fetchAll(PDO::FETCH_ASSOC);

$status_kendaraan = $db->query("SELECT status, COUNT(*) as jumlah FROM kendaraan GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid py-4">
    <div class="row g-4">
        
        <div class="col-md-8">
            
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-2">
                                <i class="bi bi-bicycle text-primary h4 mb-0"></i>
                            </div>
                            <h6 class="text-muted small">Total Motor</h6>
                            <h4 class="fw-bold mb-0"><?php echo $stats['total_kendaraan']; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-2">
                                <i class="bi bi-check2-circle text-success h4 mb-0"></i>
                            </div>
                            <h6 class="text-muted small">Tersedia</h6>
                            <h4 class="fw-bold mb-0"><?php echo $stats['kendaraan_tersedia']; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-2">
                                <i class="bi bi-people text-info h4 mb-0"></i>
                            </div>
                            <h6 class="text-muted small">Pelanggan</h6>
                            <h4 class="fw-bold mb-0"><?php echo $stats['total_pelanggan']; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body text-center">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-inline-block mb-2">
                                <i class="bi bi-clock-history text-warning h4 mb-0"></i>
                            </div>
                            <h6 class="text-muted small">Sewa Aktif</h6>
                            <h4 class="fw-bold mb-0"><?php echo $stats['transaksi_aktif']; ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi- receipt me-2"></i>Transaksi Terbaru</h5>
                    <a href="views/transaksi/index.php" class="btn btn-sm btn-link text-decoration-none">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 custom-table">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4">KODE</th>
                                    <th>PELANGGAN</th>
                                    <th>UNIT MOTOR</th>
                                    <th>TGL SEWA</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($recent_transaksi) > 0): ?>
                                    <?php foreach ($recent_transaksi as $trans): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-primary small"><?php echo $trans['kode_transaksi']; ?></td>
                                            <td><?php echo htmlspecialchars($trans['nama_pelanggan']); ?></td>
                                            <td><?php echo htmlspecialchars($trans['merk'] . ' ' . $trans['model']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($trans['tanggal_sewa'])); ?></td>
                                            <td>
                                                <span class="badge rounded-pill <?php 
                                                    echo $trans['status'] == 'berjalan' ? 'bg-warning bg-opacity-10 text-warning' : 
                                                        ($trans['status'] == 'selesai' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'); 
                                                ?> px-3">
                                                    <?php echo ucfirst($trans['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi sewa motor.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Unit Motor Terpopuler</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <img src="assets/img/aerox.jpg" class="card-img-top" style="height: 160px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x160?text=Yamaha+Aerox'">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">Yamaha Aerox 155</h6>
                            <p class="small text-muted mb-3">Motor matic sporty dengan performa tinggi dan irit bahan bakar.</p>
                            <a href="views/transaksi/sewa.php" class="btn btn-outline-primary btn-sm w-100">Sewa Sekarang</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <img src="assets/img/klx.jpg" class="card-img-top" style="height: 160px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x160?text=Yamaha+NMax'">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">Kawasaki KLX 15</h6>
                            <p class="small text-muted mb-3">Kenyamanan berkendara premium untuk perjalanan jarak jauh.</p>
                            <a href="views/transaksi/sewa.php" class="btn btn-outline-primary btn-sm w-100">Sewa Sekarang</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <img src="assets/img/vario.jpg" class="card-img-top" style="height: 160px; object-fit: cover;" onerror="this.src='https://via.placeholder.com/300x160?text=Honda+Vario'">
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">Honda Vario 160</h6>
                            <p class="small text-muted mb-3">Lincah di kemacetan kota dengan bagasi yang sangat luas.</p>
                            <a href="views/transaksi/sewa.php" class="btn btn-outline-primary btn-sm w-100">Sewa Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">Aksi Cepat</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="d-grid gap-2">
                        <a href="views/transaksi/sewa.php" class="btn btn-primary py-2 rounded-3 text-start">
                            <i class="bi bi-plus-circle me-2"></i> Buat Sewa Baru
                        </a>
                        <a href="views/kendaraan/create.php" class="btn btn-outline-dark py-2 rounded-3 text-start border-light-subtle">
                            <i class="bi bi-bicycle me-2"></i> Tambah Unit Motor
                        </a>
                        <a href="views/pelanggan/create.php" class="btn btn-outline-dark py-2 rounded-3 text-start border-light-subtle">
                            <i class="bi bi-person-plus me-2"></i> Registrasi Pelanggan
                        </a>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0">Status Unit</h5>
                </div>
                <div class="card-body pt-0">
                    <?php foreach ($status_kendaraan as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded-3">
                            <span class="small fw-semibold"><?php echo strtoupper($item['status']); ?></span>
                            <span class="badge bg-dark rounded-pill"><?php echo $item['jumlah']; ?> Unit</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-table tbody td {
    padding: 15px 10px;
    border-bottom: 1px solid #f1f1f1;
    font-size: 0.85rem;
}
.custom-table thead th {
    padding: 12px 10px;
    letter-spacing: 0.5px;
}
.rounded-4 { border-radius: 1.2rem !important; }
.card { transition: transform 0.2s ease; }
.card:hover { transform: translateY(-3px); }
.bg-opacity-10 { --bs-bg-opacity: 0.12; }
</style>

<?php require_once 'includes/footer.php'; ?>