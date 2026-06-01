<?php
// Memulai session untuk mengelola status login
session_start(); 

include 'koneksi.php';

// Ambil data kategori untuk menu dropdown filter
$kategori_res = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Logika Filter Kategori
$filter_kategori = "";
$kategori_aktif_nama = "Semua Produk";
if (isset($_GET['kategori_id']) && $_GET['kategori_id'] != "") {
    $id_kat = (int)$_GET['kategori_id'];
    $filter_kategori = " WHERE produk.kategori_id = $id_kat ";
    
    $nama_kat_query = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE id = $id_kat");
    if($kat_row = mysqli_fetch_assoc($nama_kat_query)){
        $kategori_aktif_nama = $kat_row['nama_kategori'];
    }
}

// Query untuk mengambil data produk
$query_string = "SELECT produk.*, kategori.nama_kategori 
                 FROM produk 
                 JOIN kategori ON produk.kategori_id = kategori.id 
                 $filter_kategori 
                 ORDER BY produk.id DESC";
$produk_query = mysqli_query($koneksi, $query_string); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Outdoor Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .card-product { transition: transform 0.3s ease, box-shadow 0.3s ease; border: none; overflow: hidden; border-radius: 12px; }
        .card-product:hover { transform: translateY(-8px); box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important; }
        .product-img-wrapper { position: relative; width: 100%; height: 300px; background-color: #f8f9fa; overflow: hidden; }
        .product-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .card-product:hover .product-img { transform: scale(1.07); }
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px; }
    </style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php">⛺ OUTDOOR STORE</a>
            <span class="navbar-text text-white-50 d-none d-sm-inline">Perlengkapan Petualang Terbaik</span>
        </div>
    </nav>

    <div class="bg-white py-5 shadow-sm mb-4">
        <div class="container-fluid px-4">
            <div class="row align-items-center position-relative">
                
                <div class="col-lg-3 text-center text-lg-start mb-3 mb-lg-0">
                    <a href="tambah_produk.php?pemicu=tambah" class="btn btn-primary fw-bold px-4 py-2 shadow-sm">
                        ➕ Tambah Produk (Admin)
                    </a>
                </div>
                
                <div class="col-lg-6 text-center">
                    <h1 class="fw-bold text-dark fs-2 mb-2">Katalog Produk Resmi</h1>
                    <p class="text-muted fs-5 mb-0">Temukan gear petualanganmu di sini dengan harga terbaik.</p>
                </div>
                
                <div class="col-lg-3 text-center text-lg-end mt-3 mt-lg-0">
                    <div class="dropdown">
                        <button class="btn btn-outline-dark dropdown-toggle text-capitalize fw-bold px-4 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            📁 Kategori: <?= $kategori_aktif_nama; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-menu-item dropdown-item" href="index.php">📦 Semua Produk</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php 
                            mysqli_data_seek($kategori_res, 0);
                            while($kat = mysqli_fetch_assoc($kategori_res)): 
                            ?>
                                <li>
                                    <a class="dropdown-item text-capitalize" href="index.php?kategori_id=<?= $kat['id']; ?>">
                                        🔹 <?= $kat['nama_kategori']; ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="container-fluid px-4 mb-5 flex-grow-1">
        <?php if (isset($_GET['beli_status'])): ?>
            <?php if ($_GET['beli_status'] == 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 text-center" role="alert">
                    🎉 <strong>Berhasil Beli!</strong> Kamu telah sukses memesan <strong><?= htmlspecialchars($_GET['nama_barang']); ?></strong> menggunakan metode <strong><?= htmlspecialchars($_GET['metode'] ?? 'Transfer'); ?></strong>. Stok barang otomatis dikurangi!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['beli_status'] == 'habis'): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4 text-center" role="alert">
                    ❌ <strong>Gagal!</strong> Maaf, produk tersebut baru saja habis dipesan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
            <?php 
            if(mysqli_num_rows($produk_query) > 0) {
                while($row = mysqli_fetch_assoc($produk_query)) { 
                    $stok = isset($row['stok']) ? (int)$row['stok'] : 0;
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm card-product">
                        <div class="product-img-wrapper border-bottom">
                            <?php if($row['gambar'] && file_exists("uploads/" . $row['gambar'])): ?>
                                <img src="uploads/<?= $row['gambar']; ?>" class="product-img" alt="<?= $row['nama_produk']; ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 text-muted d-flex flex-column align-items-center justify-content-center bg-secondary-subtle">
                                    <span style="font-size: 32px;">📷</span>
                                    <span class="small fw-bold mt-1" style="font-size: 12px;">No Image Available</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column bg-white p-3">
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning text-dark text-uppercase fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">
                                    <?= $row['nama_kategori']; ?>
                                </span>
                                <?php if($stok > 0): ?>
                                    <span class="badge bg-success-subtle text-success fw-bold" style="font-size: 10px;">Stok: <?= $stok; ?> pcs</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger fw-bold" style="font-size: 10px;">Habis</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title fw-bold text-dark mb-1 text-capitalize text-truncate" style="font-size: 16px;" title="<?= $row['nama_produk']; ?>">
                                <?= $row['nama_produk']; ?>
                            </h5>
                            <p class="card-text text-muted small flex-grow-1 mb-3 text-truncate-2">
                                <?= $row['deskripsi'] ? nl2br($row['deskripsi']) : 'Tidak ada deskripsi produk.' ?>
                            </p>
                            <div class="pt-2 border-top">
                                <span class="text-muted small d-block" style="font-size: 11px;">Harga</span>
                                <span class="fs-5 fw-bold text-success">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-3 pt-0">
                            <div class="d-grid">
                                <?php if($stok > 0): ?>
                                    <a href="pembayaran.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm fw-bold shadow-sm py-2">
                                        🛒 Beli Langsung
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm fw-bold py-2" disabled>❌ Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                } 
            } else {
                echo '<div class="col-12 text-center py-5 text-muted"><h3>Maaf, tidak ada produk di kategori ini.</h3></div>';
            }
            ?>
        </div>

        <div class="row mt-5 pt-4 border-top">
            <div class="col-12 text-center">
                <p class="text-muted small mb-2">Sudah selesai melihat-lihat atau berbelanja?</p>
                <a href="logout.php" class="btn btn-danger fw-bold px-4 py-2 shadow" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem Outdoor Store?')">
                    🚪 Keluar dari Toko (Logout)
                </a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <p class="mb-0 small">&copy; 2026 Outdoor Store. All Rights Reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>