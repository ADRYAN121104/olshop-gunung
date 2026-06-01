<?php
include 'koneksi.php';

// Mengambil ID produk dari halaman pembayaran sebelumnya
if (!isset($_GET['id_produk']) || $_GET['id_produk'] == "") {
    header("Location: index.php");
    exit;
}

$id_produk    = (int)$_GET['id_produk'];
$metode_bayar = "BRI"; // Dikunci langsung ke BRI

// Ambil info produk berdasarkan ID
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id = $id_produk");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    header("Location: index.php");
    exit;
}

// Data Rekening BRI Sesuai Request
$nama_bank      = "BRI";
$nomor_rekening = "464501015061532";
$atas_nama      = "MUHAMMAD ADRYAN RAMADHAN";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran - Outdoor Store</title>
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
            <div class="col-md-8 col-lg-5">
                <div class="card shadow border-0 rounded-3 bg-white">
                    <div class="card-body p-4 text-center">
                        
                        <div class="text-success mb-3" style="font-size: 50px;">📄</div>
                        <h4 class="fw-bold text-dark mb-1">Nota Tagihan Pembayaran</h4>
                        <p class="text-muted small">Silakan selesaikan pembayaran pesanan Anda di bawah ini</p>
                        
                        <hr>

                        <div class="p-3 bg-warning-subtle rounded text-start mb-4 border border-warning-subtle">
                            <h6 class="fw-bold text-dark mb-2">💳 Instruksi Transfer Bank (<?= $nama_bank; ?>):</h6>
                            <p class="mb-1 small text-secondary">Silakan transfer ke nomor rekening berikut:</p>
                            
                            <h5 class="fw-bold text-danger font-monospace mb-1" style="letter-spacing: 1px;">
                                <?= $nomor_rekening; ?>
                            </h5>
                            
                            <p class="small text-dark fw-bold mb-3">
                                A/N: <?= $atas_nama; ?>
                            </p>
                            
                            <p class="mb-0 small text-muted">*Harap transfer sesuai dengan nominal total tagihan di bawah agar proses verifikasi lancar.</p>
                        </div>

                        <div class="text-start mb-4">
                            <h6 class="fw-bold text-secondary mb-2">Detail Produk:</h6>
                            <div class="d-flex justify-content-between">
                                <span class="text-capitalize text-dark fw-medium"><?= htmlspecialchars($produk['nama_produk']); ?> (1x)</span>
                                <span class="fw-bold text-dark">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border mb-4">
                            <span class="fw-bold text-muted text-uppercase" style="font-size: 12px;">Total Tagihan Anda</span>
                            <span class="fs-4 fw-bold text-success">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                        </div>

                        <form action="proses_beli.php" method="POST">
                            <input type="hidden" name="id_produk" value="<?= $produk['id']; ?>">
                            <input type="hidden" name="metode_bayar" value="<?= $nama_bank; ?>">
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="konfirmasi_bayar" class="btn btn-success fw-bold py-2 shadow-sm" onclick="return confirm('Apakah Anda yakin sudah melakukan transfer dan ingin menyelesaikan transaksi?')">
                                    ✅ Saya Sudah Transfer & Selesai
                                </button>
                                <a href="index.php" class="text-muted small mt-2 text-decoration-none">← Kembali ke Katalog Utama</a>
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

</body>
</html>