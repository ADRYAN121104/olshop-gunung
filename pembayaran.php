<?php
include 'koneksi.php';

if (!isset($_GET['id']) || $_GET['id'] == "") {
    header("Location: index.php");
    exit;
}

$id_produk = (int)$_GET['id'];

$query = mysqli_query($koneksi, "SELECT produk.*, kategori.nama_kategori 
                                 FROM produk 
                                 JOIN kategori ON produk.kategori_id = kategori.id 
                                 WHERE produk.id = $id_produk");
$produk = mysqli_fetch_assoc($query);

if (!$produk || $produk['stok'] <= 0) {
    header("Location: index.php?beli_status=habis");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran - Outdoor Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php">⛺ OUTDOOR STORE</a>
        </div>
    </nav>

    <div class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow border-0 rounded-3">
                    <div class="card-header bg-dark text-white fw-bold text-center py-3">
                        🔒 PILIH METODE PEMBAYARAN
                    </div>
                    <div class="card-body p-4 bg-white">
                        
                        <h5 class="fw-bold text-secondary mb-3">Ringkasan Pesanan</h5>
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border mb-4">
                            <?php if($produk['gambar'] && file_exists("uploads/" . $produk['gambar'])): ?>
                                <img src="uploads/<?= $produk['gambar']; ?>" class="rounded border" style="width: 70px; height: 70px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary text-white rounded text-center d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-size: 10px;">NO IMG</div>
                            <?php endif; ?>
                            <div>
                                <span class="badge bg-warning text-dark text-uppercase mb-1" style="font-size: 9px;"><?= $produk['nama_kategori']; ?></span>
                                <h6 class="fw-bold mb-0 text-capitalize"><?= htmlspecialchars($produk['nama_produk']); ?></h6>
                                <span class="fw-bold text-success">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                            </div>
                        </div>

                        <form action="nota_pembayaran.php" method="GET">
                            <input type="hidden" name="id_produk" value="<?= $produk['id']; ?>">

                            <h5 class="fw-bold text-secondary mb-3">Metode Pembayaran Tersedia</h5>
                            
                            <div class="mb-3">
                                <div class="form-check p-3 border border-primary bg-primary-subtle rounded d-flex align-items-center">
                                    <input class="form-check-input ms-1" type="radio" name="metode_bayar" id="bank_bri" value="BRI" checked required>
                                    <label class="form-check-content form-check-label w-100 ms-3 fw-bold text-dark" for="bank_bri">
                                        🏦 Transfer Bank BRI (Dicek Manual)
                                    </label>
                                </div>
                            </div>

                            <div class="border-top pt-3 mt-4 d-flex justify-content-between align-items-center mb-4">
                                <span class="fw-bold text-muted">Total Tagihan:</span>
                                <span class="fs-4 fw-bold text-success">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                                    Lanjut ke Nota Pembayaran ➡️
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary py-2 fw-bold">❌ Batalkan</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0 small">&copy; 2026 Outdoor Store. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>